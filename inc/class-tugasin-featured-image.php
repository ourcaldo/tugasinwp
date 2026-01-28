<?php
/**
 * TugasinWP Featured Image Generator
 *
 * Automatically generates featured images for posts without one.
 * Uses Pixabay API for backgrounds with dynamic keyword extraction from post titles.
 *
 * @package TugasinWP
 * @since 2.19.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class Tugasin_Featured_Image
 *
 * Handles automatic featured image generation for posts.
 */
class Tugasin_Featured_Image
{

    /**
     * Image dimensions
     */
    private const WIDTH = 1200;
    private const HEIGHT = 630;

    /**
     * Indonesian stopwords to remove from title when extracting keywords
     */
    private static $stopwords = array(
        'yang',
        'dan',
        'di',
        'ke',
        'dari',
        'untuk',
        'pada',
        'adalah',
        'ini',
        'itu',
        'dengan',
        'tidak',
        'juga',
        'akan',
        'atau',
        'ada',
        'mereka',
        'sudah',
        'saya',
        'kamu',
        'kami',
        'kalian',
        'anda',
        'bisa',
        'bagi',
        'secara',
        'dalam',
        'seperti',
        'sebagai',
        'oleh',
        'karena',
        'jika',
        'maka',
        'saat',
        'ketika',
        'harus',
        'dapat',
        'telah',
        'sebuah',
        'setiap',
        'antara',
        'namun',
        'tetapi',
        'bahwa',
        'tentang',
        'wajib',
        'kamu',
        'pahami',
        'penting',
        'cara',
        'tips',
        'trik',
        'langkah',
        'the',
        'a',
        'an',
        'is',
        'are',
        'was',
        'were',
        'be',
        'been',
        'being',
        'have',
        'has',
        'had',
        'do',
        'does',
        'did',
        'will',
        'would',
        'could',
        'should',
        'may',
        'might',
        'must',
        'shall',
        'can',
        'need',
        'dare',
        'ought',
        'used',
        'to',
        'of',
        'in',
        'for',
        'on',
        'with',
        'at',
        'by',
        'from',
        'as',
        'into',
        'through',
        'during',
        'before',
        'after',
        'above',
        'below',
        'between',
        'under',
    );

    /**
     * Constructor - hooks into WordPress
     */
    public function __construct()
    {
        // Hook into post publish/update - now fires async request
        add_action('save_post', array($this, 'maybe_generate_featured_image'), 20, 3);

        // Add meta box for manual regeneration
        add_action('add_meta_boxes', array($this, 'add_regenerate_meta_box'));

        // Handle AJAX regeneration (manual button in admin)
        add_action('wp_ajax_tugasin_regenerate_featured_image', array($this, 'ajax_regenerate'));

        // Handle ASYNC generation (background, non-blocking)
        add_action('wp_ajax_tugasin_fig_async', array($this, 'handle_async_generation'));
        add_action('wp_ajax_nopriv_tugasin_fig_async', array($this, 'handle_async_generation'));

        // Cron schedule for backfill
        add_filter('cron_schedules', array($this, 'add_cron_schedule'));

        // Backfill cron hook
        add_action('tugasin_fig_backfill_cron', array($this, 'process_backfill_queue'));

        // Schedule/unschedule cron based on setting
        add_action('update_option_tugasin_fig_enable_backfill', array($this, 'toggle_backfill_cron'), 10, 2);

        // Initialize cron on first load if enabled
        if (get_option('tugasin_fig_enable_backfill', false) && !wp_next_scheduled('tugasin_fig_backfill_cron')) {
            wp_schedule_event(time(), 'tugasin_30_mins', 'tugasin_fig_backfill_cron');
        }
    }

    /**
     * Add custom cron schedule for 30 minutes
     * 
     * @param array $schedules Existing schedules.
     * @return array Modified schedules.
     */
    public function add_cron_schedule($schedules)
    {
        $schedules['tugasin_30_mins'] = array(
            'interval' => 1800, // 30 minutes in seconds
            'display' => __('Every 30 Minutes', 'tugasin'),
        );
        return $schedules;
    }

    /**
     * Toggle backfill cron based on setting change
     * 
     * @param mixed $old_value Old option value.
     * @param mixed $new_value New option value.
     */
    public function toggle_backfill_cron($old_value, $new_value)
    {
        $timestamp = wp_next_scheduled('tugasin_fig_backfill_cron');

        if ($new_value && !$timestamp) {
            // Enable: schedule cron
            wp_schedule_event(time(), 'tugasin_30_mins', 'tugasin_fig_backfill_cron');
        } elseif (!$new_value && $timestamp) {
            // Disable: unschedule cron
            wp_unschedule_event($timestamp, 'tugasin_fig_backfill_cron');
        }
    }

