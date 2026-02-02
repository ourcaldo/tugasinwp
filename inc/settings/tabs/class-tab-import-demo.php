<?php
/**
 * Import Demo Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_Import_Demo implements Tugasin_Settings_Tab_Interface
{

    use Tugasin_Settings_Fields;

    public function get_id()
    {
        return 'import-demo';
    }

    public function get_label()
    {
        return __('Import Demo Data', 'tugasin');
    }

    public function get_icon()
    {
        return 'download';
    }

    public function get_group()
    {
        return 'tools';
    }

    public function register_settings($option_group)
    {
        // No settings to register - this tab handles actions
    }

    public function render()
    {
        $demo_result = get_transient('tugasin_demo_result');
        if ($demo_result) {
            delete_transient('tugasin_demo_result');
        }
        ?>
        <section id="section-import-demo" class="tugasin-settings-section">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-download"></span>
                    <?php esc_html_e('Import Demo Data', 'tugasin'); ?>
                </h2>
                <p>
                    <?php esc_html_e('Quick setup to get your site looking like the demo.', 'tugasin'); ?>
                </p>
            </div>

            <?php if ($demo_result): ?>
                <div
                    class="tugasin-notice <?php echo $demo_result['success'] ? 'tugasin-notice-success' : 'tugasin-notice-error'; ?>">
                    <span class="dashicons dashicons-<?php echo $demo_result['success'] ? 'yes-alt' : 'warning'; ?>"></span>
                    <?php echo esc_html($demo_result['message']); ?>
                </div>
            <?php endif; ?>

            <div class="tugasin-section-card">
                <h3 class="tugasin-section-title">
                    <span class="dashicons dashicons-welcome-widgets-menus"></span>
                    <?php esc_html_e('One-Click Setup', 'tugasin'); ?>
                </h3>
                <p class="tugasin-section-desc">
                    <?php esc_html_e('This will create all necessary pages and configure basic settings automatically.', 'tugasin'); ?>
                </p>

                <div class="tugasin-demo-actions">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="tugasin_one_click_setup">
                        <?php wp_nonce_field('tugasin_demo_action', 'tugasin_demo_nonce'); ?>
                        <button type="submit" class="tugasin-btn tugasin-btn-primary tugasin-btn-lg">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php esc_html_e('Run One-Click Setup', 'tugasin'); ?>
                        </button>
                    </form>
                </div>

                <div class="tugasin-demo-info">
                    <h4>
                        <?php esc_html_e('This will:', 'tugasin'); ?>
                    </h4>
                    <ul>
                        <li><span class="dashicons dashicons-yes"></span>
                            <?php esc_html_e('Create Home, Blog, and service pages', 'tugasin'); ?>
                        </li>
                        <li><span class="dashicons dashicons-yes"></span>
                            <?php esc_html_e('Configure WordPress reading settings', 'tugasin'); ?>
                        </li>
                        <li><span class="dashicons dashicons-yes"></span>
                            <?php esc_html_e('Set up page mappings', 'tugasin'); ?>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <?php
    }
}
