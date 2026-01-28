<?php
/**
 * 404 Error Page Template (Fullscreen)
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
    <title><?php esc_html_e('404 - Halaman Tidak Ditemukan', 'tugasin'); ?> | <?php bloginfo('name'); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #059669;
            --accent-btn: #fbbf24;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, #065f46 100%);
            position: relative;
            overflow: hidden;
        }

        /* Background 404 */
        .bg-404 {
            position: absolute;
            font-size: clamp(200px, 35vw, 500px);
            font-weight: 800;
            color: rgba(255, 255, 255, 0.05);
            z-index: 0;
            user-select: none;
            pointer-events: none;
        }

        /* Floating decorations */
        .floating-icon {
            position: absolute;
            color: rgba(255, 255, 255, 0.08);
            animation: float 6s ease-in-out infinite;
        }

        .floating-icon.icon-1 {
            top: 15%;
            left: 8%;
            font-size: 80px;
        }

        .floating-icon.icon-2 {
            bottom: 20%;
            right: 10%;
            font-size: 60px;
            animation-delay: -3s;
        }

        .floating-icon.icon-3 {
            top: 60%;
            left: 5%;
            font-size: 40px;
            animation-delay: -1.5s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        /* Content */
        .content {
            text-align: center;
            position: relative;
            z-index: 1;
            max-width: 600px;
            padding: 40px 24px;
        }

        .icon-circle {
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

        .icon-circle i {
            font-size: 48px;
            color: var(--accent-btn);
        }

        h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            color: white;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .description {
            font-size: 1.125rem;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 40px;
            line-height: 1.7;
        }

        .buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
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

        .btn-accent {
            background: var(--accent-btn);
            color: #1a1a2e;
        }

        .btn-accent:hover {
            background: #f59e0b;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(251, 191, 36, 0.3);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: white;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            transform: translateY(-2px);
        }

        /* Helpful Links */
        .helpful-links {
            margin-top: 60px;
            padding-top: 40px;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
        }

        .helpful-links p {
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .links-row {
            display: flex;
            gap: 32px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .links-row a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .links-row a:hover {
            opacity: 1;
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .floating-icon {
                display: none;
            }

            .links-row {
                gap: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Background 404 -->
    <div class="bg-404">404</div>

    <!-- Floating decorations -->
    <div class="floating-icon icon-1">
        <i class="fas fa-search"></i>
    </div>
    <div class="floating-icon icon-2">
        <i class="fas fa-file-alt"></i>
    </div>
    <div class="floating-icon icon-3">
        <i class="fas fa-link"></i>
    </div>

    <!-- Content -->
    <div class="content">
        <div class="icon-circle">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <h1><?php esc_html_e('Oops! Halaman Tidak Ditemukan', 'tugasin'); ?></h1>

        <p class="description">
            <?php esc_html_e('Sepertinya halaman yang kamu cari sudah dipindahkan atau tidak ada. Yuk, kembali ke halaman utama!', 'tugasin'); ?>
        </p>

        <div class="buttons">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-accent">
                <i class="fas fa-home"></i> <?php esc_html_e('Kembali ke Home', 'tugasin'); ?>
            </a>
            <a href="<?php echo esc_url(tugasin_get_whatsapp_url()); ?>" class="btn btn-outline" target="_blank"
                rel="noopener noreferrer">
                <i class="fab fa-whatsapp"></i> <?php esc_html_e('Hubungi Kami', 'tugasin'); ?>
            </a>
        </div>

        <!-- Helpful Links -->
        <div class="helpful-links">
            <p><?php esc_html_e('Mungkin kamu mencari:', 'tugasin'); ?></p>
            <div class="links-row">
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
</body>

</html>