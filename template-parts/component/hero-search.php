<?php
/**
 * Hero Search Component Template Part
 *
 * @package TugasinWP
 * @since 1.0.0
 */

$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : __( 'Cari jurusan, kampus, atau artikel...', 'tugasin' );
$post_type   = isset( $args['post_type'] ) ? $args['post_type'] : '';
?>

<div class="hero-search" style="position: relative; max-width: 600px; margin: 0 auto;">
    <form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
        <?php if ( $post_type ) : ?>
        <input type="hidden" name="post_type" value="<?php echo esc_attr( $post_type ); ?>">
        <?php endif; ?>
        <input type="text" 
               name="s" 
               placeholder="<?php echo esc_attr( $placeholder ); ?>"
               value="<?php echo get_search_query(); ?>"
               style="width: 100%; padding: 20px 24px 20px 56px; border-radius: 50px; border: none; font-size: 1.1rem; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
        <i class="fas fa-search" style="position: absolute; left: 24px; top: 50%; transform: translateY(-50%); color: var(--text-secondary); font-size: 1.2rem;"></i>
        <button type="submit" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 50px; font-weight: 600; cursor: pointer;">
            <?php esc_html_e( 'Cari', 'tugasin' ); ?>
        </button>
    </form>
</div>
