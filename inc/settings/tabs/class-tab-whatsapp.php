<?php
/**
 * WhatsApp Widget Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_Whatsapp implements Tugasin_Settings_Tab_Interface {
    
    use Tugasin_Settings_Fields;

    public function get_id() {
        return 'whatsapp';
    }

    public function get_label() {
        return __('WhatsApp Widget', 'tugasin');
    }

    public function get_icon() {
        return 'format-chat';
    }

    public function get_group() {
        return 'settings';
    }

    public function register_settings($option_group) {
        register_setting($option_group, 'tugasin_wa_widget_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        register_setting($option_group, 'tugasin_wa_widget_cta', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => 'Halo! Kamu butuh bantuan? Tim Tugasin siap bantu kamu. Yuk konsultasi sekarang, GRATIS!',
        ));

        register_setting($option_group, 'tugasin_wa_widget_delay', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 3,
        ));
    }

    public function render() {
        ?>
        <section id="section-whatsapp" class="tugasin-settings-section">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-format-chat"></span>
                    <?php esc_html_e('WhatsApp Floating Widget', 'tugasin'); ?></h2>
                <p><?php esc_html_e('Configure the floating WhatsApp button that appears in the bottom-right corner.', 'tugasin'); ?></p>
            </div>

            <div class="tugasin-section-card">
                <div class="tugasin-checkbox-row">
                    <input type="checkbox" name="tugasin_wa_widget_enabled" id="tugasin_wa_widget_enabled" value="1"
                        <?php checked(get_option('tugasin_wa_widget_enabled', true)); ?>>
                    <label class="tugasin-checkbox-label" for="tugasin_wa_widget_enabled">
                        <strong><?php esc_html_e('Enable WhatsApp Widget', 'tugasin'); ?></strong>
                        <span><?php esc_html_e('Show a floating WhatsApp button on all pages', 'tugasin'); ?></span>
                    </label>
                </div>

                <?php
                $this->render_textarea_field(
                    'tugasin_wa_widget_cta',
                    __('Chat Bubble Message', 'tugasin'),
                    __('Message shown in the chat bubble popup above the WhatsApp button.', 'tugasin'),
                    3
                );

                $this->render_number_field(
                    'tugasin_wa_widget_delay',
                    __('Popup Delay (seconds)', 'tugasin'),
                    __('How many seconds to wait before showing the chat bubble. Set to 0 to disable auto-popup.', 'tugasin'),
                    0, 60, 1, 3
                );
                ?>
            </div>
        </section>
        <?php
    }
}
