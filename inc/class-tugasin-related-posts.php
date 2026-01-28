<?php
/**
 * Related Posts Handler
 *
 * Handles related posts logic for both inline (within content)
 * and bottom (after content) display.
 *
 * @package TugasinWP
 * @since 2.18.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Related_Posts
{

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Cache for related posts to avoid duplicate queries
     */
    private $related_posts_cache = array();

    /**
     * Get singleton instance
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        // Hook into the_content to inject inline related posts
        add_filter('the_content', array($this, 'inject_inline_related'), 20);
    }

    /**
     * Get related posts for a given post
     *
     * @param int $post_id Post ID
     * @param int $count Number of posts to return
     * @param array $exclude Post IDs to exclude
     * @return WP_Query|false
     */
    public function get_related_posts($post_id, $count = 3, $exclude = array())
    {
        // Check cache first
        $cache_key = $post_id . '_' . $count . '_' . implode('_', $exclude);
        if (isset($this->related_posts_cache[$cache_key])) {
            return $this->related_posts_cache[$cache_key];
        }

        // Get categories of current post
        $categories = get_the_category($post_id);
        if (empty($categories)) {
            return false;
        }

        // Get category IDs
        $category_ids = array_map(function ($cat) {
            return $cat->term_id;
        }, $categories);

        // Ensure current post is excluded
        if (!in_array($post_id, $exclude)) {
            $exclude[] = $post_id;
        }

        // Build query
        $args = array(
            'category__in' => $category_ids,
            'post__not_in' => $exclude,
            'posts_per_page' => $count,
            'post_type' => 'post',
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
            'orderby' => 'rand',
        );

        $query = new WP_Query($args);

        // If not enough posts in same category, try getting any recent posts
        if ($query->post_count < $count) {
            $already_found = array();
            if ($query->have_posts()) {
                while ($query->have_posts()) {
                    $query->the_post();
                    $already_found[] = get_the_ID();
                }
                wp_reset_postdata();
            }

            $needed = $count - count($already_found);
            if ($needed > 0) {
                $fallback_args = array(
                    'post__not_in' => array_merge($exclude, $already_found),
                    'posts_per_page' => $needed,
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'ignore_sticky_posts' => true,
                    'orderby' => 'date',
                    'order' => 'DESC',
                );
                $fallback_query = new WP_Query($fallback_args);

                // We'll handle the merging in the display functions
                // For now, just cache the original query
            }
        }

        // Cache the result
        $this->related_posts_cache[$cache_key] = $query;

        return $query;
    }

    /**
     * Check if inline related posts should be shown
     *
     * @return bool
     */
    private function should_show_inline()
    {
        // Only on single posts
        if (!is_single() || !is_singular('post')) {
            return false;
        }

        // Check settings (if enabled)
        $enabled = get_option('tugasin_related_inline_enabled', true);
        if (!$enabled) {
            return false;
        }

        return true;
    }

    /**
     * Inject inline related posts into content
     *
     * @param string $content The post content
     * @return string Modified content
     */
    public function inject_inline_related($content)
    {
        // Only on single post pages and main query
        if (!$this->should_show_inline() || !is_main_query()) {
            return $content;
        }

        // Prevent nested filtering (already processing)
        static $is_filtering = false;
        if ($is_filtering) {
            return $content;
        }
        $is_filtering = true;

        // Get position setting (default: after 3rd paragraph)
        $position = absint(get_option('tugasin_related_inline_position', 3));
        if ($position < 2) {
            $position = 3;
        }

        // Count paragraphs in content
        $paragraphs = preg_split('/(<\/p>)/i', $content, -1, PREG_SPLIT_DELIM_CAPTURE);
        $paragraph_count = preg_match_all('/<\/p>/i', $content);

        // Only inject if we have enough paragraphs (at least position + 1)
        if ($paragraph_count < ($position + 1)) {
            $is_filtering = false;
            return $content;
        }

        // Get related posts for inline (2 posts, exclude current)
        $post_id = get_the_ID();
        $inline_count = absint(get_option('tugasin_related_inline_count', 2));
        if ($inline_count < 1) {
            $inline_count = 2;
        }
        if ($inline_count > 4) {
            $inline_count = 4;
        }

        $related = $this->get_related_posts($post_id, $inline_count);

        if (!$related || !$related->have_posts()) {
            $is_filtering = false;
            return $content;
        }

        // Build inline related HTML
        $inline_html = $this->build_inline_html($related);

        // Insert after nth paragraph
        // Each paragraph ends with </p>, so we count </p> occurrences
        $new_content = '';
        $p_count = 0;
        $inserted = false;

        for ($i = 0; $i < count($paragraphs); $i++) {
            $new_content .= $paragraphs[$i];

            // Check if this was a closing </p> tag
            if (strtolower(trim($paragraphs[$i])) === '</p>') {
                $p_count++;

                // Insert after the target paragraph
                if ($p_count === $position && !$inserted) {
                    $new_content .= "\n" . $inline_html . "\n";
                    $inserted = true;
                }
            }
        }

        wp_reset_postdata();
        $is_filtering = false;

        return $new_content;
    }

    /**
     * Build inline related posts HTML
     *
     * @param WP_Query $related_query
     * @return string HTML
     */
    private function build_inline_html($related_query)
    {
        ob_start();
        ?>
        <aside class="inline-related-box" role="complementary" aria-label="<?php esc_attr_e('Artikel terkait', 'tugasin'); ?>">
            <div class="inline-related-title">
                <i class="fas fa-bookmark"></i>
                <span>
                    <?php esc_html_e('Baca Juga:', 'tugasin'); ?>
                </span>
            </div>
            <ul class="inline-related-list">
                <?php
                while ($related_query->have_posts()):
                    $related_query->the_post();
                    ?>
                    <li>
                        <a href="<?php the_permalink(); ?>" class="inline-related-link">
                            <i class="fas fa-angle-right"></i>
                            <span>
                                <?php the_title(); ?>
                            </span>
                        </a>
                    </li>
                    <?php
                endwhile;
                ?>
            </ul>
        </aside>
        <?php
        return ob_get_clean();
    }

    /**
     * Get bottom related posts section HTML
     *
     * @param int $post_id Current post ID
     * @param int $count Number of posts to show
     * @return string HTML output
     */
    public function get_bottom_related_html($post_id, $count = 3)
    {
        // Check if bottom related is enabled
        $enabled = get_option('tugasin_related_bottom_enabled', true);
        if (!$enabled) {
            return '';
        }

        // Get inline posts to exclude them from bottom
        $inline_count = absint(get_option('tugasin_related_inline_count', 2));
        $inline_related = $this->get_related_posts($post_id, $inline_count);

        $exclude = array($post_id);
        if ($inline_related && $inline_related->have_posts()) {
            while ($inline_related->have_posts()) {
                $inline_related->the_post();
                $exclude[] = get_the_ID();
            }
            wp_reset_postdata();
        }

        // Get bottom related posts (different from inline ones)
        $categories = get_the_category($post_id);
        if (empty($categories)) {
            return '';
        }

        $category_ids = array_map(function ($cat) {
            return $cat->term_id;
        }, $categories);

        $args = array(
            'category__in' => $category_ids,
            'post__not_in' => $exclude,
            'posts_per_page' => $count,
            'post_type' => 'post',
            'post_status' => 'publish',
            'ignore_sticky_posts' => true,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $bottom_related = new WP_Query($args);

        if (!$bottom_related->have_posts()) {
            wp_reset_postdata();
            return '';
        }

        ob_start();
        ?>
        <section class="related-posts-section" aria-label="<?php esc_attr_e('Artikel Terkait', 'tugasin'); ?>">
            <strong class="related-posts-title">
                <i class="fas fa-newspaper"></i>
                <?php esc_html_e('Artikel Terkait', 'tugasin'); ?>
            </strong>
            <div class="related-posts-grid">
                <?php
                while ($bottom_related->have_posts()):
                    $bottom_related->the_post();
                    ?>
                    <a href="<?php the_permalink(); ?>" class="related-post-card">
                        <?php if (has_post_thumbnail()): ?>
                            <div class="related-post-image">
                                <?php the_post_thumbnail('tugasin-card', array('loading' => 'lazy')); ?>
                            </div>
                        <?php else: ?>
                            <div class="related-post-image related-post-image-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                        <?php endif; ?>
                        <div class="related-post-content">
                            <strong class="related-post-title">
                                <?php the_title(); ?>
                            </strong>
                            <span class="related-post-date">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo esc_html(get_the_date()); ?>
                            </span>
                        </div>
                    </a>
                    <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}

/**
 * Initialize the Related Posts class
 */
function tugasin_related_posts_init()
{
    return Tugasin_Related_Posts::get_instance();
}
add_action('init', 'tugasin_related_posts_init');

/**
 * Helper function to get bottom related posts HTML
 *
 * @param int $post_id Optional. Post ID. Default current post.
 * @param int $count Optional. Number of posts. Default 3.
 * @return string HTML
 */
function tugasin_get_bottom_related($post_id = null, $count = 3)
{
    if (null === $post_id) {
        $post_id = get_the_ID();
    }
    $instance = Tugasin_Related_Posts::get_instance();
    return $instance->get_bottom_related_html($post_id, $count);
}
