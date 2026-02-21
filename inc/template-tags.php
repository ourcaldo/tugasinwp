<?php
/**
 * Template Tags
 *
 * Custom template functions for use in theme templates.
 *
 * @package TugasinWP
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Display custom breadcrumbs
 * 
 * Dynamic breadcrumb with:
 * - Homepage title from front page (not hardcoded "Home")
 * - Category before post title for blog posts
 * - Blog page title from actual page
 */
function tugasin_breadcrumb() {
    if ( is_front_page() ) {
        return;
    }

    $separator = '<i class="fas fa-chevron-right breadcrumb-separator"></i>';
    
    // Get homepage title dynamically
    $front_page_id = get_option( 'page_on_front' );
    $home_title = $front_page_id ? get_the_title( $front_page_id ) : __( 'Beranda', 'tugasin' );
    
    // Get blog page title dynamically
    $blog_page_id = get_option( 'page_for_posts' );
    $blog_title = $blog_page_id ? get_the_title( $blog_page_id ) : __( 'Blog', 'tugasin' );
    
    echo '<nav class="tugasin-breadcrumb">';
    
    // Home link with dynamic title
    echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="breadcrumb-link">' . esc_html( $home_title ) . '</a>';
    echo $separator;

    if ( is_singular( 'major' ) ) {
        // Major Archive link
        echo '<a href="' . esc_url( get_post_type_archive_link( 'major' ) ) . '" class="breadcrumb-link">' . esc_html__( 'Kamus Jurusan', 'tugasin' ) . '</a>';
        echo $separator;
        
        // Major Category (Taxonomy Term) - if exists
        $terms = get_the_terms( get_the_ID(), 'major_category' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $term = $terms[0];
            echo '<a href="' . esc_url( get_term_link( $term ) ) . '" class="breadcrumb-link">' . esc_html( $term->name ) . '</a>';
            echo $separator;
        }
        
        // Current Post Title
        echo '<span class="breadcrumb-current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_singular( 'university' ) ) {
        // University Archive link
        echo '<a href="' . esc_url( get_post_type_archive_link( 'university' ) ) . '" class="breadcrumb-link">' . esc_html__( 'Kamus Kampus', 'tugasin' ) . '</a>';
        echo $separator;
        
        // University Type (Taxonomy Term) - if exists
        $terms = get_the_terms( get_the_ID(), 'university_type' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $term = $terms[0];
            echo '<a href="' . esc_url( get_term_link( $term ) ) . '" class="breadcrumb-link">' . esc_html( $term->name ) . '</a>';
            echo $separator;
        }
        
        // Current Post Title
        echo '<span class="breadcrumb-current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_singular( 'post' ) ) {
        // Blog link
        $blog_url = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
        echo '<a href="' . esc_url( $blog_url ) . '" class="breadcrumb-link">' . esc_html( $blog_title ) . '</a>';
        echo $separator;
        
        // Category link (first category)
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            $category = $categories[0];
            echo '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '" class="breadcrumb-link">' . esc_html( $category->name ) . '</a>';
            echo $separator;
        }
        
        // Post title
        echo '<span class="breadcrumb-current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_home() ) {
        // Blog archive page
        echo '<span class="breadcrumb-current">' . esc_html( $blog_title ) . '</span>';
    } elseif ( is_category() ) {
        // Category archive
        $blog_url = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
        echo '<a href="' . esc_url( $blog_url ) . '" class="breadcrumb-link">' . esc_html( $blog_title ) . '</a>';
        echo $separator;
        echo '<span class="breadcrumb-current">' . single_cat_title( '', false ) . '</span>';
    } elseif ( is_post_type_archive( 'major' ) ) {
        echo '<span class="breadcrumb-current">' . esc_html__( 'Kamus Jurusan', 'tugasin' ) . '</span>';
    } elseif ( is_post_type_archive( 'university' ) ) {
        echo '<span class="breadcrumb-current">' . esc_html__( 'Kamus Kampus', 'tugasin' ) . '</span>';
    } elseif ( is_tax( 'major_category' ) ) {
        echo '<a href="' . esc_url( get_post_type_archive_link( 'major' ) ) . '" class="breadcrumb-link">' . esc_html__( 'Kamus Jurusan', 'tugasin' ) . '</a>';
        echo $separator;
        echo '<span class="breadcrumb-current">' . single_term_title( '', false ) . '</span>';
    } elseif ( is_tax( 'university_type' ) || is_tax( 'accreditation' ) ) {
        echo '<a href="' . esc_url( get_post_type_archive_link( 'university' ) ) . '" class="breadcrumb-link">' . esc_html__( 'Kamus Kampus', 'tugasin' ) . '</a>';
        echo $separator;
        echo '<span class="breadcrumb-current">' . single_term_title( '', false ) . '</span>';
    } elseif ( is_tag() ) {
        $blog_url = $blog_page_id ? get_permalink( $blog_page_id ) : home_url( '/blog/' );
        echo '<a href="' . esc_url( $blog_url ) . '" class="breadcrumb-link">' . esc_html( $blog_title ) . '</a>';
        echo $separator;
        echo '<span class="breadcrumb-current">' . single_tag_title( '', false ) . '</span>';
    } elseif ( is_search() ) {
        echo '<span class="breadcrumb-current">' . esc_html__( 'Hasil Pencarian', 'tugasin' ) . '</span>';
    } elseif ( is_404() ) {
        echo '<span class="breadcrumb-current">' . esc_html__( 'Halaman Tidak Ditemukan', 'tugasin' ) . '</span>';
    } elseif ( is_page() ) {
        // Check for parent pages
        $post = get_post();
        if ( $post->post_parent ) {
            $ancestors = get_post_ancestors( $post->ID );
            $ancestors = array_reverse( $ancestors );
            foreach ( $ancestors as $ancestor ) {
                echo '<a href="' . esc_url( get_permalink( $ancestor ) ) . '" class="breadcrumb-link">' . esc_html( get_the_title( $ancestor ) ) . '</a>';
                echo $separator;
            }
        }
        echo '<span class="breadcrumb-current">' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_archive() ) {
        echo '<span class="breadcrumb-current">' . get_the_archive_title() . '</span>';
    }

    echo '</nav>';
}

