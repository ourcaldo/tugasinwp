<?php
/**
 * Single template for University CPT
 * Phase 21: Fixed structure, accordion, default CTA
 *
 * @package TugasinWP
 * @since 2.10.0
 */

get_header();
?>

<main id="main-content" role="main">
<?php
// ACF Fields
$logo          = get_field( 'uni_logo' );
$banner_color  = get_field( 'uni_banner_color' ) ?: '#064e3b';
$motto         = get_field( 'uni_motto' );
$location      = get_field( 'uni_location' );
$website       = get_field( 'uni_website' );
$phone         = get_field( 'uni_phone' );
$youtube       = get_field( 'uni_youtube' );

// Content fields
$sejarah       = get_field( 'uni_sejarah' );
$visi          = get_field( 'uni_visi' );
$misi          = get_field( 'uni_misi' );

$faculties     = get_field( 'uni_faculties' );
$admission     = get_field( 'uni_admission' );

// Get taxonomies
$uni_type = get_the_terms( get_the_ID(), 'university_type' );
$accred = get_the_terms( get_the_ID(), 'accreditation' );

// Get biaya range - format: Rp. X - Y per Semester
$biaya_display = '';
if ( $admission && is_array( $admission ) ) {
    $min_biaya = null;
    $max_biaya = null;
    foreach ( $admission as $path ) {
        $min = isset( $path['biaya_min'] ) ? intval( $path['biaya_min'] ) : 0;
        $max = isset( $path['biaya_max'] ) ? intval( $path['biaya_max'] ) : 0;
        if ( $min && ( $min_biaya === null || $min < $min_biaya ) ) {
            $min_biaya = $min;
        }
        if ( $max && ( $max_biaya === null || $max > $max_biaya ) ) {
            $max_biaya = $max;
        }
    }
    if ( $min_biaya && $max_biaya ) {
        $biaya_display = 'Rp. ' . number_format( $min_biaya, 0, ',', '.' ) . ' - ' . number_format( $max_biaya, 0, ',', '.' ) . ' per Semester';
    }
}

// Get featured image for hero background
$hero_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
$has_hero = $hero_image ? true : false;
?>

<!-- Hero Section with Featured Image Background + Logo Overlay -->
<section class="uni-hero <?php echo $has_hero ? 'has-featured-image' : ''; ?>" style="<?php echo $has_hero ? 'background-image: url(\'' . esc_url( $hero_image ) . '\');' : 'background: ' . esc_attr( $banner_color ) . ';'; ?>">
    <div class="uni-hero-overlay"></div>
    <?php if ( $logo ) : ?>
    <div class="uni-hero-logo">
        <div class="uni-logo-wrap">
            <img src="<?php echo esc_url( $logo ); ?>" alt="<?php the_title_attribute(); ?>">
        </div>
    </div>
    <?php endif; ?>
</section>

<?php
// Content accordion
$content = get_the_content();
$word_count = str_word_count( strip_tags( $content ) );
$show_accordion = $word_count > 60;
?>

