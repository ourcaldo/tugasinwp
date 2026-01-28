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
<section class="hero" style="padding: 120px 0 80px;">
    <div class="container" style="text-align: center;">
        <h1 style="font-size: 2.5rem; margin-bottom: 24px;">
            <?php
            printf(
                esc_html__( 'Hasil pencarian: "%s"', 'tugasin' ),
                '<span style="color: var(--accent-btn);">' . get_search_query() . '</span>'
            );
            ?>
        </h1>
        <p style="color: rgba(255,255,255,0.8); font-size: 1.2rem;">
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
<section style="padding: 60px 0 100px; background: var(--bg-body);">
    <div class="container">
        <?php if ( have_posts() ) : ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 24px;">
            <?php
            while ( have_posts() ) :
                the_post();
                $post_type = get_post_type();
                ?>
                <article style="background: white; border-radius: 16px; padding: 24px; box-shadow: var(--shadow-sm);">
                    <span style="display: inline-block; background: var(--pastel-indigo); color: var(--primary); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; margin-bottom: 12px;">
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
                    <h3 style="font-size: 1.1rem; margin-bottom: 12px;">
                        <a href="<?php the_permalink(); ?>" style="color: inherit; text-decoration: none;">
                            <?php the_title(); ?>
                        </a>
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 16px;">
                        <?php echo wp_trim_words( get_the_excerpt(), 15 ); ?>
                    </p>
                    <a href="<?php the_permalink(); ?>" class="btn btn-outline" style="padding: 8px 16px; font-size: 0.85rem;">
                        <?php esc_html_e( 'Lihat', 'tugasin' ); ?> →
                    </a>
                </article>
                <?php
            endwhile;
            ?>
        </div>
        
        <?php tugasin_pagination(); ?>
        
        <?php else : ?>
        <div style="text-align: center; padding: 80px 0;">
            <div style="width: 80px; height: 80px; background: var(--pastel-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                <i class="fas fa-search" style="font-size: 2rem; color: var(--text-secondary);"></i>
            </div>
            <h2 style="margin-bottom: 12px;"><?php esc_html_e( 'Tidak ada hasil ditemukan', 'tugasin' ); ?></h2>
            <p style="color: var(--text-secondary); margin-bottom: 24px;"><?php esc_html_e( 'Coba kata kunci lain atau jelajahi halaman kami.', 'tugasin' ); ?></p>
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
