<?php
/**
 * Table of Contents Generator
 * 
 * Parses post content for headings and generates a TOC.
 *
 * @package TugasinWP
 * @since 2.6.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Generate Table of Contents from content headings
 *
 * @param string $content The post content.
 * @return array Array with 'toc' HTML and 'content' with IDs added.
 */
function tugasin_generate_toc($content)
{
    // Match h2 and h3 headings
    $pattern = '/<h([2-3])([^>]*)>(.*?)<\/h\1>/i';

    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

    if (empty($matches)) {
        return array(
            'toc' => '',
            'content' => $content,
        );
    }

    $toc_items = array();
    $modified_content = $content;

    foreach ($matches as $index => $match) {
        $level = $match[1]; // 2 or 3
        $attrs = $match[2];
        $heading_text = strip_tags($match[3]);
        $original_heading = $match[0];

        // Generate unique ID from heading text
        $id = sanitize_title($heading_text);
        $id = $id . '-' . $index; // Ensure uniqueness

        // Check if heading already has an ID
        if (preg_match('/id=["\']([^"\']+)["\']/', $attrs, $id_match)) {
            $id = $id_match[1];
            $new_heading = $original_heading;
        } else {
            // Add ID to heading
            $new_heading = sprintf(
                '<h%s id="%s"%s>%s</h%s>',
                $level,
                esc_attr($id),
                $attrs,
                $match[3],
                $level
            );
        }

        // Replace original heading with new one
        $modified_content = str_replace($original_heading, $new_heading, $modified_content);

        // Add to TOC items
        $toc_items[] = array(
            'id' => $id,
            'text' => $heading_text,
            'level' => (int) $level,
        );
    }

    // Build TOC HTML with hierarchical numbering
    $toc_html = '<ul class="toc-list">';

    $h2_count = 0;
    $h3_count = 0;

    foreach ($toc_items as $item) {
        $indent_class = $item['level'] === 3 ? 'toc-item-sub' : '';

        // Generate hierarchical number
        if ($item['level'] === 2) {
            $h2_count++;
            $h3_count = 0; // Reset H3 counter for new H2
            $number = $h2_count;
        } else {
            // H3 - subheading
            $h3_count++;
            $number = $h2_count . '.' . $h3_count;
        }

        $toc_html .= sprintf(
            '<li class="toc-item %s"><a href="#%s" class="toc-link" data-target="%s"><span class="toc-number">%s</span> %s</a></li>',
            $indent_class,
            esc_attr($item['id']),
            esc_attr($item['id']),
            esc_html($number),
            esc_html($item['text'])
        );
    }

    $toc_html .= '</ul>';

    return array(
        'toc' => $toc_html,
        'content' => $modified_content,
        'count' => count($toc_items),
    );
}

/**
 * Render the desktop TOC sidebar
 *
 * @param string $toc_html The TOC HTML.
 */
function tugasin_toc_desktop($toc_html)
{
    if (empty($toc_html)) {
        return;
    }
    ?>
    <aside class="toc-sidebar" id="toc-sidebar">
        <div class="toc-sidebar-inner">
            <div class="toc-header">
                <strong class="toc-title">
                    <i class="fas fa-list-ul"></i>
                    <?php esc_html_e('Daftar Isi', 'tugasin'); ?>
                </strong>
                <button class="toc-close" id="toc-close" aria-label="<?php esc_attr_e('Tutup daftar isi', 'tugasin'); ?>">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="toc-nav">
                <?php echo $toc_html; ?>
            </nav>
        </div>
    </aside>
    <button class="toc-toggle" id="toc-toggle" aria-label="<?php esc_attr_e('Buka daftar isi', 'tugasin'); ?>">
        <i class="fas fa-chevron-right"></i>
    </button>
    <?php
}

/**
 * Render the mobile TOC bar
 *
 * @param string $toc_html The TOC HTML.
 * @param int    $count    Number of TOC items.
 */
function tugasin_toc_mobile($toc_html, $count = 0)
{
    if (empty($toc_html)) {
        return;
    }
    ?>
    <div class="toc-mobile" id="toc-mobile">
        <button class="toc-mobile-header" id="toc-mobile-toggle" aria-expanded="false">
            <span class="toc-mobile-title">
                <i class="fas fa-list-ul"></i>
                <?php esc_html_e('Daftar Isi', 'tugasin'); ?>
                <?php if ($count > 0): ?>
                    <span class="toc-count"><?php echo esc_html($count); ?></span>
                <?php endif; ?>
            </span>
            <i class="fas fa-chevron-up toc-mobile-arrow"></i>
        </button>
        <nav class="toc-mobile-content" id="toc-mobile-content">
            <?php echo $toc_html; ?>
        </nav>
    </div>
    <?php
}
