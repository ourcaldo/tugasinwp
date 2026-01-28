<?php
/**
 * Archive template for Major CPT
 * With AJAX category filtering
 *
 * @package TugasinWP
 * @since 2.3.0
 */

get_header();
?>

<main id="main-content" role="main">
<!-- Hero Search -->
<section class="hero" style="padding: 140px 0 100px;">
    <div class="container" style="text-align: center; max-width: 800px;">
        <h1 style="margin-bottom: 24px;"><?php esc_html_e( 'Explore Jurusan Kuliah', 'tugasin' ); ?></h1>
        <p style="font-size: 1.2rem; color: rgba(255,255,255,0.9); margin-bottom: 40px;">
            <?php esc_html_e( 'Jangan salah pilih jurusan. Cari tahu apa yang dipelajari dan prospek kerjanya di sini.', 'tugasin' ); ?>
        </p>

        <div style="position: relative; max-width: 600px; margin: 0 auto;">
            <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <input type="hidden" name="post_type" value="major">
                <input type="text" name="s" placeholder="<?php esc_attr_e( 'Cari jurusan (Contoh: Manajemen, Informatika, Hukum)...', 'tugasin' ); ?>"
                    style="width: 100%; padding: 20px 24px 20px 56px; border-radius: 50px; border: none; font-size: 1.1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <i class="fas fa-search" style="position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); font-size: 1.2rem;"></i>
            </form>
        </div>
    </div>
</section>

<!-- Filter & Grid -->
<section class="services" style="padding: 40px 0; background: var(--bg-body);">
    <div class="container">
        <!-- Category Filter with AJAX -->
        <div class="archive-filter-container" data-post-type="major" data-taxonomy="major_category" style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; margin-bottom: 48px;">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'major' ) ); ?>" 
               class="btn archive-filter-btn <?php echo ! is_tax() ? 'btn-primary' : 'btn-outline'; ?>" 
               data-term-id=""
               data-term-slug=""
               style="padding: 8px 24px;">
                <?php esc_html_e( 'Semua', 'tugasin' ); ?>
            </a>
            <?php
            $categories = get_terms( array(
                'taxonomy'   => 'major_category',
                'hide_empty' => true,
            ) );
            if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) :
                foreach ( $categories as $cat ) :
                    $is_active = is_tax( 'major_category', $cat->term_id );
                    ?>
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" 
                       class="btn archive-filter-btn <?php echo $is_active ? 'btn-primary' : 'btn-outline'; ?>" 
                       data-term-id="<?php echo esc_attr( $cat->term_id ); ?>"
                       data-term-slug="<?php echo esc_attr( $cat->slug ); ?>"
                       style="padding: 8px 24px; <?php echo ! $is_active ? 'border-color: #e5e7eb;' : ''; ?>">
                        <?php echo esc_html( $cat->name ); ?>
                    </a>
                    <?php
                endforeach;
            endif;
            ?>
        </div>

        <!-- Grid -->
        <div id="archive-grid" class="services-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
            <?php
            if ( have_posts() ) :
                while ( have_posts() ) :
                    the_post();
                    get_template_part( 'template-parts/card/major' );
                endwhile;
            else :
                ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px;">
                    <p><?php esc_html_e( 'Tidak ada jurusan ditemukan.', 'tugasin' ); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>

        <!-- Pagination -->
        <div id="archive-pagination">
            <?php tugasin_pagination(); ?>
        </div>
    </div>
</section>

<!-- Loading State CSS -->
<style>
#archive-grid.loading {
    opacity: 0.5;
    pointer-events: none;
    position: relative;
}
#archive-grid.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 40px;
    height: 40px;
    margin: -20px 0 0 -20px;
    border: 4px solid #e5e7eb;
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
</main>

<?php
get_footer();
