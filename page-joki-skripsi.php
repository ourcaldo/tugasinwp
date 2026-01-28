<?php
/**
 * Template Name: Joki Skripsi
 * EXACT copy from ui/joki-skripsi.html - DO NOT SIMPLIFY
 *
 * @package TugasinWP
 * @since 1.3.0
 */

get_header();
?>

<main id="main-content" role="main">
    <!-- Hero with Enhanced Visuals -->
    <section class="hero">
        <div class="container hero-grid">
            <div class="hero-content">
                <span class="pill-badge"
                    style="background: rgba(255,255,255,0.2); color: white; margin-bottom: 16px;"><i
                        class="fas fa-graduation-cap" style="margin-right: 8px;"></i>Joki Skripsi</span>
                <h1>Skripsi Terasa <span class="text-highlight">Berat?</span> Kamu Nggak Sendirian.</h1>
                <p>Deadline mepet, dosen susah ditemui, revisi nggak ada habisnya. Tugasin paham rasanya dan hadir untuk
                    bantu kamu melewati fase tersulit ini.</p>
                <div class="hero-btns">
                    <?php echo tugasin_cta_button( __( 'Konsultasi Gratis', 'tugasin' ), 'btn btn-accent' ); ?>
                </div>
            </div>
            <div class="hero-visual">
                <!-- Main Stats Card -->
                <div class="card float-1" style="top: 0; right: 5%; padding: 28px; width: 280px;">
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                        <div
                            style="width: 56px; height: 56px; background: var(--pastel-indigo); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-graduation-cap" style="font-size: 1.5rem; color: #4f46e5;"></i>
                        </div>
                        <div>
                            <strong style="font-size: 1.8rem; font-weight: 800; color: #4f46e5; display: block;">98%</strong>
                            <p style="color: var(--text-secondary); font-size: 0.85rem;">Tingkat Kelulusan</p>
                        </div>
                    </div>
                    <div style="display: flex; gap: 12px;">
                        <div
                            style="flex: 1; background: var(--bg-light); padding: 12px; border-radius: 12px; text-align: center;">
                            <span style="font-weight: 800; color: var(--primary);">500+</span>
                            <p style="font-size: 0.75rem; color: var(--text-secondary);">Skripsi</p>
                        </div>
                        <div
                            style="flex: 1; background: var(--bg-light); padding: 12px; border-radius: 12px; text-align: center;">
                            <span style="font-weight: 800; color: var(--primary);">50+</span>
                            <p style="font-size: 0.75rem; color: var(--text-secondary);">Kampus</p>
                        </div>
                    </div>
                </div>
                <!-- Progress Card -->
                <div class="card"
                    style="bottom: 25%; left: 0; padding: 20px; width: 220px; animation: float-rev 7s ease-in-out infinite;">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                        <div
                            style="width: 40px; height: 40px; background: var(--pastel-green); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check" style="color: #059669;"></i>
                        </div>
                        <span style="font-weight: 700;">Progress</span>
                    </div>
                    <div style="background: #e5e7eb; height: 8px; border-radius: 4px; overflow: hidden;">
                        <div
                            style="background: linear-gradient(90deg, #4f46e5, #06b6d4); height: 100%; width: 85%; border-radius: 4px;">
                        </div>
                    </div>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 8px;">Bab 4 - Analisis Data
                    </p>
                </div>
                <!-- Chat Bubble -->
                <div class="card"
                    style="top: 55%; right: 0; padding: 16px; max-width: 200px; animation: float 5s ease-in-out infinite;">
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 8px;">"Revisi ACC! ✨"</p>
                    <p style="font-size: 0.75rem; color: #059669; font-weight: 600;">Dosen Pembimbing</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Unique Section: The Journey -->
    <section class="values" style="background: var(--bg-light);">
        <div class="container">
            <div class="section-center">
                <h2>Perjalanan Skripsi yang Melelahkan</h2>
                <p>Kamu pasti pernah ngerasain salah satu dari ini.</p>
            </div>
            <div class="tugasin-grid-4">
                <div class="value-card" style="text-align: center; padding: 32px 24px;">
                    <div
                        style="width: 72px; height: 72px; background: var(--pastel-indigo); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-times-circle" style="font-size: 2rem; color: #4f46e5;"></i>
                    </div>
                    <h3 style="margin-bottom: 8px; font-size: 1.1rem;">Judul Ditolak</h3>
                    <p style="font-size: 0.9rem; color: var(--text-secondary);">Sudah ke-5 kalinya dan masih belum ACC.
                    </p>
                </div>
                <div class="value-card" style="text-align: center; padding: 32px 24px;">
                    <div
                        style="width: 72px; height: 72px; background: var(--pastel-yellow); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-hourglass-half" style="font-size: 2rem; color: #d97706;"></i>
                    </div>
                    <h3 style="margin-bottom: 8px; font-size: 1.1rem;">Deadline Mepet</h3>
                    <p style="font-size: 0.9rem; color: var(--text-secondary);">Semester depan harus sudah sidang.</p>
                </div>
                <div class="value-card" style="text-align: center; padding: 32px 24px;">
                    <div
                        style="width: 72px; height: 72px; background: var(--pastel-purple); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-sync-alt" style="font-size: 2rem; color: #9333ea;"></i>
                    </div>
                    <h3 style="margin-bottom: 8px; font-size: 1.1rem;">Revisi Terus</h3>
                    <p style="font-size: 0.9rem; color: var(--text-secondary);">Catatan dosen nggak habis-habis.</p>
                </div>
                <div class="value-card" style="text-align: center; padding: 32px 24px;">
                    <div
                        style="width: 72px; height: 72px; background: var(--pastel-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-chart-pie" style="font-size: 2rem; color: #059669;"></i>
                    </div>
                    <h3 style="margin-bottom: 8px; font-size: 1.1rem;">Bingung Olah Data</h3>
                    <p style="font-size: 0.9rem; color: var(--text-secondary);">SPSS? Regresi? Nggak ngerti sama sekali.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- What Tugasin Covers - Timeline Layout -->
    <section class="services">
        <div class="container">
            <div class="section-center">
                <h2>Tugasin Bantu di Setiap Tahap</h2>
                <p>Dari awal sampai akhir, Tugasin mendampingi perjalanan skripsimu.</p>
            </div>
            <div class="process-timeline">
                <!-- Step 1 -->
                <div class="process-timeline-step">
                    <div
                        style="width: 64px; height: 64px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.5rem;">
                        1</div>
                    <div>
                        <h3 style="font-size: 0.95rem; margin-bottom: 4px;">Topik &amp; Judul</h3>
                        <p style="font-size: 0.8rem; color: var(--text-secondary);">Cari ide yang tepat</p>
                    </div>
                </div>
                <div class="process-timeline-separator"
                    style="background: linear-gradient(90deg, var(--primary), #06b6d4);">
                </div>
                <!-- Step 2 -->
                <div class="process-timeline-step">
                    <div
                        style="width: 64px; height: 64px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.5rem;">
                        2</div>
                    <div>
                        <h3 style="font-size: 0.95rem; margin-bottom: 4px;">Proposal</h3>
                        <p style="font-size: 0.8rem; color: var(--text-secondary);">Susun kerangka awal</p>
                    </div>
                </div>
                <div class="process-timeline-separator"
                    style="background: linear-gradient(90deg, #06b6d4, var(--primary));">
                </div>
                <!-- Step 3 -->
                <div class="process-timeline-step">
                    <div
                        style="width: 64px; height: 64px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.5rem;">
                        3</div>
                    <div>
                        <h3 style="font-size: 0.95rem; margin-bottom: 4px;">Bab 1-3</h3>
                        <p style="font-size: 0.8rem; color: var(--text-secondary);">Landasan teori</p>
                    </div>
                </div>
                <div class="process-timeline-separator"
                    style="background: linear-gradient(90deg, var(--primary), #06b6d4);">
                </div>
                <!-- Step 4 -->
                <div class="process-timeline-step">
                    <div
                        style="width: 64px; height: 64px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.5rem;">
                        4</div>
                    <div>
                        <h3 style="font-size: 0.95rem; margin-bottom: 4px;">Olah Data</h3>
                        <p style="font-size: 0.8rem; color: var(--text-secondary);">SPSS, analisis</p>
                    </div>
                </div>
                <div class="process-timeline-separator"
                    style="background: linear-gradient(90deg, #06b6d4, #059669);">
                </div>
                <!-- Step 5 -->
                <div class="process-timeline-step">
                    <div
                        style="width: 64px; height: 64px; background: #059669; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 1.5rem;">
                        <i class="fas fa-check"></i></div>
                    <div>
                        <h3 style="font-size: 0.95rem; margin-bottom: 4px;">Bab 4-5</h3>
                        <p style="font-size: 0.8rem; color: var(--text-secondary);">Siap sidang!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="values" style="background: white;">
        <div class="container">
            <div class="section-center">
                <h2>Investasi untuk Kelulusanmu</h2>
                <p>Pilih paket yang sesuai dengan kebutuhan skripsimu.</p>
            </div>
            <div class="services-grid">
                <div class="service-card bg-pastel-indigo" style="padding: 40px;">
                    <span
                        style="background: white; padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; color: #4f46e5;">Per
                        Bab</span>
                    <strong class="price-tag" style="margin-top: 20px; font-size: 2.5rem; font-weight: 800; display: block;">Mulai 300K</strong>
                    <p style="margin-top: 8px; margin-bottom: 24px;">Cocok kalau kamu butuh bantuan di bab tertentu aja.
                    </p>
                    <ul style="list-style: none; padding: 0; margin-bottom: 24px;">
                        <li style="padding: 8px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Penulisan 1 bab</li>
                        <li style="padding: 8px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Revisi 2x</li>
                        <li style="padding: 8px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Konsultasi via chat</li>
                    </ul>
                    <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                        class="btn btn-primary" style="width: 100%; justify-content: center;">Konsultasi <i
                            class="fas fa-arrow-right"></i></a>
                </div>
                <div class="service-card bg-pastel-green" style="padding: 40px; border: 3px solid var(--primary);">
                    <span
                        style="background: var(--primary); padding: 6px 14px; border-radius: 20px; font-size: 0.85rem; font-weight: 700; color: white;">Full
                        Package ⭐</span>
                    <strong class="price-tag" style="margin-top: 20px; font-size: 2.5rem; font-weight: 800; display: block;">Mulai 2.5Jt</strong>
                    <p style="margin-top: 8px; margin-bottom: 24px;">Dari judul sampai siap sidang. Paling laris!</p>
                    <ul style="list-style: none; padding: 0; margin-bottom: 24px;">
                        <li style="padding: 8px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Bab 1 - 5 lengkap</li>
                        <li style="padding: 8px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Olah data (SPSS/dll)</li>
                        <li style="padding: 8px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Revisi unlimited</li>
                        <li style="padding: 8px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Free cek plagiarism</li>
                    </ul>
                    <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                        class="btn btn-primary" style="width: 100%; justify-content: center;">Konsultasi <i
                            class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <p style="text-align: center; margin-top: 24px; color: var(--text-secondary);">*Harga bisa berbeda
                tergantung kompleksitas, jurusan, dan deadline. Konsultasi dulu untuk estimasi akurat.</p>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="talent" style="background: var(--bg-light);">
        <div class="container">
            <div class="section-center">
                <h2>Cerita Mereka yang Sudah Wisuda</h2>
            </div>
            <div class="values-grid">
                <?php 
                $testimonials = tugasin_get_testimonials( 'joki_skripsi' );
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
                    // Fallback to hardcoded if no testimonials configured
                ?>
                <div class="value-card" style="text-align: left;">
                    <div style="display: flex; gap: 4px; margin-bottom: 12px; color: #fbbf24;"><i
                            class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                            class="fas fa-star"></i><i class="fas fa-star"></i></div>
                    <p style="color: var(--text-secondary); margin-bottom: 16px;">"Skripsi yang tadinya stuck 6 bulan, selesai dalam 2 bulan dengan Tugasin. Tim-nya responsif banget!"</p>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <img src="<?php echo esc_url( $placeholder_image ); ?>" alt="Foto Rina A." style="border-radius: 50%; width: 48px; height: 48px; object-fit: cover;">
                        <div><strong>Rina A.</strong>
                            <p style="font-size: 0.8rem; color: var(--text-secondary);">Psikologi - UI</p>
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
            <h2 style="font-size: 2.5rem; margin-bottom: 16px;">Siap Akhiri Drama Skripsi?</h2>
            <p style="color: rgba(255,255,255,0.8); margin-bottom: 32px;">Konsultasi gratis sekarang. Ceritakan kondisi
                skripsimu, dapatkan estimasi harga dan waktu.</p>
            <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                class="btn btn-accent" style="font-size: 1.1rem; padding: 16px 32px;"><i class="fab fa-whatsapp"></i> Chat via WhatsApp</a>
        </div>
    </section>
</main>

<?php
get_footer();
