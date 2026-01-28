<?php
/**
 * Theme Settings Class
 *
 * Creates an admin settings page with sidebar navigation.
 *
 * @package TugasinWP
 * @since 2.8.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Settings
{

    /**
     * Option group name
     */
    private $option_group = 'tugasin_settings';

    /**
     * Service slugs for schema settings
     */
    private $service_slugs = array(
        'joki-skripsi' => 'Joki Skripsi',
        'joki-makalah' => 'Joki Makalah',
        'joki-tugas' => 'Joki Tugas',
        'cek-plagiarism' => 'Cek Plagiarism',
    );

    /**
     * Testimonial pages for Data settings
     */
    private $testimonial_pages = array(
        'joki_skripsi' => 'Joki Skripsi',
        'joki_makalah' => 'Joki Makalah',
        'joki_tugas' => 'Joki Tugas',
        'cek_plagiarism' => 'Cek Plagiarism',
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_tugasin_one_click_setup', array($this, 'handle_one_click_setup'));

        // Import/Export AJAX handlers
        add_action('wp_ajax_tugasin_export_settings', array($this, 'handle_export_settings'));
        add_action('wp_ajax_tugasin_import_settings', array($this, 'handle_import_settings'));
    }

    /**
     * Handle one-click setup form submission via admin-post.php
     */
    public function handle_one_click_setup()
    {
        // Verify nonce
        if (!isset($_POST['tugasin_demo_nonce']) || !wp_verify_nonce($_POST['tugasin_demo_nonce'], 'tugasin_demo_action')) {
            wp_die(__('Security check failed.', 'tugasin'));
        }

        // Check user capability
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'tugasin'));
        }

        // Run the one-click setup
        $result = $this->run_one_click_setup();

        // Store the result in a transient for display
        set_transient('tugasin_demo_result', $result, 60);

        // Redirect back to the settings page
        wp_redirect(admin_url('admin.php?page=tugasin-settings&tab=import-demo&demo_complete=1'));
        exit;
    }

    /**
     * Run the complete one-click setup
     * Creates pages, sets reading settings, and updates page mappings
     * 
     * @return array Result with success status and message
     */
    private function run_one_click_setup()
    {
        $messages = array();
        $success = true;

        // Step 1: Create demo pages
        $pages_result = $this->create_demo_pages();
        $messages[] = $pages_result['message'];

        // Step 2: Configure reading settings
        $reading_result = $this->configure_reading_settings();
        $messages[] = $reading_result['message'];
        if (!$reading_result['success']) {
            $success = false;
        }

        // Step 3: Update page mappings
        $mapping_result = $this->update_page_mappings();
        $messages[] = $mapping_result['message'];

        return array(
            'success' => $success,
            'message' => implode(' ', $messages),
        );
    }

    /**
     * Update page mapping settings with the created pages
     * 
     * @return array Result with success status and message
     */
    private function update_page_mappings()
    {
        $updated = 0;

        // Map Layanan page
        $layanan = get_page_by_path('layanan');
        if ($layanan) {
            update_option('tugasin_page_layanan', $layanan->ID);
            $updated++;
        }

        // Map sub-service pages
        $mappings = array(
            'layanan/joki-skripsi' => 'tugasin_page_joki_skripsi',
            'layanan/joki-makalah' => 'tugasin_page_joki_makalah',
            'layanan/joki-tugas' => 'tugasin_page_joki_tugas',
            'layanan/cek-plagiarisme' => 'tugasin_page_cek_plagiarism',
        );

        foreach ($mappings as $path => $option_key) {
            $page = get_page_by_path($path);
            if ($page) {
                update_option($option_key, $page->ID);
                $updated++;
            }
        }

        if ($updated > 0) {
            return array(
                'success' => true,
                'message' => sprintf(__('Page mappings updated (%d pages mapped).', 'tugasin'), $updated),
            );
        }

        return array(
            'success' => true,
            'message' => __('No page mappings to update.', 'tugasin'),
        );
    }

    /**
     * Add settings page as standalone top-level menu
     */
    public function add_settings_page()
    {
        add_menu_page(
            __('TugasinWP Settings', 'tugasin'),  // Page title
            __('TugasinWP', 'tugasin'),           // Menu title
            'manage_options',                        // Capability
            'tugasin-settings',                      // Menu slug
            array($this, 'render_settings_page'), // Callback
            'dashicons-bolt',                        // Icon (matches theme logo)
            3                                        // Position (after Dashboard)
        );
    }

    /**
     * Register all settings
     */
    public function register_settings()
    {
        // =============================================
        // GENERAL SETTINGS
        // =============================================

        // WhatsApp Number
        register_setting($this->option_group, 'tugasin_wa_number', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '6281234567890',
        ));

        // WhatsApp Message Template
        register_setting($this->option_group, 'tugasin_wa_template', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => 'Halo, saya ingin konsultasi tentang bantuan tugas.',
        ));

        // CTA Text
        register_setting($this->option_group, 'tugasin_cta_text', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Konsultasi Sekarang!',
        ));

        // =============================================
        // WHATSAPP WIDGET SETTINGS
        // =============================================

        register_setting($this->option_group, 'tugasin_wa_widget_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        register_setting($this->option_group, 'tugasin_wa_widget_cta', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => 'Halo! Kamu butuh bantuan? Tim Tugasin siap bantu kamu. Yuk konsultasi sekarang, GRATIS!',
        ));

        register_setting($this->option_group, 'tugasin_wa_widget_delay', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 3,
        ));

        // =============================================
        // BRANDING SETTINGS
        // =============================================

        register_setting($this->option_group, 'tugasin_logo', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 0,
        ));

        register_setting($this->option_group, 'tugasin_site_icon', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 0,
        ));

        register_setting($this->option_group, 'tugasin_color_primary', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#064e3b',
        ));

        // =============================================
        // PAGE MAPPING SETTINGS
        // =============================================

        $page_mappings = array('page_layanan', 'page_joki_skripsi', 'page_joki_makalah', 'page_joki_tugas', 'page_cek_plagiarism');
        foreach ($page_mappings as $key) {
            register_setting($this->option_group, 'tugasin_' . $key, array(
                'type' => 'integer',
                'sanitize_callback' => 'absint',
                'default' => 0,
            ));
        }

        // =============================================
        // SCHEMA SETTINGS
        // =============================================

        register_setting($this->option_group, 'tugasin_schema_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        register_setting($this->option_group, 'tugasin_schema_org_name', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => get_bloginfo('name'),
        ));

        register_setting($this->option_group, 'tugasin_schema_org_logo', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 0,
        ));

        register_setting($this->option_group, 'tugasin_schema_org_phone', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ));

        // Layanan Archive Schema Settings
        register_setting($this->option_group, 'tugasin_schema_layanan_name', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => __('Layanan Tugasin', 'tugasin'),
        ));

        register_setting($this->option_group, 'tugasin_schema_layanan_desc', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => __('Daftar layanan jasa akademik dari Tugasin', 'tugasin'),
        ));

        register_setting($this->option_group, 'tugasin_schema_layanan_rating_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => false,
        ));

        register_setting($this->option_group, 'tugasin_schema_layanan_rating_value', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '4.9',
        ));

        register_setting($this->option_group, 'tugasin_schema_layanan_rating_count', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 500,
        ));

        // Per-service schema settings
        foreach ($this->service_slugs as $slug => $label) {
            $prefix = 'tugasin_schema_service_' . str_replace('-', '_', $slug);

            register_setting($this->option_group, $prefix . '_name', array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => $label,
            ));

            register_setting($this->option_group, $prefix . '_desc', array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => '',
            ));

            register_setting($this->option_group, $prefix . '_rating_enabled', array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false,
            ));

            register_setting($this->option_group, $prefix . '_rating_value', array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => '4.9',
            ));

            register_setting($this->option_group, $prefix . '_rating_count', array(
                'type' => 'integer',
                'sanitize_callback' => 'absint',
                'default' => 100,
            ));

            // Price settings
            register_setting($this->option_group, $prefix . '_price_from', array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => '',
            ));

            register_setting($this->option_group, $prefix . '_price_currency', array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'IDR',
            ));
        }

        // =============================================
        // DATA/TESTIMONIALS SETTINGS
        // =============================================

        // Hero Tutor Profiles (array of 3)
        register_setting($this->option_group, 'tugasin_hero_tutors', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_tutors_array'),
            'default' => array(),
        ));

        // Default testimonials (array of 3)
        register_setting($this->option_group, 'tugasin_testimonials_default', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_testimonials_array'),
            'default' => array(),
        ));

        // Per-page testimonial settings
        foreach ($this->testimonial_pages as $key => $label) {
            register_setting($this->option_group, 'tugasin_testimonials_' . $key . '_use_default', array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => true,
            ));

            register_setting($this->option_group, 'tugasin_testimonials_' . $key, array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize_testimonials_array'),
                'default' => array(),
            ));
        }

        // =============================================
        // OPTIMIZATION SETTINGS (Phase 28)
        // =============================================

        // Defer JS
        register_setting($this->option_group, 'tugasin_opt_defer_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        register_setting($this->option_group, 'tugasin_opt_defer_scripts', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => "tugasin-main\ntugasin-archive-filter",
        ));

        register_setting($this->option_group, 'tugasin_opt_defer_exclude', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => '',
        ));

        // Preconnect
        register_setting($this->option_group, 'tugasin_opt_preconnect_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        register_setting($this->option_group, 'tugasin_opt_preconnect_urls', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => 'https://cdnjs.cloudflare.com',
        ));

        // WebP
        register_setting($this->option_group, 'tugasin_opt_webp_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        // Lazy Load
        register_setting($this->option_group, 'tugasin_opt_lazyload_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        register_setting($this->option_group, 'tugasin_opt_lazyload_exclude', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => '',
        ));

        // Cache Lifetime (.htaccess)
        register_setting($this->option_group, 'tugasin_opt_cache_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => false,
        ));

        // Async CSS Load (reduce render-blocking)
        register_setting($this->option_group, 'tugasin_opt_async_css_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => false,
        ));

        // Disable jQuery Migrate
        register_setting($this->option_group, 'tugasin_opt_disable_jquery_migrate', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => false,
        ));

        // =============================================
        // RELATED POSTS SETTINGS (Phase 29)
        // =============================================

        // Enable inline related posts (within content)
        register_setting($this->option_group, 'tugasin_related_inline_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        // Inline related posts position (after nth paragraph)
        register_setting($this->option_group, 'tugasin_related_inline_position', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 3,
        ));

        // Inline related posts count
        register_setting($this->option_group, 'tugasin_related_inline_count', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 2,
        ));

        // Enable bottom related posts (after content)
        register_setting($this->option_group, 'tugasin_related_bottom_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        // =============================================
        // FEATURED IMAGE GENERATOR SETTINGS (Phase 30)
        // =============================================

        // Enable auto featured image generation
        register_setting($this->option_group, 'tugasin_fig_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => false,
        ));

        // Pixabay API Key
        register_setting($this->option_group, 'tugasin_fig_pixabay_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ));

        // Enable Title Translation (Phase 31)
        register_setting($this->option_group, 'tugasin_fig_enable_translation', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        // Google Translate API Key (Phase 31)
        register_setting($this->option_group, 'tugasin_fig_google_translate_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ));

        // Search Source (Phase 33) - title or category
        register_setting($this->option_group, 'tugasin_fig_search_source', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'title',
        ));

        // Translation Provider (Phase 32)
        register_setting($this->option_group, 'tugasin_fig_translation_provider', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'google',
        ));

        // AWS Access Key ID (Phase 32)
        register_setting($this->option_group, 'tugasin_fig_aws_access_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ));

        // AWS Secret Access Key (Phase 32)
        register_setting($this->option_group, 'tugasin_fig_aws_secret_key', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ));

        // AWS Region (Phase 32)
        register_setting($this->option_group, 'tugasin_fig_aws_region', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'us-east-1',
        ));

        // Logo Image (attachment ID)
        register_setting($this->option_group, 'tugasin_fig_logo_image', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 0,
        ));

        // Logo Size
        register_setting($this->option_group, 'tugasin_fig_logo_size', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 56,
        ));

        // Gradient Color (fog overlay)
        register_setting($this->option_group, 'tugasin_fig_gradient_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#ffffff',
        ));

        // Text Color
        register_setting($this->option_group, 'tugasin_fig_text_color', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#1e3a5f',
        ));

        // Fallback Search Query
        register_setting($this->option_group, 'tugasin_fig_fallback_query', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'students education university',
        ));

        // Auto Alt Text
        register_setting($this->option_group, 'tugasin_fig_auto_alt', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        // Enable Backfill (Phase 34)
        register_setting($this->option_group, 'tugasin_fig_enable_backfill', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => false,
        ));
    }

    /**
     * Sanitize testimonials array
     */
    public function sanitize_testimonials_array($input)
    {
        if (!is_array($input)) {
            return array();
        }

        $sanitized = array();
        foreach ($input as $i => $item) {
            if (!is_array($item)) {
                continue;
            }
            $sanitized[$i] = array(
                'name' => isset($item['name']) ? sanitize_text_field($item['name']) : '',
                'role' => isset($item['role']) ? sanitize_text_field($item['role']) : '',
                'image' => isset($item['image']) ? esc_url_raw($item['image']) : '',
                'text' => isset($item['text']) ? sanitize_textarea_field($item['text']) : '',
                'alt' => isset($item['alt']) ? sanitize_text_field($item['alt']) : '',
            );
        }
        return $sanitized;
    }

    /**
     * Sanitize tutors array (hero section)
     */
    public function sanitize_tutors_array($input)
    {
        if (!is_array($input)) {
            return array();
        }

        $sanitized = array();
        foreach ($input as $i => $item) {
            if (!is_array($item)) {
                continue;
            }
            $sanitized[$i] = array(
                'name' => isset($item['name']) ? sanitize_text_field($item['name']) : '',
                'role' => isset($item['role']) ? sanitize_text_field($item['role']) : '',
                'image' => isset($item['image']) ? esc_url_raw($item['image']) : '',
                'rating' => isset($item['rating']) ? sanitize_text_field($item['rating']) : '',
                'count' => isset($item['count']) ? absint($item['count']) : 0,
            );
        }
        return $sanitized;
    }

    /**
     * Render settings page with sidebar navigation
     */
    public function render_settings_page()
    {
        // Enqueue color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        // Enqueue media uploader
        wp_enqueue_media();
        // Enqueue admin settings CSS
        wp_enqueue_style(
            'tugasin-admin-settings',
            TUGASIN_URI . '/assets/css/admin-settings.css',
            array(),
            TUGASIN_VERSION
        );
        ?>
        <div class="tugasin-settings-wrap">
            <!-- Header Banner -->
            <div class="tugasin-settings-header">
                <div class="header-icon">
                    <span class="dashicons dashicons-bolt"></span>
                </div>
                <div class="header-content">
                    <h1><?php esc_html_e('TugasinWP Settings', 'tugasin'); ?></h1>
                    <p class="header-subtitle">
                        <?php esc_html_e('Configure your theme settings and customize your site.', 'tugasin'); ?>
                    </p>
                </div>
                <span class="version-badge">v<?php echo esc_html(TUGASIN_VERSION); ?></span>
            </div>

            <form action="options.php" method="post" id="tugasin-settings-form" style="display: contents;">
                <?php settings_fields($this->option_group); ?>
                <input type="hidden" name="tugasin_active_tab" id="tugasin_active_tab"
                    value="<?php echo esc_attr(isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general'); ?>">

                <!-- Sidebar Navigation -->
                <aside class="tugasin-settings-sidebar">
                    <nav class="tugasin-settings-nav">
                        <span class="tugasin-nav-label"><?php esc_html_e('Settings', 'tugasin'); ?></span>

                        <button type="button" class="tugasin-nav-item active" data-section="general">
                            <span class="dashicons dashicons-admin-generic"></span>
                            <?php esc_html_e('General', 'tugasin'); ?>
                        </button>

                        <button type="button" class="tugasin-nav-item" data-section="whatsapp">
                            <span class="dashicons dashicons-format-chat"></span>
                            <?php esc_html_e('WhatsApp Widget', 'tugasin'); ?>
                        </button>

                        <button type="button" class="tugasin-nav-item" data-section="branding">
                            <span class="dashicons dashicons-admin-appearance"></span>
                            <?php esc_html_e('Branding', 'tugasin'); ?>
                        </button>

                        <button type="button" class="tugasin-nav-item" data-section="pages">
                            <span class="dashicons dashicons-admin-page"></span>
                            <?php esc_html_e('Pages', 'tugasin'); ?>
                        </button>

                        <button type="button" class="tugasin-nav-item" data-section="data">
                            <span class="dashicons dashicons-database"></span>
                            <?php esc_html_e('Data', 'tugasin'); ?>
                        </button>

                        <div class="tugasin-nav-separator"></div>
                        <span class="tugasin-nav-label"><?php esc_html_e('SEO', 'tugasin'); ?></span>

                        <button type="button" class="tugasin-nav-item" data-section="schema">
                            <span class="dashicons dashicons-media-code"></span>
                            <?php esc_html_e('Schema Markup', 'tugasin'); ?>
                        </button>

                        <div class="tugasin-nav-separator"></div>
                        <span class="tugasin-nav-label"><?php esc_html_e('Performance', 'tugasin'); ?></span>

                        <button type="button" class="tugasin-nav-item" data-section="optimization">
                            <span class="dashicons dashicons-performance"></span>
                            <?php esc_html_e('Optimization', 'tugasin'); ?>
                        </button>

                        <button type="button" class="tugasin-nav-item" data-section="related">
                            <span class="dashicons dashicons-format-aside"></span>
                            <?php esc_html_e('Related Articles', 'tugasin'); ?>
                        </button>

                        <button type="button" class="tugasin-nav-item" data-section="featured-image">
                            <span class="dashicons dashicons-format-image"></span>
                            <?php esc_html_e('Featured Image', 'tugasin'); ?>
                        </button>

                        <div class="tugasin-nav-separator"></div>
                        <span class="tugasin-nav-label"><?php esc_html_e('Tools', 'tugasin'); ?></span>

                        <button type="button" class="tugasin-nav-item" data-section="import-demo">
                            <span class="dashicons dashicons-download"></span>
                            <?php esc_html_e('Import Demo Data', 'tugasin'); ?>
                        </button>

                        <button type="button" class="tugasin-nav-item" data-section="import-export">
                            <span class="dashicons dashicons-database-export"></span>
                            <?php esc_html_e('Import & Export', 'tugasin'); ?>
                        </button>

                        <button type="button" class="tugasin-nav-item" data-section="about">
                            <span class="dashicons dashicons-info"></span>
                            <?php esc_html_e('About', 'tugasin'); ?>
                        </button>
                    </nav>
                </aside>

                <!-- Main Content Area -->
                <main class="tugasin-settings-main">

                    <!-- General Section -->
                    <section id="section-general" class="tugasin-settings-section active">
                        <div class="tugasin-section-header">
                            <h2><span class="dashicons dashicons-admin-generic"></span>
                                <?php esc_html_e('General Settings', 'tugasin'); ?></h2>
                            <p><?php esc_html_e('Configure your contact settings and call-to-action buttons.', 'tugasin'); ?>
                            </p>
                        </div>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-whatsapp"></span>
                                <?php esc_html_e('WhatsApp Settings', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('Configure your WhatsApp contact settings. All CTA buttons will use this number.', 'tugasin'); ?>
                            </p>

                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('WhatsApp Number', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <input type="text" name="tugasin_wa_number"
                                        value="<?php echo esc_attr(get_option('tugasin_wa_number', '6281234567890')); ?>"
                                        placeholder="6281234567890">
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('Enter number with country code, no + or spaces (e.g., 6281234567890)', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('Default Message', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <textarea name="tugasin_wa_template"
                                        rows="3"><?php echo esc_textarea(get_option('tugasin_wa_template', 'Halo, saya ingin konsultasi tentang bantuan tugas.')); ?></textarea>
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('Default message that will be pre-filled when users click WhatsApp buttons.', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-megaphone"></span>
                                <?php esc_html_e('Call to Action', 'tugasin'); ?>
                            </h3>

                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('Header CTA Text', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <input type="text" name="tugasin_cta_text"
                                        value="<?php echo esc_attr(get_option('tugasin_cta_text', 'Konsultasi Sekarang!')); ?>"
                                        placeholder="Konsultasi Sekarang!">
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('Text displayed on the header CTA button.', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- WhatsApp Widget Section -->
                    <section id="section-whatsapp" class="tugasin-settings-section">
                        <div class="tugasin-section-header">
                            <h2><span class="dashicons dashicons-format-chat"></span>
                                <?php esc_html_e('WhatsApp Floating Widget', 'tugasin'); ?></h2>
                            <p><?php esc_html_e('Configure the floating WhatsApp button that appears in the bottom-right corner.', 'tugasin'); ?>
                            </p>
                        </div>

                        <div class="tugasin-section-card">
                            <div class="tugasin-checkbox-row">
                                <input type="checkbox" name="tugasin_wa_widget_enabled" id="tugasin_wa_widget_enabled" value="1"
                                    <?php checked(get_option('tugasin_wa_widget_enabled', true)); ?>>
                                <label class="tugasin-checkbox-label" for="tugasin_wa_widget_enabled">
                                    <strong><?php esc_html_e('Enable WhatsApp Widget', 'tugasin'); ?></strong>
                                    <span><?php esc_html_e('Show a floating WhatsApp button on all pages', 'tugasin'); ?></span>
                                </label>
                            </div>

                            <div class="tugasin-field-row">
                                <label
                                    class="tugasin-field-label"><?php esc_html_e('Chat Bubble Message', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <textarea name="tugasin_wa_widget_cta"
                                        rows="3"><?php echo esc_textarea(get_option('tugasin_wa_widget_cta', 'Halo! Kamu butuh bantuan? Tim Tugasin siap bantu kamu. Yuk konsultasi sekarang, GRATIS!')); ?></textarea>
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('Message shown in the chat bubble popup above the WhatsApp button.', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="tugasin-field-row">
                                <label
                                    class="tugasin-field-label"><?php esc_html_e('Popup Delay (seconds)', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <input type="number" name="tugasin_wa_widget_delay"
                                        value="<?php echo esc_attr(get_option('tugasin_wa_widget_delay', 3)); ?>" min="0"
                                        max="60" style="width: 100px;">
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('How many seconds to wait before showing the chat bubble. Set to 0 to disable auto-popup.', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Branding Section -->
                    <section id="section-branding" class="tugasin-settings-section">
                        <div class="tugasin-section-header">
                            <h2><span class="dashicons dashicons-admin-appearance"></span>
                                <?php esc_html_e('Branding', 'tugasin'); ?></h2>
                            <p><?php esc_html_e('Customize your site logo, icon, and brand colors.', 'tugasin'); ?></p>
                        </div>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-format-image"></span>
                                <?php esc_html_e('Logo & Icons', 'tugasin'); ?>
                            </h3>

                            <?php
                            $logo_id = get_option('tugasin_logo', 0);
                            $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'medium') : '';
                            ?>
                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('Site Logo', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <div class="tugasin-media-field">
                                        <div class="tugasin-media-preview-box <?php echo $logo_url ? 'has-image' : ''; ?>">
                                            <?php if ($logo_url): ?>
                                                <img src="<?php echo esc_url($logo_url); ?>" alt="">
                                            <?php else: ?>
                                                <span class="placeholder-icon dashicons dashicons-format-image"></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="tugasin-media-actions">
                                            <input type="hidden" name="tugasin_logo" id="tugasin_logo"
                                                value="<?php echo esc_attr($logo_id); ?>">
                                            <button type="button" class="tugasin-btn tugasin-btn-secondary tugasin-upload-btn"
                                                data-target="tugasin_logo">
                                                <span class="dashicons dashicons-upload"></span>
                                                <?php esc_html_e('Upload Logo', 'tugasin'); ?>
                                            </button>
                                            <button type="button" class="tugasin-btn tugasin-btn-danger tugasin-remove-btn"
                                                data-target="tugasin_logo" <?php echo !$logo_id ? 'style="display:none;"' : ''; ?>>
                                                <span class="dashicons dashicons-trash"></span>
                                                <?php esc_html_e('Remove', 'tugasin'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('Upload your site logo. If empty, site title text will be displayed.', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>

                            <?php
                            $icon_id = get_option('tugasin_site_icon', 0);
                            $icon_url = $icon_id ? wp_get_attachment_image_url($icon_id, 'thumbnail') : '';
                            ?>
                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('Site Icon', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <div class="tugasin-media-field">
                                        <div class="tugasin-media-preview-box <?php echo $icon_url ? 'has-image' : ''; ?>">
                                            <?php if ($icon_url): ?>
                                                <img src="<?php echo esc_url($icon_url); ?>" alt="">
                                            <?php else: ?>
                                                <span class="placeholder-icon dashicons dashicons-admin-site-alt3"></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="tugasin-media-actions">
                                            <input type="hidden" name="tugasin_site_icon" id="tugasin_site_icon"
                                                value="<?php echo esc_attr($icon_id); ?>">
                                            <button type="button" class="tugasin-btn tugasin-btn-secondary tugasin-upload-btn"
                                                data-target="tugasin_site_icon">
                                                <span class="dashicons dashicons-upload"></span>
                                                <?php esc_html_e('Upload Icon', 'tugasin'); ?>
                                            </button>
                                            <button type="button" class="tugasin-btn tugasin-btn-danger tugasin-remove-btn"
                                                data-target="tugasin_site_icon" <?php echo !$icon_id ? 'style="display:none;"' : ''; ?>>
                                                <span class="dashicons dashicons-trash"></span>
                                                <?php esc_html_e('Remove', 'tugasin'); ?>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('Upload your site icon (favicon). Recommended size: 512x512 pixels.', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-art"></span>
                                <?php esc_html_e('Colors', 'tugasin'); ?>
                            </h3>

                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('Primary Color', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <input type="text" name="tugasin_color_primary"
                                        value="<?php echo esc_attr(get_option('tugasin_color_primary', '#064e3b')); ?>"
                                        class="tugasin-color-picker" data-default-color="#064e3b">
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('Main theme color used for buttons, links, and accents.', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Pages Section -->
                    <section id="section-pages" class="tugasin-settings-section">
                        <div class="tugasin-section-header">
                            <h2><span class="dashicons dashicons-admin-page"></span>
                                <?php esc_html_e('Page Mapping', 'tugasin'); ?></h2>
                            <p><?php esc_html_e('Select which pages correspond to each section of your site. This allows you to use any page slug.', 'tugasin'); ?>
                            </p>
                        </div>

                        <div class="tugasin-section-card">
                            <table class="tugasin-page-table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Section', 'tugasin'); ?></th>
                                        <th><?php esc_html_e('Assigned Page', 'tugasin'); ?></th>
                                        <th><?php esc_html_e('Fallback Slug', 'tugasin'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $page_mappings = array(
                                        'page_layanan' => array('label' => __('Layanan (Services)', 'tugasin'), 'slug' => 'layanan'),
                                        'page_joki_skripsi' => array('label' => __('Joki Skripsi', 'tugasin'), 'slug' => 'joki-skripsi'),
                                        'page_joki_makalah' => array('label' => __('Joki Makalah', 'tugasin'), 'slug' => 'joki-makalah'),
                                        'page_joki_tugas' => array('label' => __('Joki Tugas', 'tugasin'), 'slug' => 'joki-tugas'),
                                        'page_cek_plagiarism' => array('label' => __('Cek Plagiarism', 'tugasin'), 'slug' => 'cek-plagiarism'),
                                    );

                                    foreach ($page_mappings as $key => $data):
                                        $option_name = 'tugasin_' . $key;
                                        $value = get_option($option_name, 0);
                                        ?>
                                        <tr>
                                            <td><strong><?php echo esc_html($data['label']); ?></strong></td>
                                            <td>
                                                <?php
                                                wp_dropdown_pages(array(
                                                    'name' => $option_name,
                                                    'selected' => $value,
                                                    'show_option_none' => __('â€” Select Page â€”', 'tugasin'),
                                                    'option_none_value' => 0,
                                                ));
                                                ?>
                                            </td>
                                            <td><span class="slug-hint"><code><?php echo esc_html($data['slug']); ?></code></span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-wordpress"></span>
                                <?php esc_html_e('WordPress Pages', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('These pages are configured elsewhere in WordPress.', 'tugasin'); ?>
                            </p>

                            <?php
                            $reading_url = admin_url('options-reading.php');
                            $home_id = get_option('page_on_front');
                            $home_title = $home_id ? get_the_title($home_id) : __('Not set', 'tugasin');
                            $blog_id = get_option('page_for_posts');
                            $blog_title = $blog_id ? get_the_title($blog_id) : __('Not set', 'tugasin');
                            ?>

                            <table class="tugasin-readonly-table">
                                <tr>
                                    <td class="page-name"><?php esc_html_e('Home Page', 'tugasin'); ?></td>
                                    <td class="page-value"><?php echo esc_html($home_title); ?></td>
                                    <td class="page-action">
                                        <a href="<?php echo esc_url($reading_url); ?>"
                                            class="button button-small"><?php esc_html_e('Change', 'tugasin'); ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="page-name"><?php esc_html_e('Blog Page', 'tugasin'); ?></td>
                                    <td class="page-value"><?php echo esc_html($blog_title); ?></td>
                                    <td class="page-action">
                                        <a href="<?php echo esc_url($reading_url); ?>"
                                            class="button button-small"><?php esc_html_e('Change', 'tugasin'); ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="page-name"><?php esc_html_e('Kamus Jurusan', 'tugasin'); ?></td>
                                    <td class="page-value">
                                        <code><?php echo esc_html(get_post_type_archive_link('major')); ?></code>
                                    </td>
                                    <td class="page-action"><em><?php esc_html_e('CPT Archive', 'tugasin'); ?></em></td>
                                </tr>
                                <tr>
                                    <td class="page-name"><?php esc_html_e('Kamus Kampus', 'tugasin'); ?></td>
                                    <td class="page-value">
                                        <code><?php echo esc_html(get_post_type_archive_link('university')); ?></code>
                                    </td>
                                    <td class="page-action"><em><?php esc_html_e('CPT Archive', 'tugasin'); ?></em></td>
                                </tr>
                            </table>
                        </div>
                    </section>

                    <!-- Data Section -->
                    <section id="section-data" class="tugasin-settings-section">
                        <div class="tugasin-section-header">
                            <h2><span class="dashicons dashicons-database"></span>
                                <?php esc_html_e('Data Management', 'tugasin'); ?></h2>
                            <p><?php esc_html_e('Manage testimonials and reviews displayed on service pages. This replaces hardcoded external images with local uploads.', 'tugasin'); ?>
                            </p>
                        </div>

                        <!-- Hero Tutor Profiles -->
                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-groups"></span>
                                <?php esc_html_e('Hero Section Tutors', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('Configure the 3 tutor profiles displayed in the hero carousel on the homepage.', 'tugasin'); ?>
                            </p>

                            <?php
                            $hero_tutors = get_option('tugasin_hero_tutors', array());
                            $default_tutors = array(
                                array('name' => 'Sarah Wijaya', 'role' => 'Expert Skripsi', 'rating' => '4.9', 'count' => 127),
                                array('name' => 'Budi Santoso', 'role' => 'Expert Makalah', 'rating' => '4.8', 'count' => 98),
                                array('name' => 'Dewi Lestari', 'role' => 'Expert Tugas', 'rating' => '4.9', 'count' => 156),
                            );
                            for ($i = 0; $i < 3; $i++):
                                $tutor = isset($hero_tutors[$i]) ? $hero_tutors[$i] : array();
                                $default = isset($default_tutors[$i]) ? $default_tutors[$i] : array();
                                $name = isset($tutor['name']) && $tutor['name'] ? $tutor['name'] : (isset($default['name']) ? $default['name'] : '');
                                $role = isset($tutor['role']) && $tutor['role'] ? $tutor['role'] : (isset($default['role']) ? $default['role'] : '');
                                $image = isset($tutor['image']) ? $tutor['image'] : '';
                                $rating = isset($tutor['rating']) && $tutor['rating'] ? $tutor['rating'] : (isset($default['rating']) ? $default['rating'] : '');
                                $count = isset($tutor['count']) && $tutor['count'] ? $tutor['count'] : (isset($default['count']) ? $default['count'] : 0);
                                ?>
                                <div class="tugasin-testimonial-item<?php echo $i > 0 ? ' has-border-top' : ''; ?>">
                                    <h4 class="testimonial-number"><?php printf(esc_html__('Tutor #%d', 'tugasin'), $i + 1); ?>
                                    </h4>

                                    <div class="tugasin-field-row tugasin-field-row-2col">
                                        <div class="tugasin-field-col">
                                            <label class="tugasin-field-label"><?php esc_html_e('Name', 'tugasin'); ?></label>
                                            <input type="text" name="tugasin_hero_tutors[<?php echo $i; ?>][name]"
                                                value="<?php echo esc_attr($name); ?>"
                                                placeholder="<?php esc_attr_e('Sarah Wijaya', 'tugasin'); ?>">
                                        </div>
                                        <div class="tugasin-field-col">
                                            <label
                                                class="tugasin-field-label"><?php esc_html_e('Role / Expertise', 'tugasin'); ?></label>
                                            <input type="text" name="tugasin_hero_tutors[<?php echo $i; ?>][role]"
                                                value="<?php echo esc_attr($role); ?>"
                                                placeholder="<?php esc_attr_e('Expert Skripsi', 'tugasin'); ?>">
                                        </div>
                                    </div>

                                    <div class="tugasin-field-row">
                                        <label class="tugasin-field-label"><?php esc_html_e('Photo URL', 'tugasin'); ?></label>
                                        <div class="tugasin-field-input tugasin-testimonial-image-field">
                                            <div class="tugasin-testimonial-preview">
                                                <?php if ($image): ?>
                                                    <img src="<?php echo esc_url($image); ?>" alt="">
                                                <?php else: ?>
                                                    <span class="dashicons dashicons-admin-users"></span>
                                                <?php endif; ?>
                                            </div>
                                            <input type="url" name="tugasin_hero_tutors[<?php echo $i; ?>][image]"
                                                value="<?php echo esc_url($image); ?>" placeholder="https://..."
                                                class="tugasin-testimonial-image-input">
                                            <button type="button"
                                                class="button tugasin-upload-image-btn"><?php esc_html_e('Upload', 'tugasin'); ?></button>
                                        </div>
                                    </div>

                                    <div class="tugasin-field-row tugasin-field-row-2col">
                                        <div class="tugasin-field-col">
                                            <label
                                                class="tugasin-field-label"><?php esc_html_e('Rating (e.g. 4.9)', 'tugasin'); ?></label>
                                            <input type="text" name="tugasin_hero_tutors[<?php echo $i; ?>][rating]"
                                                value="<?php echo esc_attr($rating); ?>" placeholder="4.9" style="width: 80px;">
                                        </div>
                                        <div class="tugasin-field-col">
                                            <label
                                                class="tugasin-field-label"><?php esc_html_e('Review Count', 'tugasin'); ?></label>
                                            <input type="number" name="tugasin_hero_tutors[<?php echo $i; ?>][count]"
                                                value="<?php echo esc_attr($count); ?>" placeholder="127" style="width: 100px;"
                                                min="0">
                                        </div>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-format-quote"></span>
                                <?php esc_html_e('Default Testimonials', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('These testimonials are used by default on all service pages. You can override them per-page below.', 'tugasin'); ?>
                            </p>

                            <?php
                            $default_testimonials = get_option('tugasin_testimonials_default', array());
                            for ($i = 0; $i < 3; $i++):
                                $testimonial = isset($default_testimonials[$i]) ? $default_testimonials[$i] : array();
                                $name = isset($testimonial['name']) ? $testimonial['name'] : '';
                                $role = isset($testimonial['role']) ? $testimonial['role'] : '';
                                $image = isset($testimonial['image']) ? $testimonial['image'] : '';
                                $text = isset($testimonial['text']) ? $testimonial['text'] : '';
                                $alt = isset($testimonial['alt']) ? $testimonial['alt'] : '';
                                ?>
                                <div class="tugasin-testimonial-item<?php echo $i > 0 ? ' has-border-top' : ''; ?>">
                                    <h4 class="testimonial-number">
                                        <?php printf(esc_html__('Testimonial #%d', 'tugasin'), $i + 1); ?>
                                    </h4>

                                    <div class="tugasin-field-row tugasin-field-row-2col">
                                        <div class="tugasin-field-col">
                                            <label class="tugasin-field-label"><?php esc_html_e('Name', 'tugasin'); ?></label>
                                            <input type="text" name="tugasin_testimonials_default[<?php echo $i; ?>][name]"
                                                value="<?php echo esc_attr($name); ?>"
                                                placeholder="<?php esc_attr_e('Ahmad R.', 'tugasin'); ?>">
                                        </div>
                                        <div class="tugasin-field-col">
                                            <label
                                                class="tugasin-field-label"><?php esc_html_e('Working Field / Role', 'tugasin'); ?></label>
                                            <input type="text" name="tugasin_testimonials_default[<?php echo $i; ?>][role]"
                                                value="<?php echo esc_attr($role); ?>"
                                                placeholder="<?php esc_attr_e('Mahasiswa Teknik UI', 'tugasin'); ?>">
                                        </div>
                                    </div>

                                    <div class="tugasin-field-row">
                                        <label class="tugasin-field-label"><?php esc_html_e('Picture URL', 'tugasin'); ?></label>
                                        <div class="tugasin-field-input tugasin-testimonial-image-field">
                                            <div class="tugasin-testimonial-preview">
                                                <?php if ($image): ?>
                                                    <img src="<?php echo esc_url($image); ?>" alt="">
                                                <?php else: ?>
                                                    <span class="dashicons dashicons-admin-users"></span>
                                                <?php endif; ?>
                                            </div>
                                            <input type="url" name="tugasin_testimonials_default[<?php echo $i; ?>][image]"
                                                value="<?php echo esc_url($image); ?>" placeholder="https://..."
                                                class="tugasin-testimonial-image-input">
                                            <button type="button"
                                                class="button tugasin-upload-image-btn"><?php esc_html_e('Upload', 'tugasin'); ?></button>
                                        </div>
                                    </div>

                                    <div class="tugasin-field-row">
                                        <label
                                            class="tugasin-field-label"><?php esc_html_e('Testimonial Text', 'tugasin'); ?></label>
                                        <textarea name="tugasin_testimonials_default[<?php echo $i; ?>][text]" rows="2"
                                            placeholder="<?php esc_attr_e('Review or testimonial text...', 'tugasin'); ?>"><?php echo esc_textarea($text); ?></textarea>
                                    </div>

                                    <div class="tugasin-field-row">
                                        <label
                                            class="tugasin-field-label"><?php esc_html_e('Alt Text for Image', 'tugasin'); ?></label>
                                        <input type="text" name="tugasin_testimonials_default[<?php echo $i; ?>][alt]"
                                            value="<?php echo esc_attr($alt); ?>"
                                            placeholder="<?php esc_attr_e('Foto Ahmad R.', 'tugasin'); ?>">
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-admin-page"></span>
                                <?php esc_html_e('Page-Specific Overrides', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('Override the default testimonials for specific service pages. Expand each page to configure.', 'tugasin'); ?>
                            </p>

                            <?php foreach ($this->testimonial_pages as $key => $label):
                                $use_default = get_option('tugasin_testimonials_' . $key . '_use_default', true);
                                $page_testimonials = get_option('tugasin_testimonials_' . $key, array());
                                ?>
                                <div class="tugasin-schema-service">
                                    <div class="tugasin-schema-service-header"
                                        onclick="this.parentElement.classList.toggle('open')">
                                        <h4>
                                            <span class="dashicons dashicons-admin-page"></span>
                                            <?php echo esc_html($label); ?>
                                        </h4>
                                        <span class="dashicons dashicons-arrow-down-alt2 toggle-icon"></span>
                                    </div>
                                    <div class="tugasin-schema-service-body">
                                        <div class="tugasin-checkbox-row" style="margin-bottom: 16px;">
                                            <input type="checkbox"
                                                name="tugasin_testimonials_<?php echo esc_attr($key); ?>_use_default"
                                                id="tugasin_testimonials_<?php echo esc_attr($key); ?>_use_default" value="1" <?php checked($use_default); ?>>
                                            <label class="tugasin-checkbox-label"
                                                for="tugasin_testimonials_<?php echo esc_attr($key); ?>_use_default">
                                                <strong><?php esc_html_e('Use Default Testimonials', 'tugasin'); ?></strong>
                                                <span><?php esc_html_e('When checked, this page uses the default testimonials above.', 'tugasin'); ?></span>
                                            </label>
                                        </div>

                                        <div class="tugasin-custom-testimonials-wrapper"
                                            style="<?php echo $use_default ? 'display:none;' : ''; ?>">
                                            <h5 class="custom-testimonials-title">
                                                <?php esc_html_e('Custom Testimonials for This Page', 'tugasin'); ?>
                                            </h5>
                                            <?php
                                            for ($i = 0; $i < 3; $i++):
                                                $testimonial = isset($page_testimonials[$i]) ? $page_testimonials[$i] : array();
                                                $name = isset($testimonial['name']) ? $testimonial['name'] : '';
                                                $role = isset($testimonial['role']) ? $testimonial['role'] : '';
                                                $image = isset($testimonial['image']) ? $testimonial['image'] : '';
                                                $text = isset($testimonial['text']) ? $testimonial['text'] : '';
                                                $alt = isset($testimonial['alt']) ? $testimonial['alt'] : '';
                                                ?>
                                                <div class="tugasin-testimonial-item<?php echo $i > 0 ? ' has-border-top' : ''; ?>">
                                                    <h4 class="testimonial-number">
                                                        <?php printf(esc_html__('Testimonial #%d', 'tugasin'), $i + 1); ?>
                                                    </h4>

                                                    <div class="tugasin-field-row tugasin-field-row-2col">
                                                        <div class="tugasin-field-col">
                                                            <label
                                                                class="tugasin-field-label"><?php esc_html_e('Name', 'tugasin'); ?></label>
                                                            <input type="text"
                                                                name="tugasin_testimonials_<?php echo esc_attr($key); ?>[<?php echo $i; ?>][name]"
                                                                value="<?php echo esc_attr($name); ?>">
                                                        </div>
                                                        <div class="tugasin-field-col">
                                                            <label
                                                                class="tugasin-field-label"><?php esc_html_e('Working Field / Role', 'tugasin'); ?></label>
                                                            <input type="text"
                                                                name="tugasin_testimonials_<?php echo esc_attr($key); ?>[<?php echo $i; ?>][role]"
                                                                value="<?php echo esc_attr($role); ?>">
                                                        </div>
                                                    </div>

                                                    <div class="tugasin-field-row">
                                                        <label
                                                            class="tugasin-field-label"><?php esc_html_e('Picture URL', 'tugasin'); ?></label>
                                                        <div class="tugasin-field-input tugasin-testimonial-image-field">
                                                            <div class="tugasin-testimonial-preview">
                                                                <?php if ($image): ?>
                                                                    <img src="<?php echo esc_url($image); ?>" alt="">
                                                                <?php else: ?>
                                                                    <span class="dashicons dashicons-admin-users"></span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <input type="url"
                                                                name="tugasin_testimonials_<?php echo esc_attr($key); ?>[<?php echo $i; ?>][image]"
                                                                value="<?php echo esc_url($image); ?>"
                                                                class="tugasin-testimonial-image-input">
                                                            <button type="button"
                                                                class="button tugasin-upload-image-btn"><?php esc_html_e('Upload', 'tugasin'); ?></button>
                                                        </div>
                                                    </div>

                                                    <div class="tugasin-field-row">
                                                        <label
                                                            class="tugasin-field-label"><?php esc_html_e('Testimonial Text', 'tugasin'); ?></label>
                                                        <textarea
                                                            name="tugasin_testimonials_<?php echo esc_attr($key); ?>[<?php echo $i; ?>][text]"
                                                            rows="2"><?php echo esc_textarea($text); ?></textarea>
                                                    </div>

                                                    <div class="tugasin-field-row">
                                                        <label
                                                            class="tugasin-field-label"><?php esc_html_e('Alt Text', 'tugasin'); ?></label>
                                                        <input type="text"
                                                            name="tugasin_testimonials_<?php echo esc_attr($key); ?>[<?php echo $i; ?>][alt]"
                                                            value="<?php echo esc_attr($alt); ?>">
                                                    </div>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Schema Section -->
                    <section id="section-schema" class="tugasin-settings-section">
                        <div class="tugasin-section-header">
                            <h2><span class="dashicons dashicons-media-code"></span>
                                <?php esc_html_e('Schema Markup (SEO)', 'tugasin'); ?></h2>
                            <p><?php esc_html_e('Configure structured data for rich snippets in search results. Disable this if using an SEO plugin like RankMath or Yoast.', 'tugasin'); ?>
                            </p>
                        </div>

                        <div class="tugasin-section-card">
                            <div class="tugasin-checkbox-row">
                                <input type="checkbox" name="tugasin_schema_enabled" id="tugasin_schema_enabled" value="1" <?php checked(get_option('tugasin_schema_enabled', true)); ?>>
                                <label class="tugasin-checkbox-label" for="tugasin_schema_enabled">
                                    <strong><?php esc_html_e('Enable Schema Markup', 'tugasin'); ?></strong>
                                    <span><?php esc_html_e('Output JSON-LD structured data for Organization and Services', 'tugasin'); ?></span>
                                </label>
                            </div>
                        </div>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-building"></span>
                                <?php esc_html_e('Organization Schema', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('This information appears site-wide and helps Google understand your business.', 'tugasin'); ?>
                            </p>

                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('Organization Name', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <input type="text" name="tugasin_schema_org_name"
                                        value="<?php echo esc_attr(get_option('tugasin_schema_org_name', get_bloginfo('name'))); ?>">
                                </div>
                            </div>

                            <?php
                            $schema_logo_id = get_option('tugasin_schema_org_logo', 0);
                            $schema_logo_url = $schema_logo_id ? wp_get_attachment_image_url($schema_logo_id, 'medium') : '';
                            ?>
                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('Organization Logo', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <div class="tugasin-media-field">
                                        <div
                                            class="tugasin-media-preview-box <?php echo $schema_logo_url ? 'has-image' : ''; ?>">
                                            <?php if ($schema_logo_url): ?>
                                                <img src="<?php echo esc_url($schema_logo_url); ?>" alt="">
                                            <?php else: ?>
                                                <span class="placeholder-icon dashicons dashicons-format-image"></span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="tugasin-media-actions">
                                            <input type="hidden" name="tugasin_schema_org_logo" id="tugasin_schema_org_logo"
                                                value="<?php echo esc_attr($schema_logo_id); ?>">
                                            <button type="button" class="tugasin-btn tugasin-btn-secondary tugasin-upload-btn"
                                                data-target="tugasin_schema_org_logo">
                                                <span class="dashicons dashicons-upload"></span>
                                                <?php esc_html_e('Upload', 'tugasin'); ?>
                                            </button>
                                            <button type="button" class="tugasin-btn tugasin-btn-danger tugasin-remove-btn"
                                                data-target="tugasin_schema_org_logo" <?php echo !$schema_logo_id ? 'style="display:none;"' : ''; ?>>
                                                <span class="dashicons dashicons-trash"></span>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('Logo for Google Knowledge Graph. If empty, uses the Site Logo above.', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('Contact Phone', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <input type="text" name="tugasin_schema_org_phone"
                                        value="<?php echo esc_attr(get_option('tugasin_schema_org_phone', '')); ?>"
                                        placeholder="+62-21-1234567">
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('Phone number for customer service contact point.', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-list-view"></span>
                                <?php esc_html_e('Layanan Archive Schema', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('Configure schema for the main Layanan page (service list). This shows on the layanan archive page.', 'tugasin'); ?>
                            </p>

                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('List Name', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <input type="text" name="tugasin_schema_layanan_name"
                                        value="<?php echo esc_attr(get_option('tugasin_schema_layanan_name', __('Layanan Tugasin', 'tugasin'))); ?>">
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('Name that appears in rich results for the service list.', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('List Description', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <textarea name="tugasin_schema_layanan_desc"
                                        rows="2"><?php echo esc_textarea(get_option('tugasin_schema_layanan_desc', __('Daftar layanan jasa akademik dari Tugasin', 'tugasin'))); ?></textarea>
                                </div>
                            </div>

                            <div class="tugasin-checkbox-row" style="margin-top: 16px;">
                                <input type="checkbox" name="tugasin_schema_layanan_rating_enabled"
                                    id="tugasin_schema_layanan_rating_enabled" value="1" <?php checked(get_option('tugasin_schema_layanan_rating_enabled', false)); ?>>
                                <label class="tugasin-checkbox-label" for="tugasin_schema_layanan_rating_enabled">
                                    <strong><?php esc_html_e('Show Rating Stars', 'tugasin'); ?></strong>
                                    <span><?php esc_html_e('Display aggregate rating for the entire service list', 'tugasin'); ?></span>
                                </label>
                            </div>

                            <div class="tugasin-field-row" style="margin-top: 16px;">
                                <label class="tugasin-field-label"><?php esc_html_e('Rating Value', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <input type="text" name="tugasin_schema_layanan_rating_value"
                                        value="<?php echo esc_attr(get_option('tugasin_schema_layanan_rating_value', '4.9')); ?>"
                                        style="width: 80px;" placeholder="4.9">
                                    <p class="tugasin-field-desc"><?php esc_html_e('Average rating (0-5)', 'tugasin'); ?></p>
                                </div>
                            </div>

                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('Review Count', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <input type="number" name="tugasin_schema_layanan_rating_count"
                                        value="<?php echo esc_attr(get_option('tugasin_schema_layanan_rating_count', 500)); ?>"
                                        style="width: 100px;" min="0">
                                    <p class="tugasin-field-desc"><?php esc_html_e('Total number of reviews', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-clipboard"></span>
                                <?php esc_html_e('Service Schema', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('Configure structured data for each service page. This helps show rich snippets with ratings in search results.', 'tugasin'); ?>
                            </p>

                            <?php foreach ($this->service_slugs as $slug => $label):
                                $prefix = 'tugasin_schema_service_' . str_replace('-', '_', $slug);
                                ?>
                                <div class="tugasin-schema-service">
                                    <div class="tugasin-schema-service-header"
                                        onclick="this.parentElement.classList.toggle('open')">
                                        <h4>
                                            <span class="dashicons dashicons-admin-page"></span>
                                            <?php echo esc_html($label); ?>
                                        </h4>
                                        <span class="dashicons dashicons-arrow-down-alt2 toggle-icon"></span>
                                    </div>
                                    <div class="tugasin-schema-service-body">
                                        <div class="tugasin-field-row">
                                            <label
                                                class="tugasin-field-label"><?php esc_html_e('Service Name', 'tugasin'); ?></label>
                                            <div class="tugasin-field-input">
                                                <input type="text" name="<?php echo esc_attr($prefix); ?>_name"
                                                    value="<?php echo esc_attr(get_option($prefix . '_name', $label)); ?>">
                                            </div>
                                        </div>

                                        <div class="tugasin-field-row">
                                            <label
                                                class="tugasin-field-label"><?php esc_html_e('Description', 'tugasin'); ?></label>
                                            <div class="tugasin-field-input">
                                                <textarea name="<?php echo esc_attr($prefix); ?>_desc"
                                                    rows="2"><?php echo esc_textarea(get_option($prefix . '_desc', '')); ?></textarea>
                                            </div>
                                        </div>

                                        <div class="tugasin-checkbox-row" style="margin-top: 16px;">
                                            <input type="checkbox" name="<?php echo esc_attr($prefix); ?>_rating_enabled"
                                                id="<?php echo esc_attr($prefix); ?>_rating_enabled" value="1" <?php checked(get_option($prefix . '_rating_enabled', false)); ?>>
                                            <label class="tugasin-checkbox-label"
                                                for="<?php echo esc_attr($prefix); ?>_rating_enabled">
                                                <strong><?php esc_html_e('Show Rating Stars', 'tugasin'); ?></strong>
                                                <span><?php esc_html_e('Display aggregate rating in search results', 'tugasin'); ?></span>
                                            </label>
                                        </div>

                                        <div class="tugasin-field-row" style="margin-top: 16px;">
                                            <label
                                                class="tugasin-field-label"><?php esc_html_e('Rating Value', 'tugasin'); ?></label>
                                            <div class="tugasin-field-input">
                                                <input type="text" name="<?php echo esc_attr($prefix); ?>_rating_value"
                                                    value="<?php echo esc_attr(get_option($prefix . '_rating_value', '4.9')); ?>"
                                                    style="width: 80px;" placeholder="4.9">
                                                <p class="tugasin-field-desc">
                                                    <?php esc_html_e('Average rating (0-5)', 'tugasin'); ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="tugasin-field-row">
                                            <label
                                                class="tugasin-field-label"><?php esc_html_e('Review Count', 'tugasin'); ?></label>
                                            <div class="tugasin-field-input">
                                                <input type="number" name="<?php echo esc_attr($prefix); ?>_rating_count"
                                                    value="<?php echo esc_attr(get_option($prefix . '_rating_count', 100)); ?>"
                                                    style="width: 100px;" min="0">
                                                <p class="tugasin-field-desc">
                                                    <?php esc_html_e('Total number of reviews', 'tugasin'); ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="tugasin-field-row" style="margin-top: 16px;">
                                            <label
                                                class="tugasin-field-label"><?php esc_html_e('Price From (IDR)', 'tugasin'); ?></label>
                                            <div class="tugasin-field-input">
                                                <input type="number" name="<?php echo esc_attr($prefix); ?>_price_from"
                                                    value="<?php echo esc_attr(get_option($prefix . '_price_from', '')); ?>"
                                                    style="width: 150px;" min="0" placeholder="50000">
                                                <p class="tugasin-field-desc">
                                                    <?php esc_html_e('Starting price for this service. Leave empty to hide price in schema.', 'tugasin'); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Optimization Section (Phase 28) -->
                    <section id="section-optimization" class="tugasin-settings-section">
                        <div class="tugasin-section-header">
                            <h2><span class="dashicons dashicons-performance"></span>
                                <?php esc_html_e('Optimization Settings', 'tugasin'); ?></h2>
                            <p><?php esc_html_e('Configure performance optimizations for faster page load times.', 'tugasin'); ?>
                            </p>
                        </div>

                        <!-- Defer JavaScript -->
                        <div class="tugasin-section-card">
                            <div class="tugasin-opt-header">
                                <div class="opt-title-wrap">
                                    <h3 class="tugasin-section-title">
                                        <span class="dashicons dashicons-clock"></span>
                                        <?php esc_html_e('Defer JavaScript', 'tugasin'); ?>
                                    </h3>
                                    <span class="tugasin-help-icon"
                                        data-tooltip="<?php esc_attr_e('Delays JavaScript execution until HTML is fully parsed. Improves page load speed. Be cautious: some scripts may break if deferred. Only defer scripts that do not need to run immediately.', 'tugasin'); ?>">?</span>
                                </div>
                                <label class="tugasin-toggle">
                                    <input type="checkbox" name="tugasin_opt_defer_enabled" value="1" <?php checked(get_option('tugasin_opt_defer_enabled', true)); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="tugasin-opt-fields" data-depends="tugasin_opt_defer_enabled">
                                <div class="tugasin-field-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Scripts to Defer', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <textarea name="tugasin_opt_defer_scripts" rows="4"
                                            placeholder="tugasin-main&#10;tugasin-archive-filter"><?php echo esc_textarea(get_option('tugasin_opt_defer_scripts', "tugasin-main\ntugasin-archive-filter")); ?></textarea>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('One per line. Use script handle, URL, or regex pattern (e.g., /elementor-.*/).', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="tugasin-field-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Scripts to Exclude', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <textarea name="tugasin_opt_defer_exclude" rows="3"
                                            placeholder="jquery&#10;wp-includes"><?php echo esc_textarea(get_option('tugasin_opt_defer_exclude', '')); ?></textarea>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Scripts that should NEVER be deferred. One per line.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preconnect Hints -->
                        <div class="tugasin-section-card">
                            <div class="tugasin-opt-header">
                                <div class="opt-title-wrap">
                                    <h3 class="tugasin-section-title">
                                        <span class="dashicons dashicons-admin-links"></span>
                                        <?php esc_html_e('Preconnect Hints', 'tugasin'); ?>
                                    </h3>
                                    <span class="tugasin-help-icon"
                                        data-tooltip="<?php esc_attr_e('Pre-establishes connections to external origins (CDNs, fonts, APIs) before the browser needs them. Reduces connection latency by about 100-300ms.', 'tugasin'); ?>">?</span>
                                </div>
                                <label class="tugasin-toggle">
                                    <input type="checkbox" name="tugasin_opt_preconnect_enabled" value="1" <?php checked(get_option('tugasin_opt_preconnect_enabled', true)); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="tugasin-opt-fields" data-depends="tugasin_opt_preconnect_enabled">
                                <div class="tugasin-field-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Preconnect URLs', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <textarea name="tugasin_opt_preconnect_urls" rows="3"
                                            placeholder="https://cdnjs.cloudflare.com&#10;https://fonts.googleapis.com"><?php echo esc_textarea(get_option('tugasin_opt_preconnect_urls', 'https://cdnjs.cloudflare.com')); ?></textarea>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('One URL per line. Include protocol (https://).', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- WebP Support -->
                        <div class="tugasin-section-card">
                            <div class="tugasin-opt-header">
                                <div class="opt-title-wrap">
                                    <h3 class="tugasin-section-title">
                                        <span class="dashicons dashicons-format-image"></span>
                                        <?php esc_html_e('WebP Upload Support', 'tugasin'); ?>
                                    </h3>
                                    <span class="tugasin-help-icon"
                                        data-tooltip="<?php esc_attr_e('Enables WebP image format uploads in WordPress Media Library. WebP offers 25-35% better compression than JPEG/PNG with similar quality.', 'tugasin'); ?>">?</span>
                                </div>
                                <label class="tugasin-toggle">
                                    <input type="checkbox" name="tugasin_opt_webp_enabled" value="1" <?php checked(get_option('tugasin_opt_webp_enabled', true)); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <p class="tugasin-section-desc" style="margin-top: 12px;">
                                <?php esc_html_e('When enabled, you can upload .webp images directly to the Media Library.', 'tugasin'); ?>
                            </p>
                        </div>

                        <!-- Lazy Load Images -->
                        <div class="tugasin-section-card">
                            <div class="tugasin-opt-header">
                                <div class="opt-title-wrap">
                                    <h3 class="tugasin-section-title">
                                        <span class="dashicons dashicons-images-alt2"></span>
                                        <?php esc_html_e('Lazy Load Images', 'tugasin'); ?>
                                    </h3>
                                    <span class="tugasin-help-icon"
                                        data-tooltip="<?php esc_attr_e('Defers loading of images until they are about to enter the viewport. Significantly reduces initial page weight and speeds up first contentful paint.', 'tugasin'); ?>">?</span>
                                </div>
                                <label class="tugasin-toggle">
                                    <input type="checkbox" name="tugasin_opt_lazyload_enabled" value="1" <?php checked(get_option('tugasin_opt_lazyload_enabled', true)); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>

                            <div class="tugasin-opt-fields" data-depends="tugasin_opt_lazyload_enabled">
                                <div class="tugasin-field-row">
                                    <label class="tugasin-field-label"><?php esc_html_e('Exclude Images', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <textarea name="tugasin_opt_lazyload_exclude" rows="3"
                                            placeholder="logo.png&#10;hero-image&#10;/above-fold-.*/"><?php echo esc_textarea(get_option('tugasin_opt_lazyload_exclude', '')); ?></textarea>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Images that should load immediately (above-the-fold). Use filename, partial URL, or regex.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cache Lifetime (.htaccess) -->
                        <div class="tugasin-section-card">
                            <div class="tugasin-opt-header">
                                <div class="opt-title-wrap">
                                    <h3 class="tugasin-section-title">
                                        <span class="dashicons dashicons-database"></span>
                                        <?php esc_html_e('Browser Cache (.htaccess)', 'tugasin'); ?>
                                    </h3>
                                    <span class="tugasin-help-icon"
                                        data-tooltip="<?php esc_attr_e('Adds browser caching rules to your .htaccess file. Images and fonts are cached for 1 year, CSS/JS for 1 month. Apache servers only. Nginx users should configure cache rules in server config.', 'tugasin'); ?>">?</span>
                                </div>
                                <label class="tugasin-toggle">
                                    <input type="checkbox" name="tugasin_opt_cache_enabled" value="1" <?php checked(get_option('tugasin_opt_cache_enabled', false)); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <p class="tugasin-section-desc" style="margin-top: 12px;">
                                <?php esc_html_e('When enabled, cache rules are automatically added to your .htaccess file. Disabled by default to prevent conflicts with caching plugins.', 'tugasin'); ?>
                                <?php if (class_exists('Tugasin_Optimization')):
                                    $opt = new Tugasin_Optimization();
                                    if ($opt->has_cache_rules()): ?>
                                        <br><span style="color: #16a34a;"><span class="dashicons dashicons-yes"></span>
                                            <?php esc_html_e('Cache rules are currently active in .htaccess', 'tugasin'); ?></span>
                                    <?php endif;
                                endif; ?>
                            </p>
                        </div>

                        <!-- Async CSS Load -->
                        <div class="tugasin-section-card">
                            <div class="tugasin-opt-header">
                                <div class="opt-title-wrap">
                                    <h3 class="tugasin-section-title">
                                        <span class="dashicons dashicons-editor-code"></span>
                                        <?php esc_html_e('Async CSS Load', 'tugasin'); ?>
                                    </h3>
                                    <span class="tugasin-help-icon"
                                        data-tooltip="<?php esc_attr_e('Loads CSS files asynchronously using rel=preload with onload swap. Eliminates render-blocking CSS but may cause brief flash of unstyled content (FOUC). Critical CSS is still inlined.', 'tugasin'); ?>">?</span>
                                </div>
                                <label class="tugasin-toggle">
                                    <input type="checkbox" name="tugasin_opt_async_css_enabled" value="1" <?php checked(get_option('tugasin_opt_async_css_enabled', false)); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <p class="tugasin-section-desc" style="margin-top: 12px;">
                                <?php esc_html_e('When enabled, main-bundle.css and Font Awesome CSS are loaded asynchronously. Disabled by default as it may cause a brief flash of unstyled content.', 'tugasin'); ?>
                            </p>
                        </div>

                        <!-- Disable jQuery Migrate -->
                        <div class="tugasin-section-card">
                            <div class="tugasin-opt-header">
                                <div class="opt-title-wrap">
                                    <h3 class="tugasin-section-title">
                                        <span class="dashicons dashicons-dismiss"></span>
                                        <?php esc_html_e('Disable jQuery Migrate', 'tugasin'); ?>
                                    </h3>
                                    <span class="tugasin-help-icon"
                                        data-tooltip="<?php esc_attr_e('Removes the jquery-migrate.js script (~10KB). jQuery Migrate provides backwards compatibility for deprecated jQuery features. Disable only if you are sure no plugins require it.', 'tugasin'); ?>">?</span>
                                </div>
                                <label class="tugasin-toggle">
                                    <input type="checkbox" name="tugasin_opt_disable_jquery_migrate" value="1" <?php checked(get_option('tugasin_opt_disable_jquery_migrate', false)); ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                            <p class="tugasin-section-desc" style="margin-top: 12px;">
                                <?php esc_html_e('When enabled, removes the render-blocking jquery-migrate.js script. Test thoroughly after enabling to ensure no plugin breaks.', 'tugasin'); ?>
                            </p>
                        </div>
                    </section>

                    <!-- Related Posts Section (Phase 29) -->
                    <section id="section-related" class="tugasin-settings-section">
                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-format-aside"></span>
                                <?php esc_html_e('Related Articles Settings', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('Configure how related articles are displayed on single blog posts. Two sections: inline (within content) and bottom (after content).', 'tugasin'); ?>
                            </p>

                            <!-- Inline Related Posts -->
                            <div class="tugasin-subsection">
                                <h4 class="tugasin-subsection-title"><?php esc_html_e('Inline Related Posts', 'tugasin'); ?>
                                </h4>
                                <p class="tugasin-field-desc" style="margin-bottom: 16px;">
                                    <?php esc_html_e('Text-only links inserted within the article content.', 'tugasin'); ?>
                                </p>

                                <div class="tugasin-field-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Enable Inline Related', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <label class="tugasin-toggle">
                                            <input type="checkbox" name="tugasin_related_inline_enabled" value="1" <?php checked(get_option('tugasin_related_inline_enabled', true)); ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </div>
                                </div>

                                <div class="tugasin-field-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Position (After Paragraph)', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <select name="tugasin_related_inline_position">
                                            <?php
                                            $current_position = get_option('tugasin_related_inline_position', 3);
                                            for ($i = 2; $i <= 6; $i++):
                                                ?>
                                                <option value="<?php echo esc_attr($i); ?>" <?php selected($current_position, $i); ?>>
                                                    <?php printf(esc_html__('After paragraph %d', 'tugasin'), $i); ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Insert the "Baca Juga" box after this paragraph number.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>

                                <div class="tugasin-field-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Number of Links', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <select name="tugasin_related_inline_count">
                                            <?php
                                            $current_count = get_option('tugasin_related_inline_count', 2);
                                            for ($i = 1; $i <= 4; $i++):
                                                ?>
                                                <option value="<?php echo esc_attr($i); ?>" <?php selected($current_count, $i); ?>>
                                                    <?php echo esc_html($i); ?>
                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Bottom Related Posts -->
                            <div class="tugasin-subsection"
                                style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                                <h4 class="tugasin-subsection-title"><?php esc_html_e('Bottom Related Posts', 'tugasin'); ?>
                                </h4>
                                <p class="tugasin-field-desc" style="margin-bottom: 16px;">
                                    <?php esc_html_e('Cards with featured images displayed after the article content.', 'tugasin'); ?>
                                </p>

                                <div class="tugasin-field-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Enable Bottom Related', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <label class="tugasin-toggle">
                                            <input type="checkbox" name="tugasin_related_bottom_enabled" value="1" <?php checked(get_option('tugasin_related_bottom_enabled', true)); ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Displays 3 related articles with images at the bottom of each post.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Featured Image Generator Section -->
                    <section id="section-featured-image" class="tugasin-settings-section">
                        <div class="tugasin-section-header">
                            <h2><span class="dashicons dashicons-format-image"></span>
                                <?php esc_html_e('Featured Image Generator', 'tugasin'); ?></h2>
                            <p><?php esc_html_e('Automatically generate featured images for posts without one using Pixabay API.', 'tugasin'); ?>
                            </p>
                        </div>

                        <div class="tugasin-section-content">
                            <!-- Enable/Disable -->
                            <div class="tugasin-field-row">
                                <label
                                    class="tugasin-field-label"><?php esc_html_e('Enable Auto Generation', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <label class="tugasin-toggle">
                                        <input type="checkbox" name="tugasin_fig_enabled" value="1" <?php checked(get_option('tugasin_fig_enabled', false)); ?>>
                                        <span class="toggle-slider"></span>
                                    </label>
                                    <p class="tugasin-field-desc">
                                        <?php esc_html_e('When enabled, featured images will be auto-generated for posts on publish.', 'tugasin'); ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Pixabay API Key -->
                            <div class="tugasin-field-row">
                                <label class="tugasin-field-label"><?php esc_html_e('Pixabay API Key', 'tugasin'); ?></label>
                                <div class="tugasin-field-input">
                                    <input type="password" name="tugasin_fig_pixabay_key"
                                        value="<?php echo esc_attr(get_option('tugasin_fig_pixabay_key', '')); ?>"
                                        class="regular-text" placeholder="Your Pixabay API key">
                                    <p class="tugasin-field-desc">
                                        <?php
                                        printf(
                                            /* translators: %s: Pixabay API docs link */
                                            esc_html__('Get your free API key from %s', 'tugasin'),
                                            '<a href="https://pixabay.com/api/docs/" target="_blank">pixabay.com/api/docs</a>'
                                        );
                                        ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Translation Settings Subsection -->
                            <div class="tugasin-subsection"
                                style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                                <h4 class="tugasin-subsection-title">
                                    <?php esc_html_e('Search Source & Translation', 'tugasin'); ?>
                                </h4>
                                <p style="color: #6b7280; margin-bottom: 16px; font-size: 13px;">
                                    <?php esc_html_e('Configure what text to use for Pixabay image search and translation settings.', 'tugasin'); ?>
                                </p>

                                <!-- Search Source -->
                                <div class="tugasin-field-row">
                                    <label class="tugasin-field-label"><?php esc_html_e('Search Source', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <?php $search_source = get_option('tugasin_fig_search_source', 'title'); ?>
                                        <select name="tugasin_fig_search_source" class="regular-text">
                                            <option value="title" <?php selected($search_source, 'title'); ?>>
                                                <?php esc_html_e('Post Title', 'tugasin'); ?>
                                            </option>
                                            <option value="category" <?php selected($search_source, 'category'); ?>>
                                                <?php esc_html_e('Post Category', 'tugasin'); ?>
                                            </option>
                                        </select>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Choose what text to use for searching images on Pixabay. "Post Title" extracts keywords from the title, "Post Category" uses the category name.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Enable Translation -->
                                <div class="tugasin-field-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Enable Translation', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <label class="tugasin-toggle">
                                            <input type="checkbox" name="tugasin_fig_enable_translation" value="1" <?php checked(get_option('tugasin_fig_enable_translation', true)); ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('When enabled, Indonesian titles will be translated to English for better Pixabay search results.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Translation Provider (moved up before API keys) -->
                                <div class="tugasin-field-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Translation Provider', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <?php $provider = get_option('tugasin_fig_translation_provider', 'google'); ?>
                                        <select name="tugasin_fig_translation_provider" id="tugasin_fig_translation_provider"
                                            class="regular-text">
                                            <option value="google" <?php selected($provider, 'google'); ?>>
                                                <?php esc_html_e('Google Cloud Translation', 'tugasin'); ?>
                                            </option>
                                            <option value="aws" <?php selected($provider, 'aws'); ?>>
                                                <?php esc_html_e('AWS Translate', 'tugasin'); ?>
                                            </option>
                                        </select>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Choose your translation provider. Google offers 500K chars/month free, AWS offers 2M chars/month free for 12 months.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Google Translate API Key (conditionally visible) -->
                                <div class="tugasin-field-row tugasin-provider-google" id="google-api-key-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Google Translate API Key', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <input type="password" name="tugasin_fig_google_translate_key"
                                            value="<?php echo esc_attr(get_option('tugasin_fig_google_translate_key', '')); ?>"
                                            class="regular-text" placeholder="Your Google Cloud Translation API key">
                                        <p class="tugasin-field-desc">
                                            <?php
                                            printf(
                                                /* translators: %s: Google Cloud Console link */
                                                esc_html__('Get your API key from %s. Enable Cloud Translation API in your project.', 'tugasin'),
                                                '<a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a>'
                                            );
                                            ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- AWS Access Key ID (conditionally visible) -->
                                <div class="tugasin-field-row tugasin-provider-aws" id="aws-access-key-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('AWS Access Key ID', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <input type="text" name="tugasin_fig_aws_access_key"
                                            value="<?php echo esc_attr(get_option('tugasin_fig_aws_access_key', '')); ?>"
                                            class="regular-text" placeholder="AKIA...">
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Your AWS Access Key ID with Translate permissions.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- AWS Secret Access Key (conditionally visible) -->
                                <div class="tugasin-field-row tugasin-provider-aws" id="aws-secret-key-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('AWS Secret Access Key', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <input type="password" name="tugasin_fig_aws_secret_key"
                                            value="<?php echo esc_attr(get_option('tugasin_fig_aws_secret_key', '')); ?>"
                                            class="regular-text" placeholder="Your AWS Secret Key">
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Your AWS Secret Access Key. Keep this secure.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- AWS Region (conditionally visible) -->
                                <div class="tugasin-field-row tugasin-provider-aws" id="aws-region-row">
                                    <label class="tugasin-field-label"><?php esc_html_e('AWS Region', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <?php $region = get_option('tugasin_fig_aws_region', 'us-east-1'); ?>
                                        <select name="tugasin_fig_aws_region" class="regular-text">
                                            <option value="us-east-1" <?php selected($region, 'us-east-1'); ?>>US East (N.
                                                Virginia)</option>
                                            <option value="us-east-2" <?php selected($region, 'us-east-2'); ?>>US East (Ohio)
                                            </option>
                                            <option value="us-west-2" <?php selected($region, 'us-west-2'); ?>>US West (Oregon)
                                            </option>
                                            <option value="eu-west-1" <?php selected($region, 'eu-west-1'); ?>>EU (Ireland)
                                            </option>
                                            <option value="eu-central-1" <?php selected($region, 'eu-central-1'); ?>>EU
                                                (Frankfurt)</option>
                                            <option value="ap-southeast-1" <?php selected($region, 'ap-southeast-1'); ?>>Asia
                                                Pacific (Singapore)</option>
                                            <option value="ap-northeast-1" <?php selected($region, 'ap-northeast-1'); ?>>Asia
                                                Pacific (Tokyo)</option>
                                        </select>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Select the AWS region closest to your server for best performance.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>

                                <script>
                                    (function () {
                                        var providerSelect = document.getElementById('tugasin_fig_translation_provider');
                                        if (!providerSelect) return;

                                        function toggleProviderFields() {
                                            var selectedProvider = providerSelect.value;
                                            var googleFields = document.querySelectorAll('.tugasin-provider-google');
                                            var awsFields = document.querySelectorAll('.tugasin-provider-aws');

                                            // Hide all first
                                            googleFields.forEach(function (el) { el.style.display = 'none'; });
                                            awsFields.forEach(function (el) { el.style.display = 'none'; });

                                            // Show based on selection
                                            if (selectedProvider === 'google') {
                                                googleFields.forEach(function (el) { el.style.display = ''; });
                                            } else if (selectedProvider === 'aws') {
                                                awsFields.forEach(function (el) { el.style.display = ''; });
                                            }
                                        }

                                        // Run on load
                                        toggleProviderFields();

                                        // Run on change
                                        providerSelect.addEventListener('change', toggleProviderFields);
                                    })();
                                </script>
                            </div>

                            <!-- Design Settings Subsection -->
                            <div class="tugasin-subsection"
                                style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                                <h4 class="tugasin-subsection-title"><?php esc_html_e('Design Settings', 'tugasin'); ?></h4>

                                <!-- Logo Upload -->
                                <div class="tugasin-field-row">
                                    <label class="tugasin-field-label"><?php esc_html_e('Logo Image', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <?php
                                        $logo_id = get_option('tugasin_fig_logo_image', 0);
                                        $logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'thumbnail') : '';
                                        ?>
                                        <div class="media-upload-field">
                                            <input type="hidden" name="tugasin_fig_logo_image" id="tugasin_fig_logo_image"
                                                value="<?php echo esc_attr($logo_id); ?>">
                                            <div class="preview-wrapper" style="margin-bottom: 10px;">
                                                <?php if ($logo_url): ?>
                                                    <img src="<?php echo esc_url($logo_url); ?>"
                                                        style="max-width: 150px; height: auto;">
                                                <?php endif; ?>
                                            </div>
                                            <button type="button" class="button tugasin-upload-btn"
                                                data-target="tugasin_fig_logo_image">
                                                <?php esc_html_e('Upload Logo', 'tugasin'); ?>
                                            </button>
                                            <button type="button" class="button tugasin-remove-btn"
                                                data-target="tugasin_fig_logo_image" <?php echo !$logo_id ? 'style="display:none;"' : ''; ?>>
                                                <?php esc_html_e('Remove', 'tugasin'); ?>
                                            </button>
                                            <p class="tugasin-field-desc">
                                                <?php esc_html_e('Leave empty to use default theme logo.', 'tugasin'); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Logo Size -->
                                <div class="tugasin-field-row">
                                    <label class="tugasin-field-label"><?php esc_html_e('Logo Size (px)', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <input type="number" name="tugasin_fig_logo_size"
                                            value="<?php echo esc_attr(get_option('tugasin_fig_logo_size', 56)); ?>" min="20"
                                            max="120" step="1" style="width: 80px;">
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Height of the logo in the generated image.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Gradient Color -->
                                <div class="tugasin-field-row">
                                    <label class="tugasin-field-label"><?php esc_html_e('Gradient Color', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <input type="color" name="tugasin_fig_gradient_color"
                                            value="<?php echo esc_attr(get_option('tugasin_fig_gradient_color', '#ffffff')); ?>">
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Fog overlay color for the text area.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Text Color -->
                                <div class="tugasin-field-row">
                                    <label class="tugasin-field-label"><?php esc_html_e('Text Color', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <input type="color" name="tugasin_fig_text_color"
                                            value="<?php echo esc_attr(get_option('tugasin_fig_text_color', '#1e3a5f')); ?>">
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Title text color on the generated image.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Search Settings Subsection -->
                            <div class="tugasin-subsection"
                                style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                                <h4 class="tugasin-subsection-title"><?php esc_html_e('Search Settings', 'tugasin'); ?></h4>
                                <p class="tugasin-field-desc" style="margin-bottom: 16px;">
                                    <?php esc_html_e('Keywords are automatically extracted from the post title. These settings are fallbacks.', 'tugasin'); ?>
                                </p>

                                <!-- Fallback Query -->
                                <div class="tugasin-field-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Fallback Search Query', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <input type="text" name="tugasin_fig_fallback_query"
                                            value="<?php echo esc_attr(get_option('tugasin_fig_fallback_query', 'students education university')); ?>"
                                            class="regular-text">
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Used when no keywords can be extracted from the title.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>

                                <!-- Auto Alt Text -->
                                <div class="tugasin-field-row">
                                    <label class="tugasin-field-label"><?php esc_html_e('Auto Alt Text', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <label class="tugasin-toggle">
                                            <input type="checkbox" name="tugasin_fig_auto_alt" value="1" <?php checked(get_option('tugasin_fig_auto_alt', true)); ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('Automatically set the post title as image alt text (recommended for SEO).', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <!-- Background Processing Subsection -->
                            <div class="tugasin-subsection"
                                style="margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;">
                                <h4 class="tugasin-subsection-title"><?php esc_html_e('Background Processing', 'tugasin'); ?>
                                </h4>
                                <p class="tugasin-field-desc" style="margin-bottom: 16px;">
                                    <?php esc_html_e('Automatically generate featured images for older posts missing them.', 'tugasin'); ?>
                                </p>

                                <!-- Enable Backfill -->
                                <div class="tugasin-field-row">
                                    <label
                                        class="tugasin-field-label"><?php esc_html_e('Enable Auto-Backfill', 'tugasin'); ?></label>
                                    <div class="tugasin-field-input">
                                        <label class="tugasin-toggle">
                                            <input type="checkbox" name="tugasin_fig_enable_backfill" value="1" <?php checked(get_option('tugasin_fig_enable_backfill', false)); ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <p class="tugasin-field-desc">
                                            <?php esc_html_e('When enabled, a background process runs every 30 minutes to generate featured images for up to 5 posts missing them.', 'tugasin'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Import Demo Data Section -->
                    <section id="section-import-demo" class="tugasin-settings-section">
                        <div class="tugasin-section-header">
                            <h2><span class="dashicons dashicons-download"></span>
                                <?php esc_html_e('Import Demo Data', 'tugasin'); ?></h2>
                            <p><?php esc_html_e('One-click setup to create demo pages, configure WordPress settings, and set up page mappings.', 'tugasin'); ?>
                            </p>
                        </div>

                        <?php
                        // Check for result message from transient
                        $demo_result = get_transient('tugasin_demo_result');
                        if ($demo_result) {
                            delete_transient('tugasin_demo_result');
                            $message_class = $demo_result['success'] ? 'success' : 'error';
                            ?>
                            <div class="tugasin-notice tugasin-notice-<?php echo esc_attr($message_class); ?>"
                                style="margin-bottom: 20px; padding: 12px 16px; border-radius: 8px; background: <?php echo $demo_result['success'] ? '#dcfce7' : '#fee2e2'; ?>; border-left: 4px solid <?php echo $demo_result['success'] ? '#16a34a' : '#dc2626'; ?>;">
                                <?php echo esc_html($demo_result['message']); ?>
                            </div>
                        <?php } ?>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-superhero"></span>
                                <?php esc_html_e('One Click Setup', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('This will automatically:', 'tugasin'); ?>
                            </p>
                            <ul style="margin: 16px 0 16px 24px; list-style: disc;">
                                <li><strong><?php esc_html_e('Create Pages', 'tugasin'); ?></strong> - Beranda, Blog, Layanan,
                                    Hubungi Kami, Tentang Kami, Syarat dan Ketentuan, Kebijakan Privasi</li>
                                <li><strong><?php esc_html_e('Create Sub-Pages', 'tugasin'); ?></strong> - Joki Tugas, Joki
                                    Skripsi, Joki Makalah, Cek Plagiarisme (<?php esc_html_e('under Layanan', 'tugasin'); ?>)
                                </li>
                                <li><strong><?php esc_html_e('Configure Reading Settings', 'tugasin'); ?></strong> -
                                    <?php esc_html_e('Set "Beranda" as homepage and "Blog" as posts page', 'tugasin'); ?>
                                </li>
                                <li><strong><?php esc_html_e('Set Page Mappings', 'tugasin'); ?></strong> -
                                    <?php esc_html_e('Configure Layanan and sub-service page mappings', 'tugasin'); ?>
                                </li>
                            </ul>
                            <p class="tugasin-field-desc" style="margin-bottom: 16px;">
                                <span class="dashicons dashicons-info-outline" style="color: #6b7280;"></span>
                                <?php esc_html_e('Existing pages will be skipped (safe to run multiple times).', 'tugasin'); ?>
                            </p>

                            <button type="submit" name="tugasin_one_click_setup"
                                formaction="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                                class="tugasin-btn tugasin-btn-primary" style="font-size: 16px; padding: 12px 24px;"
                                onclick="var actionInput = document.createElement('input'); actionInput.type='hidden'; actionInput.name='action'; actionInput.value='tugasin_one_click_setup'; this.form.appendChild(actionInput);">
                                <span class="dashicons dashicons-superhero" style="margin-right: 8px;"></span>
                                <?php esc_html_e('Run One Click Setup', 'tugasin'); ?>
                            </button>
                            <?php wp_nonce_field('tugasin_demo_action', 'tugasin_demo_nonce'); ?>
                        </div>
                    </section>

                    <!-- Import & Export Section -->
                    <section id="section-import-export" class="tugasin-settings-section">
                        <div class="tugasin-section-header">
                            <h2><span class="dashicons dashicons-database-export"></span>
                                <?php esc_html_e('Import & Export Settings', 'tugasin'); ?></h2>
                            <p><?php esc_html_e('Export your current settings to a JSON file or import settings from a previously exported file.', 'tugasin'); ?>
                            </p>
                        </div>

                        <div class="tugasin-section-card">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-upload"></span>
                                <?php esc_html_e('Export Settings', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('Download a JSON file containing all your TugasinWP theme settings. Use this to backup your settings or transfer them to another site.', 'tugasin'); ?>
                            </p>
                            <button type="button" id="tugasin-export-btn" class="tugasin-btn tugasin-btn-primary"
                                style="margin-top: 16px;">
                                <span class="dashicons dashicons-download" style="margin-right: 8px;"></span>
                                <?php esc_html_e('Export Settings', 'tugasin'); ?>
                            </button>
                        </div>

                        <div class="tugasin-section-card" style="margin-top: 24px;">
                            <h3 class="tugasin-section-title">
                                <span class="dashicons dashicons-download"></span>
                                <?php esc_html_e('Import Settings', 'tugasin'); ?>
                            </h3>
                            <p class="tugasin-section-desc">
                                <?php esc_html_e('Upload a previously exported JSON file to restore your TugasinWP theme settings. This will overwrite your current settings.', 'tugasin'); ?>
                            </p>
                            <div class="tugasin-field-row" style="margin-top: 16px;">
                                <input type="file" id="tugasin-import-file" accept=".json,application/json"
                                    style="display: none;">
                                <button type="button" id="tugasin-import-btn" class="tugasin-btn tugasin-btn-secondary">
                                    <span class="dashicons dashicons-upload" style="margin-right: 8px;"></span>
                                    <?php esc_html_e('Choose File & Import', 'tugasin'); ?>
                                </button>
                                <span id="tugasin-import-filename" style="margin-left: 12px; color: #6b7280;"></span>
                            </div>
                            <p class="tugasin-field-desc" style="margin-top: 12px;">
                                <span class="dashicons dashicons-warning" style="color: #f59e0b;"></span>
                                <?php esc_html_e('Warning: Importing settings will overwrite all current theme settings. Make sure to export your current settings first as a backup.', 'tugasin'); ?>
                            </p>
                            <div id="tugasin-import-result" style="margin-top: 16px; display: none;"></div>
                        </div>
                    </section>

                    <!-- About Section -->
                    <section id="section-about" class="tugasin-settings-section">
                        <div class="tugasin-about-hero">
                            <div class="hero-icon">
                                <span class="dashicons dashicons-bolt"></span>
                            </div>
                            <h2>TugasinWP Theme</h2>
                            <p class="tagline">
                                <?php esc_html_e('A modern, soft, and playful academic assistance theme for Indonesian students.', 'tugasin'); ?>
                            </p>
                        </div>

                        <div class="tugasin-about-info">
                            <div class="tugasin-about-card">
                                <div class="card-label"><?php esc_html_e('Version', 'tugasin'); ?></div>
                                <div class="card-value"><?php echo esc_html(TUGASIN_VERSION); ?></div>
                            </div>
                            <div class="tugasin-about-card">
                                <div class="card-label"><?php esc_html_e('Author', 'tugasin'); ?></div>
                                <div class="card-value">Tugasin Team</div>
                            </div>
                            <div class="tugasin-about-card">
                                <div class="card-label"><?php esc_html_e('Website', 'tugasin'); ?></div>
                                <div class="card-value"><a href="https://tugasin.com" target="_blank">tugasin.com</a></div>
                            </div>
                            <div class="tugasin-about-card">
                                <div class="card-label"><?php esc_html_e('Support', 'tugasin'); ?></div>
                                <div class="card-value"><a href="https://tugasin.com/support"
                                        target="_blank"><?php esc_html_e('Get Help', 'tugasin'); ?></a></div>
                            </div>
                        </div>
                    </section>

                </main>

                <!-- Footer with Submit -->
                <div class="tugasin-settings-footer">
                    <span
                        class="footer-note"><?php esc_html_e('Changes will take effect immediately after saving.', 'tugasin'); ?></span>
                    <button type="submit" class="tugasin-submit-btn">
                        <span class="dashicons dashicons-saved"></span>
                        <?php esc_html_e('Save Settings', 'tugasin'); ?>
                    </button>
                </div>
            </form>
        </div>

        <script>
            jQuery(document).ready(function ($) {
                // Get saved tab from localStorage or URL hash or hidden field
                var savedTab = localStorage.getItem('tugasin_settings_tab') || '';
                var hashTab = window.location.hash ? window.location.hash.substring(1) : '';
                var hiddenTab = $('#tugasin_active_tab').val();
                var activeTab = hashTab || savedTab || hiddenTab || 'general';

                // Function to switch tabs
                function switchToTab(tabId) {
                    if (!tabId) return;
                    var $navItem = $('[data-section="' + tabId + '"]');
                    if ($navItem.length) {
                        $('.tugasin-nav-item').removeClass('active');
                        $navItem.addClass('active');
                        $('.tugasin-settings-section').removeClass('active');
                        $('#section-' + tabId).addClass('active');
                        $('#tugasin_active_tab').val(tabId);
                        localStorage.setItem('tugasin_settings_tab', tabId);
                    }
                }

                // Switch to active tab on load
                switchToTab(activeTab);

                // Sidebar navigation click handler
                $('.tugasin-nav-item').on('click', function () {
                    var sectionId = $(this).data('section');
                    switchToTab(sectionId);

                    // Update URL hash for bookmarking
                    if (history.replaceState) {
                        history.replaceState(null, null, '#' + sectionId);
                    }
                });

                // Before form submit, update hidden field with current active tab
                $('#tugasin-settings-form').on('submit', function () {
                    var currentTab = $('.tugasin-nav-item.active').data('section') || 'general';
                    $('#tugasin_active_tab').val(currentTab);
                    localStorage.setItem('tugasin_settings_tab', currentTab);
                });

                // Color picker
                $('.tugasin-color-picker').wpColorPicker();

                // Media uploader
                $('.tugasin-upload-btn').on('click', function (e) {
                    e.preventDefault();
                    var $btn = $(this);
                    var target = $btn.data('target');
                    var $field = $btn.closest('.tugasin-media-field');

                    var frame = wp.media({
                        title: '<?php esc_html_e('Select Image', 'tugasin'); ?>',
                        button: { text: '<?php esc_html_e('Use Image', 'tugasin'); ?>' },
                        multiple: false
                    });

                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $('#' + target).val(attachment.id);
                        $field.find('.tugasin-media-preview-box')
                            .addClass('has-image')
                            .html('<img src="' + attachment.url + '" alt="">');
                        $field.find('.tugasin-remove-btn').show();
                    });

                    frame.open();
                });

                // Remove image
                $('.tugasin-remove-btn').on('click', function (e) {
                    e.preventDefault();
                    var $btn = $(this);
                    var target = $btn.data('target');
                    var $field = $btn.closest('.tugasin-media-field');
                    var placeholderIcon = 'dashicons-format-image';

                    $('#' + target).val('');
                    $field.find('.tugasin-media-preview-box')
                        .removeClass('has-image')
                        .html('<span class="placeholder-icon dashicons ' + placeholderIcon + '"></span>');
                    $btn.hide();
                });

                // ============================================
                // TESTIMONIAL SETTINGS
                // ============================================

                // Toggle custom testimonials visibility when checkbox changes
                $('input[name$="_use_default"]').on('change', function () {
                    var $checkbox = $(this);
                    var $wrapper = $checkbox.closest('.tugasin-schema-service-body').find('.tugasin-custom-testimonials-wrapper');

                    if ($checkbox.is(':checked')) {
                        $wrapper.slideUp(200);
                    } else {
                        $wrapper.slideDown(200);
                    }
                });

                // Testimonial image upload button
                $('.tugasin-upload-image-btn').on('click', function (e) {
                    e.preventDefault();
                    var $btn = $(this);
                    var $field = $btn.closest('.tugasin-testimonial-image-field');
                    var $input = $field.find('.tugasin-testimonial-image-input');
                    var $preview = $field.find('.tugasin-testimonial-preview');

                    var frame = wp.media({
                        title: '<?php esc_html_e('Select Testimonial Photo', 'tugasin'); ?>',
                        button: { text: '<?php esc_html_e('Use This Photo', 'tugasin'); ?>' },
                        multiple: false,
                        library: { type: 'image' }
                    });

                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $input.val(attachment.url);
                        $preview.html('<img src="' + attachment.url + '" alt="">');
                    });

                    frame.open();
                });

                // Live preview for testimonial image URL input
                $('.tugasin-testimonial-image-input').on('change blur', function () {
                    var $input = $(this);
                    var url = $input.val();
                    var $preview = $input.closest('.tugasin-testimonial-image-field').find('.tugasin-testimonial-preview');

                    if (url) {
                        $preview.html('<img src="' + url + '" alt="">');
                    } else {
                        $preview.html('<span class="dashicons dashicons-admin-users"></span>');
                    }
                });

                // ============================================
                // OPTIMIZATION TOGGLE FIELDS (Phase 28)
                // ============================================

                // Function to toggle conditional fields visibility
                function toggleOptFields($checkbox) {
                    var inputName = $checkbox.attr('name');
                    var $fields = $('[data-depends="' + inputName + '"]');

                    if ($checkbox.is(':checked')) {
                        $fields.slideDown(200).attr('data-hidden', 'false');
                    } else {
                        $fields.slideUp(200).attr('data-hidden', 'true');
                    }
                }

                // Initialize on page load
                $('.tugasin-toggle input[type="checkbox"]').each(function () {
                    var $checkbox = $(this);
                    var inputName = $checkbox.attr('name');
                    var $fields = $('[data-depends="' + inputName + '"]');

                    if (!$checkbox.is(':checked')) {
                        $fields.hide().attr('data-hidden', 'true');
                    }
                });

                // Toggle on change
                $('.tugasin-toggle input[type="checkbox"]').on('change', function () {
                    toggleOptFields($(this));
                });

                // ============================================
                // IMPORT & EXPORT SETTINGS (Phase 35)
                // ============================================

                // Export settings
                $('#tugasin-export-btn').on('click', function (e) {
                    e.preventDefault();
                    var $btn = $(this);
                    $btn.prop('disabled', true).html('<span class="dashicons dashicons-update-alt spin" style="margin-right: 8px;"></span><?php echo esc_js(__('Exporting...', 'tugasin')); ?>');

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'tugasin_export_settings',
                            nonce: '<?php echo wp_create_nonce('tugasin_export_settings'); ?>'
                        },
                        success: function (response) {
                            if (response.success) {
                                // Create download
                                var blob = new Blob([JSON.stringify(response.data.settings, null, 2)], { type: 'application/json' });
                                var url = window.URL.createObjectURL(blob);
                                var a = document.createElement('a');
                                a.href = url;
                                a.download = response.data.filename;
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);
                                document.body.removeChild(a);
                            } else {
                                alert(response.data || 'Export failed');
                            }
                        },
                        error: function () {
                            alert('Export failed. Please try again.');
                        },
                        complete: function () {
                            $btn.prop('disabled', false).html('<span class="dashicons dashicons-download" style="margin-right: 8px;"></span><?php echo esc_js(__('Export Settings', 'tugasin')); ?>');
                        }
                    });
                });

                // Import settings - trigger file input
                $('#tugasin-import-btn').on('click', function (e) {
                    e.preventDefault();
                    $('#tugasin-import-file').click();
                });

                // Import settings - handle file selection
                $('#tugasin-import-file').on('change', function (e) {
                    var file = this.files[0];
                    if (!file) return;

                    $('#tugasin-import-filename').text(file.name);

                    if (!confirm('<?php echo esc_js(__('Are you sure you want to import these settings? This will overwrite all current theme settings.', 'tugasin')); ?>')) {
                        $(this).val('');
                        $('#tugasin-import-filename').text('');
                        return;
                    }

                    var $btn = $('#tugasin-import-btn');
                    var $result = $('#tugasin-import-result');

                    $btn.prop('disabled', true).html('<span class="dashicons dashicons-update-alt spin" style="margin-right: 8px;"></span><?php echo esc_js(__('Importing...', 'tugasin')); ?>');
                    $result.hide();

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        try {
                            var settings = JSON.parse(e.target.result);

                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'tugasin_import_settings',
                                    nonce: '<?php echo wp_create_nonce('tugasin_import_settings'); ?>',
                                    settings: JSON.stringify(settings)
                                },
                                success: function (response) {
                                    if (response.success) {
                                        $result.html('<div style="padding: 12px 16px; border-radius: 8px; background: #dcfce7; border-left: 4px solid #16a34a;">' + response.data + '</div>').slideDown();
                                        setTimeout(function () {
                                            location.reload();
                                        }, 1500);
                                    } else {
                                        $result.html('<div style="padding: 12px 16px; border-radius: 8px; background: #fee2e2; border-left: 4px solid #dc2626;">' + (response.data || 'Import failed') + '</div>').slideDown();
                                    }
                                },
                                error: function () {
                                    $result.html('<div style="padding: 12px 16px; border-radius: 8px; background: #fee2e2; border-left: 4px solid #dc2626;">Import failed. Please try again.</div>').slideDown();
                                },
                                complete: function () {
                                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-upload" style="margin-right: 8px;"></span><?php echo esc_js(__('Choose File & Import', 'tugasin')); ?>');
                                    $('#tugasin-import-file').val('');
                                    $('#tugasin-import-filename').text('');
                                }
                            });
                        } catch (err) {
                            $result.html('<div style="padding: 12px 16px; border-radius: 8px; background: #fee2e2; border-left: 4px solid #dc2626;">Invalid JSON file. Please upload a valid settings export file.</div>').slideDown();
                            $btn.prop('disabled', false).html('<span class="dashicons dashicons-upload" style="margin-right: 8px;"></span><?php echo esc_js(__('Choose File & Import', 'tugasin')); ?>');
                            $('#tugasin-import-file').val('');
                            $('#tugasin-import-filename').text('');
                        }
                    };
                    reader.readAsText(file);
                });
            });
        </script>
        <?php
    }

    /**
     * Create demo pages for the theme
     * 
     * @return array Result with success status and message
     */
    private function create_demo_pages()
    {
        $pages_created = 0;
        $pages_skipped = 0;

        // Define pages to create
        $pages = array(
            'beranda' => array(
                'title' => 'Beranda',
                'template' => 'front-page.php',
            ),
            'blog' => array(
                'title' => 'Blog',
                'template' => '',
            ),
            'layanan' => array(
                'title' => 'Layanan',
                'template' => 'page-layanan.php',
            ),
            'hubungi-kami' => array(
                'title' => 'Hubungi Kami',
                'template' => '',
            ),
            'tentang-kami' => array(
                'title' => 'Tentang Kami',
                'template' => '',
            ),
            'syarat-dan-ketentuan' => array(
                'title' => 'Syarat dan Ketentuan',
                'template' => '',
            ),
            'kebijakan-privasi' => array(
                'title' => 'Kebijakan Privasi',
                'template' => '',
            ),
        );

        // Sub-pages for Layanan
        $layanan_subpages = array(
            'joki-tugas' => array(
                'title' => 'Joki Tugas',
                'template' => 'page-joki-tugas.php',
            ),
            'joki-skripsi' => array(
                'title' => 'Joki Skripsi',
                'template' => 'page-joki-skripsi.php',
            ),
            'joki-makalah' => array(
                'title' => 'Joki Makalah',
                'template' => 'page-joki-makalah.php',
            ),
            'cek-plagiarisme' => array(
                'title' => 'Cek Plagiarisme',
                'template' => 'page-cek-plagiarism.php',
            ),
        );

        $layanan_id = 0;

        // Create main pages
        foreach ($pages as $slug => $data) {
            $existing = get_page_by_path($slug);

            if ($existing) {
                $pages_skipped++;
                if ($slug === 'layanan') {
                    $layanan_id = $existing->ID;
                }
                continue;
            }

            $page_data = array(
                'post_title' => $data['title'],
                'post_name' => $slug,
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '',
            );

            $page_id = wp_insert_post($page_data);

            if (!is_wp_error($page_id)) {
                $pages_created++;

                // Set page template if specified
                if (!empty($data['template'])) {
                    update_post_meta($page_id, '_wp_page_template', $data['template']);
                }

                if ($slug === 'layanan') {
                    $layanan_id = $page_id;
                }
            }
        }

        // Create Layanan sub-pages
        if ($layanan_id) {
            foreach ($layanan_subpages as $slug => $data) {
                $existing = get_page_by_path('layanan/' . $slug);

                if ($existing) {
                    $pages_skipped++;
                    continue;
                }

                $page_data = array(
                    'post_title' => $data['title'],
                    'post_name' => $slug,
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_parent' => $layanan_id,
                    'post_content' => '',
                );

                $page_id = wp_insert_post($page_data);

                if (!is_wp_error($page_id)) {
                    $pages_created++;

                    if (!empty($data['template'])) {
                        update_post_meta($page_id, '_wp_page_template', $data['template']);
                    }
                }
            }
        }

        if ($pages_created > 0) {
            return array(
                'success' => true,
                'message' => sprintf(
                    __('%d pages created, %d pages already existed.', 'tugasin'),
                    $pages_created,
                    $pages_skipped
                ),
            );
        } else {
            return array(
                'success' => true,
                'message' => __('All pages already exist. No new pages were created.', 'tugasin'),
            );
        }
    }

    /**
     * Configure WordPress reading settings
     * 
     * @return array Result with success status and message
     */
    private function configure_reading_settings()
    {
        $beranda = get_page_by_path('beranda');
        $blog = get_page_by_path('blog');

        if (!$beranda) {
            return array(
                'success' => false,
                'message' => __('Error: "Beranda" page not found. Please create demo pages first.', 'tugasin'),
            );
        }

        if (!$blog) {
            return array(
                'success' => false,
                'message' => __('Error: "Blog" page not found. Please create demo pages first.', 'tugasin'),
            );
        }

        // Set reading settings
        update_option('show_on_front', 'page');
        update_option('page_on_front', $beranda->ID);
        update_option('page_for_posts', $blog->ID);

        return array(
            'success' => true,
            'message' => __('Reading settings configured successfully! Homepage is now "Beranda" and Posts page is "Blog".', 'tugasin'),
        );
    }

    /**
     * Handle export settings AJAX request
     */
    public function handle_export_settings()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tugasin_export_settings')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'tugasin')));
        }

        // Check user capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to export settings.', 'tugasin')));
        }

        // Define all known settings with defaults to ensure complete export
        $known_settings = array(
            // General
            'tugasin_wa_number' => '6281234567890',
            'tugasin_wa_template' => 'Halo, saya ingin konsultasi tentang bantuan tugas.',
            'tugasin_cta_text' => 'Konsultasi Sekarang!',
            // WhatsApp Widget
            'tugasin_wa_widget_enabled' => true,
            'tugasin_wa_widget_cta' => 'Halo! Kamu butuh bantuan? Tim Tugasin siap bantu kamu.',
            'tugasin_wa_widget_delay' => 3,
            // Branding
            'tugasin_logo' => 0,
            'tugasin_site_icon' => 0,
            'tugasin_color_primary' => '#064e3b',
            // Page Mappings
            'tugasin_page_layanan' => 0,
            'tugasin_page_joki_skripsi' => 0,
            'tugasin_page_joki_makalah' => 0,
            'tugasin_page_joki_tugas' => 0,
            'tugasin_page_cek_plagiarism' => 0,
            // Schema
            'tugasin_schema_enabled' => true,
            'tugasin_schema_org_name' => '',
            'tugasin_schema_org_logo' => 0,
            'tugasin_schema_org_phone' => '',
            // FIG Settings
            'tugasin_fig_enabled' => false,
            'tugasin_fig_enable_backfill' => false,
            'tugasin_fig_pixabay_key' => '',
            'tugasin_fig_enable_translation' => true,
            'tugasin_fig_google_translate_key' => '',
            'tugasin_fig_search_source' => 'title',
            'tugasin_fig_translation_provider' => 'google',
            'tugasin_fig_aws_access_key' => '',
            'tugasin_fig_aws_secret_key' => '',
            'tugasin_fig_aws_region' => 'us-east-1',
            'tugasin_fig_logo_image' => 0,
            'tugasin_fig_logo_size' => 56,
            'tugasin_fig_gradient_color' => '#ffffff',
            'tugasin_fig_text_color' => '#1e3a5f',
            'tugasin_fig_fallback_query' => 'students education university',
            'tugasin_fig_auto_alt' => true,
            'tugasin_fig_used_images' => array(),
            // Optimization
            'tugasin_opt_defer_enabled' => true,
            'tugasin_opt_defer_scripts' => "tugasin-main\ntugasin-archive-filter",
            'tugasin_opt_defer_exclude' => '',
            'tugasin_opt_preconnect_enabled' => true,
            'tugasin_opt_preconnect_urls' => 'https://cdnjs.cloudflare.com',
            'tugasin_opt_webp_enabled' => true,
            'tugasin_opt_lazyload_enabled' => true,
            // Data
            'tugasin_hero_tutors' => array(),
            'tugasin_testimonials_default' => array(),
        );

        // Start with defaults
        $settings = array();
        foreach ($known_settings as $key => $default) {
            $settings[$key] = get_option($key, $default);
        }

        // Also get any other tugasin_ options from database that might not be in known list
        global $wpdb;
        $db_options = $wpdb->get_results(
            "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'tugasin_%'",
            ARRAY_A
        );

        foreach ($db_options as $option) {
            $value = maybe_unserialize($option['option_value']);
            $settings[$option['option_name']] = $value;
        }

        // Sort by key for cleaner output
        ksort($settings);

        // Generate filename with date
        $filename = 'tugasinwp-settings-' . date('Y-m-d') . '.json';

        wp_send_json_success(array(
            'settings' => $settings,
            'filename' => $filename,
            'exported_at' => current_time('mysql'),
            'version' => TUGASIN_VERSION,
        ));
    }

    /**
     * Handle import settings AJAX request
     */
    public function handle_import_settings()
    {
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'tugasin_import_settings')) {
            wp_send_json_error(array('message' => __('Security check failed.', 'tugasin')));
        }

        // Check user capability
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to import settings.', 'tugasin')));
        }

        // Get and validate settings data
        if (!isset($_POST['settings']) || empty($_POST['settings'])) {
            wp_send_json_error(array('message' => __('No settings data provided.', 'tugasin')));
        }

        $settings = json_decode(stripslashes($_POST['settings']), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(array('message' => __('Invalid JSON data.', 'tugasin')));
        }

        if (!is_array($settings)) {
            wp_send_json_error(array('message' => __('Settings must be an array.', 'tugasin')));
        }

        // Update each individual option
        $updated = 0;
        foreach ($settings as $option_name => $option_value) {
            // Only update options that start with tugasin_
            if (strpos($option_name, 'tugasin_') === 0) {
                update_option($option_name, $option_value);
                $updated++;
            }
        }

        if ($updated > 0) {
            wp_send_json_success(array('message' => sprintf(__('Settings imported successfully! (%d options updated)', 'tugasin'), $updated)));
        } else {
            wp_send_json_error(array('message' => __('No valid settings found to import.', 'tugasin')));
        }
    }
}


