<?php
/**
 * Default Page Template
 *
 * @package TugasinWP
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" role="main" class="site-main">
    <?php
    while ( have_posts() ) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <!-- Hero -->
            <section class="hero page-hero">
                <div class="container">
                    <?php tugasin_breadcrumb(); ?>
                    <h1><?php the_title(); ?></h1>
                </div>
            </section>

            <!-- Content -->
            <section class="page-content-section">
                <div class="container page-content-wrapper">
                    <div class="entry-content">
                        <?php
                        the_content();

                        wp_link_pages( array(
                            'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'tugasin' ),
                            'after'  => '</div>',
                        ) );
                        ?>
                    </div>
                </div>
            </section>
        </article>
        <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