/**
 * Display custom pagination
 */
function tugasin_pagination() {
    global $wp_query;

    if ( $wp_query->max_num_pages <= 1 ) {
        return;
    }

    $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
    $max   = intval( $wp_query->max_num_pages );

    echo '<div class="tugasin-pagination">';

    // Previous
    if ( $paged > 1 ) {
        echo '<a href="' . esc_url( get_pagenum_link( $paged - 1 ) ) . '" class="btn btn-outline"><i class="fas fa-chevron-left"></i></a>';
    }

    // Page numbers with ellipsis
    $range = 2;
    $show_dots_start = false;
    $show_dots_end = false;

    for ( $i = 1; $i <= $max; $i++ ) {
        if ( $i === 1 || $i === $max || ( $i >= $paged - $range && $i <= $paged + $range ) ) {
            if ( $i === $paged ) {
                echo '<span class="current">' . $i . '</span>';
            } else {
                echo '<a href="' . esc_url( get_pagenum_link( $i ) ) . '">' . $i . '</a>';
            }
        } elseif ( $i < $paged && ! $show_dots_start ) {
            $show_dots_start = true;
            echo '<span class="pagination-ellipsis">&hellip;</span>';
        } elseif ( $i > $paged && ! $show_dots_end ) {
            $show_dots_end = true;
            echo '<span class="pagination-ellipsis">&hellip;</span>';
        }
    }

    // Next
    if ( $paged < $max ) {
        echo '<a href="' . esc_url( get_pagenum_link( $paged + 1 ) ) . '" class="btn btn-outline"><i class="fas fa-chevron-right"></i></a>';
    }

    echo '</div>';
}

/**
 * Get CTA button with WhatsApp link
 *
 * @param string $text Button text.
 * @param string $class Additional CSS classes.
 * @return string HTML button.
 */
function tugasin_cta_button( $text = '', $class = 'btn btn-primary' ) {
    if ( empty( $text ) ) {
        $text = __( 'Konsultasi Sekarang', 'tugasin' );
    }

    $url = tugasin_get_whatsapp_url();

    return sprintf(
        '<a href="%s" class="%s" target="_blank" rel="noopener noreferrer"><i class="fab fa-whatsapp"></i> %s</a>',
        esc_url( $url ),
        esc_attr( $class ),
        esc_html( $text )
    );
}

