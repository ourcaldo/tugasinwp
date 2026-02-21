<?php
/**
 * Single template for Major CPT
 *
 * @package TugasinWP
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" role="main">
<?php
// ACF Fields
$icon_class      = get_field( 'major_icon_class' ) ?: 'fa-graduation-cap';
$icon_bg         = get_field( 'major_icon_bg_color' ) ?: '#e0e7ff';
$icon_color      = get_field( 'major_icon_color' ) ?: '#4f46e5';
$what_you_learn  = get_field( 'major_what_you_learn' );
$courses         = get_field( 'major_courses' );
$career_prospects = get_field( 'major_career_prospects' );
$related_unis    = get_field( 'major_related_unis' );
$cta_title       = get_field( 'major_cta_title' ) ?: __( 'Butuh Bantuan Tugas?', 'tugasin' );
$cta_text        = get_field( 'major_cta_text' ) ?: __( 'Konsultasi dengan tim ahli kami sekarang.', 'tugasin' );
$cta_btn_text    = get_field( 'major_cta_btn_text' ) ?: __( 'Konsultasi Sekarang', 'tugasin' );
?>

<!-- Hero -->
<section class="hero page-hero">
    <div class="container">
        <?php tugasin_breadcrumb(); ?>
        <div class="major-hero-row">
            <div class="major-hero-icon" style="background: <?php echo esc_attr( $icon_bg ); ?>; color: <?php echo esc_attr( $icon_color ); ?>;">
                <i class="fas <?php echo esc_attr( $icon_class ); ?>"></i>
            </div>
            <div class="major-hero-info">
                <?php tugasin_category_badge(); ?>
                <h1><?php the_title(); ?></h1>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="page-content-section">
    <div class="container">
        <div class="page-sidebar-layout">
            <!-- Main Content -->
            <div>
                <?php if ( $what_you_learn ) : ?>
                <div class="major-section">
                    <h2><?php esc_html_e( 'Apa yang Dipelajari?', 'tugasin' ); ?></h2>
                    <div class="major-content-text">
                        <?php echo wp_kses_post( $what_you_learn ); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $courses && is_array( $courses ) ) : ?>
                <div class="major-section">
                    <h2><?php esc_html_e( 'Mata Kuliah Utama', 'tugasin' ); ?></h2>
                    <div class="major-courses-list">
                        <?php foreach ( $courses as $course ) : ?>
                        <span class="major-course-tag">
                            <?php echo esc_html( $course['course_name'] ); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $career_prospects && is_array( $career_prospects ) ) : ?>
                <div class="major-section">
                    <h2><?php esc_html_e( 'Prospek Kerja', 'tugasin' ); ?></h2>
                    <div class="major-career-grid">
                        <?php foreach ( $career_prospects as $career ) : ?>
                        <div class="major-career-card">
                            <h4><?php echo esc_html( $career['job_title'] ); ?></h4>
                            <p><?php echo esc_html( $career['job_desc'] ); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- CTA Card -->
                <div class="major-cta-card">
                    <strong><?php echo esc_html( $cta_title ); ?></strong>
                    <p><?php echo esc_html( $cta_text ); ?></p>
                    <?php echo tugasin_cta_button( $cta_btn_text, 'btn btn-accent' ); ?>
                </div>

                <?php if ( $related_unis && is_array( $related_unis ) ) : ?>
                <!-- Top Universities -->
                <div class="major-related-unis">
                    <h4><?php esc_html_e( 'Kampus Terbaik', 'tugasin' ); ?></h4>
                    <div class="major-uni-list">
                        <?php foreach ( $related_unis as $uni ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $uni->ID ) ); ?>" class="major-uni-link">
                            <?php 
                            $uni_logo = get_field( 'uni_logo', $uni->ID );
                            if ( $uni_logo ) : ?>
                            <img src="<?php echo esc_url( $uni_logo ); ?>" alt="<?php echo esc_attr( $uni->post_title ); ?>">
                            <?php endif; ?>
                            <span><?php echo esc_html( $uni->post_title ); ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
</main>

<?php
get_footer();
