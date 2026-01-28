<?php
/**
 * Site Branding Template Part
 *
 * @package TugasinWP
 * @since 1.0.0
 */

// Get logo from Tugasin Settings
$tugasin_logo_id = get_option( 'tugasin_logo', 0 );
$has_tugasin_logo = $tugasin_logo_id && wp_get_attachment_image_url( $tugasin_logo_id, 'full' );
?>

<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo" rel="home">
    <?php if ( $has_tugasin_logo ) : ?>
        <img src="<?php echo esc_url( wp_get_attachment_image_url( $tugasin_logo_id, 'full' ) ); ?>" 
             alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" 
             class="custom-logo"
             style="max-height: 40px; width: auto;">
    <?php elseif ( has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
    <?php else : ?>
        <div class="logo-icon"><i class="fas fa-bolt"></i></div>
        <span><?php bloginfo( 'name' ); ?></span>
    <?php endif; ?>
</a>
