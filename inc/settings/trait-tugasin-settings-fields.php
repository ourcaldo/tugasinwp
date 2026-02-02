<?php
/**
 * Settings Fields Trait
 *
 * Provides reusable field rendering methods for settings pages.
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

trait Tugasin_Settings_Fields {

    /**
     * Render a text input field
     *
     * @param string $name Option name
     * @param string $label Field label
     * @param string $desc Field description
     * @param string $placeholder Placeholder text
     * @param string $type Input type (text, email, url, etc.)
     */
    protected function render_text_field($name, $label, $desc = '', $placeholder = '', $type = 'text') {
        $value = get_option($name, '');
        ?>
        <div class="tugasin-field-row">
            <label class="tugasin-field-label" for="<?php echo esc_attr($name); ?>">
                <?php echo esc_html($label); ?>
            </label>
            <div class="tugasin-field-input">
                <input type="<?php echo esc_attr($type); ?>" 
                       name="<?php echo esc_attr($name); ?>" 
                       id="<?php echo esc_attr($name); ?>" 
                       value="<?php echo esc_attr($value); ?>" 
                       class="tugasin-input" 
                       placeholder="<?php echo esc_attr($placeholder); ?>">
                <?php if ($desc) : ?>
                    <p class="tugasin-field-desc"><?php echo esc_html($desc); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render a textarea field
     *
     * @param string $name Option name
     * @param string $label Field label
     * @param string $desc Field description
     * @param int $rows Number of rows
     */
    protected function render_textarea_field($name, $label, $desc = '', $rows = 4) {
        $value = get_option($name, '');
        ?>
        <div class="tugasin-field-row">
            <label class="tugasin-field-label" for="<?php echo esc_attr($name); ?>">
                <?php echo esc_html($label); ?>
            </label>
            <div class="tugasin-field-input">
                <textarea name="<?php echo esc_attr($name); ?>" 
                          id="<?php echo esc_attr($name); ?>" 
                          class="tugasin-textarea" 
                          rows="<?php echo esc_attr($rows); ?>"><?php echo esc_textarea($value); ?></textarea>
                <?php if ($desc) : ?>
                    <p class="tugasin-field-desc"><?php echo esc_html($desc); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render a toggle/switch field
     *
     * @param string $name Option name
     * @param string $label Field label
     * @param string $desc Field description
     * @param bool $default Default value
     */
    protected function render_toggle_field($name, $label, $desc = '', $default = false) {
        $value = get_option($name, $default);
        ?>
        <div class="tugasin-field-row">
            <label class="tugasin-field-label"><?php echo esc_html($label); ?></label>
            <div class="tugasin-field-input">
                <label class="tugasin-toggle">
                    <input type="checkbox" 
                           name="<?php echo esc_attr($name); ?>" 
                           value="1" 
                           <?php checked($value, true); ?>>
                    <span class="tugasin-toggle-slider"></span>
                </label>
                <?php if ($desc) : ?>
                    <p class="tugasin-field-desc"><?php echo esc_html($desc); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render a media upload field
     *
     * @param string $name Option name
     * @param string $label Field label
     * @param string $desc Field description
     * @param string $size Image size for preview
     * @param string $button_text Upload button text
     */
    protected function render_media_field($name, $label, $desc = '', $size = 'medium', $button_text = '') {
        $media_id = get_option($name, 0);
        $media_url = $media_id ? wp_get_attachment_image_url($media_id, $size) : '';
        $button_text = $button_text ?: __('Upload', 'tugasin');
        ?>
        <div class="tugasin-field-row">
            <label class="tugasin-field-label"><?php echo esc_html($label); ?></label>
            <div class="tugasin-field-input">
                <div class="tugasin-media-field">
                    <div class="tugasin-media-preview-box <?php echo $media_url ? 'has-image' : ''; ?>">
                        <?php if ($media_url) : ?>
                            <img src="<?php echo esc_url($media_url); ?>" alt="">
                        <?php else : ?>
                            <span class="placeholder-icon dashicons dashicons-format-image"></span>
                        <?php endif; ?>
                    </div>
                    <div class="tugasin-media-actions">
                        <input type="hidden" name="<?php echo esc_attr($name); ?>" 
                               id="<?php echo esc_attr($name); ?>" 
                               value="<?php echo esc_attr($media_id); ?>">
                        <button type="button" class="tugasin-btn tugasin-btn-secondary tugasin-upload-btn" 
                                data-target="<?php echo esc_attr($name); ?>">
                            <span class="dashicons dashicons-upload"></span>
                            <?php echo esc_html($button_text); ?>
                        </button>
                        <button type="button" class="tugasin-btn tugasin-btn-danger tugasin-remove-btn" 
                                data-target="<?php echo esc_attr($name); ?>" 
                                <?php echo !$media_id ? 'style="display:none;"' : ''; ?>>
                            <span class="dashicons dashicons-trash"></span>
                            <?php esc_html_e('Remove', 'tugasin'); ?>
                        </button>
                    </div>
                </div>
                <?php if ($desc) : ?>
                    <p class="tugasin-field-desc"><?php echo esc_html($desc); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render a color picker field
     *
     * @param string $name Option name
     * @param string $label Field label
     * @param string $desc Field description
     * @param string $default Default color
     */
    protected function render_color_field($name, $label, $desc = '', $default = '#4f46e5') {
        $value = get_option($name, $default);
        ?>
        <div class="tugasin-field-row">
            <label class="tugasin-field-label" for="<?php echo esc_attr($name); ?>">
                <?php echo esc_html($label); ?>
            </label>
            <div class="tugasin-field-input">
                <input type="text" 
                       name="<?php echo esc_attr($name); ?>" 
                       id="<?php echo esc_attr($name); ?>" 
                       value="<?php echo esc_attr($value); ?>" 
                       class="tugasin-color-picker" 
                       data-default-color="<?php echo esc_attr($default); ?>">
                <?php if ($desc) : ?>
                    <p class="tugasin-field-desc"><?php echo esc_html($desc); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render a select dropdown field
     *
     * @param string $name Option name
     * @param string $label Field label
     * @param array $options Array of value => label options
     * @param string $desc Field description
     * @param string $default Default value
     */
    protected function render_select_field($name, $label, $options, $desc = '', $default = '') {
        $value = get_option($name, $default);
        ?>
        <div class="tugasin-field-row">
            <label class="tugasin-field-label" for="<?php echo esc_attr($name); ?>">
                <?php echo esc_html($label); ?>
            </label>
            <div class="tugasin-field-input">
                <select name="<?php echo esc_attr($name); ?>" 
                        id="<?php echo esc_attr($name); ?>" 
                        class="tugasin-select">
                    <?php foreach ($options as $opt_value => $opt_label) : ?>
                        <option value="<?php echo esc_attr($opt_value); ?>" <?php selected($value, $opt_value); ?>>
                            <?php echo esc_html($opt_label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($desc) : ?>
                    <p class="tugasin-field-desc"><?php echo esc_html($desc); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render a number input field
     *
     * @param string $name Option name
     * @param string $label Field label
     * @param string $desc Field description
     * @param int $min Minimum value
     * @param int $max Maximum value
     * @param int $step Step value
     * @param int $default Default value
     */
    protected function render_number_field($name, $label, $desc = '', $min = 0, $max = 100, $step = 1, $default = 0) {
        $value = get_option($name, $default);
        ?>
        <div class="tugasin-field-row">
            <label class="tugasin-field-label" for="<?php echo esc_attr($name); ?>">
                <?php echo esc_html($label); ?>
            </label>
            <div class="tugasin-field-input">
                <input type="number" 
                       name="<?php echo esc_attr($name); ?>" 
                       id="<?php echo esc_attr($name); ?>" 
                       value="<?php echo esc_attr($value); ?>" 
                       class="tugasin-input tugasin-input-number" 
                       min="<?php echo esc_attr($min); ?>" 
                       max="<?php echo esc_attr($max); ?>" 
                       step="<?php echo esc_attr($step); ?>">
                <?php if ($desc) : ?>
                    <p class="tugasin-field-desc"><?php echo esc_html($desc); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render a page select dropdown
     *
     * @param string $name Option name
     * @param string $label Field label
     * @param string $desc Field description
     */
    protected function render_page_select_field($name, $label, $desc = '') {
        $selected = get_option($name, 0);
        ?>
        <div class="tugasin-field-row">
            <label class="tugasin-field-label" for="<?php echo esc_attr($name); ?>">
                <?php echo esc_html($label); ?>
            </label>
            <div class="tugasin-field-input">
                <?php
                wp_dropdown_pages(array(
                    'name' => $name,
                    'id' => $name,
                    'selected' => $selected,
                    'show_option_none' => __('— Select —', 'tugasin'),
                    'option_none_value' => '0',
                    'class' => 'tugasin-select',
                ));
                ?>
                <?php if ($desc) : ?>
                    <p class="tugasin-field-desc"><?php echo esc_html($desc); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Start a settings section card
     *
     * @param string $title Section title
     * @param string $icon Dashicon class name
     */
    protected function start_section_card($title, $icon = 'admin-generic') {
        ?>
        <div class="tugasin-section-card">
            <h3 class="tugasin-section-title">
                <span class="dashicons dashicons-<?php echo esc_attr($icon); ?>"></span>
                <?php echo esc_html($title); ?>
            </h3>
        <?php
    }

    /**
     * End a settings section card
     */
    protected function end_section_card() {
        ?>
        </div>
        <?php
    }
}
