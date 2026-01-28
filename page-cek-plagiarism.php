<?php
/**
 * Template Name: Cek Plagiarism
 * EXACT copy from ui/cek-plagiarism.html
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
                    style="background: rgba(255,255,255,0.2); color: white; margin-bottom: 16px;"><i
                        class="fas fa-shield-alt" style="margin-right: 8px;"></i>Cek Plagiarism</span>
                <h1>Takut <span class="text-highlight">Kena Plagiat?</span> Cek Dulu Sebelum Submit.</h1>
                <p>Plagiarism bisa bikin kamu nggak lulus, bahkan kena sanksi akademik. Pastikan tulisanmu aman dengan
                    cek Turnitin resmi dari Tugasin.</p>
                <div class="hero-btns">
                    <?php echo tugasin_cta_button( __( 'Cek Sekarang', 'tugasin' ), 'btn btn-accent' ); ?>
                </div>
            </div>
            <div class="hero-visual">
                <!-- Turnitin Badge -->
                <div class="card float-1" style="top: 0; right: 10%; padding: 28px; width: 270px;">
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                        <div
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #059669, #34d399); border-radius: 16px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-check-double" style="font-size: 1.5rem; color: white;"></i>
                        </div>
                        <div>
                            <span
                                style="background: var(--pastel-green); color: #059669; padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700;">✓
                                VERIFIED</span>
                            <strong class="stat-title" style="margin-top: 4px;">Turnitin Resmi</strong>
                        </div>
                    </div>
                    <div style="background: var(--bg-light); padding: 16px; border-radius: 12px;">
                        <p style="font-size: 0.85rem; color: var(--text-secondary);">Bukan software peniru. Akun asli
                            dengan akses full database.</p>
                    </div>
                </div>
                <!-- Similarity Meter -->
                <div class="card"
                    style="bottom: 25%; left: 0; padding: 20px; width: 200px; animation: float-rev 6s ease-in-out infinite;">
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 12px;">Similarity Index
                    </p>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div
                            style="width: 60px; height: 60px; border-radius: 50%; border: 4px solid #059669; display: flex; align-items: center; justify-content: center;">
                            <span style="font-weight: 800; font-size: 1.2rem; color: #059669;">12%</span>
                        </div>
                        <div>
                            <span style="color: #059669; font-weight: 700;">Safe <i class="fas fa-check"></i></span>
                            <p style="font-size: 0.75rem; color: var(--text-secondary);">Bisa submit!</p>
                        </div>
                    </div>
                </div>
                <!-- Speed Badge -->
                <div class="card" style="top: 60%; right: 5%; padding: 16px; animation: float 5s ease-in-out infinite;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-clock" style="color: #4f46e5; font-size: 1.2rem;"></i>
                        <span style="font-weight: 700;">Hasil 1-3 Jam</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Check - Risk Awareness (White background) -->
    <section class="values" style="background: var(--bg-light);">
        <div class="container">
            <div class="section-center">
                <h2>Apa Risiko Tidak Cek Plagiarism?</h2>
                <p>Ini bukan sekedar formalitas, tapi keharusan.</p>
            </div>
            <div class="values-grid">
                <div class="value-card" style="text-align: center; padding: 32px;">
                    <div
                        style="width: 72px; height: 72px; background: #fef2f2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-times-circle" style="font-size: 2rem; color: #ef4444;"></i>
                    </div>
                    <h3 style="margin-bottom: 8px; font-size: 1.1rem;">Tugas Ditolak</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Dosen langsung reject tanpa baca isi.
                    </p>
                </div>
                <div class="value-card" style="text-align: center; padding: 32px;">
                    <div
                        style="width: 72px; height: 72px; background: #fef3c7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-file-excel" style="font-size: 2rem; color: #d97706;"></i>
                    </div>
                    <h3 style="margin-bottom: 8px; font-size: 1.1rem;">Dapat Nilai E</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Plagiarism = nilai terendah otomatis.
                    </p>
                </div>
                <div class="value-card" style="text-align: center; padding: 32px;">
                    <div
                        style="width: 72px; height: 72px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-ban" style="font-size: 2rem; color: #dc2626;"></i>
                    </div>
                    <h3 style="margin-bottom: 8px; font-size: 1.1rem;">Sanksi Akademik</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Skors, DO, atau masuk catatan hitam.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- What You Get -->
    <section class="services">
        <div class="container">
            <div class="section-center">
                <h2>Yang Kamu Dapatkan</h2>
                <p>Bukan cuma persentase, tapi laporan detail.</p>
            </div>
            <div class="tugasin-grid-2">
                <div
                    style="display: flex; gap: 20px; align-items: flex-start; background: white; padding: 32px; border-radius: 20px; border: 1px solid #f3f4f6;">
                    <div
                        style="width: 56px; height: 56px; background: var(--pastel-gray); border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-percentage" style="color: #374151; font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem;">Persentase Similarity</h3>
                        <p style="color: var(--text-secondary); font-size: 0.9rem;">Tahu berapa persen kesamaan dengan
                            database Turnitin global.</p>
                    </div>
                </div>
                <div
                    style="display: flex; gap: 20px; align-items: flex-start; background: white; padding: 32px; border-radius: 20px; border: 1px solid #f3f4f6;">
                    <div
                        style="width: 56px; height: 56px; background: var(--pastel-indigo); border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-file-pdf" style="color: #4f46e5; font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem;">Laporan PDF Lengkap</h3>
                        <p style="color: var(--text-secondary); font-size: 0.9rem;">Report detail yang bisa kamu simpan
                            sebagai bukti.</p>
                    </div>
                </div>
                <div
                    style="display: flex; gap: 20px; align-items: flex-start; background: white; padding: 32px; border-radius: 20px; border: 1px solid #f3f4f6;">
                    <div
                        style="width: 56px; height: 56px; background: var(--pastel-yellow); border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-highlighter" style="color: #d97706; font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem;">Highlight Bagian Bermasalah</h3>
                        <p style="color: var(--text-secondary); font-size: 0.9rem;">Tahu bagian mana yang terdeteksi,
                            jadi bisa diperbaiki.</p>
                    </div>
                </div>
                <div
                    style="display: flex; gap: 20px; align-items: flex-start; background: white; padding: 32px; border-radius: 20px; border: 1px solid #f3f4f6;">
                    <div
                        style="width: 56px; height: 56px; background: var(--pastel-green); border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-link" style="color: #059669; font-size: 1.3rem;"></i>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem;">Daftar Sumber Kesamaan</h3>
                        <p style="color: var(--text-secondary); font-size: 0.9rem;">Lihat dari mana sumber yang
                            terdeteksi mirip.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="values" style="background: white;">
        <div class="container">
            <div class="section-center">
                <h2>Harga Super Terjangkau</h2>
                <p>Cuma sebatas jajan, tapi bisa selamatkan kuliahmu.</p>
            </div>
            <div class="tugasin-grid-2" style="max-width: 800px; margin: 0 auto;">
                <div class="service-card" style="padding: 36px; background: white; border: 1px solid #e5e7eb;">
                    <h3 style="margin-bottom: 8px; font-size: 1.1rem;">Cek Plagiarism</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 16px;">Cek saja tanpa
                        revisi</p>
                    <strong class="price-tag" style="font-size: 2rem; font-weight: 800; margin-bottom: 20px; display: block;">Mulai 3K</strong>
                    <ul style="list-style: none; padding: 0; margin-bottom: 24px; font-size: 0.9rem;">
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Turnitin resmi</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Laporan PDF lengkap</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Mode "No Repository"</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Hasil 1-3 jam</li>
                    </ul>
                    <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                        class="btn btn-outline" style="width: 100%; justify-content: center;">Konsultasi</a>
                </div>
                <div class="service-card" style="padding: 36px; background: white; border: 3px solid var(--primary);">
                    <span
                        style="background: var(--primary); color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">Populer</span>
                    <h3 style="margin-top: 12px; margin-bottom: 8px; font-size: 1.1rem;">Cek Plagiarism + Revisi</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 16px;">Cek & bantu
                        perbaiki</p>
                    <strong class="price-tag" style="font-size: 2rem; font-weight: 800; margin-bottom: 20px; display: block;">Mulai 100K</strong>
                    <ul style="list-style: none; padding: 0; margin-bottom: 24px; font-size: 0.9rem;">
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Semua fitur Cek Plagiarism</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Parafrase bagian bermasalah</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Target plagiarism rendah</li>
                        <li style="padding: 6px 0; color: var(--text-secondary);"><i class="fas fa-check"
                                style="color: #059669; margin-right: 8px;"></i>Re-check gratis setelah revisi</li>
                    </ul>
                    <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                        class="btn btn-primary" style="width: 100%; justify-content: center;"><i class="fab fa-whatsapp"></i> Konsultasi</a>
                </div>
            </div>
            <p style="text-align: center; margin-top: 24px; color: var(--text-secondary);">*Harga tergantung jumlah
                halaman dan tingkat plagiarism. Tanya dulu aja!</p>
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
                $testimonials = tugasin_get_testimonials( 'cek_plagiarism' );
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
                    <p style="color: var(--text-secondary); margin-bottom: 16px;">"Hampir submit skripsi tanpa cek plagiarism. Ternyata ada 28%! Untung cek dulu, bisa revisi sebelum terlambat."</p>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <img src="<?php echo esc_url( $placeholder_image ); ?>" alt="Foto Fitria N." style="border-radius: 50%; width: 48px; height: 48px; object-fit: cover;">
                        <div><strong>Fitria N.</strong>
                            <p style="font-size: 0.8rem; color: var(--text-secondary);">Ilmu Komunikasi - UNJ</p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="services">
        <div class="container">
            <div class="section-center">
                <h2>Pertanyaan yang Sering Ditanyakan</h2>
            </div>
            <div style="max-width: 800px; margin: 0 auto; display: flex; flex-direction: column; gap: 16px;">
                <div style="background: var(--bg-light); padding: 24px; border-radius: 16px;">
                    <h3 style="margin-bottom: 8px; font-size: 1rem;"><i class="fas fa-question-circle"
                            style="color: var(--primary); margin-right: 8px;"></i>Apakah Turnitin-nya resmi?</h3>
                    <p style="color: var(--text-secondary);">100% resmi. Tugasin menggunakan akun institusi yang
                        terverifikasi, bukan software peniru atau alternatif lainnya.</p>
                </div>
                <div style="background: var(--bg-light); padding: 24px; border-radius: 16px;">
                    <h3 style="margin-bottom: 8px; font-size: 1rem;"><i class="fas fa-question-circle"
                            style="color: var(--primary); margin-right: 8px;"></i>Apakah dokumen saya tersimpan di
                        database?</h3>
                    <p style="color: var(--text-secondary);">Tidak. Tugasin menggunakan mode "No Repository" sehingga
                        dokumenmu tidak akan masuk ke database Turnitin.</p>
                </div>
                <div style="background: var(--bg-light); padding: 24px; border-radius: 16px;">
                    <h3 style="margin-bottom: 8px; font-size: 1rem;"><i class="fas fa-question-circle"
                            style="color: var(--primary); margin-right: 8px;"></i>Berapa lama hasil keluar?</h3>
                    <p style="color: var(--text-secondary);">Rata-rata 1-3 jam. Untuk dokumen sangat panjang atau saat
                        ramai, bisa sampai 6 jam.</p>
                </div>
                <div style="background: var(--bg-light); padding: 24px; border-radius: 16px;">
                    <h3 style="margin-bottom: 8px; font-size: 1rem;"><i class="fas fa-question-circle"
                            style="color: var(--primary); margin-right: 8px;"></i>Berapa persen yang dianggap aman?</h3>
                    <p style="color: var(--text-secondary);">Tergantung kebijakan kampus, tapi umumnya di bawah 20-25%
                        dianggap aman. Tugasin akan kasih saran jika persentase terlalu tinggi.</p>
                </div>
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
            <h2 style="font-size: 2.5rem; margin-bottom: 16px;">Jangan Gambling dengan Plagiarism.</h2>
            <p style="color: rgba(255,255,255,0.8); margin-bottom: 32px;">Investasi kecil untuk ketenangan besar. Cek
                dulu sebelum menyesal.</p>
            <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                class="btn btn-accent" style="font-size: 1.1rem; padding: 16px 32px;"><i class="fab fa-whatsapp"></i> Chat via WhatsApp</a>
        </div>
    </section>
</main>

<?php
get_footer();