<!-- Main Content Card -->
<section class="uni-main-card">
    <div class="container">
        <div class="uni-breadcrumb">
            <?php tugasin_breadcrumb(); ?>
        </div>
        
        <div class="uni-content-grid">
            <!-- Left Column: Title & Description -->
            <div class="uni-content-left">
                <h1 class="uni-title"><?php the_title(); ?></h1>
                
                <?php if ( $content ) : ?>
                <div class="uni-description" id="uni-desc">
                    <?php if ( $show_accordion ) : ?>
                        <p><?php echo esc_html( wp_trim_words( $content, 60, '...' ) ); ?></p>
                        <button class="uni-read-more" id="uni-expand-btn" data-state="collapsed">
                            <?php esc_html_e( 'Selengkapnya', 'tugasin' ); ?> <i class="fas fa-chevron-down"></i>
                        </button>
                    <?php else : ?>
                        <?php the_content(); ?>
                    <?php endif; ?>
                </div>
                
                <?php if ( $show_accordion ) : ?>
                <div id="uni-full-content" class="uni-full-content" style="display: none;">
                    <?php the_content(); ?>
                    <button class="uni-read-more" id="uni-collapse-btn">
                        <?php esc_html_e( 'Sembunyikan', 'tugasin' ); ?> <i class="fas fa-chevron-up"></i>
                    </button>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <!-- Right Column: Quick Info -->
            <div class="uni-content-right">
                <div class="uni-info-list">
                    <?php if ( $uni_type && ! is_wp_error( $uni_type ) ) : ?>
                    <div class="uni-info-row">
                        <span class="uni-info-icon"><i class="fas fa-university"></i></span>
                        <span class="uni-info-label"><?php esc_html_e( 'Status', 'tugasin' ); ?></span>
                        <span class="uni-info-sep">:</span>
                        <span class="uni-info-value"><?php echo esc_html( $uni_type[0]->name ); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ( $biaya_display ) : ?>
                    <div class="uni-info-row">
                        <span class="uni-info-icon"><i class="fas fa-wallet"></i></span>
                        <span class="uni-info-label"><?php esc_html_e( 'Biaya', 'tugasin' ); ?></span>
                        <span class="uni-info-sep">:</span>
                        <span class="uni-info-value"><?php echo esc_html( $biaya_display ); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ( $accred && ! is_wp_error( $accred ) ) : ?>
                    <div class="uni-info-row">
                        <span class="uni-info-icon"><i class="fas fa-award"></i></span>
                        <span class="uni-info-label"><?php esc_html_e( 'Akreditasi', 'tugasin' ); ?></span>
                        <span class="uni-info-sep">:</span>
                        <span class="uni-info-value"><?php echo esc_html( $accred[0]->name ); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ( $location ) : ?>
                    <div class="uni-info-row">
                        <span class="uni-info-icon"><i class="fas fa-map-marker-alt"></i></span>
                        <span class="uni-info-label"><?php esc_html_e( 'Lokasi', 'tugasin' ); ?></span>
                        <span class="uni-info-sep">:</span>
                        <span class="uni-info-value"><?php echo esc_html( $location ); ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ( $website ) : ?>
                    <div class="uni-info-row">
                        <span class="uni-info-icon"><i class="fas fa-globe"></i></span>
                        <span class="uni-info-label"><?php esc_html_e( 'Website', 'tugasin' ); ?></span>
                        <span class="uni-info-sep">:</span>
                        <a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener" class="uni-info-value uni-info-link"><?php echo esc_html( parse_url( $website, PHP_URL_HOST ) ); ?></a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content Sections -->
