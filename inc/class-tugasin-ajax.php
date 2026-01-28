<?php
/**
 * AJAX Handler Class
 *
 * Handles AJAX requests for archive filtering
 *
 * @package TugasinWP
 * @since 2.3.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tugasin_Ajax {

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_ajax_tugasin_filter_archive', array( $this, 'filter_archive' ) );
        add_action( 'wp_ajax_nopriv_tugasin_filter_archive', array( $this, 'filter_archive' ) );
    }

    /**
     * Handle archive filter AJAX request
     */
    public function filter_archive() {
        // Verify nonce
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'tugasin_ajax_nonce' ) ) {
            wp_send_json_error( array( 'message' => 'Invalid nonce' ) );
        }

        // Security: Whitelist allowed post types to prevent arbitrary queries
        $allowed_post_types = array( 'post', 'major', 'university' );
        $post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : 'post';
        
        if ( ! in_array( $post_type, $allowed_post_types, true ) ) {
            wp_send_json_error( array( 'message' => 'Invalid post type' ) );
        }

        $taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_text_field( $_POST['taxonomy'] ) : 'category';
        $term_id = isset( $_POST['term_id'] ) ? absint( $_POST['term_id'] ) : 0;
        $paged = isset( $_POST['paged'] ) ? absint( $_POST['paged'] ) : 1;

        // Build query args
        $args = array(
            'post_type'      => $post_type,
            'posts_per_page' => get_option( 'posts_per_page', 9 ),
            'paged'          => $paged,
            'post_status'    => 'publish',
        );

        // Add taxonomy query if term is selected
        if ( $term_id > 0 ) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => $term_id,
                ),
            );
        }

        $query = new WP_Query( $args );

        ob_start();

        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();
                
                // Render appropriate card based on post type
                switch ( $post_type ) {
                    case 'major':
                        get_template_part( 'template-parts/card/major' );
                        break;
                    case 'university':
                        get_template_part( 'template-parts/card/university' );
                        break;
                    default:
                        // Blog post card
                        $this->render_blog_card();
                        break;
                }
            }
        } else {
            $this->render_no_posts( $post_type );
        }

        $html = ob_get_clean();

        // Generate pagination
        ob_start();
        $this->render_pagination( $query, $paged );
        $pagination = ob_get_clean();

        wp_reset_postdata();

        wp_send_json_success( array(
            'html'       => $html,
            'pagination' => $pagination,
            'found'      => $query->found_posts,
        ) );
    }

    /**
     * Render blog post card
     */
    private function render_blog_card() {
        $cats = get_the_category();
        $cat_name = ! empty( $cats ) ? $cats[0]->name : '';
        
        $content = get_the_content();
        $word_count = str_word_count( strip_tags( $content ) );
        $read_time = max( 1, ceil( $word_count / 200 ) );
        
        $thumb_url = get_the_post_thumbnail_url( null, 'large' );
        if ( ! $thumb_url ) {
            $thumb_url = 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?q=80&w=1000&auto=format&fit=crop';
        }
        ?>
        <div class="blog-card">
            <a href="<?php the_permalink(); ?>" class="card-image">
                <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>">
            </a>
            <div class="card-content">
                <?php if ( $cat_name ) : ?>
                    <span class="card-category"><?php echo esc_html( $cat_name ); ?></span>
                <?php endif; ?>
                <h3 class="card-title">
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                <p class="card-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?></p>
                <div class="card-meta">
                    <span><i class="far fa-calendar"></i> <?php echo get_the_date( 'd M Y' ); ?></span>
                    <span><i class="far fa-clock"></i> <?php echo esc_html( $read_time ); ?> min read</span>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Render no posts message
     */
    private function render_no_posts( $post_type ) {
        $message = __( 'Tidak ada artikel ditemukan.', 'tugasin' );
        if ( $post_type === 'major' ) {
            $message = __( 'Tidak ada jurusan ditemukan.', 'tugasin' );
        } elseif ( $post_type === 'university' ) {
            $message = __( 'Tidak ada kampus ditemukan.', 'tugasin' );
        }
        ?>
        <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
            <div style="width: 80px; height: 80px; background: var(--pastel-indigo); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                <i class="fas fa-file-alt" style="font-size: 2rem; color: #4f46e5;"></i>
            </div>
            <h3 style="margin-bottom: 12px;"><?php echo esc_html( $message ); ?></h3>
        </div>
        <?php
    }

    /**
     * Render pagination
     */
    private function render_pagination( $query, $current_page ) {
        $total_pages = $query->max_num_pages;
        if ( $total_pages <= 1 ) {
            return;
        }
        ?>
        <div style="display: flex; justify-content: center; margin-top: 60px; gap: 8px;">
            <?php for ( $i = 1; $i <= min( $total_pages, 5 ); $i++ ) : 
                $is_current = ( $i == $current_page );
            ?>
                <a href="#" 
                   class="btn pagination-link <?php echo $is_current ? 'btn-primary' : 'btn-outline'; ?>"
                   data-page="<?php echo esc_attr( $i ); ?>"
                   style="width: 40px; height: 40px; padding: 0; justify-content: center; <?php echo ! $is_current ? 'border-color: #e5e7eb;' : ''; ?>">
                    <?php echo esc_html( $i ); ?>
                </a>
            <?php endfor; ?>
            
            <?php if ( $current_page < $total_pages ) : ?>
                <a href="#" 
                   class="btn btn-outline pagination-link"
                   data-page="<?php echo esc_attr( $current_page + 1 ); ?>"
                   style="width: 40px; height: 40px; padding: 0; justify-content: center; border-color: #e5e7eb;">
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php
    }
}
