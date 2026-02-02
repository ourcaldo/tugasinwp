<?php
/**
 * Optimization Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_Optimization implements Tugasin_Settings_Tab_Interface {
    
    use Tugasin_Settings_Fields;

    public function get_id() {
        return 'optimization';
    }

    public function get_label() {
        return __('Optimization', 'tugasin');
    }

    public function get_icon() {
        return 'performance';
    }

    public function get_group() {
        return 'performance';
    }

    public function register_settings($option_group) {
        register_setting($option_group, 'tugasin_lazyload_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        register_setting($option_group, 'tugasin_critical_css_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        register_setting($option_group, 'tugasin_defer_js', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));
    }

    public function render() {
        ?>
        <section id="section-optimization" class="tugasin-settings-section">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-performance"></span>
                    <?php esc_html_e('Performance Optimization', 'tugasin'); ?></h2>
                <p><?php esc_html_e('Configure performance settings to improve page load speed.', 'tugasin'); ?></p>
            </div>

            <div class="tugasin-section-card">
                <h3 class="tugasin-section-title">
                    <span class="dashicons dashicons-images-alt2"></span>
                    <?php esc_html_e('Image Optimization', 'tugasin'); ?>
                </h3>

                <div class="tugasin-checkbox-row">
                    <input type="checkbox" name="tugasin_lazyload_enabled" id="tugasin_lazyload_enabled" value="1"
                        <?php checked(get_option('tugasin_lazyload_enabled', true)); ?>>
                    <label class="tugasin-checkbox-label" for="tugasin_lazyload_enabled">
                        <strong><?php esc_html_e('Enable Lazy Loading', 'tugasin'); ?></strong>
                        <span><?php esc_html_e('Load images only when they enter the viewport', 'tugasin'); ?></span>
                    </label>
                </div>
            </div>

            <div class="tugasin-section-card">
                <h3 class="tugasin-section-title">
                    <span class="dashicons dashicons-editor-code"></span>
                    <?php esc_html_e('CSS & JavaScript', 'tugasin'); ?>
                </h3>

                <div class="tugasin-checkbox-row">
                    <input type="checkbox" name="tugasin_critical_css_enabled" id="tugasin_critical_css_enabled" value="1"
                        <?php checked(get_option('tugasin_critical_css_enabled', true)); ?>>
                    <label class="tugasin-checkbox-label" for="tugasin_critical_css_enabled">
                        <strong><?php esc_html_e('Enable Critical CSS', 'tugasin'); ?></strong>
                        <span><?php esc_html_e('Inline critical CSS for faster first paint', 'tugasin'); ?></span>
                    </label>
                </div>

                <div class="tugasin-checkbox-row">
                    <input type="checkbox" name="tugasin_defer_js" id="tugasin_defer_js" value="1"
                        <?php checked(get_option('tugasin_defer_js', true)); ?>>
                    <label class="tugasin-checkbox-label" for="tugasin_defer_js">
                        <strong><?php esc_html_e('Defer JavaScript', 'tugasin'); ?></strong>
                        <span><?php esc_html_e('Load JavaScript files asynchronously', 'tugasin'); ?></span>
                    </label>
                </div>
            </div>
        </section>
        <?php
    }
}
