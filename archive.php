<?php
/**
 * Blog Archive Template
 *
 * @package TugasinWP
 * @since 1.5.0
 */

get_header();
?>

<main id="main-content" role="main">
    <!-- Blog Hero -->
    <section class="hero blog-hero">
        <div class="container">
            <h1><?php esc_html_e('Tips & Insight Akademik', 'tugasin'); ?></h1>
            <p>
                <?php esc_html_e('Temukan panduan skripsi, tips belajar, dan informasi seputar dunia perkuliahan yang kamu butuhkan.', 'tugasin'); ?>
            </p>
        </div>
    </section>

    <!-- Categories & Blog Grid -->
    <section class="blog-section">
        <div class="container">
            <!-- Category Filters -->
            <div class="archive-filters">
                <?php
                $current_cat = get_queried_object();
                $is_all = !is_category();
                ?>
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"
                    class="btn <?php echo $is_all ? 'btn-primary' : 'btn-outline'; ?>">
                    <?php esc_html_e('Semua', 'tugasin'); ?>
                </a>
                <?php
                $categories = get_categories(array(
                    'hide_empty' => true,
                    'number' => 6,
                    'orderby' => 'count',
                    'order' => 'DESC',
                ));
                foreach ($categories as $category):
                    $is_current = is_category($category->term_id);
                    ?>
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>"
                        class="btn <?php echo $is_current ? 'btn-primary' : 'btn-outline'; ?>">
                        <?php echo esc_html($category->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Blog Grid -->
            <div class="services-grid blog-grid">
                <?php
                if (have_posts()):
                    while (have_posts()):
                        the_post();
                        get_template_part('template-parts/content/blog-card');
                    endwhile;
                else:
                    ?>
                    <div class="no-posts">
                        <div class="no-posts-icon-circle">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h3><?php esc_html_e('Belum Ada Artikel', 'tugasin'); ?></h3>
                        <p><?php esc_html_e('Artikel akan segera hadir. Stay tuned!', 'tugasin'); ?></p>
                    </div>
                    <?php
                endif;
                ?>
            </div>

            <!-- Pagination -->
            <?php
            $total_pages = $GLOBALS['wp_query']->max_num_pages;
            if ($total_pages > 1):
                $current_page = max(1, get_query_var('paged'));
                ?>
                <div class="archive-pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="<?php echo esc_url(get_pagenum_link($current_page - 1)); ?>" class="btn btn-outline">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php
                    // Smart pagination with ellipsis
                    $range = 2;
                    $show_dots_start = false;
                    $show_dots_end = false;

                    for ($i = 1; $i <= $total_pages; $i++):
                        if ($i === 1 || $i === $total_pages || ($i >= $current_page - $range && $i <= $current_page + $range)):
                            $is_current = ($i === $current_page);
                            ?>
                            <a href="<?php echo esc_url(get_pagenum_link($i)); ?>"
                                class="btn <?php echo $is_current ? 'btn-primary' : 'btn-outline'; ?>">
                                <?php echo esc_html($i); ?>
                            </a>
                            <?php
                        elseif ($i < $current_page && !$show_dots_start):
                            $show_dots_start = true;
                            echo '<span class="btn btn-outline pagination-ellipsis">&hellip;</span>';
                        elseif ($i > $current_page && !$show_dots_end):
                            $show_dots_end = true;
                            echo '<span class="btn btn-outline pagination-ellipsis">&hellip;</span>';
                        endif;
                    endfor;
                    ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="<?php echo esc_url(get_pagenum_link($current_page + 1)); ?>" class="btn btn-outline">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
get_footer();
