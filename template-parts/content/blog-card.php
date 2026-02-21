<?php
/**
 * Blog Card Template Part
 *
 * Shared card component used by archive.php, home.php, and AJAX handler.
 * Uses .blog-card CSS classes from _cards.css.
 *
 * @package TugasinWP
 * @since 2.26.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get category
$cats     = get_the_category();
$cat_name = ! empty( $cats ) ? $cats[0]->name : '';

// Calculate read time (rough estimate: 200 words per minute)
$content    = get_the_content();
$word_count = preg_match_all( '/\S+/', strip_tags( $content ) );
$read_time  = max( 1, ceil( $word_count / 200 ) );

// Get featured image or placeholder
$thumb_url = get_the_post_thumbnail_url( null, 'large' );
if ( ! $thumb_url ) {
    $thumb_url = get_template_directory_uri() . '/assets/images/placeholder-blog.jpg';
}
?>
<div class="blog-card">
    <a href="<?php the_permalink(); ?>" class="card-image">
        <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
    </a>
    <div class="card-content">
        <?php if ( $cat_name ) : ?>
            <span class="card-category"><?php echo esc_html( $cat_name ); ?></span>
        <?php endif; ?>
        <h3 class="card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <p class="card-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?></p>
        <div class="card-meta">
            <span><i class="far fa-calendar"></i> <?php echo get_the_date( 'd M Y' ); ?></span>
            <span><i class="far fa-clock"></i> <?php printf( esc_html__( '%d min read', 'tugasin' ), $read_time ); ?></span>
        </div>
    </div>
</div>
