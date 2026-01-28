<?php
/**
 * Mobile Menu Template Part
 * Uses WordPress Menus with custom Walker for mobile accordion styling
 *
 * @package TugasinWP
 * @since 2.0.0
 */

// Get logo from Tugasin Settings (same logic as site-branding.php)
$tugasin_logo_id = get_option('tugasin_logo', 0);
$has_tugasin_logo = $tugasin_logo_id && wp_get_attachment_image_url($tugasin_logo_id, 'full');
?>

<div class="mobile-menu">
    <div class="mobile-menu-header">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="mobile-menu-logo">
            <?php if ($has_tugasin_logo): ?>
                <img src="<?php echo esc_url(wp_get_attachment_image_url($tugasin_logo_id, 'full')); ?>"
                    alt="<?php echo esc_attr(get_bloginfo('name')); ?>" style="max-height: 32px; width: auto;">
            <?php elseif (has_custom_logo()): ?>
                <?php the_custom_logo(); ?>
            <?php else: ?>
                <div class="logo-icon"><i class="fas fa-bolt"></i></div>
                <span>
                    <?php bloginfo('name'); ?>
                </span>
            <?php endif; ?>
        </a>
        <button class="mobile-menu-close" aria-label="<?php esc_attr_e('Close menu', 'tugasin'); ?>">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <?php
    // Check if Walker class exists and menu is assigned
    if (class_exists('Tugasin_Mobile_Menu_Walker') && has_nav_menu('mobile')) {
        wp_nav_menu(array(
            'theme_location' => 'mobile',
            'container' => false,
            'items_wrap' => '%3$s',
            'walker' => new Tugasin_Mobile_Menu_Walker(),
            'fallback_cb' => false,
            'depth' => 2,
        ));
    } elseif (class_exists('Tugasin_Mobile_Menu_Walker') && has_nav_menu('primary')) {
        // Fallback to primary menu
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container' => false,
            'items_wrap' => '%3$s',
            'walker' => new Tugasin_Mobile_Menu_Walker(),
            'fallback_cb' => false,
            'depth' => 2,
        ));
    } else {
        // Static fallback - get homepage title dynamically
        $front_page_id = get_option('page_on_front');
        $home_title = $front_page_id ? get_the_title($front_page_id) : __('Beranda', 'tugasin');
        ?>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="mobile-menu-link">
            <i class="fas fa-home"></i> <?php echo esc_html($home_title); ?>
        </a>

        <div class="mobile-accordion">
            <button class="accordion-header">
                <span><i class="fas fa-briefcase"></i> <?php esc_html_e('Layanan', 'tugasin'); ?></span>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </button>
            <div class="accordion-content">
                <a href="<?php echo esc_url(tugasin_get_page_url('joki_skripsi')); ?>" class="accordion-item">
                    <div class="accordion-item-icon bg-pastel-indigo"><i class="fas fa-graduation-cap"></i></div>
                    <div>
                        <strong><?php esc_html_e('Joki Skripsi', 'tugasin'); ?></strong><span><?php esc_html_e('Bantuan skripsi', 'tugasin'); ?></span>
                    </div>
                </a>
                <a href="<?php echo esc_url(tugasin_get_page_url('joki_makalah')); ?>" class="accordion-item">
                    <div class="accordion-item-icon bg-pastel-yellow"><i class="fas fa-book"></i></div>
                    <div>
                        <strong><?php esc_html_e('Joki Makalah', 'tugasin'); ?></strong><span><?php esc_html_e('Makalah & karya ilmiah', 'tugasin'); ?></span>
                    </div>
                </a>
                <a href="<?php echo esc_url(tugasin_get_page_url('joki_tugas')); ?>" class="accordion-item">
                    <div class="accordion-item-icon bg-pastel-green"><i class="fas fa-tasks"></i></div>
                    <div>
                        <strong><?php esc_html_e('Joki Tugas', 'tugasin'); ?></strong><span><?php esc_html_e('Tugas harian', 'tugasin'); ?></span>
                    </div>
                </a>
                <a href="<?php echo esc_url(tugasin_get_page_url('cek_plagiarism')); ?>" class="accordion-item">
                    <div class="accordion-item-icon bg-pastel-gray"><i class="fas fa-search"></i></div>
                    <div>
                        <strong><?php esc_html_e('Cek Plagiarism', 'tugasin'); ?></strong><span><?php esc_html_e('Cek Turnitin', 'tugasin'); ?></span>
                    </div>
                </a>
            </div>
        </div>

        <div class="mobile-accordion">
            <button class="accordion-header">
                <span><i class="fas fa-book-open"></i> <?php esc_html_e('Resources', 'tugasin'); ?></span>
                <i class="fas fa-chevron-down accordion-icon"></i>
            </button>
            <div class="accordion-content">
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="accordion-item">
                    <div class="accordion-item-icon bg-pastel-purple"><i class="fas fa-blog"></i></div>
                    <div>
                        <strong><?php esc_html_e('Blog', 'tugasin'); ?></strong><span><?php esc_html_e('Tips & panduan', 'tugasin'); ?></span>
                    </div>
                </a>
                <a href="<?php echo esc_url(get_post_type_archive_link('major')); ?>" class="accordion-item">
                    <div class="accordion-item-icon bg-pastel-indigo"><i class="fas fa-university"></i></div>
                    <div>
                        <strong><?php esc_html_e('Kamus Jurusan', 'tugasin'); ?></strong><span><?php esc_html_e('Info jurusan', 'tugasin'); ?></span>
                    </div>
                </a>
                <a href="<?php echo esc_url(get_post_type_archive_link('university')); ?>" class="accordion-item">
                    <div class="accordion-item-icon bg-pastel-green"><i class="fas fa-school"></i></div>
                    <div>
                        <strong><?php esc_html_e('Kamus Kampus', 'tugasin'); ?></strong><span><?php esc_html_e('Direktori kampus', 'tugasin'); ?></span>
                    </div>
                </a>
            </div>
        </div>
        <?php
    }
    ?>

    <a href="<?php echo esc_url(tugasin_get_whatsapp_url()); ?>" class="btn btn-primary mobile-cta" target="_blank"
        rel="noopener noreferrer">
        <i class="fab fa-whatsapp"></i>
        <?php
        $cta_text = get_option('tugasin_cta_text', 'Konsultasi Sekarang!');
        echo esc_html($cta_text ? $cta_text : __('Konsultasi Sekarang!', 'tugasin'));
        ?>
    </a>
</div>