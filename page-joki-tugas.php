<?php
/**
 * Template Name: Joki Tugas
 * EXACT copy from ui/joki-tugas.html
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
                    style="background: rgba(255,255,255,0.2); color: white; margin-bottom: 16px;"><i class="fas fa-bolt"
                        style="margin-right: 8px;"></i>Joki Tugas</span>
                <h1>Tugas <span class="text-highlight">Menumpuk?</span> Waktumu Terlalu Berharga.</h1>
                <p>Setiap hari ada tugas baru. Belum selesai yang satu, sudah datang yang lain. Tugasin bantu kamu
                    supaya masih bisa tidur nyenyak dan punya waktu untuk diri sendiri.</p>
                <div class="hero-btns">
                    <?php echo tugasin_cta_button( __( 'Order Sekarang', 'tugasin' ), 'btn btn-accent' ); ?>
                </div>
            </div>
            <div class="hero-visual">
                <!-- Speed Indicator -->
                <div class="card float-1" style="top: 0; right: 5%; padding: 24px; width: 260px;">
                    <div
                        style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                        <strong class="stat-title">Respons Time</strong>
                        <span
                            style="background: var(--pastel-green); color: #059669; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">⚡
                            Fast</span>
                    </div>
                    <div style="font-size: 3rem; font-weight: 800; color: #059669; margin-bottom: 8px;">
                        < 1 Jam</div>
                            <p style="color: var(--text-secondary); font-size: 0.85rem;">Rata-rata waktu respons pertama
                            </p>
                    </div>
                    <!-- Task Categories -->
                    <div class="card"
                        style="bottom: 20%; left: 0; padding: 20px; width: 200px; animation: float-rev 6s ease-in-out infinite;">
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            <span
                                style="background: var(--pastel-indigo); padding: 8px 12px; border-radius: 20px; font-size: 0.8rem;"><i
                                    class="fas fa-calculator" style="margin-right: 4px;"></i>Hitungan</span>
                            <span
                                style="background: var(--pastel-yellow); padding: 8px 12px; border-radius: 20px; font-size: 0.8rem;"><i
                                    class="fas fa-file-word" style="margin-right: 4px;"></i>Essay</span>
                            <span
                                style="background: var(--pastel-green); padding: 8px 12px; border-radius: 20px; font-size: 0.8rem;"><i
                                    class="fas fa-code" style="margin-right: 4px;"></i>Coding</span>
                            <span
                                style="background: var(--pastel-purple); padding: 8px 12px; border-radius: 20px; font-size: 0.8rem;"><i
                                    class="fas fa-palette" style="margin-right: 4px;"></i>Desain</span>
                        </div>
                    </div>
                    <!-- Completed Counter -->
                    <div class="card"
                        style="top: 55%; right: 0; padding: 16px; animation: float 5s ease-in-out infinite;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div
                                style="width: 40px; height: 40px; background: var(--pastel-green); border-radius: 10px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-check" style="color: #059669;"></i>
                            </div>
                            <div>
                                <span style="font-weight: 800; font-size: 1.2rem;">3000+</span>
                                <p style="font-size: 0.75rem; color: var(--text-secondary);">Tugas Selesai</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <!-- Subject Categories - Infinite Slider -->
    <section class="values" style="background: var(--bg-light); overflow: hidden;">
        <div class="container">
            <div class="section-center">
                <h2>Hampir Semua Jenis Tugas Bisa!</h2>
                <p>Dari yang simpel sampai yang bikin pusing.</p>
            </div>
        </div>
        <!-- Infinite Marquee -->
        <div style="overflow: hidden; width: 100%; padding: 20px 0;">
            <div class="marquee-track" style="display: flex; gap: 20px; animation: marquee 30s linear infinite;">
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-calculator" style="font-size: 1.5rem; color: #4f46e5;"></i>
                    <span style="font-weight: 600;">Matematika</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-chart-bar" style="font-size: 1.5rem; color: #d97706;"></i>
                    <span style="font-weight: 600;">Statistik</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-code" style="font-size: 1.5rem; color: #059669;"></i>
                    <span style="font-weight: 600;">Programming</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-file-word" style="font-size: 1.5rem; color: #9333ea;"></i>
                    <span style="font-weight: 600;">Essay</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-table" style="font-size: 1.5rem; color: #059669;"></i>
                    <span style="font-weight: 600;">Akuntansi</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-palette" style="font-size: 1.5rem; color: #ec4899;"></i>
                    <span style="font-weight: 600;">Desain</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-flask" style="font-size: 1.5rem; color: #06b6d4;"></i>
                    <span style="font-weight: 600;">Kimia</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-atom" style="font-size: 1.5rem; color: #4f46e5;"></i>
                    <span style="font-weight: 600;">Fisika</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-language" style="font-size: 1.5rem; color: #d97706;"></i>
                    <span style="font-weight: 600;">Bahasa Inggris</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-gavel" style="font-size: 1.5rem; color: #374151;"></i>
                    <span style="font-weight: 600;">Hukum</span>
                </div>
                <!-- Duplicate for infinite loop -->
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-calculator" style="font-size: 1.5rem; color: #4f46e5;"></i>
                    <span style="font-weight: 600;">Matematika</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-chart-bar" style="font-size: 1.5rem; color: #d97706;"></i>
                    <span style="font-weight: 600;">Statistik</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-code" style="font-size: 1.5rem; color: #059669;"></i>
                    <span style="font-weight: 600;">Programming</span>
                </div>
                <div
                    style="flex-shrink: 0; background: white; padding: 20px 32px; border-radius: 16px; text-align: center; border: 1px solid #f3f4f6; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-file-word" style="font-size: 1.5rem; color: #9333ea;"></i>
                    <span style="font-weight: 600;">Essay</span>
                </div>
            </div>
        </div>
        <p style="text-align: center; color: var(--text-secondary);">Dan masih banyak lagi! Tanya dulu aja, pasti bisa
            dibantu.</p>
    </section>

    <style>
        @keyframes marquee {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }
    </style>

    <!-- Speed Guarantee -->
    <section class="services">
        <div class="container">
            <div class="section-center">
                <h2>Kecepatan adalah Segalanya</h2>
            </div>
            <div class="tugasin-grid-3">
                <div style="text-align: center;">
                    <div
                        style="width: 100px; height: 100px; background: linear-gradient(135deg, #059669, #34d399); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                        <span style="color: white; font-size: 2.5rem; font-weight: 800;">1-3</span>
                    </div>
                    <h3>Jam</h3>
                    <p style="color: var(--text-secondary);">Tugas simpel, deadline hari ini</p>
                </div>
                <div style="text-align: center;">
                    <div
                        style="width: 100px; height: 100px; background: linear-gradient(135deg, #4f46e5, #818cf8); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                        <span style="color: white; font-size: 2.5rem; font-weight: 800;">1</span>
                    </div>
                    <h3>Hari</h3>
                    <p style="color: var(--text-secondary);">Tugas menengah, butuh riset</p>
                </div>
                <div style="text-align: center;">
                    <div
                        style="width: 100px; height: 100px; background: linear-gradient(135deg, #d97706, #fbbf24); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                        <span style="color: white; font-size: 2.5rem; font-weight: 800;">3+</span>
                    </div>
                    <h3>Hari</h3>
                    <p style="color: var(--text-secondary);">Project besar, coding, desain</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section - Same style as other pages -->
    <section class="values" style="background: white;">
        <div class="container">
            <div class="section-center">
                <h2>Harga Mulai dari Segelas Kopi</h2>
                <p>Serius. Budget mahasiswa banget.</p>
            </div>
            <div class="services-grid">
                <div class="service-card bg-pastel-green" style="padding: 36px;">
                    <h3 style="margin-bottom: 8px; font-size: 1.1rem;">Tugas Simpel</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 16px;">Soal singkat,
                        essay pendek</p>
                    <strong class="price-tag" style="font-size: 2rem; font-weight: 800; margin-bottom: 20px; display: block;">Mulai 25K</strong>
                    <ul style="list-style: none; padding: 0; margin-bottom: 24px; font-size: 0.9rem;">
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Pengerjaan cepat</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Revisi 1x</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Support via chat</li>
                    </ul>
                    <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                        class="btn btn-outline" style="width: 100%; justify-content: center;">Konsultasi</a>
                </div>
                <div class="service-card bg-pastel-indigo" style="padding: 36px; border: 3px solid var(--primary);">
                    <span
                        style="background: var(--primary); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">Populer</span>
                    <h3 style="margin-top: 12px; margin-bottom: 8px; font-size: 1.1rem;">Tugas Kompleks</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 16px;">Coding, project,
                        presentasi</p>
                    <strong class="price-tag" style="font-size: 2rem; font-weight: 800; margin-bottom: 20px; display: block;">Mulai 75K</strong>
                    <ul style="list-style: none; padding: 0; margin-bottom: 24px; font-size: 0.9rem;">
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Full pengerjaan</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Revisi 2x</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Penjelasan jika perlu</li>
                    </ul>
                    <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                        class="btn btn-primary" style="width: 100%; justify-content: center;">Konsultasi</a>
                </div>
            </div>
            <p style="text-align: center; margin-top: 24px; color: var(--text-secondary);">*Harga tergantung tingkat
                kesulitan dan deadline. Tanya dulu pasti dikasih estimasi!</p>
        </div>
    </section>

    <!-- How to Order - Super Simple -->
    <section class="services" style="background: var(--bg-light);">
        <div class="container">
            <div class="section-center">
                <h2>Order Gampang Banget</h2>
            </div>
            <div style="display: flex; justify-content: center; gap: 48px; flex-wrap: wrap;">
                <div style="text-align: center; max-width: 200px;">
                    <div
                        style="width: 64px; height: 64px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; margin: 0 auto 16px;">
                        1</div>
                    <h3 style="font-size: 1rem;">Foto Tugasnya</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Screenshot atau foto instruksi tugas.
                    </p>
                </div>
                <div style="text-align: center; max-width: 200px;">
                    <div
                        style="width: 64px; height: 64px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; margin: 0 auto 16px;">
                        2</div>
                    <h3 style="font-size: 1rem;">Kirim ke WA</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Kasih tau deadline dan detail lainnya.
                    </p>
                </div>
                <div style="text-align: center; max-width: 200px;">
                    <div
                        style="width: 64px; height: 64px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; margin: 0 auto 16px;">
                        3</div>
                    <h3 style="font-size: 1rem;">Terima Hasil</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Tugas selesai, tinggal submit!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="talent" style="background: white;">
        <div class="container">
            <div class="section-center">
                <h2>Kata Mereka yang Sudah Pakai</h2>
            </div>
            <div class="values-grid">
                <?php 
                $testimonials = tugasin_get_testimonials( 'joki_tugas' );
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
                    <p style="color: var(--text-secondary); margin-bottom: 16px;">"Tugas coding Python saya dikerjain dengan cepat banget. Deadline 3 jam selesai tepat waktu!"</p>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <img src="<?php echo esc_url( $placeholder_image ); ?>" alt="Foto Fajar A." style="border-radius: 50%; width: 48px; height: 48px; object-fit: cover;">
                        <div><strong>Fajar A.</strong>
                            <p style="font-size: 0.8rem; color: var(--text-secondary);">Teknik Informatika - Binus</p>
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
            <h2 style="font-size: 2.5rem; margin-bottom: 16px;">Biar Tugasin yang Handle.</h2>
            <p style="color: rgba(255,255,255,0.8); margin-bottom: 32px;">Fokus ke aktivitas lain. Tugas biar Tugasin
                yang urus, kamu tinggal terima beres.</p>
            <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                class="btn btn-accent" style="font-size: 1.1rem; padding: 16px 32px;"><i class="fab fa-whatsapp"></i> Chat via WhatsApp</a>
        </div>
    </section>
</main>

<?php
get_footer();
