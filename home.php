<?php
/**
 * Blog Home Template
 * Used when a static front page is set and this is the posts page.
 * With AJAX filtering using blog-card CSS
 *
 * @package TugasinWP
 * @since 2.4.0
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
            <!-- Category Filters with AJAX -->
            <div class="archive-filter-container" data-post-type="post" data-taxonomy="category"
                style="display: flex; gap: 16px; flex-wrap: wrap; justify-content: center; margin-bottom: 40px;">
                <?php
                $current_cat = get_queried_object();
                $is_all = !is_category();
                ?>
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>"
                    class="btn archive-filter-btn <?php echo $is_all ? 'btn-primary' : 'btn-outline'; ?>"
                    data-term-id="" data-term-slug=""
                    style="padding: 8px 20px; font-size: 0.9rem; <?php echo !$is_all ? 'border-color: #e5e7eb;' : ''; ?>">
                    <?php esc_html_e('Semua', 'tugasin'); ?>
                </a>
                <?php
                // Get top 6 parent categories by post count, excluding Uncategorized
                $categories = get_categories(array(
                    'hide_empty' => true,
                    'exclude' => array(1), // Exclude Uncategorized (ID 1)
                    'number' => 6,          // Limit to 6 categories
                    'orderby' => 'count',    // Order by post count
                    'order' => 'DESC',     // Most posts first
                    'parent' => 0,          // Only parent categories
                ));
                foreach ($categories as $category):
                    // Double check to exclude uncategorized by slug
                    if ($category->slug === 'uncategorized' || $category->slug === 'tidak-berkategori') {
                        continue;
                    }
                    $is_current = is_category($category->term_id);
                    ?>
                    <a href="<?php echo esc_url(get_category_link($category->term_id)); ?>"
                        class="btn archive-filter-btn <?php echo $is_current ? 'btn-primary' : 'btn-outline'; ?>"
                        data-term-id="<?php echo esc_attr($category->term_id); ?>"
                        data-term-slug="<?php echo esc_attr($category->slug); ?>"
                        style="padding: 8px 20px; font-size: 0.9rem; <?php echo !$is_current ? 'border-color: #e5e7eb;' : ''; ?>">
                        <?php echo esc_html($category->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Blog Grid -->
            <div id="archive-grid" class="services-grid"
                style="grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));">
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
                        <!-- Blog Card using CSS class -->
                        <div class="blog-card">
                            <a href="<?php the_permalink(); ?>" class="card-image">
                                <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>">
                            </a>
                            <div class="card-content">
                                <?php if ($cat_name): ?>
                                    <span class="card-category"><?php echo esc_html($cat_name); ?></span>
                                <?php endif; ?>
                                <h3 class="card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>
                                <p class="card-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                                <div class="card-meta">
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
            <div id="archive-pagination">
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
                                class="btn pagination-link <?php echo $is_current ? 'btn-primary' : 'btn-outline'; ?>"
                                data-page="<?php echo esc_attr($i); ?>"
                                style="width: 40px; height: 40px; padding: 0; justify-content: center; <?php echo !$is_current ? 'border-color: #e5e7eb;' : ''; ?>">
                                <?php echo esc_html($i); ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($current_page < $total_pages): ?>
                            <a href="<?php echo esc_url(get_pagenum_link($current_page + 1)); ?>"
                                class="btn btn-outline pagination-link" data-page="<?php echo esc_attr($current_page + 1); ?>"
                                style="width: 40px; height: 40px; padding: 0; justify-content: center; border-color: #e5e7eb;">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Loading State CSS -->
    <style>
        #archive-grid.loading {
            opacity: 0.5;
            pointer-events: none;
            position: relative;
        }

        #archive-grid.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 40px;
            height: 40px;
            margin: -20px 0 0 -20px;
            border: 4px solid #e5e7eb;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</main>

<?php
get_footer();
