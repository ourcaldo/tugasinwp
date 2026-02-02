<?php
/**
 * Related Articles Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_Related implements Tugasin_Settings_Tab_Interface
{

    use Tugasin_Settings_Fields;

    public function get_id()
    {
        return 'related';
    }

    public function get_label()
    {
        return __('Related Articles', 'tugasin');
    }

    public function get_icon()
    {
        return 'format-aside';
    }

    public function get_group()
    {
        return 'performance';
    }

    public function register_settings($option_group)
    {
        register_setting($option_group, 'tugasin_related_inline_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        register_setting($option_group, 'tugasin_related_inline_position', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 3,
        ));

        register_setting($option_group, 'tugasin_related_inline_count', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 2,
        ));

        register_setting($option_group, 'tugasin_related_bottom_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));
    }

    public function render()
    {
        ?>
        <section id="section-related" class="tugasin-settings-section">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-format-aside"></span>
                    <?php esc_html_e('Related Articles', 'tugasin'); ?>
                </h2>
                <p>
                    <?php esc_html_e('Configure how related articles are displayed on single posts.', 'tugasin'); ?>
                </p>
            </div>

            <div class="tugasin-section-card">
                <h3 class="tugasin-section-title">
                    <span class="dashicons dashicons-editor-insertmore"></span>
                    <?php esc_html_e('Inline Related Posts', 'tugasin'); ?>
                </h3>
                <p class="tugasin-section-desc">
                    <?php esc_html_e('These appear within the post content.', 'tugasin'); ?>
                </p>

                <div class="tugasin-checkbox-row">
                    <input type="checkbox" name="tugasin_related_inline_enabled" id="tugasin_related_inline_enabled" value="1"
                        <?php checked(get_option('tugasin_related_inline_enabled', true)); ?>>
                    <label class="tugasin-checkbox-label" for="tugasin_related_inline_enabled">
                        <strong>
                            <?php esc_html_e('Enable Inline Related Posts', 'tugasin'); ?>
                        </strong>
                        <span>
                            <?php esc_html_e('Show related posts within the article content', 'tugasin'); ?>
                        </span>
                    </label>
                </div>

                <?php
                $this->render_number_field(
                    'tugasin_related_inline_position',
                    __('Position (after paragraph #)', 'tugasin'),
                    __('Insert related posts after this paragraph number.', 'tugasin'),
                    2,
                    10,
                    1,
                    3
                );

                $this->render_number_field(
                    'tugasin_related_inline_count',
                    __('Number of Posts', 'tugasin'),
                    __('How many related posts to show inline.', 'tugasin'),
                    1,
                    4,
                    1,
                    2
                );
                ?>
            </div>

            <div class="tugasin-section-card">
                <h3 class="tugasin-section-title">
                    <span class="dashicons dashicons-grid-view"></span>
                    <?php esc_html_e('Bottom Related Posts', 'tugasin'); ?>
                </h3>
                <p class="tugasin-section-desc">
                    <?php esc_html_e('These appear after the post content.', 'tugasin'); ?>
                </p>

                <div class="tugasin-checkbox-row">
                    <input type="checkbox" name="tugasin_related_bottom_enabled" id="tugasin_related_bottom_enabled" value="1"
                        <?php checked(get_option('tugasin_related_bottom_enabled', true)); ?>>
                    <label class="tugasin-checkbox-label" for="tugasin_related_bottom_enabled">
                        <strong>
                            <?php esc_html_e('Enable Bottom Related Posts', 'tugasin'); ?>
                        </strong>
                        <span>
                            <?php esc_html_e('Show related posts section after the article', 'tugasin'); ?>
                        </span>
                    </label>
                </div>
            </div>
        </section>
        <?php
    }
}
