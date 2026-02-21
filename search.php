<?php
/**
 * Search Results Template
 *
 * @package TugasinWP
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" role="main">
<!-- Hero -->
<section class="hero search-hero">
    <div class="container">
        <h1>
            <?php
            printf(
                esc_html__( 'Hasil pencarian: "%s"', 'tugasin' ),
                '<span class="search-query">' . get_search_query() . '</span>'
            );
            ?>
        </h1>
        <p>
            <?php
            global $wp_query;
            printf(
                esc_html( _n( 'Ditemukan %d hasil', 'Ditemukan %d hasil', $wp_query->found_posts, 'tugasin' ) ),
                $wp_query->found_posts
            );
            ?>
        </p>
    </div>
</section>

<!-- Results -->
<section class="search-results-section">
    <div class="container">
        <?php if ( have_posts() ) : ?>
        <div class="search-results-grid">
            <?php
            while ( have_posts() ) :
                the_post();
                $post_type = get_post_type();
                ?>
                <article class="search-result-card">
                    <span class="search-result-type">
                        <?php
                        if ( 'major' === $post_type ) {
                            esc_html_e( 'Jurusan', 'tugasin' );
                        } elseif ( 'university' === $post_type ) {
                            esc_html_e( 'Kampus', 'tugasin' );
                        } else {
                            esc_html_e( 'Artikel', 'tugasin' );
                        }
                        ?>
                    </span>
                    <h3>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h3>
                    <p>
                        <?php echo wp_trim_words( get_the_excerpt(), 15 ); ?>
                    </p>
                    <a href="<?php the_permalink(); ?>" class="btn btn-outline">
                        <?php esc_html_e( 'Lihat', 'tugasin' ); ?> →
                    </a>
                </article>
                <?php
            endwhile;
            ?>
        </div>
        
        <?php tugasin_pagination(); ?>
        
        <?php else : ?>
        <div class="search-no-results">
            <div class="search-no-results-icon">
                <i class="fas fa-search"></i>
            </div>
            <h2><?php esc_html_e( 'Tidak ada hasil ditemukan', 'tugasin' ); ?></h2>
            <p><?php esc_html_e( 'Coba kata kunci lain atau jelajahi halaman kami.', 'tugasin' ); ?></p>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
                <?php esc_html_e( 'Kembali ke Home', 'tugasin' ); ?>
            </a>
        </div>
        <?php endif; ?>
    </div>
</section>
</main>

<?php
get_footer();
