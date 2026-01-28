<?php
/**
 * TugasinWP functions and definitions
 *
 * @package TugasinWP
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('TUGASIN_VERSION', '2.25.0');
define('TUGASIN_DIR', get_template_directory());
define('TUGASIN_URI', get_template_directory_uri());

/**
 * Autoload theme classes from inc/ directory
 */
function tugasin_autoload_classes()
{
    $classes = array(
        'class-tugasin-setup',
        'class-tugasin-cpt',
        'class-tugasin-acf',
        'class-tugasin-settings',
        'class-tugasin-elementor',
        'class-tugasin-mega-menu-walker',
        'class-tugasin-mobile-menu-walker',
        'class-tugasin-menu-fields',
        'class-tugasin-ajax',
        'class-tugasin-schema',
        'class-tugasin-optimization',
        'class-tugasin-related-posts',
        'class-tugasin-featured-image',
    );

    foreach ($classes as $class) {
        $file = TUGASIN_DIR . '/inc/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }

    // Load template tags (not a class)
    $template_tags = TUGASIN_DIR . '/inc/template-tags.php';
    if (file_exists($template_tags)) {
        require_once $template_tags;
    }

    // Load SEO functions
    $seo_functions = TUGASIN_DIR . '/inc/seo-functions.php';
    if (file_exists($seo_functions)) {
        require_once $seo_functions;
    }

    // Load TOC generator
    $toc_generator = TUGASIN_DIR . '/inc/toc-generator.php';
    if (file_exists($toc_generator)) {
        require_once $toc_generator;
    }
}
tugasin_autoload_classes();

/**
 * Initialize theme classes
 */
function tugasin_init()
{
    if (class_exists('Tugasin_Setup')) {
        new Tugasin_Setup();
    }
    if (class_exists('Tugasin_CPT')) {
        new Tugasin_CPT();
    }
    if (class_exists('Tugasin_ACF')) {
        new Tugasin_ACF();
    }
    if (class_exists('Tugasin_Settings')) {
        new Tugasin_Settings();
    }
    if (class_exists('Tugasin_Elementor')) {
        new Tugasin_Elementor();
    }
    if (class_exists('Tugasin_Menu_Fields')) {
        new Tugasin_Menu_Fields();
    }
    if (class_exists('Tugasin_Ajax')) {
        new Tugasin_Ajax();
    }
    if (class_exists('Tugasin_Schema')) {
        new Tugasin_Schema();
    }
}
add_action('after_setup_theme', 'tugasin_init');

/**
 * Helper function to get theme option
 */
function tugasin_get_option($key, $default = '')
{
    return get_option('tugasin_' . $key, $default);
}

/**
 * Helper function to get WhatsApp URL
 */
function tugasin_get_whatsapp_url($message = '')
{
    $number = tugasin_get_option('wa_number', '6281234567890');
    if (empty($message)) {
        $message = tugasin_get_option('wa_template', 'Halo, saya ingin konsultasi tentang bantuan tugas.');
    }
    return 'https://wa.me/' . esc_attr($number) . '?text=' . rawurlencode($message);
}

/**
 * Helper function to get mapped page URL
 * 
 * Retrieves the URL for a mapped page from Tugasin Settings.
 * Falls back to slug-based lookup if no mapping is set.
 * 
 * @param string $key Page key (e.g., 'layanan', 'joki_skripsi')
 * @return string Page URL or home URL if not found
 */
function tugasin_get_page_url($key)
{
    // Normalize key (convert hyphens to underscores for option name)
    $option_key = str_replace('-', '_', $key);
    $page_id = get_option('tugasin_page_' . $option_key, 0);

    // If page is mapped, return its permalink
    if ($page_id) {
        $permalink = get_permalink($page_id);
        if ($permalink) {
            return $permalink;
        }
    }

    // Fallback to slug-based lookup
    $slug_map = array(
        'layanan' => 'layanan',
        'joki_skripsi' => 'joki-skripsi',
        'joki_makalah' => 'joki-makalah',
        'joki_tugas' => 'joki-tugas',
        'cek_plagiarism' => 'cek-plagiarism',
    );

    $slug = isset($slug_map[$option_key]) ? $slug_map[$option_key] : $key;
    $page = get_page_by_path($slug);

    if ($page) {
        return get_permalink($page);
    }

    // Final fallback
    return home_url('/');
}

/**
 * Helper function to get mapped page ID
 * 
 * @param string $key Page key
 * @return int Page ID or 0 if not found
 */
function tugasin_get_page_id($key)
{
    $option_key = str_replace('-', '_', $key);
    $page_id = get_option('tugasin_page_' . $option_key, 0);

    if ($page_id) {
        return $page_id;
    }

    // Fallback to slug-based lookup
    $slug_map = array(
        'layanan' => 'layanan',
        'joki_skripsi' => 'joki-skripsi',
        'joki_makalah' => 'joki-makalah',
        'joki_tugas' => 'joki-tugas',
        'cek_plagiarism' => 'cek-plagiarism',
    );

    $slug = isset($slug_map[$option_key]) ? $slug_map[$option_key] : $key;
    $page = get_page_by_path($slug);

    return $page ? $page->ID : 0;
}

/**
 * ============================================
 * TEMPLATE INCLUDE FILTER - PAGE ID MAPPING
 * ============================================
 * 
 * This filter ensures templates are loaded based on page ID mappings
 * from Tugasin Settings, NOT based on page slugs.
 * 
 * This allows users to change page slugs without breaking templates.
 */
function tugasin_template_include($template)
{
    // Only process pages
    if (!is_page()) {
        return $template;
    }

    $current_page_id = get_the_ID();

    // Define page mapping: option_key => template_file
    $page_template_map = array(
        'tugasin_page_layanan' => 'page-layanan.php',
        'tugasin_page_joki_skripsi' => 'page-joki-skripsi.php',
        'tugasin_page_joki_makalah' => 'page-joki-makalah.php',
        'tugasin_page_joki_tugas' => 'page-joki-tugas.php',
        'tugasin_page_cek_plagiarism' => 'page-cek-plagiarism.php',
    );

    // Check each mapping
    foreach ($page_template_map as $option_key => $template_file) {
        $mapped_page_id = get_option($option_key, 0);

        // If current page matches a mapped page, use the corresponding template
        if ($mapped_page_id && $current_page_id == $mapped_page_id) {
            $new_template = locate_template($template_file);
            if ($new_template) {
                return $new_template;
            }
        }
    }

    return $template;
}
add_filter('template_include', 'tugasin_template_include', 99);

/**
 * ============================================
 * ONE CLICK DEMO IMPORT CONFIGURATION
 * ============================================
 */

/**
 * Define predefined import files for OCDI
 */
function tugasin_ocdi_import_files()
{
    return array(
        array(
            'import_file_name' => 'TugasinWP Demo',
            'categories' => array('Main Demo'),
            'local_import_file' => trailingslashit(get_template_directory()) . 'demo-content/content.xml',
            'local_import_widget_file' => trailingslashit(get_template_directory()) . 'demo-content/widgets.json',
            'local_import_customizer_file' => trailingslashit(get_template_directory()) . 'demo-content/customizer.dat',
            'import_preview_image_url' => trailingslashit(get_template_directory_uri()) . 'assets/images/demo-preview.png',
            'preview_url' => 'https://tugasin.com/',
        ),
    );
}
add_filter('ocdi/import_files', 'tugasin_ocdi_import_files');

/**
 * OCDI after import - set up pages and menus
 */
function tugasin_ocdi_after_import()
{
    // Set front page
    $front_page = get_page_by_title('Home');
    if ($front_page) {
        update_option('page_on_front', $front_page->ID);
        update_option('show_on_front', 'page');
    }

    // Set blog page
    $blog_page = get_page_by_title('Blog');
    if ($blog_page) {
        update_option('page_for_posts', $blog_page->ID);
    }

    // Assign primary menu
    $main_menu = get_term_by('name', 'Primary Menu', 'nav_menu');
    if ($main_menu) {
        set_theme_mod('nav_menu_locations', array(
            'primary' => $main_menu->term_id,
            'mobile' => $main_menu->term_id,
            'footer' => $main_menu->term_id,
        ));
    }

    // Flush rewrite rules
    flush_rewrite_rules();
}
add_action('ocdi/after_import', 'tugasin_ocdi_after_import');

/**
 * OCDI plugin page customization
 */
function tugasin_ocdi_plugin_page_setup($default_settings)
{
    $default_settings['parent_slug'] = 'themes.php';
    $default_settings['page_title'] = esc_html__('Import Demo Data', 'tugasin');
    $default_settings['menu_title'] = esc_html__('Import Demo Data', 'tugasin');
    $default_settings['capability'] = 'import';
    $default_settings['menu_slug'] = 'tugasin-demo-import';
    return $default_settings;
}
add_filter('ocdi/plugin_page_setup', 'tugasin_ocdi_plugin_page_setup');
