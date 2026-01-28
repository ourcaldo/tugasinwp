<?php
/**
 * Navigation Template Part
 * Uses WordPress Menus with custom Walker for mega menu styling
 *
 * @package TugasinWP
 * @since 2.0.0
 */

?>

<nav class="nav-desktop" role="navigation" aria-label="<?php esc_attr_e('Primary Navigation', 'tugasin'); ?>">
    <?php
    // Check if Walker class exists
    if (class_exists('Tugasin_Mega_Menu_Walker')) {
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container' => false,
            'items_wrap' => '%3$s',
            'walker' => new Tugasin_Mega_Menu_Walker(),
            'fallback_cb' => 'tugasin_fallback_menu',
            'depth' => 2,
        ));
    } else {
        // Fallback if Walker not available
        tugasin_fallback_menu();
    }
    ?>
</nav>

<?php
/**
 * Fallback menu when no menu is assigned
 */
function tugasin_fallback_menu()
{
    // Get homepage title from front page (dynamic)
    $front_page_id = get_option('page_on_front');
    $home_title = $front_page_id ? get_the_title($front_page_id) : __('Beranda', 'tugasin');
    ?>
    <a href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html($home_title); ?></a>

    <div class="nav-item-dropdown">
        <a href="<?php echo esc_url(tugasin_get_page_url('layanan')); ?>" class="nav-link">
            <?php esc_html_e('Layanan', 'tugasin'); ?> <i class="fas fa-chevron-down"></i>
        </a>
        <div class="dropdown-menu service-menu-enhanced">
            <div class="dropdown-panel-left">
                <a href="<?php echo esc_url(tugasin_get_page_url('layanan')); ?>" class="view-all-link">
                    <div class="view-all-icon"><i class="fas fa-th-large"></i></div>
                    <div>
                        <h5><?php esc_html_e('Lihat Semua Layanan', 'tugasin'); ?></h5>
                        <p><?php esc_html_e('Jelajahi semua layanan', 'tugasin'); ?></p>
                    </div>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="dropdown-panel-right">
                <a href="<?php echo esc_url(tugasin_get_page_url('joki_skripsi')); ?>" class="dropdown-item">
                    <div class="dd-icon bg-pastel-indigo"><i class="fas fa-graduation-cap"></i></div>
                    <div>
                        <h5><?php esc_html_e('Joki Skripsi', 'tugasin'); ?></h5>
                        <p><?php esc_html_e('Bantuan skripsi profesional.', 'tugasin'); ?></p>
                    </div>
                </a>
                <a href="<?php echo esc_url(tugasin_get_page_url('joki_makalah')); ?>" class="dropdown-item">
                    <div class="dd-icon bg-pastel-yellow"><i class="fas fa-book"></i></div>
                    <div>
                        <h5><?php esc_html_e('Joki Makalah', 'tugasin'); ?></h5>
                        <p><?php esc_html_e('Bantuan makalah & karya ilmiah.', 'tugasin'); ?></p>
                    </div>
                </a>
                <a href="<?php echo esc_url(tugasin_get_page_url('joki_tugas')); ?>" class="dropdown-item">
                    <div class="dd-icon bg-pastel-green"><i class="fas fa-tasks"></i></div>
                    <div>
                        <h5><?php esc_html_e('Joki Tugas', 'tugasin'); ?></h5>
                        <p><?php esc_html_e('Solusi cepat tugas harian.', 'tugasin'); ?></p>
                    </div>
                </a>
                <a href="<?php echo esc_url(tugasin_get_page_url('cek_plagiarism')); ?>" class="dropdown-item">
                    <div class="dd-icon bg-pastel-gray"><i class="fas fa-search"></i></div>
                    <div>
                        <h5><?php esc_html_e('Cek Plagiarism', 'tugasin'); ?></h5>
                        <p><?php esc_html_e('Cek Turnitin resmi & akurat.', 'tugasin'); ?></p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="nav-item-dropdown">
        <a href="#" class="nav-link">
            <?php esc_html_e('Resources', 'tugasin'); ?> <i class="fas fa-chevron-down"></i>
        </a>
        <div class="dropdown-menu resource-menu">
            <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="dropdown-item">
                <div class="dd-icon bg-pastel-purple"><i class="fas fa-blog"></i></div>
                <div>
                    <h5><?php esc_html_e('Blog', 'tugasin'); ?></h5>
                    <p><?php esc_html_e('Tips & panduan akademik.', 'tugasin'); ?></p>
                </div>
            </a>
            <a href="<?php echo esc_url(get_post_type_archive_link('major')); ?>" class="dropdown-item">
                <div class="dd-icon bg-pastel-indigo"><i class="fas fa-university"></i></div>
                <div>
                    <h5><?php esc_html_e('Kamus Jurusan', 'tugasin'); ?></h5>
                    <p><?php esc_html_e('Panduan jurusan kuliah.', 'tugasin'); ?></p>
                </div>
            </a>
            <a href="<?php echo esc_url(get_post_type_archive_link('university')); ?>" class="dropdown-item">
                <div class="dd-icon bg-pastel-green"><i class="fas fa-school"></i></div>
                <div>
                    <h5><?php esc_html_e('Kamus Kampus', 'tugasin'); ?></h5>
                    <p><?php esc_html_e('Direktori universitas.', 'tugasin'); ?></p>
                </div>
            </a>
        </div>
    </div>
    <?php
}