    /**
     * Process backfill queue - finds posts without featured images and generates them
     */
    public function process_backfill_queue()
    {
        // Check if backfill is enabled
        if (!get_option('tugasin_fig_enable_backfill', false)) {
            return;
        }

        // Check if FIG is enabled
        if (!get_option('tugasin_fig_enabled', false)) {
            return;
        }

        $this->log('Backfill cron starting...', 'info');

        // Query posts without featured images
        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 5, // Limit to 5 per run
            'meta_query' => array(
                array(
                    'key' => '_thumbnail_id',
                    'compare' => 'NOT EXISTS',
                ),
            ),
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $posts = get_posts($args);

        if (empty($posts)) {
            $this->log('Backfill: No posts without featured images found.', 'info');
            return;
        }

        $this->log('Backfill: Found ' . count($posts) . ' posts to process.', 'info');

        foreach ($posts as $post) {
            $this->log('Backfill: Processing post ID ' . $post->ID . ' - ' . $post->post_title, 'info');

            $result = $this->generate_image($post->ID);

            if ($result) {
                $this->log('Backfill: Successfully generated image for post ID ' . $post->ID, 'info');
            } else {
                $this->log('Backfill: Failed to generate image for post ID ' . $post->ID, 'error');
            }

            // Small delay to prevent API rate limiting
            usleep(500000); // 0.5 seconds
        }

        $this->log('Backfill cron completed.', 'info');
    }

    /**
     * Log debug messages
     *
     * @param string $message Message to log.
     * @param string $level   Log level (info, error, debug).
     */
    private function log($message, $level = 'info')
    {
        // Only log errors
        if ($level !== 'error') {
            return;
        }

        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        $prefix = '[TugasinWP FIG] [ERROR] ';
        error_log($prefix . $message);
    }

    /**
     * Check if post needs featured image and trigger async generation
     * 
     * This method fires a non-blocking AJAX request to generate the image
     * in the background, so the save_post action returns immediately.
     *
     * @param int     $post_id Post ID.
     * @param WP_Post $post    Post object.
     * @param bool    $update  Whether this is an update.
     */
    public function maybe_generate_featured_image($post_id, $post, $update)
    {
        // Only for 'post' type
        if ($post->post_type !== 'post') {
            return;
        }

        // Only for published posts
        if ($post->post_status !== 'publish') {
            return;
        }

        // Skip autosaves and revisions
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // Check if feature is enabled
        $settings = self::get_settings();
        if (!$settings['enabled']) {
            return;
        }

        // Skip if already has featured image
        if (has_post_thumbnail($post_id)) {
            return;
        }

        // Prevent duplicate async requests for same post
        $lock_key = 'tugasin_fig_lock_' . $post_id;
        if (get_transient($lock_key)) {
            return;
        }
        set_transient($lock_key, true, 60); // Lock for 60 seconds

        // Fire async request (non-blocking)
        $this->fire_async_generation($post_id);
    }

    /**
     * Fire a non-blocking AJAX request to generate featured image
     *
     * @param int $post_id Post ID.
     */
    /**
     * Fire a non-blocking AJAX request to generate featured image
     *
     * @param int $post_id Post ID.
     */
    private function fire_async_generation($post_id)
    {
        $ajax_url = admin_url('admin-ajax.php');

        // Create a secure token for this specific post
        // Using transient because nonce verification fails in async context (different session)
        $token = wp_generate_password(32, false);
        set_transient('tugasin_fig_token_' . $post_id, $token, 300); // Valid for 5 minutes

        // Fire non-blocking request
        wp_remote_post($ajax_url, array(
            'timeout' => 0.01,  // Fire and forget
            'blocking' => false,
            'sslverify' => false,
            'body' => array(
                'action' => 'tugasin_fig_async',
                'post_id' => $post_id,
                'token' => $token,
            ),
        ));
    }

    /**
     * Handle async generation AJAX request
     * 
     * This runs in a separate PHP process after the main request completes.
     */
    public function handle_async_generation()
    {
        // Get and validate post ID
        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';

        if (!$post_id) {
            wp_die('Invalid post ID');
        }

        // Verify token using transient (more reliable than nonces for async)
        $stored_token = get_transient('tugasin_fig_token_' . $post_id);
        if (!$stored_token || $stored_token !== $token) {
            $this->clear_lock($post_id);
            wp_die('Security check failed');
        }

        // Clear the token (one-time use)
        delete_transient('tugasin_fig_token_' . $post_id);

        // Get post
        $post = get_post($post_id);
        if (!$post || $post->post_status !== 'publish') {
            $this->clear_lock($post_id);
            wp_die('Post not found or not published');
        }

        // Double-check it doesn't have featured image (race condition protection)
        if (has_post_thumbnail($post_id)) {
            $this->clear_lock($post_id);
            wp_die('Post already has featured image');
        }

        // Get settings and generate
        $settings = self::get_settings();

        $result = $this->generate_image($post_id, $post->post_title, $settings);

        // Clear the lock
        $this->clear_lock($post_id);

        if (!$result) {
            $this->log('Failed to generate featured image for post ID: ' . $post_id, 'error');
        }

        wp_die(); // Proper AJAX termination
    }

    /**
     * Clear the generation lock for a post
     *
     * @param int $post_id Post ID.
     */
    private function clear_lock($post_id)
    {
        delete_transient('tugasin_fig_lock_' . $post_id);
    }

