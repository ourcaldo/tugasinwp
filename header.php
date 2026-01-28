<?php
/**
 * The header for our theme
 *
 * @package TugasinWP
 * @since 1.0.0
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Accessibility: Skip to Content Link -->
<a class="skip-link screen-reader-text" href="#main-content"><?php esc_html_e( 'Skip to content', 'tugasin' ); ?></a>

<header class="header" role="banner">
    <div class="container header-container">
        <?php get_template_part( 'template-parts/header/site-branding' ); ?>
        <?php get_template_part( 'template-parts/header/navigation' ); ?>
        <div class="header-actions">
            <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>" class="btn btn-primary" target="_blank" rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i>
                <?php 
                $cta_text = get_option( 'tugasin_cta_text', 'Konsultasi Sekarang!' );
                echo esc_html( $cta_text ? $cta_text : __( 'Konsultasi Sekarang!', 'tugasin' ) ); 
                ?>
            </a>
            <button class="mobile-toggle" aria-label="<?php esc_attr_e( 'Toggle menu', 'tugasin' ); ?>">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<?php get_template_part( 'template-parts/component/mobile-menu' ); ?>
