<?php
/**
 * Theme Settings Class (Refactored)
 *
 * Main controller for the admin settings page.
 * Delegates rendering and registration to individual tab classes.
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Settings
{

    /**
     * Option group name
     */
    private $option_group = 'tugasin_settings';

    /**
     * Settings factory instance
     */
    private $factory = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_tugasin_one_click_setup', array($this, 'handle_one_click_setup'));

        // Import/Export AJAX handlers
        add_action('wp_ajax_tugasin_export_settings', array($this, 'handle_export_settings'));
        add_action('wp_ajax_tugasin_import_settings', array($this, 'handle_import_settings'));
    }

    /**
     * Get factory instance (lazy loading)
     */
    private function get_factory()
    {
        if (null === $this->factory) {
            require_once TUGASIN_DIR . '/inc/settings/class-tugasin-settings-factory.php';
            $this->factory = Tugasin_Settings_Factory::get_instance();
        }
        return $this->factory;
    }

    /**
     * Handle one-click setup form submission
     */
    public function handle_one_click_setup()
    {
        if (!isset($_POST['tugasin_demo_nonce']) || !wp_verify_nonce($_POST['tugasin_demo_nonce'], 'tugasin_demo_action')) {
            wp_die(__('Security check failed.', 'tugasin'));
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have permission to perform this action.', 'tugasin'));
        }

        $result = $this->run_one_click_setup();
        set_transient('tugasin_demo_result', $result, 60);

        wp_redirect(admin_url('admin.php?page=tugasin-settings&tab=import-demo&demo_complete=1'));
        exit;
    }

    /**
     * Run the one-click setup
     */
    private function run_one_click_setup()
    {
        $messages = array();
        $success = true;

        $pages_result = $this->create_demo_pages();
        $messages[] = $pages_result['message'];

        $reading_result = $this->configure_reading_settings();
        $messages[] = $reading_result['message'];
        if (!$reading_result['success']) {
            $success = false;
        }

        $mapping_result = $this->update_page_mappings();
        $messages[] = $mapping_result['message'];

        return array(
            'success' => $success,
            'message' => implode(' ', $messages),
        );
    }

    /**
     * Update page mappings
     */
    private function update_page_mappings()
    {
        $updated = 0;

        $layanan = get_page_by_path('layanan');
        if ($layanan) {
            update_option('tugasin_page_layanan', $layanan->ID);
            $updated++;
        }

        $mappings = array(
            'layanan/joki-skripsi' => 'tugasin_page_joki_skripsi',
            'layanan/joki-makalah' => 'tugasin_page_joki_makalah',
            'layanan/joki-tugas' => 'tugasin_page_joki_tugas',
            'layanan/cek-plagiarisme' => 'tugasin_page_cek_plagiarism',
        );

        foreach ($mappings as $path => $option_key) {
            $page = get_page_by_path($path);
            if ($page) {
                update_option($option_key, $page->ID);
                $updated++;
            }
        }

        if ($updated > 0) {
            return array(
                'success' => true,
                'message' => sprintf(__('Page mappings updated (%d pages mapped).', 'tugasin'), $updated),
            );
        }

        return array(
            'success' => true,
            'message' => __('No page mappings to update.', 'tugasin'),
        );
    }

    /**
     * Add settings page as top-level menu
     */
    public function add_settings_page()
    {
        add_menu_page(
            __('TugasinWP Settings', 'tugasin'),
            __('TugasinWP', 'tugasin'),
            'manage_options',
            'tugasin-settings',
            array($this, 'render_settings_page'),
            'dashicons-bolt',
            3
        );
    }

    /**
     * Register all settings via factory
     */
    public function register_settings()
    {
        $this->get_factory()->register_all_settings($this->option_group);
    }

    /**
     * Render settings page with sidebar navigation
     */
    public function render_settings_page()
    {
        // Enqueue dependencies
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();
        wp_enqueue_style(
            'tugasin-admin-settings',
            TUGASIN_URI . '/assets/css/admin-settings.css',
            array(),
            TUGASIN_VERSION
        );

        $factory = $this->get_factory();
        $tabs = $factory->get_tabs();
        $groups = $factory->get_tabs_by_group();
        $group_labels = $factory->get_group_labels();
        $current_tab = isset($_GET['tab']) ? sanitize_key($_GET['tab']) : 'general';
        ?>
        <div class="tugasin-settings-wrap">
            <!-- Header Banner -->
            <div class="tugasin-settings-header">
                <div class="header-icon">
                    <span class="dashicons dashicons-bolt"></span>
                </div>
                <div class="header-content">
                    <h1><?php esc_html_e('TugasinWP Settings', 'tugasin'); ?></h1>
                    <p class="header-subtitle">
                        <?php esc_html_e('Configure your theme settings and customize your site.', 'tugasin'); ?>
                    </p>
                </div>
                <span class="version-badge">v<?php echo esc_html(TUGASIN_VERSION); ?></span>
            </div>

            <form action="options.php" method="post" id="tugasin-settings-form" style="display: contents;">
                <?php settings_fields($this->option_group); ?>
                <input type="hidden" name="tugasin_active_tab" id="tugasin_active_tab"
                    value="<?php echo esc_attr($current_tab); ?>">

                <!-- Sidebar Navigation -->
                <aside class="tugasin-settings-sidebar">
                    <nav class="tugasin-settings-nav">
                        <?php foreach ($groups as $group_id => $group): ?>
                            <div class="tugasin-nav-separator"></div>
                            <span class="tugasin-nav-label"><?php echo esc_html($group_labels[$group_id]); ?></span>

                            <?php foreach ($group['tabs'] as $tab):
                                $is_first = ($tab->get_id() === 'general');
                                ?>
                                <button type="button" class="tugasin-nav-item<?php echo $is_first ? ' active' : ''; ?>"
                                    data-section="<?php echo esc_attr($tab->get_id()); ?>">
                                    <span class="dashicons dashicons-<?php echo esc_attr($tab->get_icon()); ?>"></span>
                                    <?php echo esc_html($tab->get_label()); ?>
                                </button>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </nav>
                </aside>

                <!-- Main Content Area -->
                <main class="tugasin-settings-main">
                    <?php foreach ($tabs as $tab): ?>
                        <?php $tab->render(); ?>
                    <?php endforeach; ?>

                    <!-- Save Button -->
                    <div class="tugasin-save-bar">
                        <button type="submit" class="tugasin-btn tugasin-btn-primary tugasin-btn-lg">
                            <span class="dashicons dashicons-saved"></span>
                            <?php esc_html_e('Save All Settings', 'tugasin'); ?>
                        </button>
                    </div>
                </main>
            </form>
        </div>

        <?php $this->render_admin_scripts(); ?>
    <?php
    }

    /**
     * Render admin JavaScript
     */
    private function render_admin_scripts()
    {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                // Tab navigation
                $('.tugasin-nav-item').on('click', function () {
                    var section = $(this).data('section');

                    $('.tugasin-nav-item').removeClass('active');
                    $(this).addClass('active');

                    $('.tugasin-settings-section').removeClass('active');
                    $('#section-' + section).addClass('active');

                    $('#tugasin_active_tab').val(section);

                    history.replaceState(null, '', '?page=tugasin-settings&tab=' + section);
                });

                // Restore active tab
                var activeTab = $('#tugasin_active_tab').val() || 'general';
                $('.tugasin-nav-item[data-section="' + activeTab + '"]').trigger('click');

                // Color picker init
                $('.tugasin-color-picker').wpColorPicker();

                // Media uploader
                $('.tugasin-upload-btn').on('click', function (e) {
                    e.preventDefault();
                    var target = $(this).data('target');
                    var frame = wp.media({
                        title: '<?php echo esc_js(__('Select Image', 'tugasin')); ?>',
                        multiple: false
                    });
                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $('#' + target).val(attachment.id);
                        var $preview = $(this).closest('.tugasin-media-field').find('.tugasin-media-preview-box');
                        $preview.addClass('has-image').html('<img src="' + attachment.url + '" alt="">');
                        $(this).closest('.tugasin-media-field').find('.tugasin-remove-btn').show();
                    }.bind(this));
                    frame.open();
                });

                $('.tugasin-remove-btn').on('click', function (e) {
                    e.preventDefault();
                    var target = $(this).data('target');
                    $('#' + target).val('');
                    var $preview = $(this).closest('.tugasin-media-field').find('.tugasin-media-preview-box');
                    $preview.removeClass('has-image').html('<span class="placeholder-icon dashicons dashicons-format-image"></span>');
                    $(this).hide();
                });

                // Testimonial image uploader
                $('.tugasin-upload-image-btn').on('click', function (e) {
                    e.preventDefault();
                    var $input = $(this).siblings('.tugasin-testimonial-image-input');
                    var $preview = $(this).siblings('.tugasin-testimonial-preview');
                    var frame = wp.media({
                        title: '<?php echo esc_js(__('Select Image', 'tugasin')); ?>',
                        multiple: false
                    });
                    frame.on('select', function () {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $input.val(attachment.url);
                        $preview.html('<img src="' + attachment.url + '" alt="">');
                    });
                    frame.open();
                });

                // Export settings
                $('#tugasin-export-btn').on('click', function () {
                    var $btn = $(this);
                    $btn.prop('disabled', true);

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'tugasin_export_settings',
                            nonce: '<?php echo wp_create_nonce('tugasin_export_nonce'); ?>'
                        },
                        success: function (response) {
                            if (response.success) {
                                var blob = new Blob([JSON.stringify(response.data, null, 2)], { type: 'application/json' });
                                var url = URL.createObjectURL(blob);
                                var a = document.createElement('a');
                                a.href = url;
                                a.download = 'tugasin-settings-' + new Date().toISOString().slice(0, 10) + '.json';
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                                URL.revokeObjectURL(url);
                            } else {
                                alert(response.data || 'Export failed');
                            }
                        },
                        error: function () {
                            alert('Export failed. Please try again.');
                        },
                        complete: function () {
                            $btn.prop('disabled', false);
                        }
                    });
                });

                // Import file selection
                $('#tugasin-import-file').on('change', function () {
                    var filename = $(this).val().split('\\').pop();
                    $('#tugasin-import-filename').text(filename);
                    $('#tugasin-import-btn').prop('disabled', !filename);
                });

                // Import settings
                $('#tugasin-import-btn').on('click', function () {
                    var file = $('#tugasin-import-file')[0].files[0];
                    if (!file) return;

                    var $btn = $(this);
                    var $result = $('#tugasin-import-result');
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        try {
                            var settings = JSON.parse(e.target.result);
                            $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Importing...');

                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'tugasin_import_settings',
                                    nonce: '<?php echo wp_create_nonce('tugasin_import_nonce'); ?>',
                                    settings: JSON.stringify(settings)
                                },
                                success: function (response) {
                                    if (response.success) {
                                        $result.html('<div class="tugasin-notice tugasin-notice-success">' + response.data + '</div>').slideDown();
                                        setTimeout(function () {
                                            location.reload();
                                        }, 1500);
                                    } else {
                                        $result.html('<div class="tugasin-notice tugasin-notice-error">' + (response.data || 'Import failed') + '</div>').slideDown();
                                    }
                                },
                                error: function () {
                                    $result.html('<div class="tugasin-notice tugasin-notice-error">Import failed.</div>').slideDown();
                                },
                                complete: function () {
                                    $btn.prop('disabled', false).html('<span class="dashicons dashicons-yes"></span> Import Settings');
                                    $('#tugasin-import-file').val('');
                                    $('#tugasin-import-filename').text('');
                                }
                            });
                        } catch (err) {
                            $result.html('<div class="tugasin-notice tugasin-notice-error">Invalid JSON file.</div>').slideDown();
                            $btn.prop('disabled', false);
                        }
                    };
                    reader.readAsText(file);
                });
            });
        </script>
        <?php
    }

    /**
     * Create demo pages
     */
    private function create_demo_pages()
    {
        $pages_created = 0;
        $pages_skipped = 0;

        $pages = array(
            'beranda' => array('title' => 'Beranda', 'template' => 'front-page.php'),
            'blog' => array('title' => 'Blog', 'template' => ''),
            'layanan' => array('title' => 'Layanan', 'template' => 'page-layanan.php'),
        );

        $subpages = array(
            'joki-skripsi' => array('title' => 'Joki Skripsi', 'parent' => 'layanan', 'template' => 'page-service.php'),
            'joki-makalah' => array('title' => 'Joki Makalah', 'parent' => 'layanan', 'template' => 'page-service.php'),
            'joki-tugas' => array('title' => 'Joki Tugas', 'parent' => 'layanan', 'template' => 'page-service.php'),
            'cek-plagiarisme' => array('title' => 'Cek Plagiarisme', 'parent' => 'layanan', 'template' => 'page-service.php'),
        );

        foreach ($pages as $slug => $data) {
            $existing = get_page_by_path($slug);
            if ($existing) {
                $pages_skipped++;
                continue;
            }

            $page_id = wp_insert_post(array(
                'post_title' => $data['title'],
                'post_name' => $slug,
                'post_status' => 'publish',
                'post_type' => 'page',
            ));

            if ($page_id && !is_wp_error($page_id) && $data['template']) {
                update_post_meta($page_id, '_wp_page_template', $data['template']);
            }

            if ($page_id && !is_wp_error($page_id)) {
                $pages_created++;
            }
        }

        $layanan = get_page_by_path('layanan');
        if ($layanan) {
            foreach ($subpages as $slug => $data) {
                $existing = get_page_by_path('layanan/' . $slug);
                if ($existing) {
                    $pages_skipped++;
                    continue;
                }

                $page_id = wp_insert_post(array(
                    'post_title' => $data['title'],
                    'post_name' => $slug,
                    'post_parent' => $layanan->ID,
                    'post_status' => 'publish',
                    'post_type' => 'page',
                ));

                if ($page_id && !is_wp_error($page_id) && $data['template']) {
                    update_post_meta($page_id, '_wp_page_template', $data['template']);
                }

                if ($page_id && !is_wp_error($page_id)) {
                    $pages_created++;
                }
            }
        }

        return array(
            'success' => true,
            'message' => sprintf(__('%d pages created, %d already existed.', 'tugasin'), $pages_created, $pages_skipped),
        );
    }

    /**
     * Configure reading settings
     */
    private function configure_reading_settings()
    {
        $home = get_page_by_path('beranda');
        $blog = get_page_by_path('blog');

        if ($home && $blog) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $home->ID);
            update_option('page_for_posts', $blog->ID);
            return array(
                'success' => true,
                'message' => __('Reading settings configured.', 'tugasin'),
            );
        }

        return array(
            'success' => false,
            'message' => __('Could not configure reading settings (pages not found).', 'tugasin'),
        );
    }

    /**
     * Handle export settings AJAX
     */
    public function handle_export_settings()
    {
        check_ajax_referer('tugasin_export_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied.', 'tugasin'));
        }

        global $wpdb;
        $options = $wpdb->get_results(
            "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'tugasin_%'"
        );

        $export = array(
            'version' => TUGASIN_VERSION,
            'exported_at' => current_time('mysql'),
            'settings' => array(),
        );

        foreach ($options as $option) {
            $value = maybe_unserialize($option->option_value);
            $export['settings'][$option->option_name] = $value;
        }

        wp_send_json_success($export);
    }

    /**
     * Handle import settings AJAX
     */
    public function handle_import_settings()
    {
        check_ajax_referer('tugasin_import_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('Permission denied.', 'tugasin'));
        }

        $settings = isset($_POST['settings']) ? json_decode(stripslashes($_POST['settings']), true) : null;

        if (!$settings || !isset($settings['settings'])) {
            wp_send_json_error(__('Invalid settings file.', 'tugasin'));
        }

        $imported = 0;
        foreach ($settings['settings'] as $key => $value) {
            if (strpos($key, 'tugasin_') === 0) {
                update_option($key, $value);
                $imported++;
            }
        }

        wp_send_json_success(sprintf(__('%d settings imported successfully.', 'tugasin'), $imported));
    }
}
