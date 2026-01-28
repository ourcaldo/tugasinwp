<?php
/**
 * Template Name: Joki Makalah
 * EXACT copy from ui/joki-makalah.html
 *
 * @package TugasinWP
 * @since 1.3.0
 */

get_header();
?>

<main id="main-content" role="main">
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-content">
                <span class="pill-badge"
                    style="background: rgba(255,255,255,0.2); color: white; margin-bottom: 16px;"><i class="fas fa-book"
                        style="margin-right: 8px;"></i>Joki Makalah</span>
                <h1>Makalah <span class="text-highlight">Numpuk?</span> Tenang, Ada Solusinya.</h1>
                <p>Tiap minggu ada aja makalah baru. Referensi susah, format beda-beda, deadline berdekatan. Tugasin
                    bantu kamu kelola semuanya tanpa stress.</p>
                <div class="hero-btns">
                    <?php echo tugasin_cta_button( __( 'Konsultasi Gratis', 'tugasin' ), 'btn btn-accent' ); ?>
                </div>
            </div>
            <div class="hero-visual">
                <!-- Stack of Papers Animation -->
                <div class="card float-1"
                    style="top: 5%; right: 10%; padding: 0; width: 260px; overflow: hidden; border-radius: 20px;">
                    <div style="background: var(--pastel-yellow); padding: 20px;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <span style="font-weight: 800; color: #d97706;">1000+</span>
                            <i class="fas fa-file-alt" style="color: #d97706; font-size: 1.2rem;"></i>
                        </div>
                        <strong class="stat-title" style="color: var(--text-primary);">Makalah Selesai</strong>
                        <p style="font-size: 0.85rem; color: var(--text-secondary);">Berbagai topik & jurusan</p>
                    </div>
                    <div style="padding: 16px; background: white;">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <span
                                style="background: var(--bg-light); padding: 6px 12px; border-radius: 20px; font-size: 0.75rem;">Ekonomi</span>
                            <span
                                style="background: var(--bg-light); padding: 6px 12px; border-radius: 20px; font-size: 0.75rem;">Hukum</span>
                            <span
                                style="background: var(--bg-light); padding: 6px 12px; border-radius: 20px; font-size: 0.75rem;">Teknik</span>
                            <span
                                style="background: var(--bg-light); padding: 6px 12px; border-radius: 20px; font-size: 0.75rem;">Kesehatan</span>
                        </div>
                    </div>
                </div>
                <!-- Format Badge -->
                <div class="card"
                    style="bottom: 30%; left: 0; padding: 20px; animation: float-rev 6s ease-in-out infinite;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div
                            style="width: 48px; height: 48px; background: var(--pastel-purple); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-quote-left" style="color: #9333ea;"></i>
                        </div>
                        <div>
                            <strong class="stat-title" style="font-size: 1rem;">Sitasi Lengkap</strong>
                            <p style="font-size: 0.8rem; color: var(--text-secondary);">APA, MLA, Chicago</p>
                        </div>
                    </div>
                </div>
                <!-- Quick Turnaround -->
                <div class="card" style="top: 60%; right: 5%; padding: 16px; animation: float 5s ease-in-out infinite;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-bolt" style="color: #059669; font-size: 1.2rem;"></i>
                        <span style="font-weight: 700;">Express 24 Jam</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Types of Papers - Card Style -->
    <section class="values" style="background: var(--bg-light);">
        <div class="container">
            <div class="section-center">
                <h2>Jenis Makalah yang Bisa Dibantu</h2>
                <p>Dari tugas kuliah biasa sampai karya ilmiah serius.</p>
            </div>
            <div class="services-grid">
                <div class="service-card bg-pastel-yellow" style="padding: 32px;">
                    <div
                        style="width: 64px; height: 64px; background: rgba(217, 119, 6, 0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <i class="fas fa-file-alt" style="font-size: 1.5rem; color: #d97706;"></i>
                    </div>
                    <h3>Makalah Kuliah</h3>
                    <p style="color: var(--text-secondary);">Tugas rutin dari dosen, berbagai mata kuliah dan topik.</p>
                </div>
                <div class="service-card bg-pastel-indigo" style="padding: 32px;">
                    <div
                        style="width: 64px; height: 64px; background: rgba(79, 70, 229, 0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <i class="fas fa-microscope" style="font-size: 1.5rem; color: #4f46e5;"></i>
                    </div>
                    <h3>Karya Ilmiah</h3>
                    <p style="color: var(--text-secondary);">Paper untuk jurnal, seminar, atau kompetisi akademik.</p>
                </div>
                <div class="service-card bg-pastel-green" style="padding: 32px;">
                    <div
                        style="width: 64px; height: 64px; background: rgba(5, 150, 105, 0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <i class="fas fa-book-open" style="font-size: 1.5rem; color: #059669;"></i>
                    </div>
                    <h3>Literature Review</h3>
                    <p style="color: var(--text-secondary);">Tinjauan pustaka yang komprehensif dan terstruktur.</p>
                </div>
                <div class="service-card bg-pastel-purple" style="padding: 32px;">
                    <div
                        style="width: 64px; height: 64px; background: rgba(147, 51, 234, 0.2); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                        <i class="fas fa-balance-scale" style="font-size: 1.5rem; color: #9333ea;"></i>
                    </div>
                    <h3>Essay & Opini</h3>
                    <p style="color: var(--text-secondary);">Tulisan argumentatif dengan sudut pandang yang kuat.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- What's Included -->
    <section class="services">
        <div class="container">
            <div class="section-center">
                <h2>Yang Kamu Dapatkan</h2>
            </div>
            <div class="values-grid">
                <div
                    style="background: white; padding: 32px; border-radius: 20px; border: 1px solid #f3f4f6; text-align: center;">
                    <div
                        style="width: 64px; height: 64px; background: var(--pastel-yellow); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-search" style="color: #d97706; font-size: 1.5rem;"></i>
                    </div>
                    <h3 style="font-size: 1rem;">Riset Mendalam</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Sumber dari jurnal dan buku terpercaya.
                    </p>
                </div>
                <div
                    style="background: white; padding: 32px; border-radius: 20px; border: 1px solid #f3f4f6; text-align: center;">
                    <div
                        style="width: 64px; height: 64px; background: var(--pastel-indigo); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-paragraph" style="color: #4f46e5; font-size: 1.5rem;"></i>
                    </div>
                    <h3 style="font-size: 1rem;">Formatting Rapi</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Sesuai format yang diminta dosen.</p>
                </div>
                <div
                    style="background: white; padding: 32px; border-radius: 20px; border: 1px solid #f3f4f6; text-align: center;">
                    <div
                        style="width: 64px; height: 64px; background: var(--pastel-green); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-redo" style="color: #059669; font-size: 1.5rem;"></i>
                    </div>
                    <h3 style="font-size: 1rem;">Revisi Gratis</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Ada koreksi? Revisi tanpa biaya
                        tambahan.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="values" style="background: white;">
        <div class="container">
            <div class="section-center">
                <h2>Harga Bersahabat untuk Kantong Mahasiswa</h2>
            </div>
            <div class="tugasin-grid-3">
                <div class="service-card bg-pastel-yellow" style="padding: 36px;">
                    <h3 style="margin-bottom: 8px; font-size: 1.1rem;">Makalah Singkat</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 16px;">5-10 halaman</p>
                    <strong class="price-tag" style="font-size: 2rem; font-weight: 800; margin-bottom: 20px; display: block;">Mulai 100K</strong>
                    <ul style="list-style: none; padding: 0; margin-bottom: 24px; font-size: 0.9rem;">
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Riset & penulisan</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Daftar pustaka</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Revisi 1x</li>
                    </ul>
                    <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                        class="btn btn-outline" style="width: 100%; justify-content: center;">Konsultasi</a>
                </div>
                <div class="service-card bg-pastel-indigo" style="padding: 36px; border: 3px solid var(--primary);">
                    <span
                        style="background: var(--primary); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">Populer</span>
                    <h3 style="margin-top: 12px; margin-bottom: 8px; font-size: 1.1rem;">Makalah Standar</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 16px;">10-20 halaman</p>
                    <strong class="price-tag" style="font-size: 2rem; font-weight: 800; margin-bottom: 20px; display: block;">Mulai 200K</strong>
                    <ul style="list-style: none; padding: 0; margin-bottom: 24px; font-size: 0.9rem;">
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Riset mendalam</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Sitasi lengkap</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Revisi 2x</li>
                    </ul>
                    <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                        class="btn btn-primary" style="width: 100%; justify-content: center;">Konsultasi</a>
                </div>
                <div class="service-card bg-pastel-green" style="padding: 36px;">
                    <h3 style="margin-bottom: 8px; font-size: 1.1rem;">Karya Ilmiah</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 16px;">Jurnal / Paper</p>
                    <strong class="price-tag" style="font-size: 2rem; font-weight: 800; margin-bottom: 20px; display: block;">Mulai 500K</strong>
                    <ul style="list-style: none; padding: 0; margin-bottom: 24px; font-size: 0.9rem;">
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Standar publikasi</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Referensi premium</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Revisi unlimited</li>
                    </ul>
                    <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                        class="btn btn-outline" style="width: 100%; justify-content: center;">Konsultasi</a>
                </div>
            </div>
            <p style="text-align: center; margin-top: 24px; color: var(--text-secondary);">*Harga bisa berbeda
                tergantung topik, deadline, dan spesifikasi dosen.</p>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="talent" style="background: var(--bg-light);">
        <div class="container">
            <div class="section-center">
                <h2>Kata Mereka yang Sudah Pakai</h2>
            </div>
            <div class="values-grid">
                <?php 
                $testimonials = tugasin_get_testimonials( 'joki_makalah' );
                $placeholder_image = get_template_directory_uri() . '/assets/images/placeholder-avatar.jpg';
                
                if ( ! empty( $testimonials ) ) :
                    foreach ( $testimonials as $i => $testimonial ) :
                        $name  = isset( $testimonial['name'] ) ? $testimonial['name'] : '';
                        $role  = isset( $testimonial['role'] ) ? $testimonial['role'] : '';
                        $image = isset( $testimonial['image'] ) && ! empty( $testimonial['image'] ) ? $testimonial['image'] : $placeholder_image;
                        $text  = isset( $testimonial['text'] ) ? $testimonial['text'] : '';
                        $alt   = isset( $testimonial['alt'] ) && ! empty( $testimonial['alt'] ) ? $testimonial['alt'] : 'Foto ' . $name;
                        
                        if ( empty( $name ) && empty( $text ) ) continue;
                ?>
                <div class="value-card" style="text-align: left;">
                    <div style="display: flex; gap: 4px; margin-bottom: 12px; color: #fbbf24;"><i
                            class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                            class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p style="color: var(--text-secondary); margin-bottom: 16px;">"<?php echo esc_html( $text ); ?>"</p>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $alt ); ?>" style="border-radius: 50%; width: 48px; height: 48px; object-fit: cover;">
                        <div><strong><?php echo esc_html( $name ); ?></strong>
                            <p style="font-size: 0.8rem; color: var(--text-secondary);"><?php echo esc_html( $role ); ?></p>
                        </div>
                    </div>
                </div>
                <?php 
                    endforeach;
                else : 
                ?>
                <div class="value-card" style="text-align: left;">
                    <div style="display: flex; gap: 4px; margin-bottom: 12px; color: #fbbf24;"><i
                            class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                            class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p style="color: var(--text-secondary); margin-bottom: 16px;">"Makalah ekonomi makro saya selesai dalam 3 hari. Sitasinya lengkap dan rapi, dosen langsung kasih nilai A!"</p>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <img src="<?php echo esc_url( $placeholder_image ); ?>" alt="Foto Andi P." style="border-radius: 50%; width: 48px; height: 48px; object-fit: cover;">
                        <div><strong>Andi P.</strong>
                            <p style="font-size: 0.8rem; color: var(--text-secondary);">Ekonomi - Unpad</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- SEO Content Box -->
    <section class="seo-section" style="padding: 60px 0;">
        <div class="container">
            <?php tugasin_seo_content_box( 5 ); ?>
        </div>
    </section>

    <!-- CTA -->
    <section class="hero" style="clip-path: polygon(0 10%, 100% 0, 100% 100%, 0 100%); padding: 120px 0 80px;">
        <div class="container" style="text-align: center;">
            <h2 style="font-size: 2.5rem; margin-bottom: 16px;">Makalah Menumpuk? Serahkan ke Tugasin.</h2>
            <p style="color: rgba(255,255,255,0.8); margin-bottom: 32px;">Fokus ke kuliah, biar Tugasin yang handle
                penulisannya.</p>
            <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                class="btn btn-accent" style="font-size: 1.1rem; padding: 16px 32px;"><i class="fab fa-whatsapp"></i> Chat via WhatsApp</a>
        </div>
    </section>
</main>

<?php
get_footer();
