<?php
/**
 * Schema Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_Schema implements Tugasin_Settings_Tab_Interface {
    
    use Tugasin_Settings_Fields;

    /**
     * Service slugs for schema settings
     */
    private $service_slugs = array(
        'joki-skripsi' => 'Joki Skripsi',
        'joki-makalah' => 'Joki Makalah',
        'joki-tugas' => 'Joki Tugas',
        'cek-plagiarism' => 'Cek Plagiarism',
    );

    public function get_id() {
        return 'schema';
    }

    public function get_label() {
        return __('Schema Markup', 'tugasin');
    }

    public function get_icon() {
        return 'media-code';
    }

    public function get_group() {
        return 'seo';
    }

    public function register_settings($option_group) {
        // Main toggle
        register_setting($option_group, 'tugasin_schema_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => true,
        ));

        // Organization settings
        register_setting($option_group, 'tugasin_schema_org_name', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => get_bloginfo('name'),
        ));

        register_setting($option_group, 'tugasin_schema_org_logo', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 0,
        ));

        register_setting($option_group, 'tugasin_schema_org_phone', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ));

        // Layanan Archive settings
        register_setting($option_group, 'tugasin_schema_layanan_name', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => __('Layanan Tugasin', 'tugasin'),
        ));

        register_setting($option_group, 'tugasin_schema_layanan_desc', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'default' => __('Daftar layanan jasa akademik dari Tugasin', 'tugasin'),
        ));

        register_setting($option_group, 'tugasin_schema_layanan_rating_enabled', array(
            'type' => 'boolean',
            'sanitize_callback' => 'rest_sanitize_boolean',
            'default' => false,
        ));

        register_setting($option_group, 'tugasin_schema_layanan_rating_value', array(
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '4.9',
        ));

        register_setting($option_group, 'tugasin_schema_layanan_rating_count', array(
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'default' => 500,
        ));

        // Service-specific settings
        foreach ($this->service_slugs as $slug => $label) {
            $prefix = 'tugasin_schema_service_' . str_replace('-', '_', $slug);
            
            register_setting($option_group, $prefix . '_name', array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => $label,
            ));

            register_setting($option_group, $prefix . '_desc', array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_textarea_field',
                'default' => '',
            ));

            register_setting($option_group, $prefix . '_rating_enabled', array(
                'type' => 'boolean',
                'sanitize_callback' => 'rest_sanitize_boolean',
                'default' => false,
            ));

            register_setting($option_group, $prefix . '_rating_value', array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => '4.9',
            ));

            register_setting($option_group, $prefix . '_rating_count', array(
                'type' => 'integer',
                'sanitize_callback' => 'absint',
                'default' => 100,
            ));

            register_setting($option_group, $prefix . '_price_from', array(
                'type' => 'integer',
                'sanitize_callback' => 'absint',
                'default' => 0,
            ));
        }
    }

    public function render() {
        ?>
        <section id="section-schema" class="tugasin-settings-section">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-media-code"></span>
                    <?php esc_html_e('Schema Markup (SEO)', 'tugasin'); ?></h2>
                <p><?php esc_html_e('Configure structured data for rich snippets in search results. Disable if using an SEO plugin.', 'tugasin'); ?></p>
            </div>

            <div class="tugasin-section-card">
                <div class="tugasin-checkbox-row">
                    <input type="checkbox" name="tugasin_schema_enabled" id="tugasin_schema_enabled" value="1"
                        <?php checked(get_option('tugasin_schema_enabled', true)); ?>>
                    <label class="tugasin-checkbox-label" for="tugasin_schema_enabled">
                        <strong><?php esc_html_e('Enable Schema Markup', 'tugasin'); ?></strong>
                        <span><?php esc_html_e('Output JSON-LD structured data for Organization and Services', 'tugasin'); ?></span>
                    </label>
                </div>
            </div>

            <?php $this->render_organization_section(); ?>
            <?php $this->render_layanan_section(); ?>
            <?php $this->render_services_section(); ?>
        </section>
        <?php
    }

    private function render_organization_section() {
        ?>
        <div class="tugasin-section-card">
            <h3 class="tugasin-section-title">
                <span class="dashicons dashicons-building"></span>
                <?php esc_html_e('Organization Schema', 'tugasin'); ?>
            </h3>
            <p class="tugasin-section-desc">
                <?php esc_html_e('This information appears site-wide and helps Google understand your business.', 'tugasin'); ?>
            </p>

            <?php
            $this->render_text_field(
                'tugasin_schema_org_name',
                __('Organization Name', 'tugasin')
            );

            $this->render_media_field(
                'tugasin_schema_org_logo',
                __('Organization Logo', 'tugasin'),
                __('Logo for Google Knowledge Graph. If empty, uses the Site Logo.', 'tugasin'),
                'medium',
                __('Upload', 'tugasin')
            );

            $this->render_text_field(
                'tugasin_schema_org_phone',
                __('Contact Phone', 'tugasin'),
                __('Phone number for customer service contact point.', 'tugasin'),
                '+62-21-1234567'
            );
            ?>
        </div>
        <?php
    }

    private function render_layanan_section() {
        ?>
        <div class="tugasin-section-card">
            <h3 class="tugasin-section-title">
                <span class="dashicons dashicons-list-view"></span>
                <?php esc_html_e('Layanan Archive Schema', 'tugasin'); ?>
            </h3>
            <p class="tugasin-section-desc">
                <?php esc_html_e('Configure schema for the main Layanan page (service list).', 'tugasin'); ?>
            </p>

            <?php
            $this->render_text_field(
                'tugasin_schema_layanan_name',
                __('List Name', 'tugasin'),
                __('Name that appears in rich results for the service list.', 'tugasin')
            );

            $this->render_textarea_field(
                'tugasin_schema_layanan_desc',
                __('List Description', 'tugasin'),
                '',
                2
            );
            ?>

            <div class="tugasin-checkbox-row" style="margin-top: 16px;">
                <input type="checkbox" name="tugasin_schema_layanan_rating_enabled" 
                       id="tugasin_schema_layanan_rating_enabled" value="1"
                    <?php checked(get_option('tugasin_schema_layanan_rating_enabled', false)); ?>>
                <label class="tugasin-checkbox-label" for="tugasin_schema_layanan_rating_enabled">
                    <strong><?php esc_html_e('Show Rating Stars', 'tugasin'); ?></strong>
                    <span><?php esc_html_e('Display aggregate rating for the entire service list', 'tugasin'); ?></span>
                </label>
            </div>

            <?php
            $this->render_text_field(
                'tugasin_schema_layanan_rating_value',
                __('Rating Value', 'tugasin'),
                __('Average rating (0-5)', 'tugasin'),
                '4.9'
            );

            $this->render_number_field(
                'tugasin_schema_layanan_rating_count',
                __('Review Count', 'tugasin'),
                __('Total number of reviews', 'tugasin'),
                0, 10000, 1, 500
            );
            ?>
        </div>
        <?php
    }

    private function render_services_section() {
        ?>
        <div class="tugasin-section-card">
            <h3 class="tugasin-section-title">
                <span class="dashicons dashicons-clipboard"></span>
                <?php esc_html_e('Service Schema', 'tugasin'); ?>
            </h3>
            <p class="tugasin-section-desc">
                <?php esc_html_e('Configure structured data for each service page.', 'tugasin'); ?>
            </p>

            <?php foreach ($this->service_slugs as $slug => $label) :
                $prefix = 'tugasin_schema_service_' . str_replace('-', '_', $slug);
            ?>
                <div class="tugasin-schema-service">
                    <div class="tugasin-schema-service-header" onclick="this.parentElement.classList.toggle('open')">
                        <h4>
                            <span class="dashicons dashicons-admin-page"></span>
                            <?php echo esc_html($label); ?>
                        </h4>
                        <span class="dashicons dashicons-arrow-down-alt2 toggle-icon"></span>
                    </div>
                    <div class="tugasin-schema-service-body">
                        <div class="tugasin-field-row">
                            <label class="tugasin-field-label"><?php esc_html_e('Service Name', 'tugasin'); ?></label>
                            <div class="tugasin-field-input">
                                <input type="text" name="<?php echo esc_attr($prefix); ?>_name"
                                    value="<?php echo esc_attr(get_option($prefix . '_name', $label)); ?>">
                            </div>
                        </div>

                        <div class="tugasin-field-row">
                            <label class="tugasin-field-label"><?php esc_html_e('Description', 'tugasin'); ?></label>
                            <div class="tugasin-field-input">
                                <textarea name="<?php echo esc_attr($prefix); ?>_desc" rows="2"><?php echo esc_textarea(get_option($prefix . '_desc', '')); ?></textarea>
                            </div>
                        </div>

                        <div class="tugasin-checkbox-row" style="margin-top: 16px;">
                            <input type="checkbox" name="<?php echo esc_attr($prefix); ?>_rating_enabled"
                                id="<?php echo esc_attr($prefix); ?>_rating_enabled" value="1"
                                <?php checked(get_option($prefix . '_rating_enabled', false)); ?>>
                            <label class="tugasin-checkbox-label" for="<?php echo esc_attr($prefix); ?>_rating_enabled">
                                <strong><?php esc_html_e('Show Rating Stars', 'tugasin'); ?></strong>
                                <span><?php esc_html_e('Display aggregate rating in search results', 'tugasin'); ?></span>
                            </label>
                        </div>

                        <div class="tugasin-field-row" style="margin-top: 16px;">
                            <label class="tugasin-field-label"><?php esc_html_e('Rating Value', 'tugasin'); ?></label>
                            <div class="tugasin-field-input">
                                <input type="text" name="<?php echo esc_attr($prefix); ?>_rating_value"
                                    value="<?php echo esc_attr(get_option($prefix . '_rating_value', '4.9')); ?>"
                                    style="width: 80px;" placeholder="4.9">
                                <p class="tugasin-field-desc"><?php esc_html_e('Average rating (0-5)', 'tugasin'); ?></p>
                            </div>
                        </div>

                        <div class="tugasin-field-row">
                            <label class="tugasin-field-label"><?php esc_html_e('Review Count', 'tugasin'); ?></label>
                            <div class="tugasin-field-input">
                                <input type="number" name="<?php echo esc_attr($prefix); ?>_rating_count"
                                    value="<?php echo esc_attr(get_option($prefix . '_rating_count', 100)); ?>"
                                    style="width: 100px;" min="0">
                                <p class="tugasin-field-desc"><?php esc_html_e('Total number of reviews', 'tugasin'); ?></p>
                            </div>
                        </div>

                        <div class="tugasin-field-row" style="margin-top: 16px;">
                            <label class="tugasin-field-label"><?php esc_html_e('Price From (IDR)', 'tugasin'); ?></label>
                            <div class="tugasin-field-input">
                                <input type="number" name="<?php echo esc_attr($prefix); ?>_price_from"
                                    value="<?php echo esc_attr(get_option($prefix . '_price_from', '')); ?>"
                                    style="width: 150px;" min="0" placeholder="50000">
                                <p class="tugasin-field-desc">
                                    <?php esc_html_e('Starting price. Leave empty to hide in schema.', 'tugasin'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }
}
