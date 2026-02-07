<?php
/**
 * SEO Functions
 *
 * Handles document title, meta descriptions, and Open Graph tags.
 * Uses WordPress filters (pre_get_document_title) for proper title handling.
 *
 * @package TugasinWP
 * @since 1.7.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Set document title properly using WordPress filter
 * Title Logic:
 * - Homepage: {site_title} - {site_tagline}
 * - Pages/Archives: {page_title} - {site_title}
 * - Posts: {post_title} - {site_title}
 *
 * @param string $title Default title.
 * @return string Modified title.
 */
function tugasin_document_title($title)
{
    // Skip if SEO plugins are active
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION')) {
        return $title;
    }

    $site_name = get_bloginfo('name');
    $site_desc = get_bloginfo('description');
    $separator = ' - ';

    // Homepage (static front page or blog front)
    if (is_front_page() && is_home()) {
        // Default homepage (latest posts)
        return $site_name . $separator . $site_desc;
    } elseif (is_front_page()) {
        // Static front page
        return $site_name . $separator . $site_desc;
    } elseif (is_home()) {
        // Blog posts page (when static front page is set)
        $blog_page_id = get_option('page_for_posts');
        if ($blog_page_id) {
            return get_the_title($blog_page_id) . $separator . $site_name;
        }
        return __('Blog', 'tugasin') . $separator . $site_name;
    } elseif (is_singular()) {
        // Single post or page
        return get_the_title() . $separator . $site_name;
    } elseif (is_post_type_archive('major')) {
        return __('Kamus Jurusan', 'tugasin') . $separator . $site_name;
    } elseif (is_post_type_archive('university')) {
        return __('Perguruan Tinggi', 'tugasin') . $separator . $site_name;
    } elseif (is_tax('major_category')) {
        $term = get_queried_object();
        return $term->name . ' - ' . __('Kamus Jurusan', 'tugasin') . $separator . $site_name;
    } elseif (is_tax('university_type') || is_tax('accreditation')) {
        $term = get_queried_object();
        return $term->name . ' - ' . __('Perguruan Tinggi', 'tugasin') . $separator . $site_name;
    } elseif (is_category() || is_tag()) {
        $term = get_queried_object();
        return $term->name . $separator . $site_name;
    } elseif (is_archive()) {
        return get_the_archive_title() . $separator . $site_name;
    } elseif (is_search()) {
        return sprintf(__('Hasil Pencarian: %s', 'tugasin'), get_search_query()) . $separator . $site_name;
    } elseif (is_404()) {
        return __('Halaman Tidak Ditemukan', 'tugasin') . $separator . $site_name;
    }

    return $site_name . $separator . $site_desc;
}
add_filter('pre_get_document_title', 'tugasin_document_title', 10);

/**
 * Output SEO meta tags in head (description, OG tags, Twitter cards)
 * Does NOT output title tag - that's handled by WordPress title-tag support
 */
function tugasin_seo_meta_tags()
{
    // Skip if SEO plugins are active
    if (defined('WPSEO_VERSION') || defined('RANK_MATH_VERSION') || defined('AIOSEO_VERSION')) {
        return;
    }

    // Add noindex for 404 pages
    if (is_404()) {
        echo '<meta name="robots" content="noindex, follow" />' . "\n";
    }

    $description = '';
    $og_image = '';
    $title = wp_get_document_title(); // Get the title we set via filter

    if (is_front_page()) {
        $description = get_bloginfo('description');
    } elseif (is_home()) {
        $description = __('Kumpulan artikel, tips kuliah, panduan skripsi, dan info akademik terbaru untuk mahasiswa Indonesia.', 'tugasin');
    } elseif (is_singular()) {
        $post = get_post();
        if ($post && !empty($post->post_excerpt)) {
            $description = wp_strip_all_tags($post->post_excerpt);
        } else {
            $description = wp_trim_words(wp_strip_all_tags(get_the_content()), 25, '...');
        }
        if (has_post_thumbnail()) {
            $og_image = get_the_post_thumbnail_url(null, 'large');
        }
    } elseif (is_post_type_archive('major') || is_tax('major_category')) {
        $description = __('Temukan jurusan kuliah yang tepat buat kamu. Info prospek kerja, mata kuliah, dan kampus terbaik.', 'tugasin');
    } elseif (is_post_type_archive('university') || is_tax('university_type') || is_tax('accreditation')) {
        $description = __('Temukan informasi lengkap tentang universitas di Indonesia. Akreditasi, fakultas, dan jalur masuk.', 'tugasin');
    } elseif (is_category() || is_tag()) {
        $description = get_the_archive_description();
    } elseif (is_search()) {
        $description = sprintf(__('Hasil pencarian untuk "%s"', 'tugasin'), get_search_query());
    } elseif (is_404()) {
        $description = __('Halaman yang kamu cari tidak ditemukan.', 'tugasin');
    }

    // Fallback description
    if (empty($description)) {
        $description = get_bloginfo('description');
    }

    // Clean up description
    $description = wp_strip_all_tags($description);
    $description = preg_replace('/\s+/', ' ', $description);
    $description = trim($description);
    if (strlen($description) > 160) {
        $description = substr($description, 0, 157) . '...';
    }

    // Output meta description
    if (!empty($description)) {
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }

    // Open Graph tags
    echo '<meta property="og:locale" content="id_ID">' . "\n";
    echo '<meta property="og:type" content="' . (is_singular() ? 'article' : 'website') . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url(tugasin_get_current_url()) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";

    if (!empty($og_image)) {
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
    }

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";

    if (!empty($og_image)) {
        echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "\n";
    }
}
add_action('wp_head', 'tugasin_seo_meta_tags', 1);

/**
 * Get current page URL
 */
function tugasin_get_current_url()
{
    global $wp;
    return home_url(add_query_arg(array(), $wp->request));
}
