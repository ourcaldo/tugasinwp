<?php
/**
 * Featured Image Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_Featured_Image implements Tugasin_Settings_Tab_Interface
{

    use Tugasin_Settings_Fields;

    public function get_id()
    {
        return 'featured-image';
    }

    public function get_label()
    {
        return __('Featured Image', 'tugasin');
    }

    public function get_icon()
    {
        return 'format-image';
    }

    public function get_group()
    {
        return 'performance';
    }

    public function register_settings($option_group)
    {
        register_setting($option_group, 'tugasin_auto_featured_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        register_setting($option_group, 'tugasin_default_featured_image', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 0,
        ));
    }

    public function render()
    {
        ?>
        <section id="section-featured-image" class="tugasin-settings-section">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-format-image"></span>
                    <?php esc_html_e('Featured Image Settings', 'tugasin'); ?>
                </h2>
                <p>
                    <?php esc_html_e('Configure automatic featured image generation and fallbacks.', 'tugasin'); ?>
                </p>
            </div>

            <div class="tugasin-section-card">
                <h3 class="tugasin-section-title">
                    <span class="dashicons dashicons-images-alt"></span>
                    <?php esc_html_e('Auto Featured Image', 'tugasin'); ?>
                </h3>

                <div class="tugasin-checkbox-row">
                    <input type="checkbox" name="tugasin_auto_featured_enabled" id="tugasin_auto_featured_enabled" value="1"
                        <?php checked(get_option('tugasin_auto_featured_enabled', true)); ?>>
                    <label class="tugasin-checkbox-label" for="tugasin_auto_featured_enabled">
                        <strong>
                            <?php esc_html_e('Enable Auto Featured Image', 'tugasin'); ?>
                        </strong>
                        <span>
                            <?php esc_html_e('Automatically set the first image in post content as featured image', 'tugasin'); ?>
                        </span>
                    </label>
                </div>
            </div>

            <div class="tugasin-section-card">
                <h3 class="tugasin-section-title">
                    <span class="dashicons dashicons-format-image"></span>
                    <?php esc_html_e('Default Featured Image', 'tugasin'); ?>
                </h3>
                <p class="tugasin-section-desc">
                    <?php esc_html_e('This image will be used when a post has no featured image.', 'tugasin'); ?>
                </p>

                <?php
                $this->render_media_field(
                    'tugasin_default_featured_image',
                    __('Default Image', 'tugasin'),
                    __('Used as fallback when no featured image is set.', 'tugasin'),
                    'medium',
                    __('Upload Image', 'tugasin')
                );
                ?>
            </div>
        </section>
        <?php
    }
}
