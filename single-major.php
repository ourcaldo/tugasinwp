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
<section class="hero" style="padding: 120px 0 80px;">
    <div class="container">
        <?php tugasin_breadcrumb(); ?>
        <div style="display: flex; align-items: center; gap: 24px; margin-top: 24px;">
            <div style="width: 80px; height: 80px; background: <?php echo esc_attr( $icon_bg ); ?>; color: <?php echo esc_attr( $icon_color ); ?>; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                <i class="fas <?php echo esc_attr( $icon_class ); ?>"></i>
            </div>
            <div>
                <?php tugasin_category_badge(); ?>
                <h1 style="font-size: 2.5rem; margin-top: 8px;"><?php the_title(); ?></h1>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section style="padding: 60px 0 100px;">
    <div class="container">
        <div class="page-sidebar-layout">
            <!-- Main Content -->
            <div>
                <?php if ( $what_you_learn ) : ?>
                <div style="margin-bottom: 48px;">
                    <h2 style="font-size: 1.75rem; margin-bottom: 24px;"><?php esc_html_e( 'Apa yang Dipelajari?', 'tugasin' ); ?></h2>
                    <div style="font-size: 1.1rem; line-height: 1.8; color: #374151;">
                        <?php echo wp_kses_post( $what_you_learn ); ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $courses && is_array( $courses ) ) : ?>
                <div style="margin-bottom: 48px;">
                    <h2 style="font-size: 1.75rem; margin-bottom: 24px;"><?php esc_html_e( 'Mata Kuliah Utama', 'tugasin' ); ?></h2>
                    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
                        <?php foreach ( $courses as $course ) : ?>
                        <span style="background: var(--pastel-indigo); color: var(--primary); padding: 8px 16px; border-radius: 20px; font-weight: 600;">
                            <?php echo esc_html( $course['course_name'] ); ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ( $career_prospects && is_array( $career_prospects ) ) : ?>
                <div style="margin-bottom: 48px;">
                    <h2 style="font-size: 1.75rem; margin-bottom: 24px;"><?php esc_html_e( 'Prospek Kerja', 'tugasin' ); ?></h2>
                    <div style="display: grid; gap: 20px;">
                        <?php foreach ( $career_prospects as $career ) : ?>
                        <div style="background: var(--bg-light); padding: 24px; border-radius: 16px;">
                            <h4 style="font-size: 1.1rem; margin-bottom: 8px;"><?php echo esc_html( $career['job_title'] ); ?></h4>
                            <p style="color: var(--text-secondary); margin: 0;"><?php echo esc_html( $career['job_desc'] ); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div>
                <!-- CTA Card -->
                <div style="background: var(--primary); color: white; padding: 32px; border-radius: 24px; margin-bottom: 32px;">
                    <strong style="font-size: 1.5rem; margin-bottom: 16px; display: block;"><?php echo esc_html( $cta_title ); ?></strong>
                    <p style="color: rgba(255,255,255,0.8); margin-bottom: 24px;"><?php echo esc_html( $cta_text ); ?></p>
                    <?php echo tugasin_cta_button( $cta_btn_text, 'btn btn-accent' ); ?>
                </div>

                <?php if ( $related_unis && is_array( $related_unis ) ) : ?>
                <!-- Top Universities -->
                <div style="background: white; border: 1px solid #e5e7eb; padding: 24px; border-radius: 24px;">
                    <h4 style="font-size: 1.1rem; margin-bottom: 20px;"><?php esc_html_e( 'Kampus Terbaik', 'tugasin' ); ?></h4>
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <?php foreach ( $related_unis as $uni ) : ?>
                        <a href="<?php echo esc_url( get_permalink( $uni->ID ) ); ?>" style="display: flex; align-items: center; gap: 12px; color: inherit; text-decoration: none;">
                            <?php 
                            $uni_logo = get_field( 'uni_logo', $uni->ID );
                            if ( $uni_logo ) : ?>
                            <img src="<?php echo esc_url( $uni_logo ); ?>" alt="" style="width: 40px; height: 40px; object-fit: contain;">
                            <?php endif; ?>
                            <span style="font-weight: 600;"><?php echo esc_html( $uni->post_title ); ?></span>
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
