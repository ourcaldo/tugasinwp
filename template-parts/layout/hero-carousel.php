<?php
/**
 * Hero Carousel Template Part
 *
 * Displays the tutor carousel in the hero section.
 * Uses CSS classes from style.css for all styling.
 *
 * @package TugasinWP
 * @since 2.5.0
 */

?>

<?php
$placeholder = get_template_directory_uri() . '/assets/images/placeholder-avatar.jpg';

// Get tutor data from settings
$hero_tutors = get_option('tugasin_hero_tutors', array());
$default_tutors = array(
    array('name' => 'Sarah Wijaya', 'role' => 'Expert Skripsi', 'rating' => '4.9', 'count' => 127),
    array('name' => 'Budi Santoso', 'role' => 'Expert Makalah', 'rating' => '4.8', 'count' => 98),
    array('name' => 'Dewi Lestari', 'role' => 'Expert Tugas', 'rating' => '4.9', 'count' => 156),
);
?>

<div class="tutor-carousel">
    <?php for ($i = 0; $i < 3; $i++):
        $tutor = isset($hero_tutors[$i]) ? $hero_tutors[$i] : array();
        $default = isset($default_tutors[$i]) ? $default_tutors[$i] : array();

        $name = !empty($tutor['name']) ? $tutor['name'] : (isset($default['name']) ? $default['name'] : '');
        $role = !empty($tutor['role']) ? $tutor['role'] : (isset($default['role']) ? $default['role'] : '');
        $image = !empty($tutor['image']) ? $tutor['image'] : $placeholder;
        $rating = !empty($tutor['rating']) ? $tutor['rating'] : (isset($default['rating']) ? $default['rating'] : '4.9');
        $count = !empty($tutor['count']) ? $tutor['count'] : (isset($default['count']) ? $default['count'] : 0);
        $active_class = ($i === 0) ? ' active' : '';
        ?>
        <div class="tutor-card<?php echo $active_class; ?>">
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($name . ' - ' . $role); ?>">
            <strong class="tutor-name"><?php echo esc_html($name); ?></strong>
            <p><?php echo esc_html($role); ?></p>
            <div class="tutor-rating">
                <i class="fas fa-star"></i> <?php echo esc_html($rating); ?> (<?php echo esc_html($count); ?>)
            </div>
        </div>
    <?php endfor; ?>
</div>

<!-- Floating Cards with CSS classes for animations -->
<div class="hero-float-card card-top-left">
    <div class="hero-float-card-content">
        <div class="hero-float-card-icon green">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div>
            <strong class="float-card-title">100% Rahasia</strong>
            <span>Data aman terjamin</span>
        </div>
    </div>
</div>

<div class="hero-float-card card-top-right">
    <div class="hero-float-card-content">
        <div class="hero-float-card-icon yellow">
            <i class="fas fa-bolt"></i>
        </div>
        <div>
            <strong class="float-card-title">Respons Cepat</strong>
            <span>24/7 Support</span>
        </div>
    </div>
</div>

<div class="hero-float-card card-bottom-left">
    <div class="hero-float-card-content">
        <div class="hero-float-card-icon indigo">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <strong class="float-card-title">5000+</strong>
            <span>Tugas Selesai</span>
        </div>
    </div>
</div>