    /**
     * Generate featured image for a post
     *
     * @param int    $post_id  Post ID.
     * @param string $title    Post title.
     * @param array  $settings Settings array.
     * @return bool Success or failure.
     */
    public function generate_image($post_id, $title, $settings)
    {
        // Check for GD library
        if (!function_exists('imagecreatetruecolor')) {
            $this->log('ERROR: GD library not available (imagecreatetruecolor function missing)', 'error');
            return false;
        }

        $this->log('GD library available. PHP version: ' . PHP_VERSION);

        // Check if API key is set
        if (empty($settings['pixabay_api_key'])) {
            $this->log('ERROR: Pixabay API key is not set', 'error');
            return false;
        }

        // Determine search text based on search_source setting
        $search_source = $settings['search_source'] ?? 'title';
        $search_text = $title;

        if ($search_source === 'category') {
            // Use post category name for search
            $category = $this->get_post_primary_category($post_id);
            if ($category) {
                $search_text = $category;
            }
        }

        // Translate search text if enabled (applies to both title and category)
        $text_for_search = $search_text;
        if (!empty($settings['enable_translation'])) {
            $translated = false;
            $provider = $settings['translation_provider'] ?? 'google';

            if ($provider === 'aws' && !empty($settings['aws_access_key']) && !empty($settings['aws_secret_key'])) {
                $translated = $this->translate_title_aws($search_text, $settings);
            } elseif ($provider === 'google' && !empty($settings['google_translate_key'])) {
                $translated = $this->translate_title($search_text, $settings['google_translate_key']);
            }

            if ($translated) {
                $text_for_search = $translated;
            }
        }

        // Extract keywords from search text (translated or original)
        $keywords = $this->extract_keywords($text_for_search);
        $search_query = !empty($keywords) ? $keywords : $settings['fallback_query'];

        // Fetch image from Pixabay
        $bg_path = $this->fetch_pixabay_image($search_query, $settings['pixabay_api_key']);

        // If Pixabay fails, skip generation (per user requirement)
        if (!$bg_path) {
            $this->log('ERROR: Failed to fetch image from Pixabay', 'error');
            return false;
        }

        $this->log('Background image downloaded: ' . $bg_path);

        // Create canvas
        $canvas = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        if (!$canvas) {
            $this->log('ERROR: Failed to create canvas', 'error');
            @unlink($bg_path);
            return false;
        }
        imagealphablending($canvas, true);

        // Load and resize background
        $bg = $this->load_image($bg_path);
        if (!$bg) {
            $this->log('ERROR: Failed to load background image', 'error');
            @unlink($bg_path);
            return false;
        }

        $this->log('Background loaded, applying transformations...');

        $this->fit_background($canvas, $bg);
        imagedestroy($bg);

        // Apply warm color filter
        $this->apply_warm_filter($canvas);

        // Apply fog gradient
        $gradient_rgb = $this->hex_to_rgb($settings['gradient_color']);
        $this->apply_fog_gradient($canvas, $gradient_rgb);

        // Add logo
        $logo_path = $this->get_logo_path($settings);
        if ($logo_path) {
            $this->log('Adding logo from: ' . $logo_path);
            $this->add_logo($canvas, $logo_path, $settings['logo_size']);
        } else {
            $this->log('No logo found, skipping logo');
        }

        // Draw title
        $text_rgb = $this->hex_to_rgb($settings['text_color']);
        $this->draw_title($canvas, $title, $text_rgb);

        $this->log('Saving image to media library...');

        // Save and attach
        $attachment_id = $this->save_and_attach($canvas, $post_id, $title);

        // Cleanup
        imagedestroy($canvas);
        @unlink($bg_path);

        if ($attachment_id) {
            // Set as featured image
            set_post_thumbnail($post_id, $attachment_id);
            $this->log('Featured image set successfully. Attachment ID: ' . $attachment_id);

            // Set alt text if enabled
            if ($settings['auto_alt_text']) {
                $this->set_alt_text($attachment_id, $title);
            }

            return true;
        }

        $this->log('ERROR: Failed to save and attach image', 'error');
        return false;
    }

    /**
     * Extract keywords from post title
     *
     * @param string $title Post title.
     * @return string Keywords for search.
     */
    private function extract_keywords($title)
    {
        // Decode HTML entities
        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');

        // Remove punctuation and special characters
        $title = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $title);

        // Convert to lowercase for stopword matching
        $title_lower = mb_strtolower($title, 'UTF-8');

        // Split into words
        $words = preg_split('/\s+/', $title_lower, -1, PREG_SPLIT_NO_EMPTY);

        // Remove stopwords
        $keywords = array();
        foreach ($words as $word) {
            if (mb_strlen($word, 'UTF-8') > 2 && !in_array($word, self::$stopwords, true)) {
                $keywords[] = $word;
            }
        }

        // Return first 5 keywords to avoid too long query
        $keywords = array_slice($keywords, 0, 5);

