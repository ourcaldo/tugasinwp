<?php
/**
 * ACF Field Groups Class
 *
 * Registers ACF field groups for Major and University CPTs.
 *
 * @package TugasinWP
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tugasin_ACF {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'acf/init', array( $this, 'register_major_fields' ) );
        add_action( 'acf/init', array( $this, 'register_university_fields' ) );
    }

    /**
     * Register Major ACF Fields
     */
    public function register_major_fields() {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        acf_add_local_field_group( array(
            'key'      => 'group_major_fields',
            'title'    => __( 'Major Details', 'tugasin' ),
            'fields'   => array(
                // Icon Settings
                array(
                    'key'           => 'field_major_icon_class',
                    'label'         => __( 'Icon Class', 'tugasin' ),
                    'name'          => 'major_icon_class',
                    'type'          => 'text',
                    'instructions'  => __( 'Font Awesome class (e.g., fa-laptop-code)', 'tugasin' ),
                    'default_value' => 'fa-graduation-cap',
                ),
                array(
                    'key'   => 'field_major_icon_bg_color',
                    'label' => __( 'Icon Background Color', 'tugasin' ),
                    'name'  => 'major_icon_bg_color',
                    'type'  => 'color_picker',
                    'default_value' => '#e0e7ff',
                ),
                array(
                    'key'   => 'field_major_icon_color',
                    'label' => __( 'Icon Color', 'tugasin' ),
                    'name'  => 'major_icon_color',
                    'type'  => 'color_picker',
                    'default_value' => '#4f46e5',
                ),
                // Short Description
                array(
                    'key'          => 'field_major_short_desc',
                    'label'        => __( 'Short Description', 'tugasin' ),
                    'name'         => 'major_short_desc',
                    'type'         => 'textarea',
                    'instructions' => __( 'Brief description for archive cards.', 'tugasin' ),
                    'rows'         => 3,
                ),
                array(
                    'key'          => 'field_major_prospects_short',
                    'label'        => __( 'Career Prospects (Short)', 'tugasin' ),
                    'name'         => 'major_prospects_short',
                    'type'         => 'text',
                    'instructions' => __( 'e.g., Software Engineer, Data Scientist', 'tugasin' ),
                ),
                // Main Content
                array(
                    'key'   => 'field_major_what_you_learn',
                    'label' => __( 'What You Learn', 'tugasin' ),
                    'name'  => 'major_what_you_learn',
                    'type'  => 'wysiwyg',
                    'tabs'  => 'all',
                ),
                // Courses Repeater
                array(
                    'key'        => 'field_major_courses',
                    'label'      => __( 'Courses', 'tugasin' ),
                    'name'       => 'major_courses',
                    'type'       => 'repeater',
                    'layout'     => 'table',
                    'button_label' => __( 'Add Course', 'tugasin' ),
                    'sub_fields' => array(
                        array(
                            'key'   => 'field_course_name',
                            'label' => __( 'Course Name', 'tugasin' ),
                            'name'  => 'course_name',
                            'type'  => 'text',
                        ),
                    ),
                ),
                // Career Prospects Repeater
                array(
                    'key'        => 'field_major_career_prospects',
                    'label'      => __( 'Career Prospects', 'tugasin' ),
                    'name'       => 'major_career_prospects',
                    'type'       => 'repeater',
                    'layout'     => 'block',
                    'button_label' => __( 'Add Career', 'tugasin' ),
                    'sub_fields' => array(
                        array(
                            'key'   => 'field_job_title',
                            'label' => __( 'Job Title', 'tugasin' ),
                            'name'  => 'job_title',
                            'type'  => 'text',
                        ),
                        array(
                            'key'   => 'field_job_desc',
                            'label' => __( 'Description', 'tugasin' ),
                            'name'  => 'job_desc',
                            'type'  => 'textarea',
                            'rows'  => 2,
                        ),
                    ),
                ),
                // Related Universities
                array(
                    'key'          => 'field_major_related_unis',
                    'label'        => __( 'Top Universities', 'tugasin' ),
                    'name'         => 'major_related_unis',
                    'type'         => 'relationship',
                    'post_type'    => array( 'university' ),
                    'filters'      => array( 'search' ),
                    'return_format'=> 'object',
                ),
                // CTA Section
                array(
                    'key'           => 'field_major_cta_title',
                    'label'         => __( 'CTA Title', 'tugasin' ),
                    'name'          => 'major_cta_title',
                    'type'          => 'text',
                    'default_value' => 'Butuh Bantuan Tugas?',
                ),
                array(
                    'key'           => 'field_major_cta_text',
                    'label'         => __( 'CTA Text', 'tugasin' ),
                    'name'          => 'major_cta_text',
                    'type'          => 'textarea',
                    'rows'          => 2,
                    'default_value' => 'Konsultasi dengan tim ahli kami sekarang.',
                ),
                array(
                    'key'           => 'field_major_cta_btn_text',
                    'label'         => __( 'CTA Button Text', 'tugasin' ),
                    'name'          => 'major_cta_btn_text',
                    'type'          => 'text',
                    'default_value' => 'Konsultasi Sekarang',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'major',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position'   => 'normal',
            'style'      => 'default',
        ) );
    }

    /**
     * Register University ACF Fields
     */
    public function register_university_fields() {
        if ( ! function_exists( 'acf_add_local_field_group' ) ) {
            return;
        }

        acf_add_local_field_group( array(
            'key'      => 'group_university_fields',
            'title'    => __( 'University Details', 'tugasin' ),
            'fields'   => array(
                // Logo
                array(
                    'key'           => 'field_uni_logo',
                    'label'         => __( 'University Logo', 'tugasin' ),
                    'name'          => 'uni_logo',
                    'type'          => 'image',
                    'return_format' => 'url',
                    'preview_size'  => 'medium',
                ),
                // Banner Color
                array(
                    'key'           => 'field_uni_banner_color',
                    'label'         => __( 'Banner Color', 'tugasin' ),
                    'name'          => 'uni_banner_color',
                    'type'          => 'color_picker',
                    'default_value' => '#064e3b',
                ),
                // Basic Info
                array(
                    'key'   => 'field_uni_motto',
                    'label' => __( 'Motto', 'tugasin' ),
                    'name'  => 'uni_motto',
                    'type'  => 'text',
                ),
                array(
                    'key'   => 'field_uni_location',
                    'label' => __( 'Location', 'tugasin' ),
                    'name'  => 'uni_location',
                    'type'  => 'text',
                ),
                array(
                    'key'   => 'field_uni_website',
                    'label' => __( 'Website', 'tugasin' ),
                    'name'  => 'uni_website',
                    'type'  => 'url',
                ),
                array(
                    'key'   => 'field_uni_phone',
                    'label' => __( 'Phone', 'tugasin' ),
                    'name'  => 'uni_phone',
                    'type'  => 'text',
                ),
                // YouTube Video URL
                array(
                    'key'          => 'field_uni_youtube',
                    'label'        => __( 'YouTube Video URL', 'tugasin' ),
                    'name'         => 'uni_youtube',
                    'type'         => 'url',
                    'instructions' => __( 'Paste YouTube video URL for profile video.', 'tugasin' ),
                ),
                // Sejarah Perguruan Tinggi (replaces About)
                array(
                    'key'   => 'field_uni_sejarah',
                    'label' => __( 'Sejarah Perguruan Tinggi', 'tugasin' ),
                    'name'  => 'uni_sejarah',
                    'type'  => 'wysiwyg',
                    'tabs'  => 'all',
                    'instructions' => __( 'History of the university.', 'tugasin' ),
                ),
                // Visi (NEW - Phase 21)
                array(
                    'key'   => 'field_uni_visi',
                    'label' => __( 'Visi', 'tugasin' ),
                    'name'  => 'uni_visi',
                    'type'  => 'textarea',
                    'rows'  => 3,
                    'instructions' => __( 'Vision statement of the university.', 'tugasin' ),
                ),
                // Misi (NEW - Phase 21)
                array(
                    'key'   => 'field_uni_misi',
                    'label' => __( 'Misi', 'tugasin' ),
                    'name'  => 'uni_misi',
                    'type'  => 'wysiwyg',
                    'tabs'  => 'all',
                    'instructions' => __( 'Mission points of the university.', 'tugasin' ),
                ),
                // Faculties Repeater (Modified Phase 21 - nested programs)
                array(
                    'key'          => 'field_uni_faculties',
                    'label'        => __( 'Fakultas & Program Studi', 'tugasin' ),
                    'name'         => 'uni_faculties',
                    'type'         => 'repeater',
                    'layout'       => 'block',
                    'button_label' => __( 'Add Faculty', 'tugasin' ),
                    'sub_fields'   => array(
                        array(
                            'key'   => 'field_faculty_name',
                            'label' => __( 'Faculty Name', 'tugasin' ),
                            'name'  => 'faculty_name',
                            'type'  => 'text',
                        ),
                        // Nested Programs Repeater (NEW - Phase 21)
                        array(
                            'key'          => 'field_faculty_programs',
                            'label'        => __( 'Program Studi', 'tugasin' ),
                            'name'         => 'programs',
                            'type'         => 'repeater',
                            'layout'       => 'table',
                            'button_label' => __( 'Add Program', 'tugasin' ),
                            'sub_fields'   => array(
                                array(
                                    'key'   => 'field_program_name',
                                    'label' => __( 'Program Name', 'tugasin' ),
                                    'name'  => 'program_name',
                                    'type'  => 'text',
                                ),
                            ),
                        ),
                    ),
                ),
                // Admission Paths Repeater (Modified Phase 21 - added biaya)
                array(
                    'key'          => 'field_uni_admission',
                    'label'        => __( 'Jalur Masuk & Biaya', 'tugasin' ),
                    'name'         => 'uni_admission',
                    'type'         => 'repeater',
                    'layout'       => 'block',
                    'button_label' => __( 'Add Admission Path', 'tugasin' ),
                    'sub_fields'   => array(
                        array(
                            'key'   => 'field_path_name',
                            'label' => __( 'Path Name', 'tugasin' ),
                            'name'  => 'path_name',
                            'type'  => 'text',
                        ),
                        array(
                            'key'   => 'field_path_desc',
                            'label' => __( 'Description', 'tugasin' ),
                            'name'  => 'path_desc',
                            'type'  => 'textarea',
                            'rows'  => 2,
                        ),
                        // Biaya fields (NEW - Phase 21)
                        array(
                            'key'          => 'field_biaya_min',
                            'label'        => __( 'Biaya Minimum (Rp)', 'tugasin' ),
                            'name'         => 'biaya_min',
                            'type'         => 'number',
                            'instructions' => __( 'Enter number only, e.g. 5000000', 'tugasin' ),
                        ),
                        array(
                            'key'          => 'field_biaya_max',
                            'label'        => __( 'Biaya Maximum (Rp)', 'tugasin' ),
                            'name'         => 'biaya_max',
                            'type'         => 'number',
                            'instructions' => __( 'Enter number only, e.g. 15000000', 'tugasin' ),
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param'    => 'post_type',
                        'operator' => '==',
                        'value'    => 'university',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position'   => 'normal',
            'style'      => 'default',
        ) );
    }
}
