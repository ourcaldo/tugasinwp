<?php
/**
 * Front Page Template
 *
 * @package TugasinWP
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content" role="main">
<!-- Hero Section -->
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-content">
            <h1><?php esc_html_e( 'Deadline Mepet?', 'tugasin' ); ?> <span class="text-highlight"><?php esc_html_e( 'Tugasin Aja!', 'tugasin' ); ?></span></h1>
            <p><?php esc_html_e( 'Partner akademik terpercaya untuk mahasiswa Indonesia. Dari skripsi sampai tugas harian, semuanya beres!', 'tugasin' ); ?></p>
            <div class="hero-btns">
                <?php echo tugasin_cta_button( __( 'Konsultasi Gratis', 'tugasin' ), 'btn btn-accent' ); ?>
                <div class="hero-trust">
                    <div class="avatars">
                        <?php 
                        $placeholder = get_template_directory_uri() . '/assets/images/placeholder-avatar.jpg';
                        $testimonials = tugasin_get_testimonials( 'joki_skripsi' );
                        
                        // Show up to 3 testimonial avatars
                        $shown = 0;
                        foreach ( $testimonials as $testimonial ) :
                            if ( $shown >= 3 ) break;
                            $image = ! empty( $testimonial['image'] ) ? $testimonial['image'] : $placeholder;
                            $alt = ! empty( $testimonial['alt'] ) ? $testimonial['alt'] : 'User';
                        ?>
                        <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>">
                        <?php 
                            $shown++;
                        endforeach;
                        
                        // Fill remaining slots with placeholders
                        while ( $shown < 3 ) :
                        ?>
                        <img src="<?php echo esc_url( $placeholder ); ?>" alt="User">
                        <?php 
                            $shown++;
                        endwhile;
                        ?>
                        <span class="avatar-plus">5K+</span>
                    </div>
                    <span style="font-size: 0.9rem; color: rgba(255,255,255,0.8);"><?php esc_html_e( 'Mahasiswa Terbantu', 'tugasin' ); ?></span>
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <?php get_template_part( 'template-parts/layout/hero-carousel' ); ?>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="values">
    <div class="container">
        <div class="section-center">
            <h2><?php esc_html_e( 'Kenapa Pilih Tugasin?', 'tugasin' ); ?></h2>
            <p><?php esc_html_e( 'Bukan sekadar jasa, tapi partner belajar yang paham kebutuhanmu.', 'tugasin' ); ?></p>
        </div>
        <div class="values-grid">
            <div class="value-card">
                <div class="icon-box bg-purple"><i class="fas fa-shield-alt"></i></div>
                <h3><?php esc_html_e( '100% Rahasia', 'tugasin' ); ?></h3>
                <p><?php esc_html_e( 'Data dan identitasmu aman. Privasi adalah prioritas utama kami.', 'tugasin' ); ?></p>
            </div>
            <div class="value-card">
                <div class="icon-box bg-yellow"><i class="fas fa-bolt"></i></div>
                <h3><?php esc_html_e( 'Respons Cepat', 'tugasin' ); ?></h3>
                <p><?php esc_html_e( 'Tim support aktif 24/7. Konsultasi kapan saja, jawaban instan.', 'tugasin' ); ?></p>
            </div>
            <div class="value-card">
                <div class="icon-box bg-green"><i class="fas fa-star"></i></div>
                <h3><?php esc_html_e( 'Kualitas Terjamin', 'tugasin' ); ?></h3>
                <p><?php esc_html_e( 'Dikerjakan oleh ahli. Garansi revisi sampai puas.', 'tugasin' ); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services">
    <div class="container">
        <div class="section-center">
            <h2><?php esc_html_e( 'Layanan Kami', 'tugasin' ); ?></h2>
            <p><?php esc_html_e( 'Solusi lengkap untuk setiap kebutuhan akademik mahasiswa.', 'tugasin' ); ?></p>
        </div>
        <div class="services-grid">
            <a href="<?php echo esc_url( tugasin_get_page_url( 'joki-skripsi' ) ); ?>" class="service-card bg-pastel-indigo">
                <div class="service-icon"><i class="fas fa-graduation-cap"></i></div>
                <h3><?php esc_html_e( 'Joki Skripsi', 'tugasin' ); ?></h3>
                <p><?php esc_html_e( 'Bantuan skripsi profesional dengan bimbingan sampai selesai.', 'tugasin' ); ?></p>
                <span class="check-circle"><i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="<?php echo esc_url( tugasin_get_page_url( 'joki-makalah' ) ); ?>" class="service-card bg-pastel-yellow">
                <div class="service-icon"><i class="fas fa-book"></i></div>
                <h3><?php esc_html_e( 'Joki Makalah', 'tugasin' ); ?></h3>
                <p><?php esc_html_e( 'Makalah dan karya ilmiah berkualitas sesuai standar.', 'tugasin' ); ?></p>
                <span class="check-circle"><i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="<?php echo esc_url( tugasin_get_page_url( 'joki-tugas' ) ); ?>" class="service-card bg-pastel-green">
                <div class="service-icon"><i class="fas fa-tasks"></i></div>
                <h3><?php esc_html_e( 'Joki Tugas', 'tugasin' ); ?></h3>
                <p><?php esc_html_e( 'Semua jenis tugas kuliah harian, dikerjakan cepat dan tepat.', 'tugasin' ); ?></p>
                <span class="check-circle"><i class="fas fa-arrow-right"></i></span>
            </a>
            <a href="<?php echo esc_url( tugasin_get_page_url( 'cek-plagiarism' ) ); ?>" class="service-card bg-pastel-gray">
                <div class="service-icon"><i class="fas fa-search"></i></div>
                <h3><?php esc_html_e( 'Cek Plagiarism', 'tugasin' ); ?></h3>
                <p><?php esc_html_e( 'Cek Turnitin resmi dan akurat, laporan lengkap.', 'tugasin' ); ?></p>
                <span class="check-circle"><i class="fas fa-arrow-right"></i></span>
            </a>
        </div>
    </div>
</section>

<!-- SEO Content Box -->
<section class="seo-section" style="padding: 60px 0 0;">
    <div class="container">
        <?php tugasin_seo_content_box( 5 ); ?>
    </div>
</section>

<!-- CTA Section -->
<section style="padding: 100px 0; background: var(--primary);">
    <div class="container" style="text-align: center;">
        <h2 style="color: white; font-size: 2.5rem; margin-bottom: 16px;"><?php esc_html_e( 'Siap Selesaikan Tugasmu?', 'tugasin' ); ?></h2>
        <p style="color: rgba(255,255,255,0.8); font-size: 1.2rem; max-width: 600px; margin: 0 auto 32px;">
            <?php esc_html_e( 'Konsultasi gratis sekarang. Tim kami siap membantu 24/7!', 'tugasin' ); ?>
        </p>
        <?php echo tugasin_cta_button( __( 'Hubungi via WhatsApp', 'tugasin' ), 'btn btn-accent' ); ?>
    </div>
</section>
</main>

<?php
get_footer();