        return implode(' ', $keywords);
    }

    /**
     * Get the primary category name for a post
     *
     * @param int $post_id Post ID.
     * @return string|false Category name or false if not found.
     */
    private function get_post_primary_category($post_id)
    {
        // Try to get Yoast SEO primary category first
        $primary_term_id = get_post_meta($post_id, '_yoast_wpseo_primary_category', true);
        if ($primary_term_id) {
            $term = get_term($primary_term_id, 'category');
            if ($term && !is_wp_error($term)) {
                return $term->name;
            }
        }

        // Fall back to first category
        $categories = get_the_category($post_id);
        if (!empty($categories)) {
            // Skip "Uncategorized" if possible
            foreach ($categories as $category) {
                if (strtolower($category->name) !== 'uncategorized' && strtolower($category->slug) !== 'uncategorized') {
                    return $category->name;
                }
            }
            // If only Uncategorized exists, return it anyway
            return $categories[0]->name;
        }

        return false;
    }

    /**
     * Translate title from Indonesian to English using Google Cloud Translation API
     *
     * @param string $title   Title to translate.
     * @param string $api_key Google Cloud Translation API key.
     * @return string|false Translated text or false on failure.
     */
    private function translate_title($title, $api_key)
    {
        if (empty($api_key) || empty($title)) {
            return false;
        }

        $url = 'https://translation.googleapis.com/language/translate/v2?' . http_build_query(array(
            'key' => $api_key,
            'q' => $title,
            'source' => 'id',
            'target' => 'en',
            'format' => 'text',
        ));

        $response = wp_remote_post($url, array(
            'timeout' => 15,
            'headers' => array('Content-Type' => 'application/json'),
        ));

        if (is_wp_error($response)) {
            $this->log('Google Translate API error: ' . $response->get_error_message(), 'error');
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data['error'])) {
            $this->log('Google Translate API error: ' . $data['error']['message'], 'error');
            return false;
        }

        if (!empty($data['data']['translations'][0]['translatedText'])) {
            $translated = $data['data']['translations'][0]['translatedText'];
            return html_entity_decode($translated, ENT_QUOTES, 'UTF-8');
        }

        return false;
    }

    /**
     * Translate title from Indonesian to English using AWS Translate API
     *
     * @param string $title    Title to translate.
     * @param array  $settings Settings array with AWS credentials.
     * @return string|false Translated text or false on failure.
     */
    private function translate_title_aws($title, $settings)
    {
        $access_key = $settings['aws_access_key'] ?? '';
        $secret_key = $settings['aws_secret_key'] ?? '';
        $region = $settings['aws_region'] ?? 'us-east-1';

        if (empty($access_key) || empty($secret_key) || empty($title)) {
            return false;
        }

        $service = 'translate';
        $host = "translate.{$region}.amazonaws.com";
        $endpoint = "https://{$host}/";

        $payload = json_encode(array(
            'SourceLanguageCode' => 'id',
            'TargetLanguageCode' => 'en',
            'Text' => $title,
        ));

        // Create AWS Signature V4
        $amz_date = gmdate('Ymd\THis\Z');
        $date_stamp = gmdate('Ymd');

        // Create canonical request
        $method = 'POST';
        $canonical_uri = '/';
        $canonical_querystring = '';
        $content_type = 'application/x-amz-json-1.1';
        $amz_target = 'AWSShineFrontendService_20170701.TranslateText';

        $canonical_headers = "content-type:{$content_type}\n" .
            "host:{$host}\n" .
            "x-amz-date:{$amz_date}\n" .
            "x-amz-target:{$amz_target}\n";

        $signed_headers = 'content-type;host;x-amz-date;x-amz-target';
        $payload_hash = hash('sha256', $payload);

        $canonical_request = "{$method}\n{$canonical_uri}\n{$canonical_querystring}\n{$canonical_headers}\n{$signed_headers}\n{$payload_hash}";

        // Create string to sign
        $algorithm = 'AWS4-HMAC-SHA256';
        $credential_scope = "{$date_stamp}/{$region}/{$service}/aws4_request";
        $string_to_sign = "{$algorithm}\n{$amz_date}\n{$credential_scope}\n" . hash('sha256', $canonical_request);

        // Create signing key
        $k_date = hash_hmac('sha256', $date_stamp, 'AWS4' . $secret_key, true);
        $k_region = hash_hmac('sha256', $region, $k_date, true);
        $k_service = hash_hmac('sha256', $service, $k_region, true);
        $k_signing = hash_hmac('sha256', 'aws4_request', $k_service, true);

        // Create signature
        $signature = hash_hmac('sha256', $string_to_sign, $k_signing);

        // Create authorization header
        $authorization_header = "{$algorithm} Credential={$access_key}/{$credential_scope}, SignedHeaders={$signed_headers}, Signature={$signature}";

        $response = wp_remote_post($endpoint, array(
            'timeout' => 15,
            'headers' => array(
                'Content-Type' => $content_type,
                'X-Amz-Date' => $amz_date,
                'X-Amz-Target' => $amz_target,
                'Authorization' => $authorization_header,
            ),
            'body' => $payload,
        ));

        if (is_wp_error($response)) {
            $this->log('AWS Translate API error: ' . $response->get_error_message(), 'error');
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data['message'])) {
            $this->log('AWS Translate API error: ' . $data['message'], 'error');
            return false;
        }

        if (!empty($data['TranslatedText'])) {
            return html_entity_decode($data['TranslatedText'], ENT_QUOTES, 'UTF-8');
        }

        return false;
    }

    /**
     * Fetch image from Pixabay API with deduplication
     *
     * @param string $query   Search query.
     * @param string $api_key Pixabay API key.
     * @return string|false Path to downloaded image or false.
     */
    private function fetch_pixabay_image($query, $api_key)
    {
        if (empty($api_key)) {
            return false;
        }

        $url = 'https://pixabay.com/api/?' . http_build_query(array(
            'key' => $api_key,
            'q' => $query,
            'image_type' => 'photo',
            'orientation' => 'horizontal',
            'safesearch' => 'true',
            'per_page' => 30,
        ));

        $response = wp_remote_get($url, array('timeout' => 30));

        if (is_wp_error($response)) {
            $this->log('Pixabay API error: ' . $response->get_error_message(), 'error');
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (empty($data['hits'])) {
            $this->log('Pixabay returned no results for query: "' . $query . '"');
            return false;
        }

        $this->log('Pixabay returned ' . count($data['hits']) . ' images');

        // Get used image IDs
        $used_ids = $this->get_used_image_ids();

        // Filter out already used images
        $available = array();
        foreach ($data['hits'] as $hit) {
            if (!in_array($hit['id'], $used_ids, true)) {
                $available[] = $hit;
            }
        }

        // If all images are used, reset and use all
        if (empty($available)) {
            $this->reset_used_image_ids();
            $available = $data['hits'];
        }

        // Pick random image
        $image = $available[array_rand($available)];

        // Mark as used
        $this->mark_image_used($image['id']);

        // Download the image (use largeImageURL for quality)
        $image_url = !empty($image['largeImageURL']) ? $image['largeImageURL'] : $image['webformatURL'];

        $image_response = wp_remote_get($image_url, array('timeout' => 30));

        if (is_wp_error($image_response)) {
            return false;
        }

        $image_data = wp_remote_retrieve_body($image_response);

        // Save to temp file
        $upload_dir = wp_upload_dir();
        $temp_path = $upload_dir['basedir'] . '/tugasin-fig-temp-' . uniqid() . '.jpg';

        if (file_put_contents($temp_path, $image_data) === false) {
            return false;
        }

        return $temp_path;
    }

    /**
     * Get list of already used Pixabay image IDs
     *
     * @return array Array of image IDs.
     */
    private function get_used_image_ids()
    {
        $ids = get_option('tugasin_fig_used_images', array());
        return is_array($ids) ? $ids : array();
    }

    /**
     * Mark a Pixabay image ID as used
     *
     * @param int $image_id Pixabay image ID.
     */
    private function mark_image_used($image_id)
    {
        $ids = $this->get_used_image_ids();
        $ids[] = (int) $image_id;

        // Keep only last 500 IDs to prevent bloat
        if (count($ids) > 500) {
            $ids = array_slice($ids, -500);
        }

        update_option('tugasin_fig_used_images', $ids, false);
    }

    /**
     * Reset used image IDs (when all are exhausted)
     */
    private function reset_used_image_ids()
    {
        update_option('tugasin_fig_used_images', array(), false);
    }

    /**
     * Load image from file path
     *
     * @param string $path Image path.
     * @return resource|false GD image resource or false.
     */
    private function load_image($path)
    {
        $info = @getimagesize($path);
        if (!$info) {
            return false;
        }

        switch ($info['mime']) {
            case 'image/jpeg':
                return @imagecreatefromjpeg($path);
            case 'image/png':
                return @imagecreatefrompng($path);
            case 'image/webp':
                return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false;
            default:
                return false;
        }
    }

    /**
     * Fit background image to canvas (cover mode)
     *
     * @param resource $canvas Canvas to draw on.
     * @param resource $bg     Background image.
     */
    private function fit_background(&$canvas, $bg)
    {
        $bw = imagesx($bg);
        $bh = imagesy($bg);

        $scale = max(self::WIDTH / $bw, self::HEIGHT / $bh);
        $nw = (int) ($bw * $scale);
        $nh = (int) ($bh * $scale);
        $ox = (int) (($nw - self::WIDTH) / 2);
        $oy = (int) (($nh - self::HEIGHT) / 2);

        $resized = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($resized, $bg, 0, 0, 0, 0, $nw, $nh, $bw, $bh);
        imagecopy($canvas, $resized, 0, 0, $ox, $oy, self::WIDTH, self::HEIGHT);
        imagedestroy($resized);
    }

    /**
     * Apply warm color filter to avoid blue-ish photos
     *
     * @param resource $canvas Canvas to modify.
     */
    private function apply_warm_filter(&$canvas)
    {
        for ($y = 0; $y < self::HEIGHT; $y++) {
            for ($x = 0; $x < self::WIDTH; $x++) {
                $rgb = imagecolorsforindex($canvas, imagecolorat($canvas, $x, $y));

                // Increase warmth
                $new_r = min(255, (int) ($rgb['red'] * 1.05));
                $new_g = min(255, (int) ($rgb['green'] * 1.02));
                $new_b = max(0, (int) ($rgb['blue'] * 0.92));

                // Add brightness
                $brightness = 10;
                $new_r = min(255, $new_r + $brightness);
                $new_g = min(255, $new_g + $brightness);
                $new_b = min(255, $new_b + $brightness);

                $new_color = imagecolorallocate($canvas, $new_r, $new_g, $new_b);
                imagesetpixel($canvas, $x, $y, $new_color);
            }
        }
    }

    /**
     * Apply white fog gradient overlay
     *
     * @param resource $canvas Canvas to modify.
     * @param array    $rgb    RGB color array.
     */
    private function apply_fog_gradient(&$canvas, $rgb)
    {
        $start_y = (int) (self::HEIGHT * 0.20);

        for ($y = $start_y; $y < self::HEIGHT; $y++) {
            $progress = ($y - $start_y) / (self::HEIGHT - $start_y);
            $blend = $progress * $progress;
            $blend = min($blend, 0.80);

            for ($x = 0; $x < self::WIDTH; $x++) {
                $pixel_rgb = imagecolorsforindex($canvas, imagecolorat($canvas, $x, $y));

                $new_r = (int) ($pixel_rgb['red'] * (1 - $blend) + $rgb[0] * $blend);
                $new_g = (int) ($pixel_rgb['green'] * (1 - $blend) + $rgb[1] * $blend);
                $new_b = (int) ($pixel_rgb['blue'] * (1 - $blend) + $rgb[2] * $blend);

                $new_color = imagecolorallocate($canvas, $new_r, $new_g, $new_b);
                imagesetpixel($canvas, $x, $y, $new_color);
            }
        }
    }

    /**
     * Add logo with rounded white card background
     *
     * @param resource $canvas    Canvas to draw on.
     * @param string   $logo_path Path to logo image.
     * @param int      $size      Target logo height.
     */
    private function add_logo(&$canvas, $logo_path, $size)
    {
        $logo = $this->load_image($logo_path);
        if (!$logo) {
            return;
        }

        $lw = imagesx($logo);
        $lh = imagesy($logo);

        $scale = $size / $lh;
        $new_width = (int) ($lw * $scale);
        $new_height = (int) ($lh * $scale);

        // Card with padding
        $padding = 10;
        $margin = 65;  // Logo image will be at 65 + 10 = 75, aligned with title
        $card_width = $new_width + ($padding * 2);
        $card_height = $new_height + ($padding * 2);
        $radius = 10;

        $x1 = $margin;
        $y1 = $margin;
        $x2 = $margin + $card_width;
        $y2 = $margin + $card_height;

        // Draw shadow
        for ($i = 4; $i >= 1; $i--) {
            $alpha = 115 - ($i * 5);
            $shadow = imagecolorallocatealpha($canvas, 20, 20, 40, $alpha);
            imagefilledrectangle($canvas, $x1 + $radius + $i, $y1 + $i, $x2 - $radius + $i, $y2 + $i, $shadow);
            imagefilledrectangle($canvas, $x1 + $i, $y1 + $radius + $i, $x2 + $i, $y2 - $radius + $i, $shadow);
        }

        // Draw rounded white card
        $white = imagecolorallocate($canvas, 255, 255, 255);
        imagefilledrectangle($canvas, $x1 + $radius, $y1, $x2 - $radius, $y2, $white);
        imagefilledrectangle($canvas, $x1, $y1 + $radius, $x2, $y2 - $radius, $white);
        imagefilledellipse($canvas, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $white);
        imagefilledellipse($canvas, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $white);
        imagefilledellipse($canvas, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $white);
        imagefilledellipse($canvas, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $white);

        // Resize and place logo
        $resized_logo = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($resized_logo, false);
        imagesavealpha($resized_logo, true);
        $transparent = imagecolorallocatealpha($resized_logo, 0, 0, 0, 127);
        imagefill($resized_logo, 0, 0, $transparent);
        imagecopyresampled($resized_logo, $logo, 0, 0, 0, 0, $new_width, $new_height, $lw, $lh);

        imagealphablending($canvas, true);
        imagecopy($canvas, $resized_logo, $margin + $padding, $margin + $padding, 0, 0, $new_width, $new_height);

        imagedestroy($logo);
        imagedestroy($resized_logo);
    }

    /**
     * Draw title text with shadow
     *
     * @param resource $canvas Canvas to draw on.
     * @param string   $title  Post title.
     * @param array    $rgb    Text RGB color.
     */
    private function draw_title(&$canvas, $title, $rgb)
    {
        $font = get_template_directory() . '/assets/fonts/plus-jakarta-sans/PlusJakartaSans-Bold.ttf';

        if (!file_exists($font)) {
            return;
        }

        $title = html_entity_decode($title, ENT_QUOTES, 'UTF-8');
        $lines = $this->wrap_text($title);

        $count = count($lines);
        $size = $count <= 2 ? 50 : ($count <= 3 ? 42 : 36);
        $lh = $size * 1.35;
        $total_h = $count * $lh;
        $start_y = self::HEIGHT - $total_h - 50;
        $margin = 75;

        $text_color = imagecolorallocate($canvas, $rgb[0], $rgb[1], $rgb[2]);
        $shadow = imagecolorallocatealpha($canvas, 100, 100, 100, 100);

        foreach ($lines as $i => $line) {
            $y = $start_y + ($i * $lh);
            imagettftext($canvas, $size, 0, $margin + 1, (int) $y + 1, $shadow, $font, $line);
            imagettftext($canvas, $size, 0, $margin, (int) $y, $text_color, $font, $line);
        }
    }

    /**
     * Wrap text into lines
     *
     * @param string $text Text to wrap.
     * @param int    $max  Max characters per line.
     * @return array Lines.
     */
    private function wrap_text($text, $max = 28)
    {
        $words = explode(' ', $text);
        $lines = array();
        $cur = '';

        foreach ($words as $w) {
            if (mb_strlen($cur . ' ' . $w, 'UTF-8') <= $max) {
                $cur .= ($cur ? ' ' : '') . $w;
            } else {
                if ($cur) {
                    $lines[] = $cur;
                }
                $cur = $w;
            }
        }
        if ($cur) {
            $lines[] = $cur;
        }

        return $lines;
    }

    /**
     * Save canvas as WebP and attach to WordPress media library
     *
     * @param resource $canvas  GD image resource.
     * @param int      $post_id Post ID.
     * @param string   $title   Post title for filename.
     * @return int|false Attachment ID or false.
     */
    private function save_and_attach($canvas, $post_id, $title)
    {
        $upload_dir = wp_upload_dir();

        // Sanitize title for filename
        $filename = sanitize_title($title);
        $filename = substr($filename, 0, 50);

        // Determine format (WebP preferred)
        $use_webp = function_exists('imagewebp');
        $extension = $use_webp ? 'webp' : 'jpg';
        $mime_type = $use_webp ? 'image/webp' : 'image/jpeg';

        $filepath = $upload_dir['path'] . '/' . $filename . '-featured.' . $extension;

        // Ensure unique filename
        $counter = 1;
        while (file_exists($filepath)) {
            $filepath = $upload_dir['path'] . '/' . $filename . '-featured-' . $counter . '.' . $extension;
            $counter++;
        }

        // Save image
        if ($use_webp) {
            imagewebp($canvas, $filepath, 85);
        } else {
            imagejpeg($canvas, $filepath, 85);
        }

        if (!file_exists($filepath)) {
            return false;
        }

        // Create attachment
        $attachment = array(
            'post_mime_type' => $mime_type,
            'post_title' => $title . ' - Featured Image',
            'post_content' => '',
            'post_status' => 'inherit',
        );

        $attachment_id = wp_insert_attachment($attachment, $filepath, $post_id);

        if (is_wp_error($attachment_id)) {
            @unlink($filepath);
            return false;
        }

        // Generate metadata
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $metadata = wp_generate_attachment_metadata($attachment_id, $filepath);
        wp_update_attachment_metadata($attachment_id, $metadata);

        return $attachment_id;
    }

    /**
     * Set alt text for attachment
     *
     * @param int    $attachment_id Attachment ID.
     * @param string $title         Alt text (post title).
     */
    private function set_alt_text($attachment_id, $title)
    {
        update_post_meta($attachment_id, '_wp_attachment_image_alt', sanitize_text_field($title));
    }

    /**
     * Get logo path from settings or default
     *
     * @param array $settings Settings array.
     * @return string|false Logo path or false.
     */
    private function get_logo_path($settings)
    {
        // Check if custom logo is set
        if (!empty($settings['logo_image'])) {
            $logo_path = get_attached_file($settings['logo_image']);
            if ($logo_path && file_exists($logo_path)) {
                return $logo_path;
            }
        }

        // Fallback to default logo
        $default_logo = get_template_directory() . '/assets/images/tugasin-logo.png';
        if (file_exists($default_logo)) {
            return $default_logo;
        }

        return false;
    }

    /**
     * Convert hex color to RGB array
     *
     * @param string $hex Hex color.
     * @return array RGB array.
     */
    private function hex_to_rgb($hex)
    {
        $hex = ltrim($hex, '#');
        return array(
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        );
    }

    /**
     * Get settings with defaults
     *
     * @return array Settings array.
     */
    public static function get_settings()
    {
        return array(
            'enabled' => (bool) get_option('tugasin_fig_enabled', true),
            'pixabay_api_key' => get_option('tugasin_fig_pixabay_key', ''),
            'search_source' => get_option('tugasin_fig_search_source', 'title'),
            'enable_translation' => (bool) get_option('tugasin_fig_enable_translation', true),
            'translation_provider' => get_option('tugasin_fig_translation_provider', 'google'),
            'google_translate_key' => get_option('tugasin_fig_google_translate_key', ''),
            'aws_access_key' => get_option('tugasin_fig_aws_access_key', ''),
            'aws_secret_key' => get_option('tugasin_fig_aws_secret_key', ''),
            'aws_region' => get_option('tugasin_fig_aws_region', 'us-east-1'),
            'logo_image' => (int) get_option('tugasin_fig_logo_image', 0),
            'logo_size' => (int) get_option('tugasin_fig_logo_size', 56),
            'gradient_color' => get_option('tugasin_fig_gradient_color', '#ffffff'),
            'text_color' => get_option('tugasin_fig_text_color', '#1e3a5f'),
            'fallback_query' => get_option('tugasin_fig_fallback_query', 'students education university'),
            'auto_alt_text' => (bool) get_option('tugasin_fig_auto_alt', true),
        );
    }

    /**
     * Add meta box for manual regeneration
     */
    public function add_regenerate_meta_box()
    {
        add_meta_box(
            'tugasin_fig_regenerate',
            __('Featured Image Generator', 'tugasin'),
            array($this, 'render_regenerate_meta_box'),
            'post',
            'side',
            'low'
        );
    }

    /**
     * Render the regenerate meta box
     *
     * @param WP_Post $post Post object.
     */
    public function render_regenerate_meta_box($post)
    {
        $settings = self::get_settings();

        if (!$settings['enabled']) {
            echo '<p style="color: #666;">' . esc_html__('Featured Image Generator is disabled.', 'tugasin') . '</p>';
            return;
        }

        if (empty($settings['pixabay_api_key'])) {
            echo '<p style="color: #c00;">' . esc_html__('Pixabay API key not configured.', 'tugasin') . '</p>';
            return;
        }

        wp_nonce_field('tugasin_fig_regenerate', 'tugasin_fig_nonce');
        ?>
        <p>
            <button type="button" id="tugasin-fig-regenerate" class="button button-secondary" style="width: 100%;">
                <span class="dashicons dashicons-update" style="margin-top: 4px;"></span>
                <?php esc_html_e('Generate Featured Image', 'tugasin'); ?>
            </button>
        </p>
        <p class="description" style="font-size: 11px; color: #666;">
            <?php esc_html_e('Generates a new featured image using Pixabay. This will replace the current featured image.', 'tugasin'); ?>
        </p>
        <div id="tugasin-fig-message" style="display: none; margin-top: 10px; padding: 8px; border-radius: 4px;"></div>

        <script>
            jQuery(function ($) {
                $('#tugasin-fig-regenerate').on('click', function () {
                    var $btn = $(this);
                    var $msg = $('#tugasin-fig-message');

                    $btn.prop('disabled', true).find('.dashicons').addClass('spin');

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'tugasin_regenerate_featured_image',
                            post_id: <?php echo (int) $post->ID; ?>,
                            nonce: $('#tugasin_fig_nonce').val()
                        },
                        success: function (response) {
                            if (response.success) {
                                $msg.css({ background: '#d4edda', color: '#155724', border: '1px solid #c3e6cb' })
                                    .text(response.data.message).show();
                                setTimeout(function () { location.reload(); }, 1500);
                            } else {
                                $msg.css({ background: '#f8d7da', color: '#721c24', border: '1px solid #f5c6cb' })
                                    .text(response.data.message).show();
                            }
                        },
                        error: function () {
                            $msg.css({ background: '#f8d7da', color: '#721c24', border: '1px solid #f5c6cb' })
                                .text('<?php echo esc_js(__('An error occurred.', 'tugasin')); ?>').show();
                        },
                        complete: function () {
                            $btn.prop('disabled', false).find('.dashicons').removeClass('spin');
                        }
                    });
                });
            });
        </script>
        <style>
            .dashicons.spin {
                animation: tugasin-spin 1s linear infinite;
            }

            @keyframes tugasin-spin {
                100% {
                    transform: rotate(360deg);
                }
            }
        </style>
        <?php
    }

    /**
     * Handle AJAX regeneration request
     */
    public function ajax_regenerate()
    {
        check_ajax_referer('tugasin_fig_regenerate', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => __('Permission denied.', 'tugasin')));
        }

        $post_id = isset($_POST['post_id']) ? (int) $_POST['post_id'] : 0;

        if (!$post_id) {
            wp_send_json_error(array('message' => __('Invalid post ID.', 'tugasin')));
        }

        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'post') {
            wp_send_json_error(array('message' => __('Invalid post.', 'tugasin')));
        }

        $settings = self::get_settings();

        // Remove existing featured image reference (but keep the attachment)
        delete_post_thumbnail($post_id);

        // Generate new image
        $result = $this->generate_image($post_id, $post->post_title, $settings);

        if ($result) {
            wp_send_json_success(array('message' => __('Featured image generated successfully!', 'tugasin')));
        } else {
            wp_send_json_error(array('message' => __('Failed to generate image. Check Pixabay API key.', 'tugasin')));
        }
    }
}

// Initialize the Featured Image Generator
new Tugasin_Featured_Image();
