<?php
/**
 * About Tab
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Tab_About implements Tugasin_Settings_Tab_Interface
{

    use Tugasin_Settings_Fields;

    public function get_id()
    {
        return 'about';
    }

    public function get_label()
    {
        return __('About', 'tugasin');
    }

    public function get_icon()
    {
        return 'info';
    }

    public function get_group()
    {
        return 'tools';
    }

    public function register_settings($option_group)
    {
        // No settings to register for About tab
    }

    public function render()
    {
        ?>
        <section id="section-about" class="tugasin-settings-section">
            <div class="tugasin-section-header">
                <h2><span class="dashicons dashicons-info"></span>
                    <?php esc_html_e('About TugasinWP', 'tugasin'); ?>
                </h2>
                <p>
                    <?php esc_html_e('Theme information and credits.', 'tugasin'); ?>
                </p>
            </div>

            <div class="tugasin-section-card">
                <div class="tugasin-about-content">
                    <div class="tugasin-about-logo">
                        <span class="dashicons dashicons-bolt"
                            style="font-size: 48px; width: 48px; height: 48px; color: var(--tugasin-primary, #4f46e5);"></span>
                    </div>
                    <h3>
                        <?php esc_html_e('TugasinWP Theme', 'tugasin'); ?>
                    </h3>
                    <p class="version">
                        <?php printf(__('Version %s', 'tugasin'), TUGASIN_VERSION); ?>
                    </p>
                    <p>
                        <?php esc_html_e('A modern WordPress theme designed for academic assistance services.', 'tugasin'); ?>
                    </p>

                    <div class="tugasin-about-features">
                        <h4>
                            <?php esc_html_e('Features', 'tugasin'); ?>
                        </h4>
                        <ul>
                            <li><span class="dashicons dashicons-yes"></span>
                                <?php esc_html_e('Responsive Design', 'tugasin'); ?>
                            </li>
                            <li><span class="dashicons dashicons-yes"></span>
                                <?php esc_html_e('WhatsApp Integration', 'tugasin'); ?>
                            </li>
                            <li><span class="dashicons dashicons-yes"></span>
                                <?php esc_html_e('Schema Markup Support', 'tugasin'); ?>
                            </li>
                            <li><span class="dashicons dashicons-yes"></span>
                                <?php esc_html_e('Custom Post Types', 'tugasin'); ?>
                            </li>
                            <li><span class="dashicons dashicons-yes"></span>
                                <?php esc_html_e('SEO Optimized', 'tugasin'); ?>
                            </li>
                            <li><span class="dashicons dashicons-yes"></span>
                                <?php esc_html_e('Performance Focused', 'tugasin'); ?>
                            </li>
                        </ul>
                    </div>

                    <div class="tugasin-about-links">
                        <a href="https://tugasin.id" target="_blank" class="tugasin-btn tugasin-btn-primary">
                            <span class="dashicons dashicons-external"></span>
                            <?php esc_html_e('Visit Website', 'tugasin'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }
}
