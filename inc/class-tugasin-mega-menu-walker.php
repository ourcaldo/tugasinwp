<?php
/**
 * Custom Mega Menu Walker
 *
 * Extends Walker_Nav_Menu to output mega menu HTML structure
 * while maintaining the existing visual design.
 *
 * @package TugasinWP
 * @since 2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tugasin_Mega_Menu_Walker extends Walker_Nav_Menu {

    /**
     * Track if we're in a dropdown/mega menu
     */
    private $in_dropdown = false;
    private $dropdown_type = '';
    private $current_parent_id = 0;

    /**
     * Starts the list before the elements are added.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            // First level dropdown - determine type based on parent classes
            $dropdown_class = 'dropdown-menu';
            if ( $this->dropdown_type === 'service' ) {
                $dropdown_class .= ' service-menu-enhanced';
            } else {
                $dropdown_class .= ' resource-menu';
            }
            $output .= '<div class="' . $dropdown_class . '">';
            
            // For service menu, add the left panel
            if ( $this->dropdown_type === 'service' ) {
                // Use mapped Layanan page URL from Tugasin Settings
                $layanan_url = function_exists( 'tugasin_get_page_url' ) 
                    ? tugasin_get_page_url( 'layanan' ) 
                    : home_url( '/layanan/' );
                
                $output .= '<div class="dropdown-panel-left">';
                $output .= '<a href="' . esc_url( $layanan_url ) . '" class="view-all-link">';
                $output .= '<div class="view-all-icon"><i class="fas fa-th-large"></i></div>';
                $output .= '<div>';
                $output .= '<strong class="menu-item-title">' . esc_html__( 'Lihat Semua Layanan', 'tugasin' ) . '</strong>';
                $output .= '<p>' . esc_html__( 'Jelajahi semua layanan', 'tugasin' ) . '</p>';
                $output .= '</div>';
                $output .= '<i class="fas fa-arrow-right"></i>';
                $output .= '</a>';
                $output .= '</div>';
                $output .= '<div class="dropdown-panel-right">';
            }
            
            $this->in_dropdown = true;
        }
    }

    /**
     * Ends the list after the elements are added.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            if ( $this->dropdown_type === 'service' ) {
                $output .= '</div>'; // Close dropdown-panel-right
            }
            $output .= '</div>'; // Close dropdown-menu
            $this->in_dropdown = false;
            $this->dropdown_type = '';
        }
    }

    /**
     * Start the element output.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $has_children = in_array( 'menu-item-has-children', $classes );
        
        // Get custom meta fields
        $icon_class = get_post_meta( $item->ID, '_menu_item_icon', true );
        $icon_bg = get_post_meta( $item->ID, '_menu_item_icon_bg', true );
        $description = get_post_meta( $item->ID, '_menu_item_description', true );
        
        // Fallback to item description if custom meta not set
        if ( empty( $description ) && ! empty( $item->description ) ) {
            $description = $item->description;
        }
        
        if ( $depth === 0 ) {
            // Top-level items
            if ( $has_children ) {
                // Determine dropdown type based on CSS classes or title
                $title_lower = strtolower( $item->title );
                if ( strpos( $title_lower, 'service' ) !== false || 
                     strpos( $title_lower, 'layanan' ) !== false ||
                     in_array( 'mega-menu-service', $classes ) ) {
                    $this->dropdown_type = 'service';
                } else {
                    $this->dropdown_type = 'resource';
                }
                $this->current_parent_id = $item->ID;
                
                $output .= '<div class="nav-item-dropdown">';
                $output .= '<a href="' . esc_url( $item->url ) . '" class="nav-link">';
                $output .= esc_html( $item->title );
                $output .= ' <i class="fas fa-chevron-down"></i>';
                $output .= '</a>';
            } else {
                // Simple top-level link
                $output .= '<a href="' . esc_url( $item->url ) . '">';
                $output .= esc_html( $item->title );
                $output .= '</a>';
            }
        } else {
            // Dropdown items (depth 1+)
            $output .= '<a href="' . esc_url( $item->url ) . '" class="dropdown-item">';
            
            // Icon (if provided)
            if ( ! empty( $icon_class ) ) {
                $bg_class = ! empty( $icon_bg ) ? $icon_bg : 'bg-pastel-indigo';
                $output .= '<div class="dd-icon ' . esc_attr( $bg_class ) . '">';
                $output .= '<i class="' . esc_attr( $icon_class ) . '"></i>';
                $output .= '</div>';
            }
            
            $output .= '<div>';
            $output .= '<strong class="menu-item-title">' . esc_html( $item->title ) . '</strong>';
            if ( ! empty( $description ) ) {
                $output .= '<p>' . esc_html( $description ) . '</p>';
            }
            $output .= '</div>';
            $output .= '</a>';
        }
    }

    /**
     * End the element output.
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $has_children = in_array( 'menu-item-has-children', $classes );
        
        if ( $depth === 0 && $has_children ) {
            $output .= '</div>'; // Close nav-item-dropdown
        }
    }
}
