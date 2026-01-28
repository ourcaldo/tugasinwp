<?php
/**
 * Elementor Integration Class
 *
 * Registers Elementor theme locations and adds compatibility.
 *
 * @package TugasinWP
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tugasin_Elementor {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'after_setup_theme', array( $this, 'add_theme_support' ) );
        add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );
    }

    /**
     * Add Elementor theme support
     */
    public function add_theme_support() {
        // Add support for Elementor Pro theme locations
        add_theme_support( 'elementor' );

        // Add post type support for Elementor
        add_post_type_support( 'page', 'elementor' );
        add_post_type_support( 'post', 'elementor' );
        add_post_type_support( 'major', 'elementor' );
        add_post_type_support( 'university', 'elementor' );
    }

    /**
     * Register Elementor theme locations
     *
     * @param object $manager Location manager instance.
     */
    public function register_locations( $manager ) {
        // Register core theme locations individually (compatible with all Elementor Pro versions)
        $manager->register_core_location( 'header' );
        $manager->register_core_location( 'footer' );
        $manager->register_core_location( 'single' );
        $manager->register_core_location( 'archive' );
    }
}