<section class="uni-sections">
    <div class="container">
        
        <!-- Sejarah Perguruan Tinggi -->
        <?php if ( $sejarah ) : 
            $sejarah_word_count = str_word_count( strip_tags( $sejarah ) );
            $sejarah_show_accordion = $sejarah_word_count > 80;
        ?>
        <div class="uni-section" id="sejarah">
            <h2 class="uni-section-title"><?php esc_html_e( 'Sejarah Perguruan Tinggi', 'tugasin' ); ?></h2>
            
            <div class="uni-accordion-content" id="sejarah-short" <?php echo $sejarah_show_accordion ? '' : 'style="display:none;"'; ?>>
                <p><?php echo esc_html( wp_trim_words( strip_tags( $sejarah ), 80, '...' ) ); ?></p>
                <button class="uni-read-more" data-expand="sejarah">
                    <?php esc_html_e( 'Selengkapnya', 'tugasin' ); ?> <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            
            <div class="uni-accordion-content" id="sejarah-full" style="display: <?php echo $sejarah_show_accordion ? 'none' : 'block'; ?>;">
                <?php echo tugasin_process_wysiwyg( $sejarah ); ?>
                <?php if ( $sejarah_show_accordion ) : ?>
                <button class="uni-read-more" data-collapse="sejarah">
                    <?php esc_html_e( 'Sembunyikan', 'tugasin' ); ?> <i class="fas fa-chevron-up"></i>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Visi dan Misi -->
        <?php if ( $visi || $misi ) : ?>
        <div class="uni-section" id="visi-misi">
            <h2 class="uni-section-title"><?php esc_html_e( 'Visi dan Misi', 'tugasin' ); ?></h2>
            
            <?php if ( $visi ) : ?>
            <div class="uni-visi-box">
                <h3><?php esc_html_e( 'Visi', 'tugasin' ); ?></h3>
                <p><?php echo esc_html( $visi ); ?></p>
            </div>
            <?php endif; ?>
            
            <?php if ( $misi ) : ?>
            <div class="uni-misi-box">
                <h3><?php esc_html_e( 'Misi', 'tugasin' ); ?></h3>
                <div class="uni-misi-content">
                    <?php echo wp_kses_post( $misi ); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Fakultas & Program Studi (H3 for faculty names) -->
        <?php if ( $faculties && is_array( $faculties ) ) : ?>
        <div class="uni-section" id="fakultas">
            <h2 class="uni-section-title"><?php esc_html_e( 'Fakultas & Program Studi', 'tugasin' ); ?></h2>
            
            <div class="fakultas-list" id="fakultas-list">
                <?php 
                $fac_count = count( $faculties );
                $fac_show_count = 3;
                $fac_hidden_count = max( 0, $fac_count - $fac_show_count );
                
                foreach ( $faculties as $index => $faculty ) : 
                    $is_hidden = $index >= $fac_show_count;
                    $programs = isset( $faculty['programs'] ) ? $faculty['programs'] : array();
                ?>
                <div class="fakultas-card<?php echo $is_hidden ? ' fakultas-hidden' : ''; ?>">
                    <h3 class="fakultas-title"><?php echo esc_html( $faculty['faculty_name'] ); ?></h3>
                    <?php if ( $programs && is_array( $programs ) ) : ?>
                    <div class="prodi-badges">
                        <?php foreach ( $programs as $program ) : ?>
                        <h4 class="prodi-badge"><?php echo esc_html( $program['program_name'] ); ?></h4>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ( $fac_hidden_count > 0 ) : ?>
            <button class="expand-btn" data-target="fakultas" data-hidden="<?php echo esc_attr( $fac_hidden_count ); ?>">
                <span class="btn-text"><?php printf( esc_html__( 'Selengkapnya (%d lagi)', 'tugasin' ), $fac_hidden_count ); ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Jalur Masuk & Biaya (H3 for path names) -->
        <?php if ( $admission && is_array( $admission ) ) : ?>
        <div class="uni-section" id="jalur-masuk">
            <h2 class="uni-section-title"><?php esc_html_e( 'Jalur Masuk', 'tugasin' ); ?></h2>
            
            <div class="admission-list" id="admission-list">
                <?php 
                $adm_count = count( $admission );
                $adm_show_count = 3;
                $adm_hidden_count = max( 0, $adm_count - $adm_show_count );
                
                foreach ( $admission as $index => $path ) : 
                    $is_hidden = $index >= $adm_show_count;
                    $min = isset( $path['biaya_min'] ) ? intval( $path['biaya_min'] ) : 0;
                    $max = isset( $path['biaya_max'] ) ? intval( $path['biaya_max'] ) : 0;
                    $biaya = '';
                    if ( $min && $max ) {
                        $biaya = 'Rp. ' . number_format( $min, 0, ',', '.' ) . ' - ' . number_format( $max, 0, ',', '.' );
                    } elseif ( $min ) {
                        $biaya = 'Rp. ' . number_format( $min, 0, ',', '.' );
                    }
                ?>
                <div class="admission-card<?php echo $is_hidden ? ' admission-hidden' : ''; ?>">
                    <h3 class="admission-title"><?php echo esc_html( $path['path_name'] ); ?></h3>
                    <?php if ( ! empty( $path['path_desc'] ) ) : ?>
                    <p class="admission-desc"><?php echo esc_html( $path['path_desc'] ); ?></p>
                    <?php endif; ?>
                    
                    <?php if ( $biaya ) : ?>
                    <div class="admission-biaya">
                        <i class="fas fa-wallet"></i>
                        <span><?php echo esc_html( $biaya ); ?>/Semester</span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if ( $adm_hidden_count > 0 ) : ?>
            <button class="expand-btn" data-target="admission" data-hidden="<?php echo esc_attr( $adm_hidden_count ); ?>">
                <span class="btn-text"><?php printf( esc_html__( 'Selengkapnya (%d lagi)', 'tugasin' ), $adm_hidden_count ); ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Kontak Section (H2 for university name) -->
        <?php if ( $location || $website || $phone || $youtube ) : ?>
        <div class="uni-section" id="kontak">
            <div class="uni-contact-wrapper">
                <!-- Left: Logo + Info -->
                <div class="uni-contact-left">
                    <?php if ( $logo ) : ?>
                    <img src="<?php echo esc_url( $logo ); ?>" alt="<?php the_title_attribute(); ?>" class="uni-contact-logo">
                    <?php endif; ?>
                    
                    <h2 class="uni-contact-name"><?php the_title(); ?></h2>
                    
                    <?php if ( $location ) : ?>
                    <div class="uni-contact-address">
                        <strong><?php esc_html_e( 'Alamat', 'tugasin' ); ?></strong>
                        <p><?php echo esc_html( $location ); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="uni-contact-links">
                        <?php if ( $website ) : ?>
                        <a href="<?php echo esc_url( $website ); ?>" target="_blank" rel="noopener" class="uni-contact-link">
                            <i class="fas fa-globe"></i>
                            <?php echo esc_html( parse_url( $website, PHP_URL_HOST ) ); ?>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ( $phone ) : ?>
                        <a href="tel:<?php echo esc_attr( $phone ); ?>" class="uni-contact-link">
                            <i class="fas fa-phone"></i>
                            <?php echo esc_html( $phone ); ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Right: YouTube Embed with Facade Pattern -->
                <?php if ( $youtube ) : ?>
                <div class="uni-contact-right">
                    <div class="uni-youtube-embed">
                        <?php tugasin_youtube_facade( $youtube, sprintf( __( 'Video Profil %s', 'tugasin' ), get_the_title() ) ); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- CTA Section (default like blog post, strong not h3) -->
        <div class="uni-cta-section">
            <strong class="uni-cta-title"><?php esc_html_e( 'Butuh Bantuan dengan Tugasmu?', 'tugasin' ); ?></strong>
            <p><?php esc_html_e( 'Tim ahli kami siap membantu kamu menyelesaikan tugas dengan cepat dan berkualitas.', 'tugasin' ); ?></p>
            <?php echo tugasin_cta_button( __( 'Konsultasi Gratis', 'tugasin' ), 'btn btn-cta-light' ); ?>
        </div>
        
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intro content accordion
    var expandBtn = document.getElementById('uni-expand-btn');
    var collapseBtn = document.getElementById('uni-collapse-btn');
    
    if (expandBtn) {
        expandBtn.addEventListener('click', function() {
            document.getElementById('uni-desc').style.display = 'none';
            document.getElementById('uni-full-content').style.display = 'block';
        });
    }
    
    if (collapseBtn) {
        collapseBtn.addEventListener('click', function() {
            document.getElementById('uni-desc').style.display = 'block';
            document.getElementById('uni-full-content').style.display = 'none';
        });
    }
    
    // Section accordion (Sejarah)
    document.querySelectorAll('[data-expand]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var section = btn.dataset.expand;
            document.getElementById(section + '-short').style.display = 'none';
            document.getElementById(section + '-full').style.display = 'block';
        });
    });
    
    document.querySelectorAll('[data-collapse]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var section = btn.dataset.collapse;
            document.getElementById(section + '-short').style.display = 'block';
            document.getElementById(section + '-full').style.display = 'none';
        });
    });
    
    // List item accordion (Fakultas, Admission)
    document.querySelectorAll('.expand-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var target = btn.dataset.target;
            var hiddenCount = parseInt(btn.dataset.hidden);
            var hiddenClass = target === 'fakultas' ? '.fakultas-hidden' : '.admission-hidden';
            var hiddenCards = document.querySelectorAll(hiddenClass);
            var isExpanded = btn.classList.contains('expanded');
            
            hiddenCards.forEach(function(card) {
                card.style.display = isExpanded ? 'none' : 'block';
            });
            
            btn.classList.toggle('expanded');
            
            var btnText = btn.querySelector('.btn-text');
            var icon = btn.querySelector('i');
            
            if (isExpanded) {
                btnText.textContent = 'Selengkapnya (' + hiddenCount + ' lagi)';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            } else {
                btnText.textContent = 'Sembunyikan';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        });
    });
});
</script>

</main>

<?php
get_footer();
