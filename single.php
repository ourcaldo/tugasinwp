<?php
/**
 * Single Blog Post Template
 *
 * Enhanced with:
 * - Featured image display
 * - Dynamic breadcrumb with category
 * - Author box before CTA
 * - Table of Contents (desktop sidebar + mobile bar)
 *
 * @package TugasinWP
 * @since 2.6.0
 */

get_header();

// Get the content and generate TOC
$content = get_the_content();
$content = apply_filters('the_content', $content);
$toc_data = function_exists('tugasin_generate_toc') ? tugasin_generate_toc($content) : array('toc' => '', 'content' => $content, 'count' => 0);
$has_toc = !empty($toc_data['toc']) && $toc_data['count'] >= 3; // Show TOC only if 3+ headings
?>

<main id="main-content" role="main" class="single-post-wrapper <?php echo $has_toc ? 'has-toc-content' : ''; ?>">
    <?php if ($has_toc): ?>
        <?php tugasin_toc_desktop($toc_data['toc']); ?>
    <?php endif; ?>

    <article class="single-post-article">
        <div class="container single-post-container">
            <?php tugasin_breadcrumb(); ?>

            <?php
            // Get author data using get_post() and get_userdata() - works outside loop
            $post_obj = get_post();
            $author_id = $post_obj->post_author;
            $user_data = get_userdata($author_id);

            if ($user_data) {
                // Get all author fields from WP_User object
                $author_email = $user_data->user_email;
                $author_first = $user_data->first_name;
                $author_last = $user_data->last_name;
                $author_nickname = $user_data->nickname;
                $author_display = $user_data->display_name;
                $author_bio = $user_data->description;
                $author_website = $user_data->user_url;

                // Build full name (First + Last, or display name as fallback)
                if ($author_first && $author_last) {
                    $author_full_name = $author_first . ' ' . $author_last;
                } elseif ($author_first) {
                    $author_full_name = $author_first;
                } else {
                    $author_full_name = $author_display;
                }
            } else {
                // Fallback if user data not found
                $author_full_name = __('Anonymous', 'tugasin');
                $author_nickname = '';
                $author_bio = '';
                $author_website = '';
                $author_email = '';
            }
            ?>

            <!-- Enhanced Header - Split Layout -->
            <div class="single-post-header">
                <?php if (has_post_thumbnail()): ?>
                    <div class="single-post-header-image">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="single-post-header-content">
                    <?php tugasin_category_badge(); ?>

                    <h1 class="single-post-title"><?php the_title(); ?></h1>

                    <!-- Meta Row: Author | Date | Category -->
                    <div class="single-post-meta-row">
                        <div class="meta-author">
                            <?php
                            echo get_avatar($author_id, 32, 'mystery', $author_full_name, array(
                                'force_display' => true
                            ));
                            ?>
                            <span class="meta-author-name"><?php echo esc_html($author_full_name); ?></span>
                        </div>
                        <span class="meta-divider"></span>
                        <div class="meta-date">
                            <i class="far fa-calendar-alt"></i>
                            <?php tugasin_posted_on(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="entry-content">
                <?php
                // Output content with TOC IDs added
                echo $toc_data['content'];

                wp_link_pages(array(
                    'before' => '<div class="page-links">' . esc_html__('Pages:', 'tugasin'),
                    'after' => '</div>',
                ));
                ?>
            </div>

            <hr class="single-post-divider">

            <!-- Author Box - ALL DYNAMIC using get_userdata() -->
            <div class="author-box">
                <?php
                echo get_avatar($author_id, 144, 'mystery', $author_full_name, array(
                    'class' => 'author-box-avatar',
                    'force_display' => true
                ));
                ?>
                <div class="author-box-info">
                    <strong class="author-box-name"><?php echo esc_html($author_full_name); ?></strong>
                    <?php if ($author_nickname && $author_nickname !== $author_full_name && $author_nickname !== $author_display): ?>
                        <p class="author-box-role"><?php echo esc_html($author_nickname); ?></p>
                    <?php endif; ?>
                    <?php if ($author_bio): ?>
                        <p class="author-box-bio"><?php echo esc_html($author_bio); ?></p>
                    <?php endif; ?>
                    <?php if ($author_website): ?>
                        <a href="<?php echo esc_url($author_website); ?>" class="author-box-link" target="_blank"
                            rel="noopener">
                            <i class="fas fa-globe"></i> <?php esc_html_e('Website', 'tugasin'); ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="single-post-cta">
                <strong class="cta-title"><?php esc_html_e('Butuh Bantuan dengan Tugasmu?', 'tugasin'); ?></strong>
                <p>
                    <?php esc_html_e('Tim ahli kami siap membantu kamu menyelesaikan tugas dengan cepat dan berkualitas.', 'tugasin'); ?>
                </p>
                <?php echo tugasin_cta_button(__('Konsultasi Gratis', 'tugasin'), 'btn btn-accent'); ?>
            </div>

            <?php
            // Related posts - use new helper function
            if (function_exists('tugasin_get_bottom_related')) {
                echo tugasin_get_bottom_related(get_the_ID(), 3);
            }
            ?>

            <?php
            // Display comments section if comments are open or there are existing comments
            if (comments_open() || get_comments_number()) {
                comments_template();
            }
            ?>
        </div>
    </article>

    <?php if ($has_toc): ?>
        <?php tugasin_toc_mobile($toc_data['toc'], $toc_data['count']); ?>
    <?php endif; ?>
</main>

<?php
get_footer();
