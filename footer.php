<?php
/**
 * The footer for our theme
 *
 * @package TugasinWP
 * @since 1.0.0
 */

// Get logo from theme settings
$logo_id = get_option('tugasin_logo');
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'medium') : '';
?>

<footer role="contentinfo">
    <div class="container footer-grid">
        <div class="footer-col brand">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                <?php if ($logo_url): ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php bloginfo('name'); ?>"
                        style="max-height: 40px; width: auto;">
                <?php else: ?>
                    <div class="logo-icon"><i class="fas fa-bolt"></i></div>
                    <span><?php bloginfo('name'); ?></span>
                <?php endif; ?>
            </a>
            <p><?php echo esc_html(get_bloginfo('description')); ?></p>
        </div>

        <div class="footer-col">
            <strong class="footer-menu-title"><?php esc_html_e('Layanan', 'tugasin'); ?></strong>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'footer',
                'container' => false,
                'depth' => 1,
                'fallback_cb' => false,
            ));
            ?>
        </div>

        <?php if (is_active_sidebar('footer-2')): ?>
            <div class="footer-col">
                <?php dynamic_sidebar('footer-2'); ?>
            </div>
        <?php endif; ?>

        <?php if (is_active_sidebar('footer-3')): ?>
            <div class="footer-col">
                <?php dynamic_sidebar('footer-3'); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="container footer-bottom">
        <p>&copy; <?php echo esc_html(wp_date('Y')); ?> <?php bloginfo('name'); ?>.
            <?php esc_html_e('All rights reserved.', 'tugasin'); ?></p>
    </div>
</footer>

<?php
// WhatsApp Floating Widget (v2.8.0)
get_template_part('template-parts/component/whatsapp-widget');
?>

<?php wp_footer(); ?>

</body>

</html>