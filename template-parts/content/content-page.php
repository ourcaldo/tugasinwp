<?php
/**
 * Page content template part
 *
 * @package TugasinWP
 * @since 1.0.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages( array(
            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'tugasin' ),
            'after'  => '</div>',
        ) );
        ?>
    </div>
</article>
