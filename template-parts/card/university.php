<?php
/**
 * University Card Template Part
 *
 * Used in archive-university.php for displaying university cards.
 *
 * @package TugasinWP
 * @since 2.3.0
 */

$logo         = get_field( 'uni_logo' );
$banner_color = get_field( 'uni_banner_color' ) ?: '#064e3b';
$location     = get_field( 'uni_location' );
?>

<a href="<?php the_permalink(); ?>" class="campus-card">
    <div class="banner" style="background: <?php echo esc_attr( $banner_color ); ?>;">
        <?php if ( $logo ) : ?>
        <div class="logo-wrap">
            <img src="<?php echo esc_url( $logo ); ?>" alt="<?php the_title_attribute(); ?>">
        </div>
        <?php endif; ?>
    </div>
    <div class="content">
        <?php tugasin_category_badge(); ?>
        <h2><?php the_title(); ?></h2>
        <?php if ( $location ) : ?>
        <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo esc_html( $location ); ?></p>
        <?php endif; ?>
        <div class="tags">
            <?php tugasin_accreditation_badge(); ?>
        </div>
    </div>
</a>
