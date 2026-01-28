<?php
/**
 * Menu Item Custom Fields
 *
 * Adds custom fields (icon, icon background, description) to menu items
 * in the WordPress admin Appearance → Menus editor.
 *
 * @package TugasinWP
 * @since 2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tugasin_Menu_Fields {

    /**
     * Constructor
     */
    public function __construct() {
        // Add custom fields to menu item editor
        add_action( 'wp_nav_menu_item_custom_fields', array( $this, 'add_custom_fields' ), 10, 5 );
        
        // Save custom fields
        add_action( 'wp_update_nav_menu_item', array( $this, 'save_custom_fields' ), 10, 3 );
        
        // Enqueue admin styles
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
    }

    /**
     * Add custom fields to menu item in admin
     */
    public function add_custom_fields( $item_id, $menu_item, $depth, $args, $current_object_id ) {
        $icon_class = get_post_meta( $item_id, '_menu_item_icon', true );
        $icon_bg = get_post_meta( $item_id, '_menu_item_icon_bg', true );
        $description = get_post_meta( $item_id, '_menu_item_description', true );
        
        $bg_options = array(
            '' => __( '-- Select Background --', 'tugasin' ),
            'bg-pastel-indigo' => __( 'Indigo (Purple)', 'tugasin' ),
            'bg-pastel-yellow' => __( 'Yellow', 'tugasin' ),
            'bg-pastel-green' => __( 'Green', 'tugasin' ),
            'bg-pastel-gray' => __( 'Gray', 'tugasin' ),
            'bg-pastel-purple' => __( 'Purple', 'tugasin' ),
            'bg-pastel-blue' => __( 'Blue', 'tugasin' ),
            'bg-pastel-red' => __( 'Red', 'tugasin' ),
        );
        ?>
        <div class="tugasin-menu-fields" style="clear: both; padding: 10px 0; border-top: 1px solid #eee; margin-top: 10px;">
            <p class="description" style="margin-bottom: 10px; font-weight: 600; color: #1e1e1e;">
                <i class="dashicons dashicons-admin-customizer"></i> <?php esc_html_e( 'Tugasin Mega Menu Settings', 'tugasin' ); ?>
            </p>
            
            <p class="field-icon-class description description-wide">
                <label for="edit-menu-item-icon-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Icon Class (Font Awesome)', 'tugasin' ); ?><br>
                    <input type="text" 
                           id="edit-menu-item-icon-<?php echo esc_attr( $item_id ); ?>" 
                           class="widefat code" 
                           name="menu-item-icon[<?php echo esc_attr( $item_id ); ?>]" 
                           value="<?php echo esc_attr( $icon_class ); ?>"
                           placeholder="fas fa-graduation-cap">
                </label>
                <span class="description" style="font-size: 11px; color: #666;">
                    <?php esc_html_e( 'Example: fas fa-graduation-cap, fas fa-book, fas fa-tasks', 'tugasin' ); ?>
                </span>
            </p>
            
            <p class="field-icon-bg description description-wide">
                <label for="edit-menu-item-icon-bg-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Icon Background', 'tugasin' ); ?><br>
                    <select id="edit-menu-item-icon-bg-<?php echo esc_attr( $item_id ); ?>" 
                            name="menu-item-icon-bg[<?php echo esc_attr( $item_id ); ?>]"
                            class="widefat">
                        <?php foreach ( $bg_options as $value => $label ) : ?>
                            <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $icon_bg, $value ); ?>>
                                <?php echo esc_html( $label ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </p>
            
            <p class="field-description-custom description description-wide">
                <label for="edit-menu-item-desc-<?php echo esc_attr( $item_id ); ?>">
                    <?php esc_html_e( 'Short Description', 'tugasin' ); ?><br>
                    <textarea id="edit-menu-item-desc-<?php echo esc_attr( $item_id ); ?>" 
                              class="widefat" 
                              rows="2"
                              name="menu-item-description-custom[<?php echo esc_attr( $item_id ); ?>]"
                              placeholder="<?php esc_attr_e( 'Brief description for mega menu', 'tugasin' ); ?>"><?php echo esc_textarea( $description ); ?></textarea>
                </label>
            </p>
        </div>
        <?php
    }

    /**
     * Save custom fields
     */
    public function save_custom_fields( $menu_id, $menu_item_db_id, $args ) {
        // Icon class
        if ( isset( $_POST['menu-item-icon'][ $menu_item_db_id ] ) ) {
            $icon = sanitize_text_field( $_POST['menu-item-icon'][ $menu_item_db_id ] );
            update_post_meta( $menu_item_db_id, '_menu_item_icon', $icon );
        }
        
        // Icon background
        if ( isset( $_POST['menu-item-icon-bg'][ $menu_item_db_id ] ) ) {
            $bg = sanitize_text_field( $_POST['menu-item-icon-bg'][ $menu_item_db_id ] );
            update_post_meta( $menu_item_db_id, '_menu_item_icon_bg', $bg );
        }
        
        // Description
        if ( isset( $_POST['menu-item-description-custom'][ $menu_item_db_id ] ) ) {
            $desc = sanitize_text_field( $_POST['menu-item-description-custom'][ $menu_item_db_id ] );
            update_post_meta( $menu_item_db_id, '_menu_item_description', $desc );
        }
    }

    /**
     * Enqueue admin styles for menu editor
     */
    public function admin_styles( $hook ) {
        if ( 'nav-menus.php' !== $hook ) {
            return;
        }
        
        wp_add_inline_style( 'wp-admin', '
            .tugasin-menu-fields {
                background: #f9f9f9;
                padding: 15px !important;
                border-radius: 4px;
                margin: 10px 0 !important;
            }
            .tugasin-menu-fields .dashicons {
                font-size: 16px;
                vertical-align: middle;
                margin-right: 5px;
            }
            .tugasin-menu-fields p.field-icon-class,
            .tugasin-menu-fields p.field-icon-bg,
            .tugasin-menu-fields p.field-description-custom {
                margin: 8px 0;
            }
        ' );
    }
}
