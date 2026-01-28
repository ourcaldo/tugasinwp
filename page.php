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
            <section class="hero" style="padding: 120px 0 80px;">
                <div class="container">
                    <?php tugasin_breadcrumb(); ?>
                    <h1 style="font-size: 3rem; margin-top: 24px;"><?php the_title(); ?></h1>
                </div>
            </section>

            <!-- Content -->
            <section style="padding: 60px 0 100px;">
                <div class="container" style="max-width: 900px;">
                    <div class="entry-content" style="font-size: 1.1rem; line-height: 1.8;">
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
