<?php
/**
 * Settings Tab Factory
 *
 * Manages loading and instantiation of settings tab classes.
 *
 * @package TugasinWP
 * @since 2.20.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Tugasin_Settings_Factory
{

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Array of loaded tab instances
     */
    private $tabs = array();

    /**
     * Tab groups for sidebar organization
     */
    private $groups = array(
        'settings' => 'Settings',
        'seo' => 'SEO',
        'performance' => 'Performance',
        'tools' => 'Tools',
    );

    /**
     * Get singleton instance
     */
    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor - load all tab classes
     */
    private function __construct()
    {
        $this->load_dependencies();
        $this->load_tabs();
    }

    /**
     * Load required files
     */
    private function load_dependencies()
    {
        $settings_dir = TUGASIN_DIR . '/inc/settings/';

        // Load trait first
        require_once $settings_dir . 'trait-tugasin-settings-fields.php';

        // Load interface
        require_once $settings_dir . 'interface-tugasin-settings-tab.php';
    }

    /**
     * Load all tab classes from tabs directory
     */
    private function load_tabs()
    {
        $tabs_dir = TUGASIN_DIR . '/inc/settings/tabs/';

        // Define tabs in order they should appear
        $tab_files = array(
            'class-tab-general.php',
            'class-tab-whatsapp.php',
            'class-tab-branding.php',
            'class-tab-pages.php',
            'class-tab-data.php',
            'class-tab-schema.php',
            'class-tab-optimization.php',
            'class-tab-related.php',
            'class-tab-featured-image.php',
            'class-tab-import-demo.php',
            'class-tab-import-export.php',
            'class-tab-about.php',
        );

        foreach ($tab_files as $file) {
            $file_path = $tabs_dir . $file;
            if (file_exists($file_path)) {
                require_once $file_path;

                // Extract class name from file name
                $class_name = 'Tugasin_Tab_' . str_replace(
                    array('class-tab-', '.php', '-'),
                    array('', '', '_'),
                    $file
                );
                $class_name = implode('_', array_map('ucfirst', explode('_', $class_name)));

                if (class_exists($class_name)) {
                    $tab = new $class_name();
                    if ($tab instanceof Tugasin_Settings_Tab_Interface) {
                        $this->tabs[$tab->get_id()] = $tab;
                    }
                }
            }
        }
    }

    /**
     * Get all loaded tabs
     *
     * @return array
     */
    public function get_tabs()
    {
        return $this->tabs;
    }

    /**
     * Get tabs organized by group
     *
     * @return array
     */
    public function get_tabs_by_group()
    {
        $grouped = array();

        foreach ($this->groups as $group_id => $group_label) {
            $grouped[$group_id] = array(
                'label' => $group_label,
                'tabs' => array(),
            );
        }

        foreach ($this->tabs as $tab) {
            $group = $tab->get_group();
            if (isset($grouped[$group])) {
                $grouped[$group]['tabs'][] = $tab;
            }
        }

        // Remove empty groups
        return array_filter($grouped, function ($group) {
            return !empty($group['tabs']);
        });
    }

    /**
     * Get a specific tab by ID
     *
     * @param string $id Tab ID
     * @return Tugasin_Settings_Tab_Interface|null
     */
    public function get_tab($id)
    {
        return isset($this->tabs[$id]) ? $this->tabs[$id] : null;
    }

    /**
     * Register all tab settings
     *
     * @param string $option_group The option group name
     */
    public function register_all_settings($option_group)
    {
        foreach ($this->tabs as $tab) {
            $tab->register_settings($option_group);
        }
    }

    /**
     * Get group labels for translation
     *
     * @return array
     */
    public function get_group_labels()
    {
        return array(
            'settings' => __('Settings', 'tugasin'),
            'seo' => __('SEO', 'tugasin'),
            'performance' => __('Performance', 'tugasin'),
            'tools' => __('Tools', 'tugasin'),
        );
    }
}
