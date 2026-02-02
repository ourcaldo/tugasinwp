<?php
/**
 * Settings Tab Interface
 *
 * Interface that all settings tab classes must implement.
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

interface Tugasin_Settings_Tab_Interface
{

    /**
     * Get the tab ID (used for data-section attribute)
     *
     * @return string
     */
    public function get_id();

    /**
     * Get the tab label for sidebar navigation
     *
     * @return string
     */
    public function get_label();

    /**
     * Get the tab icon (dashicon name without 'dashicons-' prefix)
     *
     * @return string
     */
    public function get_icon();

    /**
     * Get the tab group (for sidebar organization)
     * Returns: 'settings', 'seo', 'performance', 'tools'
     *
     * @return string
     */
    public function get_group();

    /**
     * Register settings for this tab
     *
     * @param string $option_group The option group name
     */
    public function register_settings($option_group);

    /**
     * Render the tab content
     */
    public function render();
}
