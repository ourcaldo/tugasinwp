<?php
/**
 * No content found template part
 *
 * @package TugasinWP
 * @since 1.0.0
 */

?>

<section class="no-results not-found" style="text-align: center; padding: 80px 20px;">
    <div style="width: 80px; height: 80px; background: var(--pastel-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
        <i class="fas fa-search" style="font-size: 2rem; color: var(--text-secondary);"></i>
    </div>
    
    <h1 class="page-title" style="font-size: 1.75rem; margin-bottom: 12px;">
        <?php esc_html_e( 'Tidak ada konten ditemukan', 'tugasin' ); ?>
    </h1>

    <p style="color: var(--text-secondary); max-width: 500px; margin: 0 auto 24px;">
        <?php
        if ( is_search() ) :
            esc_html_e( 'Maaf, tidak ada hasil yang cocok dengan kata kunci pencarianmu. Coba kata kunci lain.', 'tugasin' );
        else :
            esc_html_e( 'Sepertinya belum ada konten di sini. Coba lagi nanti.', 'tugasin' );
        endif;
        ?>
    </p>

    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
        <?php esc_html_e( 'Kembali ke Home', 'tugasin' ); ?>
    </a>
</section>