/**
 * Display posted on date
 */
function tugasin_posted_on() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    
    $time_string = sprintf(
        $time_string,
        esc_attr( get_the_date( DATE_W3C ) ),
        esc_html( get_the_date() )
    );

    echo '<span class="posted-on">' . $time_string . '</span>';
}

/**
 * Display post author
 */
function tugasin_posted_by() {
    echo '<span class="byline">';
    printf(
        esc_html_x( 'by %s', 'post author', 'tugasin' ),
        '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
    );
    echo '</span>';
}

/**
 * Display category badge
 *
 * @param int $post_id Post ID.
 */
function tugasin_category_badge( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $post_type = get_post_type( $post_id );

    if ( 'major' === $post_type ) {
        $terms = get_the_terms( $post_id, 'major_category' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $term = $terms[0];
            echo '<span class="category-badge category-badge--major">' . esc_html( $term->name ) . '</span>';
        }
    } elseif ( 'university' === $post_type ) {
        $terms = get_the_terms( $post_id, 'university_type' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $term = $terms[0];
            $variant = ( 'PTS' === $term->name ) ? 'category-badge--university-pts' : 'category-badge--university';
            echo '<span class="category-badge ' . esc_attr( $variant ) . '">' . esc_html( $term->name ) . '</span>';
        }
    } elseif ( 'post' === $post_type ) {
        $categories = get_the_category( $post_id );
        if ( ! empty( $categories ) ) {
            echo '<span class="category-badge category-badge--post">' . esc_html( $categories[0]->name ) . '</span>';
        }
    }
}

/**
 * Get accreditation badge for university
 *
 * @param int $post_id Post ID.
 */
function tugasin_accreditation_badge( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }

    $terms = get_the_terms( $post_id, 'accreditation' );
    if ( $terms && ! is_wp_error( $terms ) ) {
        $term = $terms[0];
        echo '<span class="accreditation-badge">' . esc_html( $term->name ) . '</span>';
    }
}

/**
 * Format biaya range in Indonesian Rupiah
 *
 * @param int $min Minimum cost.
 * @param int $max Maximum cost.
 * @return string Formatted biaya range.
 */
function tugasin_format_biaya( $min, $max ) {
    if ( empty( $min ) && empty( $max ) ) {
        return '';
    }

    if ( empty( $max ) || $min === $max ) {
        return 'Rp. ' . number_format( $min, 0, ',', '.' );
    }

    return 'Rp. ' . number_format( $min, 0, ',', '.' ) . ' - ' . number_format( $max, 0, ',', '.' );
}

/**
 * Render SEO Content Box with expandable mechanism
 * 
 * Displays content from the WordPress content field with:
 * - First N paragraphs visible
 * - Rest hidden with blur/fade effect
 * - Expand button to show full content
 * - Crawler-friendly (content always in DOM)
 *
 * @param int    $visible_paragraphs Number of paragraphs to show initially (default 5).
 * @param string $button_text        Expand button text.
 * @param string $collapse_text      Collapse button text.
 */
