<?php
/**
 * Import & Export Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_Import_Export implements Tugasin_Settings_Tab_Interface
{

    use Tugasin_Settings_Fields;

    public function get_id()
    {
        return 'import-export';
    }

    public function get_label()
    {
        return __('Import & Export', 'tugasin');
    }

    public function get_icon()
    {
        return 'database-export';
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
        ?>
        <section id="section-import-export" class="tugasin-settings-section">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-database-export"></span>
                    <?php esc_html_e('Import & Export Settings', 'tugasin'); ?>
                </h2>
                <p>
                    <?php esc_html_e('Backup and restore your theme settings.', 'tugasin'); ?>
                </p>
            </div>

            <div class="tugasin-section-card">
                <h3 class="tugasin-section-title">
                    <span class="dashicons dashicons-download"></span>
                    <?php esc_html_e('Export Settings', 'tugasin'); ?>
                </h3>
                <p class="tugasin-section-desc">
                    <?php esc_html_e('Download all TugasinWP settings as a JSON file.', 'tugasin'); ?>
                </p>

                <button type="button" id="tugasin-export-btn" class="tugasin-btn tugasin-btn-secondary">
                    <span class="dashicons dashicons-download"></span>
                    <?php esc_html_e('Export Settings', 'tugasin'); ?>
                </button>
            </div>

            <div class="tugasin-section-card">
                <h3 class="tugasin-section-title">
                    <span class="dashicons dashicons-upload"></span>
                    <?php esc_html_e('Import Settings', 'tugasin'); ?>
                </h3>
                <p class="tugasin-section-desc">
                    <?php esc_html_e('Upload a previously exported JSON file to restore settings.', 'tugasin'); ?>
                </p>

                <div class="tugasin-import-zone">
                    <input type="file" id="tugasin-import-file" accept=".json" style="display: none;">
                    <label for="tugasin-import-file" class="tugasin-btn tugasin-btn-secondary">
                        <span class="dashicons dashicons-upload"></span>
                        <?php esc_html_e('Choose File', 'tugasin'); ?>
                    </label>
                    <span id="tugasin-import-filename"></span>
                </div>

                <button type="button" id="tugasin-import-btn" class="tugasin-btn tugasin-btn-primary" disabled>
                    <span class="dashicons dashicons-yes"></span>
                    <?php esc_html_e('Import Settings', 'tugasin'); ?>
                </button>

                <div id="tugasin-import-result" class="tugasin-import-result" style="display: none;"></div>
            </div>
        </section>
        <?php
    }
}
