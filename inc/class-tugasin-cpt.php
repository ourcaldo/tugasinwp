<?php
/**
 * Custom Post Types Class
 *
 * Registers Major and University CPTs with their taxonomies.
 *
 * @package TugasinWP
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tugasin_CPT {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'init', array( $this, 'register_major_cpt' ) );
        add_action( 'init', array( $this, 'register_university_cpt' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );
    }

    /**
     * Register Major CPT (Kamus Jurusan)
     */
    public function register_major_cpt() {
        $labels = array(
            'name'                  => _x( 'Majors', 'Post type general name', 'tugasin' ),
            'singular_name'         => _x( 'Major', 'Post type singular name', 'tugasin' ),
            'menu_name'             => _x( 'Majors', 'Admin Menu text', 'tugasin' ),
            'name_admin_bar'        => _x( 'Major', 'Add New on Toolbar', 'tugasin' ),
            'add_new'               => __( 'Add New', 'tugasin' ),
            'add_new_item'          => __( 'Add New Major', 'tugasin' ),
            'new_item'              => __( 'New Major', 'tugasin' ),
            'edit_item'             => __( 'Edit Major', 'tugasin' ),
            'view_item'             => __( 'View Major', 'tugasin' ),
            'all_items'             => __( 'All Majors', 'tugasin' ),
            'search_items'          => __( 'Search Majors', 'tugasin' ),
            'parent_item_colon'     => __( 'Parent Major:', 'tugasin' ),
            'not_found'             => __( 'No majors found.', 'tugasin' ),
            'not_found_in_trash'    => __( 'No majors found in Trash.', 'tugasin' ),
            'featured_image'        => _x( 'Major Cover Image', 'Overrides the "Featured Image" phrase', 'tugasin' ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the "Set featured image" phrase', 'tugasin' ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the "Remove featured image" phrase', 'tugasin' ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the "Use as featured image" phrase', 'tugasin' ),
            'archives'              => _x( 'Major Archives', 'The post type archive label used in nav menus', 'tugasin' ),
            'insert_into_item'      => _x( 'Insert into major', 'Overrides the "Insert into post" phrase', 'tugasin' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this major', 'Overrides the "Uploaded to this post" phrase', 'tugasin' ),
            'filter_items_list'     => _x( 'Filter majors list', 'Screen reader text', 'tugasin' ),
            'items_list_navigation' => _x( 'Majors list navigation', 'Screen reader text', 'tugasin' ),
            'items_list'            => _x( 'Majors list', 'Screen reader text', 'tugasin' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'jurusan', 'with_front' => false ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-welcome-learn-more',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'show_in_rest'       => true,
        );

        register_post_type( 'major', $args );
    }

    /**
     * Register University CPT (Kamus Kampus)
     */
    public function register_university_cpt() {
        $labels = array(
            'name'                  => _x( 'Universities', 'Post type general name', 'tugasin' ),
            'singular_name'         => _x( 'University', 'Post type singular name', 'tugasin' ),
            'menu_name'             => _x( 'Universities', 'Admin Menu text', 'tugasin' ),
            'name_admin_bar'        => _x( 'University', 'Add New on Toolbar', 'tugasin' ),
            'add_new'               => __( 'Add New', 'tugasin' ),
            'add_new_item'          => __( 'Add New University', 'tugasin' ),
            'new_item'              => __( 'New University', 'tugasin' ),
            'edit_item'             => __( 'Edit University', 'tugasin' ),
            'view_item'             => __( 'View University', 'tugasin' ),
            'all_items'             => __( 'All Universities', 'tugasin' ),
            'search_items'          => __( 'Search Universities', 'tugasin' ),
            'parent_item_colon'     => __( 'Parent University:', 'tugasin' ),
            'not_found'             => __( 'No universities found.', 'tugasin' ),
            'not_found_in_trash'    => __( 'No universities found in Trash.', 'tugasin' ),
            'featured_image'        => _x( 'University Cover', 'Overrides the "Featured Image" phrase', 'tugasin' ),
            'set_featured_image'    => _x( 'Set cover image', 'Overrides the "Set featured image" phrase', 'tugasin' ),
            'remove_featured_image' => _x( 'Remove cover image', 'Overrides the "Remove featured image" phrase', 'tugasin' ),
            'use_featured_image'    => _x( 'Use as cover image', 'Overrides the "Use as featured image" phrase', 'tugasin' ),
            'archives'              => _x( 'University Archives', 'The post type archive label used in nav menus', 'tugasin' ),
            'insert_into_item'      => _x( 'Insert into university', 'Overrides the "Insert into post" phrase', 'tugasin' ),
            'uploaded_to_this_item' => _x( 'Uploaded to this university', 'Overrides the "Uploaded to this post" phrase', 'tugasin' ),
            'filter_items_list'     => _x( 'Filter universities list', 'Screen reader text', 'tugasin' ),
            'items_list_navigation' => _x( 'Universities list navigation', 'Screen reader text', 'tugasin' ),
            'items_list'            => _x( 'Universities list', 'Screen reader text', 'tugasin' ),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array( 'slug' => 'perguruan-tinggi', 'with_front' => false ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 6,
            'menu_icon'          => 'dashicons-building',
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
            'show_in_rest'       => true,
        );

        register_post_type( 'university', $args );
    }

    /**
     * Register Taxonomies
     */
    public function register_taxonomies() {
        // Major Category (SAINTEK, SOSHUM, etc.)
        $category_labels = array(
            'name'              => _x( 'Major Categories', 'taxonomy general name', 'tugasin' ),
            'singular_name'     => _x( 'Major Category', 'taxonomy singular name', 'tugasin' ),
            'search_items'      => __( 'Search Categories', 'tugasin' ),
            'all_items'         => __( 'All Categories', 'tugasin' ),
            'parent_item'       => __( 'Parent Category', 'tugasin' ),
            'parent_item_colon' => __( 'Parent Category:', 'tugasin' ),
            'edit_item'         => __( 'Edit Category', 'tugasin' ),
            'update_item'       => __( 'Update Category', 'tugasin' ),
            'add_new_item'      => __( 'Add New Category', 'tugasin' ),
            'new_item_name'     => __( 'New Category Name', 'tugasin' ),
            'menu_name'         => __( 'Categories', 'tugasin' ),
        );

        register_taxonomy( 'major_category', array( 'major' ), array(
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'kategori-jurusan' ),
            'show_in_rest'      => true,
        ) );

        // University Type (PTN, PTS, Kedinasan)
        $type_labels = array(
            'name'              => _x( 'University Types', 'taxonomy general name', 'tugasin' ),
            'singular_name'     => _x( 'University Type', 'taxonomy singular name', 'tugasin' ),
            'search_items'      => __( 'Search Types', 'tugasin' ),
            'all_items'         => __( 'All Types', 'tugasin' ),
            'edit_item'         => __( 'Edit Type', 'tugasin' ),
            'update_item'       => __( 'Update Type', 'tugasin' ),
            'add_new_item'      => __( 'Add New Type', 'tugasin' ),
            'new_item_name'     => __( 'New Type Name', 'tugasin' ),
            'menu_name'         => __( 'Types', 'tugasin' ),
        );

        register_taxonomy( 'university_type', array( 'university' ), array(
            'hierarchical'      => true,
            'labels'            => $type_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'tipe-pt' ),
            'show_in_rest'      => true,
        ) );

        // Accreditation (Unggul, Baik Sekali, Baik)
        $accreditation_labels = array(
            'name'              => _x( 'Accreditations', 'taxonomy general name', 'tugasin' ),
            'singular_name'     => _x( 'Accreditation', 'taxonomy singular name', 'tugasin' ),
            'search_items'      => __( 'Search Accreditations', 'tugasin' ),
            'all_items'         => __( 'All Accreditations', 'tugasin' ),
            'edit_item'         => __( 'Edit Accreditation', 'tugasin' ),
            'update_item'       => __( 'Update Accreditation', 'tugasin' ),
            'add_new_item'      => __( 'Add New Accreditation', 'tugasin' ),
            'new_item_name'     => __( 'New Accreditation Name', 'tugasin' ),
            'menu_name'         => __( 'Accreditation', 'tugasin' ),
        );

        register_taxonomy( 'accreditation', array( 'university' ), array(
            'hierarchical'      => true,
            'labels'            => $accreditation_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array( 'slug' => 'akreditasi' ),
            'show_in_rest'      => true,
        ) );
    }
}
