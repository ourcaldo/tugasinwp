<?php
/**
 * General Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_General implements Tugasin_Settings_Tab_Interface
{

    use Tugasin_Settings_Fields;

    public function get_id()
    {
        return 'general';
    }

    public function get_label()
    {
        return __('General', 'tugasin');
    }

    public function get_icon()
    {
        return 'admin-generic';
    }

    public function get_group()
    {
        return 'settings';
    }

    public function register_settings($option_group)
    {
        // WhatsApp Number
        register_setting($option_group, 'tugasin_wa_number', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '6281234567890',
        ));

        // WhatsApp Message Template
        register_setting($option_group, 'tugasin_wa_template', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => 'Halo, saya ingin konsultasi tentang bantuan tugas.',
        ));

        // CTA Text
        register_setting($option_group, 'tugasin_cta_text', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'Konsultasi Sekarang!',
        ));
    }

    public function render()
    {
        ?>
        <section id="section-general" class="tugasin-settings-section active">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-admin-generic"></span>
                    <?php esc_html_e('General Settings', 'tugasin'); ?>
                </h2>
                <p>
                    <?php esc_html_e('Configure your contact settings and call-to-action buttons.', 'tugasin'); ?>
                </p>
            </div>

            <?php $this->start_section_card(__('WhatsApp Settings', 'tugasin'), 'whatsapp'); ?>
            <p class="tugasin-section-desc">
                <?php esc_html_e('Configure your WhatsApp contact settings. All CTA buttons will use this number.', 'tugasin'); ?>
            </p>

            <?php
            $this->render_text_field(
                'tugasin_wa_number',
                __('WhatsApp Number', 'tugasin'),
                __('Enter number with country code, no + or spaces (e.g., 6281234567890)', 'tugasin'),
                '6281234567890'
            );

            $this->render_textarea_field(
                'tugasin_wa_template',
                __('Default Message', 'tugasin'),
                __('Default message that will be pre-filled when users click WhatsApp buttons.', 'tugasin'),
                3
            );
            ?>
            <?php $this->end_section_card(); ?>

            <?php $this->start_section_card(__('Call to Action', 'tugasin'), 'megaphone'); ?>
            <?php
            $this->render_text_field(
                'tugasin_cta_text',
                __('Header CTA Text', 'tugasin'),
                __('Text displayed on the header CTA button.', 'tugasin'),
                'Konsultasi Sekarang!'
            );
            ?>
            <?php $this->end_section_card(); ?>
        </section>
        <?php
    }
}
