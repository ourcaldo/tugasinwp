<?php
/**
 * Blog Archive Template
 * EXACT design from ui/blog.html
 *
 * @package TugasinWP
 * @since 1.5.0
 */

get_header();
?>

<main id="main-content" role="main">
    <!-- Blog Hero -->
    <section class="hero" style="padding: 140px 0 80px;">
        <div class="container" style="text-align: center; max-width: 800px;">
            <h1 style="margin-bottom: 24px;"><?php esc_html_e('Tips & Insight Akademik', 'tugasin'); ?></h1>
            <p style="font-size: 1.2rem; color: rgba(255,255,255,0.9);">
                <?php esc_html_e('Temukan panduan skripsi, tips belajar, dan informasi seputar dunia perkuliahan yang kamu butuhkan.', 'tugasin'); ?>
            </p>
        </div>
    </section>

    <!-- Categories & Blog Grid -->
    <section class="services" style="padding: 40px 0; background: var(--bg-body);">
        <div class="container">
            <!-- Category Filters -->
            <div style="display: flex; gap: 16px; flex-wrap: wrap; justify-content: center; margin-bottom: 40px;">
                <?php
                $current_cat = get_queried_object();
                $is_all = !is_category();
                ?>
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"
                    class="btn <?php echo $is_all ? 'btn-primary' : 'btn-outline'; ?>"
                    style="padding: 8px 20px; font-size: 0.9rem; <?php echo !$is_all ? 'border-color: #e5e7eb;' : ''; ?>">
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
                        class="btn <?php echo $is_current ? 'btn-primary' : 'btn-outline'; ?>"
                        style="padding: 8px 20px; font-size: 0.9rem; <?php echo !$is_current ? 'border-color: #e5e7eb;' : ''; ?>">
                        <?php echo esc_html($category->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Blog Grid -->
            <div class="services-grid" style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
                <?php
                if (have_posts()):
                    while (have_posts()):
                        the_post();

                        // Get category
                        $cats = get_the_category();
                        $cat_name = !empty($cats) ? $cats[0]->name : '';

                        // Calculate read time (rough estimate: 200 words per minute)
                        $content = get_the_content();
                        $word_count = str_word_count(strip_tags($content));
                        $read_time = max(1, ceil($word_count / 200));

                        // Get featured image or placeholder
                        $thumb_url = get_the_post_thumbnail_url(null, 'large');
                        if (!$thumb_url) {
                            $thumb_url = 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?q=80&w=1000&auto=format&fit=crop';
                        }
                        ?>
                        <!-- Blog Card -->
                        <div class="service-card"
                            style="padding: 0; overflow: hidden; display: flex; flex-direction: column; align-items: flex-start; text-align: left;">
                            <a href="<?php the_permalink(); ?>"
                                style="width: 100%; aspect-ratio: 16/9; overflow: hidden; display: block;">
                                <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>"
                                    style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;"
                                    onmouseover="this.style.transform='scale(1.05)'"
                                    onmouseout="this.style.transform='scale(1)'">
                            </a>
                            <div style="padding: 24px;">
                                <?php if ($cat_name): ?>
                                    <span
                                        style="font-size: 0.75rem; font-weight: 700; color: var(--primary); text-transform: uppercase;">
                                        <?php echo esc_html($cat_name); ?>
                                    </span>
                                <?php endif; ?>
                                <h2 style="margin: 12px 0; font-size: 1.25rem;">
                                    <a href="<?php the_permalink(); ?>" style="color: inherit; text-decoration: none;">
                                        <?php the_title(); ?>
                                    </a>
                                </h2>
                                <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 20px;">
                                    <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                                </p>
                                <div
                                    style="display: flex; align-items: center; gap: 12px; font-size: 0.85rem; color: var(--text-secondary);">
                                    <span><i class="far fa-calendar"></i> <?php echo get_the_date('d M Y'); ?></span>
                                    <span><i class="far fa-clock"></i> <?php echo esc_html($read_time); ?> min read</span>
                                </div>
                            </div>
                        </div>
                        <?php
                    endwhile;
                else:
                    ?>
                    <div class="no-posts" style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                        <div
                            style="width: 80px; height: 80px; background: var(--pastel-indigo); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                            <i class="fas fa-file-alt" style="font-size: 2rem; color: #4f46e5;"></i>
                        </div>
                        <h3 style="margin-bottom: 12px;"><?php esc_html_e('Belum Ada Artikel', 'tugasin'); ?></h3>
                        <p style="color: var(--text-secondary);">
                            <?php esc_html_e('Artikel akan segera hadir. Stay tuned!', 'tugasin'); ?></p>
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
                <div style="display: flex; justify-content: center; margin-top: 60px; gap: 8px;">
                    <?php
                    for ($i = 1; $i <= min($total_pages, 5); $i++):
                        $is_current = ($i === $current_page);
                        ?>
                        <a href="<?php echo esc_url(get_pagenum_link($i)); ?>"
                            class="btn <?php echo $is_current ? 'btn-primary' : 'btn-outline'; ?>"
                            style="width: 40px; height: 40px; padding: 0; justify-content: center; <?php echo !$is_current ? 'border-color: #e5e7eb;' : ''; ?>">
                            <?php echo esc_html($i); ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="<?php echo esc_url(get_pagenum_link($current_page + 1)); ?>" class="btn btn-outline"
                            style="width: 40px; height: 40px; padding: 0; justify-content: center; border-color: #e5e7eb;">
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
