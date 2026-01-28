<?php
/**
 * Theme Setup Class
 *
 * Handles theme support, asset enqueuing, and menu registration.
 *
 * @package TugasinWP
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tugasin_Setup {

    /**
     * Constructor
     */
    public function __construct() {
        // Call theme setup methods DIRECTLY since this class is instantiated
        // from within after_setup_theme hook (via tugasin_init)
        // Adding more after_setup_theme hooks here would be too late
        $this->theme_support();
        $this->register_menus();
        
        // Other hooks fire on different actions, so they work normally
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_conflicting_styles' ), 999 );
        add_action( 'widgets_init', array( $this, 'register_sidebars' ) );
        add_filter( 'body_class', array( $this, 'body_classes' ) );
        // Note: Title handling moved to inc/seo-functions.php via pre_get_document_title filter
        
        // Performance optimizations (Phase 27)
        add_action( 'wp_head', array( $this, 'inline_critical_css' ), 1 );
        add_filter( 'script_loader_tag', array( $this, 'add_defer_attribute' ), 10, 2 );
        add_filter( 'upload_mimes', array( $this, 'allow_webp_uploads' ) );
    }

    /**
     * Register theme support
     */
    public function theme_support() {
        // Make theme available for translation
        load_theme_textdomain( 'tugasin', TUGASIN_DIR . '/languages' );

        // Add default posts and comments RSS feed links to head
        add_theme_support( 'automatic-feed-links' );

        // Let WordPress manage the document title
        add_theme_support( 'title-tag' );

        // Enable support for Post Thumbnails
        add_theme_support( 'post-thumbnails' );
        set_post_thumbnail_size( 1200, 630, true );

        // Add custom image sizes
        add_image_size( 'tugasin-card', 400, 250, true );
        add_image_size( 'tugasin-hero', 1920, 800, true );

        // Switch default core markup to output valid HTML5
        add_theme_support( 'html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'style',
            'script',
        ) );

        // Add support for custom logo
        add_theme_support( 'custom-logo', array(
            'height'      => 80,
            'width'       => 200,
            'flex-height' => true,
            'flex-width'  => true,
        ) );

        // Add support for custom background
        add_theme_support( 'custom-background', array(
            'default-color' => 'ffffff',
        ) );

        // Add support for responsive embeds
        add_theme_support( 'responsive-embeds' );

        // Add support for full and wide align images
        add_theme_support( 'align-wide' );

        // Add support for editor styles
        add_theme_support( 'editor-styles' );

        // Add support for block styles
        add_theme_support( 'wp-block-styles' );

        // Declare WooCommerce support (future-proofing)
        add_theme_support( 'woocommerce' );
    }

    /**
     * Register navigation menus
     */
    public function register_menus() {
        register_nav_menus( array(
            'primary'   => esc_html__( 'Primary Menu', 'tugasin' ),
            'mobile'    => esc_html__( 'Mobile Menu', 'tugasin' ),
            'footer'    => esc_html__( 'Footer Menu', 'tugasin' ),
        ) );
    }

    /**
     * Register widget areas
     */
    public function register_sidebars() {
        register_sidebar( array(
            'name'          => esc_html__( 'Blog Sidebar', 'tugasin' ),
            'id'            => 'sidebar-blog',
            'description'   => esc_html__( 'Add widgets here for blog sidebar.', 'tugasin' ),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ) );

        register_sidebar( array(
            'name'          => esc_html__( 'Footer Column 1', 'tugasin' ),
            'id'            => 'footer-1',
            'description'   => esc_html__( 'Footer widget area 1.', 'tugasin' ),
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<strong class="footer-widget-title">',
            'after_title'   => '</strong>',
        ) );

        register_sidebar( array(
            'name'          => esc_html__( 'Footer Column 2', 'tugasin' ),
            'id'            => 'footer-2',
            'description'   => esc_html__( 'Footer widget area 2.', 'tugasin' ),
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<strong class="footer-widget-title">',
            'after_title'   => '</strong>',
        ) );

        register_sidebar( array(
            'name'          => esc_html__( 'Footer Column 3', 'tugasin' ),
            'id'            => 'footer-3',
            'description'   => esc_html__( 'Footer widget area 3.', 'tugasin' ),
            'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<strong class="footer-widget-title">',
            'after_title'   => '</strong>',
        ) );
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_assets() {
        // Local Fonts (Plus Jakarta Sans) - v2.8.0: Self-hosted for performance & GDPR
        wp_enqueue_style(
            'tugasin-fonts',
            TUGASIN_URI . '/assets/css/fonts.css',
            array(),
            TUGASIN_VERSION
        );

        // Font Awesome 6.4 (will be dequeued and re-registered in dequeue_conflicting_styles)
        wp_enqueue_style(
            'tugasin-font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            array(),
            '6.4.0'
        );

        // Main bundle CSS (Phase 27: Performance - replaces style.css with 19 @imports)
        // Critical CSS is inlined via inline_critical_css(), this loads non-critical styles
        wp_enqueue_style(
            'tugasin-main-bundle',
            TUGASIN_URI . '/assets/css/main-bundle.css',
            array( 'tugasin-fonts', 'tugasin-font-awesome' ),
            TUGASIN_VERSION
        );

        // Main JavaScript
        wp_enqueue_script(
            'tugasin-main',
            TUGASIN_URI . '/assets/js/main.js',
            array(),
            TUGASIN_VERSION,
            true
        );

        // Localize script with data
        wp_localize_script( 'tugasin-main', 'tugasinData', array(
            'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
            'nonce'       => wp_create_nonce( 'tugasin_nonce' ),
            'whatsappUrl' => tugasin_get_whatsapp_url(),
        ) );

        // Archive filter script (only on archive pages)
        if ( is_home() || is_archive() || is_tax() ) {
            wp_enqueue_script(
                'tugasin-archive-filter',
                TUGASIN_URI . '/assets/js/archive-filter.js',
                array(),
                TUGASIN_VERSION,
                true
            );

            wp_localize_script( 'tugasin-archive-filter', 'tugasinAjax', array(
                'ajax_url'    => admin_url( 'admin-ajax.php' ),
                'nonce'       => wp_create_nonce( 'tugasin_ajax_nonce' ),
                // i18n strings for JavaScript
                'i18n'        => array(
                    'noResults' => esc_html__( 'Tidak ada hasil ditemukan.', 'tugasin' ),
                    'loadError' => esc_html__( 'Terjadi kesalahan saat memuat data.', 'tugasin' ),
                    'loading'   => esc_html__( 'Memuat...', 'tugasin' ),
                ),
            ) );
        }
    }

    /**
     * Add custom body classes
     *
     * @param array $classes Body classes.
     * @return array
     */
    public function body_classes( $classes ) {
        // Add hfeed class to non-singular pages
        if ( ! is_singular() ) {
            $classes[] = 'hfeed';
        }

        // Add no-sidebar class when there's no sidebar
        if ( ! is_active_sidebar( 'sidebar-blog' ) ) {
            $classes[] = 'no-sidebar';
        }

        return $classes;
    }

    /**
     * Dequeue conflicting styles from plugins (e.g., Elementor's Font Awesome 4.7)
     */
    public function dequeue_conflicting_styles() {
        // Dequeue Elementor's Font Awesome 4.7 to prevent conflict with our FA 6.4
        wp_dequeue_style( 'font-awesome' );
        wp_deregister_style( 'font-awesome' );
        wp_dequeue_style( 'elementor-icons-fa-solid' );
        wp_dequeue_style( 'elementor-icons-fa-regular' );
        wp_dequeue_style( 'elementor-icons-fa-brands' );
        
        // Re-register Font Awesome 6.4 with our handle
        wp_enqueue_style(
            'tugasin-font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css',
            array(),
            '6.4.0'
        );
    }

    /**
     * Filter document title parts to ensure proper page/post titles
     *
     * @param array $title_parts Title parts.
     * @return array
     */
    public function filter_document_title( $title_parts ) {
        // For singular pages/posts, ensure we use the actual post title
        if ( is_singular() ) {
            $title_parts['title'] = get_the_title();
        }
        
        // For archives, use the archive title
        if ( is_post_type_archive( 'major' ) ) {
            $title_parts['title'] = __( 'Kamus Jurusan', 'tugasin' );
        }
        
        if ( is_post_type_archive( 'university' ) ) {
            $title_parts['title'] = __( 'Perguruan Tinggi', 'tugasin' );
        }

        return $title_parts;
    }

    /**
     * Inline critical CSS in the head for above-the-fold content
     * Phase 27: Performance optimization
     */
    public function inline_critical_css() {
        $critical_css_path = TUGASIN_DIR . '/assets/css/critical.css';
        
        if ( file_exists( $critical_css_path ) ) {
            $critical_css = file_get_contents( $critical_css_path );
            if ( $critical_css ) {
                echo '<style id="tugasin-critical-css">' . $critical_css . '</style>' . "\n";
            }
        }
    }

    /**
     * Add defer attribute to non-critical JavaScript files
     * Phase 27/28: Performance optimization - now reads from settings
     *
     * @param string $tag    The script tag.
     * @param string $handle The script handle.
     * @return string Modified script tag.
     */
    public function add_defer_attribute( $tag, $handle ) {
        // Get the script src from the tag
        $src = '';
        if ( preg_match( '/src=["\']([^"\']+)["\']/', $tag, $matches ) ) {
            $src = $matches[1];
        }

        // Check if this script should be deferred using Tugasin_Optimization helper
        if ( class_exists( 'Tugasin_Optimization' ) && Tugasin_Optimization::should_defer_script( $handle, $src ) ) {
            // Add defer if not already present
            if ( strpos( $tag, 'defer' ) === false ) {
                $tag = str_replace( ' src', ' defer src', $tag );
            }
        }

        return $tag;
    }

    /**
     * Allow WebP image uploads explicitly
     * Phase 27/28: Performance optimization - now reads from settings
     *
     * @param array $mimes Allowed mime types.
     * @return array Modified mime types.
     */
    public function allow_webp_uploads( $mimes ) {
        // Check if WebP support is enabled in settings
        if ( class_exists( 'Tugasin_Optimization' ) && Tugasin_Optimization::is_webp_enabled() ) {
            $mimes['webp'] = 'image/webp';
        }
        return $mimes;
    }
}

