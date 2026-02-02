<?php
/**
 * Data Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_Data implements Tugasin_Settings_Tab_Interface
{

    use Tugasin_Settings_Fields;

    /**
     * Testimonial pages configuration
     */
    private $testimonial_pages = array(
        'joki_skripsi' => 'Joki Skripsi',
        'joki_makalah' => 'Joki Makalah',
        'joki_tugas' => 'Joki Tugas',
        'cek_plagiarism' => 'Cek Plagiarism',
    );

    public function get_id()
    {
        return 'data';
    }

    public function get_label()
    {
        return __('Data', 'tugasin');
    }

    public function get_icon()
    {
        return 'database';
    }

    public function get_group()
    {
        return 'settings';
    }

    public function register_settings($option_group)
    {
        // Hero tutors
        register_setting($option_group, 'tugasin_hero_tutors', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_tutors_array'),
            'default' => array(),
        ));

        // Default testimonials
        register_setting($option_group, 'tugasin_testimonials_default', array(
            'type' => 'array',
            'sanitize_callback' => array($this, 'sanitize_testimonials_array'),
            'default' => array(),
        ));

        // Page-specific testimonials
        foreach ($this->testimonial_pages as $key => $label) {
            register_setting($option_group, 'tugasin_testimonials_' . $key, array(
                'type' => 'array',
                'sanitize_callback' => array($this, 'sanitize_testimonials_array'),
                'default' => array(),
            ));
        }
    }

    /**
     * Sanitize testimonials array
     */
    public function sanitize_testimonials_array($input)
    {
        if (!is_array($input)) {
            return array();
        }

        $sanitized = array();
        foreach ($input as $key => $item) {
            if (!is_array($item)) {
                continue;
            }
            $sanitized[$key] = array(
                'name' => isset($item['name']) ? sanitize_text_field($item['name']) : '',
                'role' => isset($item['role']) ? sanitize_text_field($item['role']) : '',
                'image' => isset($item['image']) ? esc_url_raw($item['image']) : '',
                'text' => isset($item['text']) ? sanitize_textarea_field($item['text']) : '',
                'alt' => isset($item['alt']) ? sanitize_text_field($item['alt']) : '',
            );
        }
        return $sanitized;
    }

    /**
     * Sanitize tutors array
     */
    public function sanitize_tutors_array($input)
    {
        if (!is_array($input)) {
            return array();
        }

        $sanitized = array();
        foreach ($input as $key => $item) {
            if (!is_array($item)) {
                continue;
            }
            $sanitized[$key] = array(
                'name' => isset($item['name']) ? sanitize_text_field($item['name']) : '',
                'role' => isset($item['role']) ? sanitize_text_field($item['role']) : '',
                'image' => isset($item['image']) ? esc_url_raw($item['image']) : '',
                'rating' => isset($item['rating']) ? sanitize_text_field($item['rating']) : '',
                'count' => isset($item['count']) ? absint($item['count']) : 0,
            );
        }
        return $sanitized;
    }

    public function render()
    {
        ?>
        <section id="section-data" class="tugasin-settings-section">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-database"></span>
                    <?php esc_html_e('Data Management', 'tugasin'); ?>
                </h2>
                <p>
                    <?php esc_html_e('Manage testimonials and reviews displayed on service pages.', 'tugasin'); ?>
                </p>
            </div>

            <?php $this->render_hero_tutors_section(); ?>
            <?php $this->render_default_testimonials_section(); ?>
            <?php $this->render_page_testimonials_section(); ?>
        </section>
        <?php
    }

    private function render_hero_tutors_section()
    {
        $hero_tutors = get_option('tugasin_hero_tutors', array());
        $default_tutors = array(
            array('name' => 'Sarah Wijaya', 'role' => 'Expert Skripsi', 'rating' => '4.9', 'count' => 127),
            array('name' => 'Budi Santoso', 'role' => 'Expert Makalah', 'rating' => '4.8', 'count' => 98),
            array('name' => 'Dewi Lestari', 'role' => 'Expert Tugas', 'rating' => '4.9', 'count' => 156),
        );
        ?>
        <div class="tugasin-section-card">
            <h3 class="tugasin-section-title">
                <span class="dashicons dashicons-groups"></span>
                <?php esc_html_e('Hero Section Tutors', 'tugasin'); ?>
            </h3>
            <p class="tugasin-section-desc">
                <?php esc_html_e('Configure the 3 tutor profiles displayed in the hero carousel.', 'tugasin'); ?>
            </p>

            <?php for ($i = 0; $i < 3; $i++):
                $tutor = isset($hero_tutors[$i]) ? $hero_tutors[$i] : array();
                $default = isset($default_tutors[$i]) ? $default_tutors[$i] : array();
                $name = isset($tutor['name']) && $tutor['name'] ? $tutor['name'] : ($default['name'] ?? '');
                $role = isset($tutor['role']) && $tutor['role'] ? $tutor['role'] : ($default['role'] ?? '');
                $image = isset($tutor['image']) ? $tutor['image'] : '';
                $rating = isset($tutor['rating']) && $tutor['rating'] ? $tutor['rating'] : ($default['rating'] ?? '');
                $count = isset($tutor['count']) && $tutor['count'] ? $tutor['count'] : ($default['count'] ?? 0);
                ?>
                <div class="tugasin-testimonial-item<?php echo $i > 0 ? ' has-border-top' : ''; ?>">
                    <h4 class="testimonial-number">
                        <?php printf(esc_html__('Tutor #%d', 'tugasin'), $i + 1); ?>
                    </h4>

                    <div class="tugasin-field-row tugasin-field-row-2col">
                        <div class="tugasin-field-col">
                            <label class="tugasin-field-label">
                                <?php esc_html_e('Name', 'tugasin'); ?>
                            </label>
                            <input type="text" name="tugasin_hero_tutors[<?php echo $i; ?>][name]"
                                value="<?php echo esc_attr($name); ?>"
                                placeholder="<?php esc_attr_e('Sarah Wijaya', 'tugasin'); ?>">
                        </div>
                        <div class="tugasin-field-col">
                            <label class="tugasin-field-label">
                                <?php esc_html_e('Role / Expertise', 'tugasin'); ?>
                            </label>
                            <input type="text" name="tugasin_hero_tutors[<?php echo $i; ?>][role]"
                                value="<?php echo esc_attr($role); ?>"
                                placeholder="<?php esc_attr_e('Expert Skripsi', 'tugasin'); ?>">
                        </div>
                    </div>

                    <div class="tugasin-field-row">
                        <label class="tugasin-field-label">
                            <?php esc_html_e('Photo URL', 'tugasin'); ?>
                        </label>
                        <div class="tugasin-field-input tugasin-testimonial-image-field">
                            <div class="tugasin-testimonial-preview">
                                <?php if ($image): ?>
                                    <img src="<?php echo esc_url($image); ?>" alt="">
                                <?php else: ?>
                                    <span class="dashicons dashicons-admin-users"></span>
                                <?php endif; ?>
                            </div>
                            <input type="url" name="tugasin_hero_tutors[<?php echo $i; ?>][image]"
                                value="<?php echo esc_url($image); ?>" placeholder="https://..."
                                class="tugasin-testimonial-image-input">
                            <button type="button" class="button tugasin-upload-image-btn">
                                <?php esc_html_e('Upload', 'tugasin'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="tugasin-field-row tugasin-field-row-2col">
                        <div class="tugasin-field-col">
                            <label class="tugasin-field-label">
                                <?php esc_html_e('Rating (e.g. 4.9)', 'tugasin'); ?>
                            </label>
                            <input type="text" name="tugasin_hero_tutors[<?php echo $i; ?>][rating]"
                                value="<?php echo esc_attr($rating); ?>" placeholder="4.9" style="width: 80px;">
                        </div>
                        <div class="tugasin-field-col">
                            <label class="tugasin-field-label">
                                <?php esc_html_e('Review Count', 'tugasin'); ?>
                            </label>
                            <input type="number" name="tugasin_hero_tutors[<?php echo $i; ?>][count]"
                                value="<?php echo esc_attr($count); ?>" placeholder="127" style="width: 100px;" min="0">
                        </div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        <?php
    }

    private function render_default_testimonials_section()
    {
        $default_testimonials = get_option('tugasin_testimonials_default', array());
        ?>
        <div class="tugasin-section-card">
            <h3 class="tugasin-section-title">
                <span class="dashicons dashicons-format-quote"></span>
                <?php esc_html_e('Default Testimonials', 'tugasin'); ?>
            </h3>
            <p class="tugasin-section-desc">
                <?php esc_html_e('These testimonials are used by default on all service pages.', 'tugasin'); ?>
            </p>

            <?php $this->render_testimonial_fields('tugasin_testimonials_default', $default_testimonials); ?>
        </div>
        <?php
    }

    private function render_page_testimonials_section()
    {
        ?>
        <div class="tugasin-section-card">
            <h3 class="tugasin-section-title">
                <span class="dashicons dashicons-admin-page"></span>
                <?php esc_html_e('Page-Specific Testimonials', 'tugasin'); ?>
            </h3>
            <p class="tugasin-section-desc">
                <?php esc_html_e('Override testimonials for specific service pages. Leave empty to use defaults.', 'tugasin'); ?>
            </p>

            <?php foreach ($this->testimonial_pages as $key => $label):
                $option_name = 'tugasin_testimonials_' . $key;
                $testimonials = get_option($option_name, array());
                ?>
                <div class="tugasin-testimonial-page-section">
                    <h4 class="tugasin-page-title" onclick="this.parentElement.classList.toggle('open')">
                        <span class="dashicons dashicons-admin-page"></span>
                        <?php echo esc_html($label); ?>
                        <span class="dashicons dashicons-arrow-down-alt2 toggle-icon"></span>
                    </h4>
                    <div class="tugasin-page-testimonials-body">
                        <?php $this->render_testimonial_fields($option_name, $testimonials); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    private function render_testimonial_fields($option_name, $testimonials)
    {
        for ($i = 0; $i < 3; $i++):
            $testimonial = isset($testimonials[$i]) ? $testimonials[$i] : array();
            $name = isset($testimonial['name']) ? $testimonial['name'] : '';
            $role = isset($testimonial['role']) ? $testimonial['role'] : '';
            $image = isset($testimonial['image']) ? $testimonial['image'] : '';
            $text = isset($testimonial['text']) ? $testimonial['text'] : '';
            $alt = isset($testimonial['alt']) ? $testimonial['alt'] : '';
            ?>
            <div class="tugasin-testimonial-item<?php echo $i > 0 ? ' has-border-top' : ''; ?>">
                <h4 class="testimonial-number">
                    <?php printf(esc_html__('Testimonial #%d', 'tugasin'), $i + 1); ?>
                </h4>

                <div class="tugasin-field-row tugasin-field-row-2col">
                    <div class="tugasin-field-col">
                        <label class="tugasin-field-label">
                            <?php esc_html_e('Name', 'tugasin'); ?>
                        </label>
                        <input type="text" name="<?php echo esc_attr($option_name); ?>[<?php echo $i; ?>][name]"
                            value="<?php echo esc_attr($name); ?>" placeholder="<?php esc_attr_e('Ahmad R.', 'tugasin'); ?>">
                    </div>
                    <div class="tugasin-field-col">
                        <label class="tugasin-field-label">
                            <?php esc_html_e('Working Field / Role', 'tugasin'); ?>
                        </label>
                        <input type="text" name="<?php echo esc_attr($option_name); ?>[<?php echo $i; ?>][role]"
                            value="<?php echo esc_attr($role); ?>"
                            placeholder="<?php esc_attr_e('Mahasiswa Teknik UI', 'tugasin'); ?>">
                    </div>
                </div>

                <div class="tugasin-field-row">
                    <label class="tugasin-field-label">
                        <?php esc_html_e('Picture URL', 'tugasin'); ?>
                    </label>
                    <div class="tugasin-field-input tugasin-testimonial-image-field">
                        <div class="tugasin-testimonial-preview">
                            <?php if ($image): ?>
                                <img src="<?php echo esc_url($image); ?>" alt="">
                            <?php else: ?>
                                <span class="dashicons dashicons-admin-users"></span>
                            <?php endif; ?>
                        </div>
                        <input type="url" name="<?php echo esc_attr($option_name); ?>[<?php echo $i; ?>][image]"
                            value="<?php echo esc_url($image); ?>" placeholder="https://..."
                            class="tugasin-testimonial-image-input">
                        <button type="button" class="button tugasin-upload-image-btn">
                            <?php esc_html_e('Upload', 'tugasin'); ?>
                        </button>
                    </div>
                </div>

                <div class="tugasin-field-row">
                    <label class="tugasin-field-label">
                        <?php esc_html_e('Picture Alt Text', 'tugasin'); ?>
                    </label>
                    <div class="tugasin-field-input">
                        <input type="text" name="<?php echo esc_attr($option_name); ?>[<?php echo $i; ?>][alt]"
                            value="<?php echo esc_attr($alt); ?>"
                            placeholder="<?php esc_attr_e('Photo of Ahmad R.', 'tugasin'); ?>">
                    </div>
                </div>

                <div class="tugasin-field-row">
                    <label class="tugasin-field-label">
                        <?php esc_html_e('Testimonial Text', 'tugasin'); ?>
                    </label>
                    <div class="tugasin-field-input">
                        <textarea name="<?php echo esc_attr($option_name); ?>[<?php echo $i; ?>][text]" rows="3"
                            placeholder="<?php esc_attr_e('Customer testimonial...', 'tugasin'); ?>"><?php echo esc_textarea($text); ?></textarea>
                    </div>
                </div>
            </div>
        <?php endfor;
    }
}
