<?php
/**
 * Floating Badge Component Template Part
 *
 * @package TugasinWP
 * @since 1.0.0
 */

$icon    = isset( $args['icon'] ) ? $args['icon'] : 'fa-check-circle';
$bg      = isset( $args['bg'] ) ? $args['bg'] : '#e0e7ff';
$color   = isset( $args['color'] ) ? $args['color'] : '#4f46e5';
$title   = isset( $args['title'] ) ? $args['title'] : '';
$text    = isset( $args['text'] ) ? $args['text'] : '';
$style   = isset( $args['style'] ) ? $args['style'] : '';
?>

<div class="hero-float-card" style="<?php echo esc_attr( $style ); ?>">
    <div style="display: flex; align-items: center; gap: 10px;">
        <div style="width: 40px; height: 40px; background: <?php echo esc_attr( $bg ); ?>; color: <?php echo esc_attr( $color ); ?>; border-radius: 10px; display: flex; align-items: center; justify-content: center;">
            <i class="fas <?php echo esc_attr( $icon ); ?>"></i>
        </div>
        <div>
            <?php if ( $title ) : ?>
            <h5 style="font-size: 0.9rem; color: #1f2937; margin: 0;"><?php echo esc_html( $title ); ?></h5>
            <?php endif; ?>
            <?php if ( $text ) : ?>
            <span style="font-size: 0.75rem; color: #6b7280;"><?php echo esc_html( $text ); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>
