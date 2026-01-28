<?php
/**
 * Optimization Class
 *
 * Handles performance optimization features including .htaccess management,
 * lazy loading, preconnect hints, and script deferring.
 *
 * @package TugasinWP
 * @since 2.17.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tugasin_Optimization {

    /**
     * Cache rules markers
     */
    const CACHE_START_MARKER = '# BEGIN TugasinWP Cache';
    const CACHE_END_MARKER   = '# END TugasinWP Cache';

    /**
     * Constructor
     */
    public function __construct() {
        // Preconnect hints
        add_action( 'wp_head', array( $this, 'output_preconnect_hints' ), 1 );
        
        // Lazy loading
        add_filter( 'wp_get_attachment_image_attributes', array( $this, 'add_lazy_loading' ), 10, 3 );
        add_filter( 'the_content', array( $this, 'add_lazy_loading_to_content' ), 99 );
        
        // Handle .htaccess on settings save
        add_action( 'update_option_tugasin_opt_cache_enabled', array( $this, 'handle_cache_option_change' ), 10, 2 );
        
        // Defer conditional hooks to ensure WordPress is fully loaded
        add_action( 'init', array( $this, 'setup_conditional_hooks' ) );
    }

    /**
     * Setup conditional hooks after WordPress is fully loaded
     * This prevents issues during options.php save process
     */
    public function setup_conditional_hooks() {
        // Async CSS loading (Phase 28 enhancement) - only on frontend
        if ( ! is_admin() && get_option( 'tugasin_opt_async_css_enabled', false ) ) {
            add_filter( 'style_loader_tag', array( $this, 'async_css_loader' ), 10, 4 );
        }
        
        // Disable jQuery Migrate (Phase 28 enhancement)
        if ( get_option( 'tugasin_opt_disable_jquery_migrate', false ) ) {
            add_action( 'wp_default_scripts', array( $this, 'remove_jquery_migrate' ) );
        }
    }

    /**
     * Make CSS loading asynchronous to eliminate render-blocking
     *
     * @param string $tag    The link tag.
     * @param string $handle The stylesheet handle.
     * @param string $href   The stylesheet href.
     * @param string $media  The stylesheet media.
     * @return string Modified link tag.
     */
    public function async_css_loader( $tag, $handle, $href, $media ) {
        // Only async the main bundle and font awesome
        $async_handles = array( 'tugasin-main-bundle', 'tugasin-font-awesome' );
        
        if ( ! in_array( $handle, $async_handles, true ) ) {
            return $tag;
        }
        
        // Convert to preload with onload swap pattern
        return sprintf(
            '<link rel="preload" href="%s" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"><noscript>%s</noscript>' . "\n",
            esc_url( $href ),
            $tag
        );
    }

    /**
     * Remove jQuery Migrate script
     *
     * @param WP_Scripts $scripts WP_Scripts instance.
     */
    public function remove_jquery_migrate( $scripts ) {
        if ( isset( $scripts->registered['jquery'] ) ) {
            $scripts->registered['jquery']->deps = array_diff(
                $scripts->registered['jquery']->deps,
                array( 'jquery-migrate' )
            );
        }
    }

    /**
     * Output preconnect hints based on settings
     */
    public function output_preconnect_hints() {
        $enabled = get_option( 'tugasin_opt_preconnect_enabled', true );
        
        if ( ! $enabled ) {
            return;
        }

        $urls = get_option( 'tugasin_opt_preconnect_urls', "https://cdnjs.cloudflare.com" );
        
        if ( empty( $urls ) ) {
            return;
        }

        $urls_array = array_filter( array_map( 'trim', explode( "\n", $urls ) ) );

        foreach ( $urls_array as $url ) {
            if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
                echo '<link rel="preconnect" href="' . esc_url( $url ) . '" crossorigin>' . "\n";
                echo '<link rel="dns-prefetch" href="' . esc_url( $url ) . '">' . "\n";
            }
        }
    }

    /**
     * Add lazy loading attribute to attachment images
     *
     * @param array   $attr       Attributes array.
     * @param WP_Post $attachment Attachment post object.
     * @param string  $size       Requested image size.
     * @return array Modified attributes.
     */
    public function add_lazy_loading( $attr, $attachment, $size ) {
        $enabled = get_option( 'tugasin_opt_lazyload_enabled', true );
        
        if ( ! $enabled ) {
            return $attr;
        }

        // Check exclusions
        $exclude = get_option( 'tugasin_opt_lazyload_exclude', '' );
        
        if ( $this->is_excluded( $attachment->post_name, $exclude ) || 
             $this->is_excluded( wp_get_attachment_url( $attachment->ID ), $exclude ) ) {
            return $attr;
        }

        // Add loading="lazy" if not already set
        if ( ! isset( $attr['loading'] ) ) {
            $attr['loading'] = 'lazy';
        }

        return $attr;
    }

    /**
     * Add lazy loading to images in post content
     *
     * @param string $content Post content.
     * @return string Modified content.
     */
    public function add_lazy_loading_to_content( $content ) {
        $enabled = get_option( 'tugasin_opt_lazyload_enabled', true );
        
        if ( ! $enabled ) {
            return $content;
        }

        $exclude = get_option( 'tugasin_opt_lazyload_exclude', '' );

        // Find all img tags without loading attribute
        $content = preg_replace_callback(
            '/<img\s+([^>]*?)(?<!\bloading=["\'][^"\']*["\'])([^>]*)>/i',
            function( $matches ) use ( $exclude ) {
                $full_tag = $matches[0];
                
                // Skip if already has loading attribute
                if ( stripos( $full_tag, 'loading=' ) !== false ) {
                    return $full_tag;
                }

                // Extract src to check exclusions
                if ( preg_match( '/src=["\']([^"\']+)["\']/i', $full_tag, $src_match ) ) {
                    $src = $src_match[1];
                    if ( $this->is_excluded( $src, $exclude ) ) {
                        return $full_tag;
                    }
                }

                // Add loading="lazy"
                return str_replace( '<img ', '<img loading="lazy" ', $full_tag );
            },
            $content
        );

        return $content;
    }

    /**
     * Check if a value matches any exclusion patterns
     *
     * @param string $value   Value to check.
     * @param string $exclude Exclusion patterns (newline separated).
     * @return bool True if excluded.
     */
    private function is_excluded( $value, $exclude ) {
        if ( empty( $exclude ) || empty( $value ) ) {
            return false;
        }

        $patterns = array_filter( array_map( 'trim', explode( "\n", $exclude ) ) );

        foreach ( $patterns as $pattern ) {
            // Check if it's a regex pattern (starts and ends with /)
            if ( preg_match( '/^\/.*\/$/', $pattern ) ) {
                // It's a regex
                if ( @preg_match( $pattern, $value ) ) {
                    return true;
                }
            } else {
                // Simple string match (case-insensitive)
                if ( stripos( $value, $pattern ) !== false ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Handle cache option change
     *
     * @param mixed $old_value Old option value.
     * @param mixed $new_value New option value.
     */
    public function handle_cache_option_change( $old_value, $new_value ) {
        if ( $new_value && ! $old_value ) {
            // Enabled - add rules
            $this->add_cache_rules();
        } elseif ( ! $new_value && $old_value ) {
            // Disabled - remove rules
            $this->remove_cache_rules();
        }
    }

    /**
     * Check if cache rules exist in .htaccess
     *
     * @return bool True if rules exist.
     */
    public function has_cache_rules() {
        $htaccess_path = $this->get_htaccess_path();
        
        if ( ! file_exists( $htaccess_path ) ) {
            return false;
        }

        $content = file_get_contents( $htaccess_path );
        return strpos( $content, self::CACHE_START_MARKER ) !== false;
    }

    /**
     * Add cache rules to .htaccess
     *
     * @return bool True on success.
     */
    public function add_cache_rules() {
        $htaccess_path = $this->get_htaccess_path();

        // Don't add if already exists
        if ( $this->has_cache_rules() ) {
            return true;
        }

        $rules = $this->get_cache_rules();

        // Read existing content
        $existing_content = '';
        if ( file_exists( $htaccess_path ) ) {
            $existing_content = file_get_contents( $htaccess_path );
        }

        // Prepend our rules (before WordPress rules)
        $new_content = $rules . "\n\n" . $existing_content;

        // Write back
        return file_put_contents( $htaccess_path, $new_content ) !== false;
    }

    /**
     * Remove cache rules from .htaccess
     *
     * @return bool True on success.
     */
    public function remove_cache_rules() {
        $htaccess_path = $this->get_htaccess_path();

        if ( ! file_exists( $htaccess_path ) ) {
            return true;
        }

        $content = file_get_contents( $htaccess_path );

        // Find and remove our rules block
        $pattern = '/' . preg_quote( self::CACHE_START_MARKER, '/' ) . '.*?' . preg_quote( self::CACHE_END_MARKER, '/' ) . '\s*/s';
        $new_content = preg_replace( $pattern, '', $content );

        if ( $new_content !== $content ) {
            return file_put_contents( $htaccess_path, $new_content ) !== false;
        }

        return true;
    }

    /**
     * Get .htaccess path
     *
     * @return string Path to .htaccess.
     */
    private function get_htaccess_path() {
        return ABSPATH . '.htaccess';
    }

    /**
     * Get cache rules content
     *
     * @return string Cache rules.
     */
    private function get_cache_rules() {
        return self::CACHE_START_MARKER . '
# Browser Caching - Added by TugasinWP

<IfModule mod_expires.c>
    ExpiresActive On
    
    # Images - 1 year
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType image/x-icon "access plus 1 year"
    
    # Fonts - 1 year
    ExpiresByType font/ttf "access plus 1 year"
    ExpiresByType font/otf "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType application/font-woff "access plus 1 year"
    ExpiresByType application/font-woff2 "access plus 1 year"
    
    # CSS/JS - 1 month
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType text/javascript "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    
    # Default
    ExpiresDefault "access plus 2 days"
</IfModule>

<IfModule mod_headers.c>
    <FilesMatch "\.(ico|jpg|jpeg|png|gif|webp|svg|woff|woff2|ttf|otf)$">
        Header set Cache-Control "max-age=31536000, public"
    </FilesMatch>
    <FilesMatch "\.(css|js)$">
        Header set Cache-Control "max-age=2628000, public"
    </FilesMatch>
</IfModule>

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css text/javascript application/javascript application/json image/svg+xml
</IfModule>

' . self::CACHE_END_MARKER;
    }

    /**
     * Check if a script should be deferred
     *
     * @param string $handle Script handle.
     * @param string $src    Script source URL.
     * @return bool True if should be deferred.
     */
    public static function should_defer_script( $handle, $src = '' ) {
        $enabled = get_option( 'tugasin_opt_defer_enabled', true );
        
        if ( ! $enabled ) {
            return false;
        }

        // Get scripts to defer
        $defer_scripts = get_option( 'tugasin_opt_defer_scripts', "tugasin-main\ntugasin-archive-filter" );
        $defer_patterns = array_filter( array_map( 'trim', explode( "\n", $defer_scripts ) ) );

        // Get scripts to exclude
        $exclude_scripts = get_option( 'tugasin_opt_defer_exclude', '' );
        $exclude_patterns = array_filter( array_map( 'trim', explode( "\n", $exclude_scripts ) ) );

        // Check exclusions first
        foreach ( $exclude_patterns as $pattern ) {
            if ( self::matches_pattern( $handle, $pattern ) || self::matches_pattern( $src, $pattern ) ) {
                return false;
            }
        }

        // Check if should defer
        foreach ( $defer_patterns as $pattern ) {
            if ( self::matches_pattern( $handle, $pattern ) || self::matches_pattern( $src, $pattern ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if value matches pattern (regex or simple string)
     *
     * @param string $value   Value to check.
     * @param string $pattern Pattern to match.
     * @return bool True if matches.
     */
    private static function matches_pattern( $value, $pattern ) {
        if ( empty( $value ) || empty( $pattern ) ) {
            return false;
        }

        // Check if it's a regex pattern
        if ( preg_match( '/^\/.*\/$/', $pattern ) ) {
            return (bool) @preg_match( $pattern, $value );
        }

        // Simple string match
        return stripos( $value, $pattern ) !== false || $value === $pattern;
    }

    /**
     * Check if WebP uploads are enabled
     *
     * @return bool True if enabled.
     */
    public static function is_webp_enabled() {
        return (bool) get_option( 'tugasin_opt_webp_enabled', true );
    }
}
