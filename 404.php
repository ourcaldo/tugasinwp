<?php
/**
 * 404 Error Page Template (Fullscreen)
 *
 * DESIGN DECISION: This template intentionally bypasses get_header()/get_footer()
 * to provide a fullscreen error page experience. It uses its own <!DOCTYPE html>
 * and <head> while still calling wp_head()/wp_footer() so plugins and the admin
 * bar can function. The header/footer elements are hidden via CSS.
 *
 * Uses WordPress hooks (wp_head/wp_footer) while maintaining fullscreen design.
 *
 * @package TugasinWP
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
    <style>
        /* 404 Page Specific Styles - Fullscreen Override */
        body.error404 {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary, #059669) 0%, #065f46 100%) !important;
            position: relative;
            overflow: hidden;
        }

        /* Hide header and footer on 404 */
        body.error404 .header,
        body.error404 header,
        body.error404 footer,
        body.error404 .whatsapp-widget {
            display: none !important;
        }

        /* Background 404 */
        .error-bg-404 {
            position: absolute;
            font-size: clamp(200px, 35vw, 500px);
            font-weight: 800;
            color: rgba(255, 255, 255, 0.05);
            z-index: 0;
            user-select: none;
            pointer-events: none;
        }

        /* Floating decorations */
        .error-floating-icon {
            position: absolute;
            color: rgba(255, 255, 255, 0.08);
            animation: error-float 6s ease-in-out infinite;
        }

        .error-floating-icon.icon-1 {
            top: 15%;
            left: 8%;
            font-size: 80px;
        }

        .error-floating-icon.icon-2 {
            bottom: 20%;
            right: 10%;
            font-size: 60px;
            animation-delay: -3s;
        }

        .error-floating-icon.icon-3 {
            top: 60%;
            left: 5%;
            font-size: 40px;
            animation-delay: -1.5s;
        }

        @keyframes error-float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        /* Content */
        .error-content {
            text-align: center;
            position: relative;
            z-index: 1;
            max-width: 600px;
            padding: 40px 24px;
        }

        .error-icon-circle {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            backdrop-filter: blur(10px);
        }

        .error-icon-circle i {
            font-size: 48px;
            color: #fbbf24;
        }

        .error-content h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            color: white;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .error-description {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 40px;
            line-height: 1.7;
        }

        .error-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .error-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 28px;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .error-btn-accent {
            background: #fbbf24;
            color: #1a1a2e;
        }

        .error-btn-accent:hover {
            background: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(251, 191, 36, 0.3);
        }

        .error-btn-outline {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: white;
        }

        .error-btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            transform: translateY(-2px);
        }

        /* Helpful Links */
        .error-helpful-links {
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .error-helpful-links p {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .error-links-row {
            display: flex;
            gap: 32px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .error-links-row a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .error-links-row a:hover {
            opacity: 1;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .error-floating-icon {
                display: none;
            }

            .error-links-row {
                gap: 20px;
            }
        }
    </style>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <!-- Background 404 -->
    <div class="error-bg-404">404</div>

    <!-- Floating decorations -->
    <div class="error-floating-icon icon-1">
        <i class="fas fa-search"></i>
    </div>
    <div class="error-floating-icon icon-2">
        <i class="fas fa-file-alt"></i>
    </div>
    <div class="error-floating-icon icon-3">
        <i class="fas fa-link"></i>
    </div>

    <!-- Content -->
    <div class="error-content">
        <div class="error-icon-circle">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <h1><?php esc_html_e('Oops! Halaman Tidak Ditemukan', 'tugasin'); ?></h1>

        <p class="error-description">
            <?php esc_html_e('Sepertinya halaman yang kamu cari sudah dipindahkan atau tidak ada. Yuk, kembali ke halaman utama!', 'tugasin'); ?>
        </p>

        <div class="error-buttons">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="error-btn error-btn-accent">
                <i class="fas fa-home"></i> <?php esc_html_e('Kembali ke Home', 'tugasin'); ?>
            </a>
            <a href="<?php echo esc_url(tugasin_get_whatsapp_url()); ?>" class="error-btn error-btn-outline"
                target="_blank" rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i> <?php esc_html_e('Hubungi Kami', 'tugasin'); ?>
            </a>
        </div>

        <!-- Helpful Links -->
        <div class="error-helpful-links">
            <p><?php esc_html_e('Mungkin kamu mencari:', 'tugasin'); ?></p>
            <div class="error-links-row">
                <a href="<?php echo esc_url(get_post_type_archive_link('major')); ?>">
                    <i class="fas fa-graduation-cap"></i> <?php esc_html_e('Kamus Jurusan', 'tugasin'); ?>
                </a>
                <a href="<?php echo esc_url(get_post_type_archive_link('university')); ?>">
                    <i class="fas fa-university"></i> <?php esc_html_e('Kamus Kampus', 'tugasin'); ?>
                </a>
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>">
                    <i class="fas fa-blog"></i> <?php esc_html_e('Blog', 'tugasin'); ?>
                </a>
            </div>
        </div>
    </div>

    <?php wp_footer(); ?>
</body>

</html>