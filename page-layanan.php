<?php
/**
 * Template Name: Layanan
 * EXACT copy from ui/services.html - DO NOT SIMPLIFY
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
                <h1>Semua yang Kamu Butuhkan untuk <span class="text-highlight">Sukses Akademik</span></h1>
                <p>Dari skripsi yang bikin pusing sampai tugas harian yang numpuk, Tugasin hadir sebagai partner
                    akademik kamu. Pilih layanan yang sesuai kebutuhanmu.</p>
                <div class="hero-btns">
                    <?php echo tugasin_cta_button( __( 'Konsultasi Gratis', 'tugasin' ), 'btn btn-accent' ); ?>
                </div>
            </div>
            <div class="hero-visual">
                <div class="card float-1" style="top: 10%; right: 20%; padding: 24px;">
                    <div class="icon-circle" style="background: var(--pastel-indigo);"><i class="fas fa-graduation-cap"
                            style="color: #4f46e5;"></i></div>
                    <strong class="float-card-title" style="margin-top: 12px; display: block;">4 Layanan</strong>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Lengkap untuk kebutuhanmu</p>
                </div>
                <div class="card service-preview" style="bottom: 30%; left: 5%;">
                    <div class="icon-circle"><i class="fas fa-check-double"></i></div>
                    <div>
                        <strong class="float-card-title">2000+</strong>
                        <p>Proyek Selesai</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Grid -->
    <section id="layanan" class="services">
        <div class="container">
            <div class="section-center">
                <h2>Pilih Layanan yang Kamu Butuhkan</h2>
                <p>Setiap layanan dirancang khusus untuk membantu kamu menyelesaikan tantangan akademik dengan lebih
                    mudah.</p>
            </div>
            <div class="services-grid">
                <!-- Joki Skripsi -->
                <a href="<?php echo esc_url( home_url( '/joki-skripsi/' ) ); ?>" class="service-card bg-pastel-indigo">
                    <div class="service-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3>Joki Skripsi</h3>
                    <p>Skripsi terasa berat? Deadline mepet? Tugasin bantu kamu dari proposal sampai sidang.</p>
                    <div class="check-circle"><i class="fas fa-arrow-right"></i></div>
                </a>
                <!-- Joki Makalah -->
                <a href="<?php echo esc_url( home_url( '/joki-makalah/' ) ); ?>" class="service-card bg-pastel-yellow">
                    <div class="service-icon"><i class="fas fa-book"></i></div>
                    <h3>Joki Makalah</h3>
                    <p>Makalah numpuk? Butuh karya ilmiah berkualitas? Tugasin siap bantu riset dan penulisan.</p>
                    <div class="check-circle"><i class="fas fa-arrow-right"></i></div>
                </a>
                <!-- Joki Tugas -->
                <a href="<?php echo esc_url( home_url( '/joki-tugas/' ) ); ?>" class="service-card bg-pastel-green">
                    <div class="service-icon"><i class="fas fa-tasks"></i></div>
                    <h3>Joki Tugas</h3>
                    <p>Tugas harian bikin overwhelmed? Tugasin bantu kamu selesaikan dengan cepat dan tepat.</p>
                    <div class="check-circle"><i class="fas fa-arrow-right"></i></div>
                </a>
                <!-- Cek Plagiarism -->
                <a href="<?php echo esc_url( home_url( '/cek-plagiarism/' ) ); ?>" class="service-card bg-pastel-gray">
                    <div class="service-icon"><i class="fas fa-search"></i></div>
                    <h3>Cek Plagiarism</h3>
                    <p>Takut kena plagiarism? Cek dulu pakai Turnitin resmi sebelum submit.</p>
                    <div class="check-circle"><i class="fas fa-arrow-right"></i></div>
                </a>
            </div>
        </div>
    </section>

    <!-- Why Tugasin -->
    <section class="values">
        <div class="container">
            <div class="section-center">
                <h2>Kenapa Pilih Tugasin?</h2>
                <p>Bukan cuma soal hasil, tapi juga pengalaman yang nyaman dan aman.</p>
            </div>
            <div class="values-grid">
                <div class="value-card">
                    <div class="icon-box bg-purple"><i class="fas fa-clock"></i></div>
                    <h3>Cepat & Tepat Waktu</h3>
                    <p>Deadline bukan masalah. Tugasin selalu on-time, bahkan untuk request dadakan.</p>
                    <div class="value-line"></div>
                </div>
                <div class="value-card">
                    <div class="icon-box bg-yellow"><i class="fas fa-lock"></i></div>
                    <h3>100% Rahasia</h3>
                    <p>Data dan identitas kamu aman. Tugasin menjaga privasi setiap klien dengan ketat.</p>
                    <div class="value-line"></div>
                </div>
                <div class="value-card">
                    <div class="icon-box bg-green"><i class="fas fa-medal"></i></div>
                    <h3>Kualitas Terjamin</h3>
                    <p>Dikerjakan oleh tim profesional yang sudah berpengalaman di bidangnya.</p>
                    <div class="value-line"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section class="partners" style="padding: 100px 0;">
        <div class="container">
            <div class="section-center">
                <h2>Cara Kerjanya Simpel</h2>
                <p>Cuma 3 langkah dan tugasmu langsung diproses.</p>
            </div>
            <div class="values-grid" style="margin-top: 40px;">
                <div class="value-card">
                    <div class="icon-box bg-purple"><i class="fas fa-comment-dots"></i></div>
                    <h3>1. Konsultasi Gratis</h3>
                    <p>Ceritakan kebutuhan kamu. Tim Tugasin akan kasih estimasi harga dan waktu pengerjaan.</p>
                </div>
                <div class="value-card">
                    <div class="icon-box bg-yellow"><i class="fas fa-file-signature"></i></div>
                    <h3>2. Deal & Proses</h3>
                    <p>Setelah deal, tim langsung mulai kerjakan. Kamu bisa pantau progress kapan saja.</p>
                </div>
                <div class="value-card">
                    <div class="icon-box bg-green"><i class="fas fa-check-circle"></i></div>
                    <h3>3. Selesai & Revisi</h3>
                    <p>Hasil dikirim tepat waktu. Ada revisi? Tenang, Tugasin kasih revisi gratis.</p>
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

    <!-- CTA Section -->
    <section class="hero" style="clip-path: polygon(0 10%, 100% 0, 100% 100%, 0 100%); padding: 120px 0 80px;">
        <div class="container" style="text-align: center;">
            <h2 style="font-size: 2.5rem; margin-bottom: 16px;">Siap Menyelesaikan Tugasmu?</h2>
            <p
                style="color: rgba(255,255,255,0.8); margin-bottom: 32px; max-width: 600px; margin-left: auto; margin-right: auto;">
                Jangan biarkan tugas menumpuk mengganggu hidupmu. Hubungi Tugasin sekarang dan rasakan bedanya.</p>
            <a href="<?php echo esc_url( tugasin_get_whatsapp_url() ); ?>"
                class="btn btn-accent" style="font-size: 1.1rem; padding: 16px 32px;"><i class="fab fa-whatsapp"></i> Chat via WhatsApp</a>
        </div>
    </section>

    <!-- Footer -->
</main>

<?php
get_footer();
