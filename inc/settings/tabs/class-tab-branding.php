<?php
/**
 * Branding Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_Branding implements Tugasin_Settings_Tab_Interface
{

    use Tugasin_Settings_Fields;

    public function get_id()
    {
        return 'branding';
    }

    public function get_label()
    {
        return __('Branding', 'tugasin');
    }

    public function get_icon()
    {
        return 'admin-appearance';
    }

    public function get_group()
    {
        return 'settings';
    }

    public function register_settings($option_group)
    {
        register_setting($option_group, 'tugasin_logo', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 0,
        ));

        register_setting($option_group, 'tugasin_site_icon', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 0,
        ));

        register_setting($option_group, 'tugasin_color_primary', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#064e3b',
        ));
    }

    public function render()
    {
        ?>
        <section id="section-branding" class="tugasin-settings-section">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-admin-appearance"></span>
                    <?php esc_html_e('Branding', 'tugasin'); ?>
                </h2>
                <p>
                    <?php esc_html_e('Customize your site logo, icon, and brand colors.', 'tugasin'); ?>
                </p>
            </div>

            <?php $this->start_section_card(__('Logo & Icons', 'tugasin'), 'format-image'); ?>
            <?php
            $this->render_media_field(
                'tugasin_logo',
                __('Site Logo', 'tugasin'),
                __('Upload your site logo. If empty, site title text will be displayed.', 'tugasin'),
                'medium',
                __('Upload Logo', 'tugasin')
            );

            $this->render_media_field(
                'tugasin_site_icon',
                __('Site Icon', 'tugasin'),
                __('Upload your site icon (favicon). Recommended size: 512x512 pixels.', 'tugasin'),
                'thumbnail',
                __('Upload Icon', 'tugasin')
            );
            ?>
            <?php $this->end_section_card(); ?>

            <?php $this->start_section_card(__('Colors', 'tugasin'), 'art'); ?>
            <?php
            $this->render_color_field(
                'tugasin_color_primary',
                __('Primary Color', 'tugasin'),
                __('Main theme color used for buttons, links, and accents.', 'tugasin'),
                '#064e3b'
            );
            ?>
            <?php $this->end_section_card(); ?>
        </section>
        <?php
    }
}
