<?php
/**
 * Major Card Template Part
 *
 * Used in archive-major.php for displaying major cards.
 *
 * @package TugasinWP
 * @since 2.3.0
 */

$icon_class    = get_field( 'major_icon_class' ) ?: 'fa-graduation-cap';
$icon_bg       = get_field( 'major_icon_bg_color' ) ?: '#e0e7ff';
$icon_color    = get_field( 'major_icon_color' ) ?: '#4f46e5';
$short_desc    = get_field( 'major_short_desc' );
$prospects     = get_field( 'major_prospects_short' );
?>

<div class="major-card">
    <div class="card-header">
        <div class="card-icon" style="background: <?php echo esc_attr( $icon_bg ); ?>; color: <?php echo esc_attr( $icon_color ); ?>;">
            <i class="fas <?php echo esc_attr( $icon_class ); ?>"></i>
        </div>
        <?php tugasin_category_badge(); ?>
    </div>
    
    <h2 class="card-title">
        <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a>
    </h2>
    
    <?php if ( $short_desc ) : ?>
    <p class="card-desc">
        <?php echo esc_html( $short_desc ); ?>
    </p>
    <?php endif; ?>
    
    <?php if ( $prospects ) : ?>
    <div class="card-prospects">
        <?php esc_html_e( 'Prospek:', 'tugasin' ); ?> <?php echo esc_html( $prospects ); ?>
    </div>
    <?php endif; ?>
    
    <a href="<?php the_permalink(); ?>" class="btn btn-outline">
        <?php esc_html_e( 'Lihat Jurusan', 'tugasin' ); ?>
    </a>
</div>