function tugasin_seo_content_box( $visible_paragraphs = 5, $button_text = '', $collapse_text = '' ) {
    $content = get_the_content();
    
    // Return early if no content
    if ( empty( $content ) ) {
        return;
    }
    
    // Apply content filters (shortcodes, wpautop, etc.)
    $content = apply_filters( 'the_content', $content );
    
    // Default button texts
    if ( empty( $button_text ) ) {
        $button_text = __( 'Lanjutkan membaca', 'tugasin' );
    }
    if ( empty( $collapse_text ) ) {
        $collapse_text = __( 'Sembunyikan', 'tugasin' );
    }
    
    // Split content by paragraph tags
    // Match both <p>...</p> and other block elements
    $pattern = '/(<(?:p|h[1-6]|ul|ol|blockquote|div|table)[^>]*>.*?<\/(?:p|h[1-6]|ul|ol|blockquote|div|table)>)/is';
    preg_match_all( $pattern, $content, $matches );
    
    $blocks = $matches[0];
    $total_blocks = count( $blocks );
    
    // If content is short enough, just display it normally
    if ( $total_blocks <= $visible_paragraphs ) {
        ?>
        <div class="seo-content-box seo-content-short">
            <div class="seo-content-inner entry-content">
                <?php echo wp_kses_post( $content ); ?>
            </div>
        </div>
        <?php
        return;
    }
    
    // Split into visible and hidden sections
    $visible_blocks = array_slice( $blocks, 0, $visible_paragraphs );
    $hidden_blocks = array_slice( $blocks, $visible_paragraphs );
    
    ?>
    <div class="seo-content-box" id="seo-content-box">
        <div class="seo-content-inner entry-content">
            <!-- Visible content (always shown) -->
            <div class="seo-content-visible">
                <?php echo wp_kses_post( implode( "\n", $visible_blocks ) ); ?>
            </div>
            
            <!-- Hidden content (expandable, but always in DOM for crawlers) -->
            <div class="seo-content-hidden" id="seo-content-hidden">
                <?php echo wp_kses_post( implode( "\n", $hidden_blocks ) ); ?>
            </div>
        </div>
        
        <!-- Gradient fade overlay -->
        <div class="seo-content-fade"></div>
        
        <!-- Expand/Collapse button -->
        <button type="button" class="seo-content-toggle" id="seo-content-toggle"
            data-expand-text="<?php echo esc_attr( $button_text ); ?>"
            data-collapse-text="<?php echo esc_attr( $collapse_text ); ?>">
            <span class="btn-text"><?php echo esc_html( $button_text ); ?></span>
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <?php
}

/**
 * Extract YouTube video ID from various URL formats
 *
 * Supports:
 * - https://www.youtube.com/watch?v=VIDEO_ID
 * - https://youtu.be/VIDEO_ID
 * - https://www.youtube.com/embed/VIDEO_ID
 * - https://www.youtube-nocookie.com/embed/VIDEO_ID
 * - Just VIDEO_ID (11 characters)
 *
 * @param string $input YouTube URL or video ID
 * @return string|false Video ID or false if not found
 * @since 2.13.0
 */
function tugasin_extract_youtube_id( $input ) {
    if ( empty( $input ) ) {
        return false;
    }
    
    $input = trim( $input );
    
    // Pattern 1: youtube.com/watch?v=VIDEO_ID
    if ( preg_match( '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/', $input, $matches ) ) {
        return $matches[1];
    }
    
    // Pattern 2: youtu.be/VIDEO_ID
    if ( preg_match( '/youtu\.be\/([a-zA-Z0-9_-]{11})/', $input, $matches ) ) {
        return $matches[1];
    }
    
    // Pattern 3: youtube.com/embed/VIDEO_ID or youtube-nocookie.com/embed/VIDEO_ID
    if ( preg_match( '/youtube(?:-nocookie)?\.com\/embed\/([a-zA-Z0-9_-]{11})/', $input, $matches ) ) {
        return $matches[1];
    }
    
    // Pattern 4: Just the video ID (11 characters, alphanumeric with - and _)
    if ( preg_match( '/^[a-zA-Z0-9_-]{11}$/', $input ) ) {
        return $input;
    }
    
    return false;
}

/**
 * Display YouTube video with facade pattern (thumbnail placeholder)
 *
 * Shows a thumbnail image with play button overlay.
 * Only loads the actual YouTube iframe when user clicks.
 * Uses youtube-nocookie.com for privacy (no third-party cookies).
 *
 * @param string $input YouTube URL or video ID
 * @param string $title Video title for accessibility
 * @return void
 * @since 2.13.0
 */
