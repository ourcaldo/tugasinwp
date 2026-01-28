/**
 * TugasinWP Admin Scripts
 *
 * @package TugasinWP
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        // Initialize color pickers
        if ($.fn.wpColorPicker) {
            $('.tugasin-color-picker').wpColorPicker();
        }

        // Settings page tabs (if needed)
        $('.tugasin-tab-nav a').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            
            $('.tugasin-tab-nav a').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
            
            $('.tugasin-tab-content').hide();
            $(target).show();
        });

        // Confirm before reset
        $('.tugasin-reset-btn').on('click', function(e) {
            if (!confirm('Are you sure you want to reset all settings to defaults?')) {
                e.preventDefault();
            }
        });
    });

})(jQuery);
