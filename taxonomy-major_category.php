<?php
/**
 * Taxonomy Archive template for Major Category
 * Uses the same layout as archive-major.php to maintain consistency
 *
 * @package TugasinWP
 * @since 1.7.0
 */

get_header();

$current_term = get_queried_object();
?>

<main id="main-content" role="main">
<!-- Hero Search -->
<section class="hero" style="padding: 140px 0 100px;">
    <div class="container" style="text-align: center; max-width: 800px;">
        <h1 style="margin-bottom: 24px;">
            <?php 
            printf( 
                esc_html__( 'Jurusan %s', 'tugasin' ), 
                '<span class="text-highlight">' . esc_html( $current_term->name ) . '</span>' 
            ); 
            ?>
        </h1>
        <p style="font-size: 1.2rem; color: rgba(255,255,255,0.9); margin-bottom: 40px;">
            <?php 
            if ( ! empty( $current_term->description ) ) {
                echo esc_html( $current_term->description );
            } else {
                esc_html_e( 'Jangan salah pilih jurusan. Cari tahu apa yang dipelajari dan prospek kerjanya di sini.', 'tugasin' );
            }
            ?>
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
        <!-- Category Filter -->
        <div style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center; margin-bottom: 48px;">
            <a href="<?php echo esc_url( get_post_type_archive_link( 'major' ) ); ?>" class="btn btn-outline" style="padding: 8px 24px; border-color: #e5e7eb;">
                <?php esc_html_e( 'Semua', 'tugasin' ); ?>
            </a>
            <?php
            $categories = get_terms( array(
                'taxonomy'   => 'major_category',
                'hide_empty' => true,
            ) );
            if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) :
                foreach ( $categories as $cat ) :
                    $is_active = ( $current_term->term_id === $cat->term_id );
                    ?>
                    <a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="btn <?php echo $is_active ? 'btn-primary' : 'btn-outline'; ?>" style="padding: 8px 24px; <?php echo ! $is_active ? 'border-color: #e5e7eb;' : ''; ?>">
                        <?php echo esc_html( $cat->name ); ?>
                    </a>
                    <?php
                endforeach;
            endif;
            ?>
        </div>

        <!-- Grid -->
        <div class="services-grid" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));">
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

        <?php tugasin_pagination(); ?>
    </div>
</section>
</main>

<?php
get_footer();