function tugasin_youtube_facade( $input, $title = '' ) {
    $video_id = tugasin_extract_youtube_id( $input );
    
    if ( ! $video_id ) {
        return;
    }
    
    // YouTube thumbnail URL (maxresdefault for HD, fallback to hqdefault)
    $thumbnail_url = 'https://img.youtube.com/vi/' . esc_attr( $video_id ) . '/maxresdefault.jpg';
    $fallback_thumbnail = 'https://img.youtube.com/vi/' . esc_attr( $video_id ) . '/hqdefault.jpg';
    
    // Embed URL with privacy-enhanced mode
    $embed_url = 'https://www.youtube-nocookie.com/embed/' . esc_attr( $video_id ) . '?autoplay=1';
    
    $alt_text = $title ? esc_attr( $title ) : esc_attr__( 'Video YouTube', 'tugasin' );
    ?>
    <div class="youtube-facade" data-video-id="<?php echo esc_attr( $video_id ); ?>" data-embed-url="<?php echo esc_url( $embed_url ); ?>">
        <img 
            src="<?php echo esc_url( $thumbnail_url ); ?>" 
            alt="<?php echo $alt_text; ?>"
            class="youtube-facade-thumbnail"
            loading="lazy"
            onerror="this.src='<?php echo esc_url( $fallback_thumbnail ); ?>'"
        >
        <button type="button" class="youtube-facade-play" aria-label="<?php esc_attr_e( 'Putar Video', 'tugasin' ); ?>">
            <svg viewBox="0 0 68 48" width="68" height="48">
                <path class="youtube-facade-play-bg" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#212121" fill-opacity="0.8"></path>
                <path d="M 45,24 27,14 27,34" fill="#fff"></path>
            </svg>
        </button>
    </div>
    <?php
}

/**
 * Apply content filters to ACF WYSIWYG content
 *
 * This enables oEmbed (auto-convert YouTube URLs) and keeps iframes.
 * Use this instead of wp_kses_post() for trusted admin content.
 *
 * @param string $content WYSIWYG content
 * @return string Processed content with embeds
 * @since 2.13.0
 */
function tugasin_process_wysiwyg( $content ) {
    if ( empty( $content ) ) {
        return '';
    }
    
    // Apply the_content filters (includes oEmbed, wpautop, shortcodes, etc.)
    return apply_filters( 'the_content', $content );
}

/**
 * Get testimonials for a specific page
 *
 * Returns testimonials from theme settings, either default or page-specific
 * based on the "Use Default" checkbox setting.
 *
 * @param string $page_key Page key (joki_skripsi, joki_makalah, joki_tugas, cek_plagiarism)
 * @return array Array of testimonials, each with: name, role, image, text, alt
 * @since 2.15.0
 */
function tugasin_get_testimonials( $page_key ) {
    // Check if using default
    $use_default = get_option( 'tugasin_testimonials_' . $page_key . '_use_default', true );
    
    if ( $use_default ) {
        $testimonials = get_option( 'tugasin_testimonials_default', array() );
    } else {
        $testimonials = get_option( 'tugasin_testimonials_' . $page_key, array() );
    }
    
    // Ensure we have an array with at least empty entries
    if ( empty( $testimonials ) || ! is_array( $testimonials ) ) {
        return array();
    }
    
    return $testimonials;
}

/**
 * Render a single testimonial card
 *
 * @param array $testimonial Testimonial data array
 * @param int   $index       Index for unique IDs
 * @return void
 * @since 2.15.0
 */
function tugasin_render_testimonial_card( $testimonial, $index = 0 ) {
    if ( empty( $testimonial ) || ! is_array( $testimonial ) ) {
        return;
    }
    
    $name  = isset( $testimonial['name'] ) ? $testimonial['name'] : '';
    $role  = isset( $testimonial['role'] ) ? $testimonial['role'] : '';
    $image = isset( $testimonial['image'] ) ? $testimonial['image'] : '';
    $text  = isset( $testimonial['text'] ) ? $testimonial['text'] : '';
    $alt   = isset( $testimonial['alt'] ) ? $testimonial['alt'] : $name;
    
    // Fallback to placeholder if no image
    if ( empty( $image ) ) {
        $image = get_template_directory_uri() . '/assets/images/placeholder-avatar.jpg';
    }
    ?>
    <div class="testimonial-card">
        <div class="testimonial-header">
            <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" class="testimonial-avatar" loading="lazy">
            <div class="testimonial-author">
                <strong class="testimonial-name"><?php echo esc_html( $name ); ?></strong>
                <span class="testimonial-role"><?php echo esc_html( $role ); ?></span>
            </div>
        </div>
        <?php if ( $text ) : ?>
        <p class="testimonial-text">"<?php echo esc_html( $text ); ?>"</p>
        <?php endif; ?>
    </div>
    <?php
}
