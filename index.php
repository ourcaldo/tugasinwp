<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 *
 * @package TugasinWP
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" role="main" class="site-main">
    <div class="container">
        <?php
        if ( have_posts() ) :

            if ( is_home() && ! is_front_page() ) :
                ?>
                <header class="page-header">
                    <h1 class="page-title"><?php single_post_title(); ?></h1>
                </header>
                <?php
            endif;

            echo '<div class="posts-grid">';

            while ( have_posts() ) :
                the_post();
                get_template_part( 'template-parts/content/content', get_post_type() );
            endwhile;

            echo '</div>';

            tugasin_pagination();

        else :

            get_template_part( 'template-parts/content/content', 'none' );

        endif;
        ?>
    </div>
</main>

<?php
get_footer();
