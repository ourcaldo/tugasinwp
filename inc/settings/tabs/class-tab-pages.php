<?php
/**
 * Pages Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_Pages implements Tugasin_Settings_Tab_Interface
{

    use Tugasin_Settings_Fields;

    /**
     * Page mappings configuration
     */
    private $page_mappings = array();

    public function __construct()
    {
        $this->page_mappings = array(
            'page_layanan' => array('label' => 'Layanan (Services)', 'slug' => 'layanan'),
            'page_joki_skripsi' => array('label' => 'Joki Skripsi', 'slug' => 'joki-skripsi'),
            'page_joki_makalah' => array('label' => 'Joki Makalah', 'slug' => 'joki-makalah'),
            'page_joki_tugas' => array('label' => 'Joki Tugas', 'slug' => 'joki-tugas'),
            'page_cek_plagiarism' => array('label' => 'Cek Plagiarism', 'slug' => 'cek-plagiarism'),
        );
    }

    public function get_id()
    {
        return 'pages';
    }

    public function get_label()
    {
        return __('Pages', 'tugasin');
    }

    public function get_icon()
    {
        return 'admin-page';
    }

    public function get_group()
    {
        return 'settings';
    }

    public function register_settings($option_group)
    {
        foreach ($this->page_mappings as $key => $data) {
            register_setting($option_group, 'tugasin_' . $key, array(
                'type' => 'integer',
                'sanitize_callback' => 'absint',
                'default' => 0,
            ));
        }
    }

    public function render()
    {
        ?>
        <section id="section-pages" class="tugasin-settings-section">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-admin-page"></span>
                    <?php esc_html_e('Page Mapping', 'tugasin'); ?>
                </h2>
                <p>
                    <?php esc_html_e('Select which pages correspond to each section of your site.', 'tugasin'); ?>
                </p>
            </div>

            <div class="tugasin-section-card">
                <table class="tugasin-page-table">
                    <thead>
                        <tr>
                            <th>
                                <?php esc_html_e('Section', 'tugasin'); ?>
                            </th>
                            <th>
                                <?php esc_html_e('Assigned Page', 'tugasin'); ?>
                            </th>
                            <th>
                                <?php esc_html_e('Fallback Slug', 'tugasin'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->page_mappings as $key => $data):
                            $option_name = 'tugasin_' . $key;
                            $value = get_option($option_name, 0);
                            ?>
                            <tr>
                                <td><strong>
                                        <?php echo esc_html($data['label']); ?>
                                    </strong></td>
                                <td>
                                    <?php
                                    wp_dropdown_pages(array(
                                        'name' => $option_name,
                                        'selected' => $value,
                                        'show_option_none' => __('— Select Page —', 'tugasin'),
                                        'option_none_value' => 0,
                                    ));
                                    ?>
                                </td>
                                <td><span class="slug-hint"><code><?php echo esc_html($data['slug']); ?></code></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="tugasin-section-card">
                <h3 class="tugasin-section-title">
                    <span class="dashicons dashicons-wordpress"></span>
                    <?php esc_html_e('WordPress Pages', 'tugasin'); ?>
                </h3>
                <p class="tugasin-section-desc">
                    <?php esc_html_e('These pages are configured elsewhere in WordPress.', 'tugasin'); ?>
                </p>

                <?php
                $reading_url = admin_url('options-reading.php');
                $home_id = get_option('page_on_front');
                $home_title = $home_id ? get_the_title($home_id) : __('Not set', 'tugasin');
                $blog_id = get_option('page_for_posts');
                $blog_title = $blog_id ? get_the_title($blog_id) : __('Not set', 'tugasin');
                ?>

                <table class="tugasin-readonly-table">
                    <tr>
                        <td class="page-name">
                            <?php esc_html_e('Home Page', 'tugasin'); ?>
                        </td>
                        <td class="page-value">
                            <?php echo esc_html($home_title); ?>
                        </td>
                        <td class="page-action">
                            <a href="<?php echo esc_url($reading_url); ?>" class="button button-small">
                                <?php esc_html_e('Change', 'tugasin'); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="page-name">
                            <?php esc_html_e('Blog Page', 'tugasin'); ?>
                        </td>
                        <td class="page-value">
                            <?php echo esc_html($blog_title); ?>
                        </td>
                        <td class="page-action">
                            <a href="<?php echo esc_url($reading_url); ?>" class="button button-small">
                                <?php esc_html_e('Change', 'tugasin'); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="page-name">
                            <?php esc_html_e('Kamus Jurusan', 'tugasin'); ?>
                        </td>
                        <td class="page-value"><code><?php echo esc_html(get_post_type_archive_link('major')); ?></code></td>
                        <td class="page-action"><em>
                                <?php esc_html_e('CPT Archive', 'tugasin'); ?>
                            </em></td>
                    </tr>
                    <tr>
                        <td class="page-name">
                            <?php esc_html_e('Kamus Kampus', 'tugasin'); ?>
                        </td>
                        <td class="page-value"><code><?php echo esc_html(get_post_type_archive_link('university')); ?></code>
                        </td>
                        <td class="page-action"><em>
                                <?php esc_html_e('CPT Archive', 'tugasin'); ?>
                            </em></td>
                    </tr>
                </table>
            </div>
        </section>
        <?php
    }
}
