<?php
/**
 * Mobile Menu Walker
 *
 * Extends Walker_Nav_Menu to output mobile accordion menu structure
 *
 * @package TugasinWP
 * @since 2.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tugasin_Mobile_Menu_Walker extends Walker_Nav_Menu {

    /**
     * Track accordion state
     */
    private $in_accordion = false;
    private $current_parent_title = '';

    /**
     * Starts the list before the elements are added.
     */
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '<div class="accordion-content">';
            
            // Add "Semua Layanan" link at top for Layanan accordion
            $title_lower = strtolower( $this->current_parent_title );
            if ( strpos( $title_lower, 'service' ) !== false || strpos( $title_lower, 'layanan' ) !== false ) {
                $layanan_url = function_exists( 'tugasin_get_page_url' ) ? tugasin_get_page_url( 'layanan' ) : '#';
                $output .= '<a href="' . esc_url( $layanan_url ) . '" class="accordion-item accordion-item-highlight">';
                $output .= '<div class="accordion-item-icon bg-primary">';
                $output .= '<i class="fas fa-th-large"></i>';
                $output .= '</div>';
                $output .= '<div>';
                $output .= '<strong>' . esc_html__( 'Semua Layanan', 'tugasin' ) . '</strong>';
                $output .= '<span>' . esc_html__( 'Lihat semua layanan kami', 'tugasin' ) . '</span>';
                $output .= '</div>';
                $output .= '</a>';
            }
            
            $this->in_accordion = true;
        }
    }

    /**
     * Ends the list after the elements are added.
     */
    public function end_lvl( &$output, $depth = 0, $args = null ) {
        if ( $depth === 0 ) {
            $output .= '</div></div>'; // Close accordion-content and mobile-accordion
            $this->in_accordion = false;
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
        
        // Fallback to item description
        if ( empty( $description ) && ! empty( $item->description ) ) {
            $description = $item->description;
        }
        
        if ( $depth === 0 ) {
            // Top-level items
            if ( $has_children ) {
                // Parent with accordion
                $this->current_parent_title = $item->title;
                
                // Determine icon for parent
                $parent_icon = 'fas fa-folder';
                $title_lower = strtolower( $item->title );
                $is_layanan = ( strpos( $title_lower, 'service' ) !== false || strpos( $title_lower, 'layanan' ) !== false );
                
                if ( $is_layanan ) {
                    $parent_icon = 'fas fa-briefcase';
                } elseif ( strpos( $title_lower, 'resource' ) !== false ) {
                    $parent_icon = 'fas fa-book-open';
                }
                
                $output .= '<div class="mobile-accordion">';
                $output .= '<button class="accordion-header">';
                $output .= '<span><i class="' . esc_attr( $parent_icon ) . '"></i> ' . esc_html( $item->title ) . '</span>';
                $output .= '<i class="fas fa-chevron-down accordion-icon"></i>';
                $output .= '</button>';
            } else {
                // Simple link
                $link_icon = 'fas fa-link';
                if ( strtolower( $item->title ) === 'home' || strtolower( $item->title ) === 'beranda' ) {
                    $link_icon = 'fas fa-home';
                }
                
                $output .= '<a href="' . esc_url( $item->url ) . '" class="mobile-menu-link">';
                $output .= '<i class="' . esc_attr( $link_icon ) . '"></i> ' . esc_html( $item->title );
                $output .= '</a>';
            }
        } else {
            // Accordion items (depth 1)
            $bg_class = ! empty( $icon_bg ) ? $icon_bg : 'bg-pastel-indigo';
            $item_icon = ! empty( $icon_class ) ? $icon_class : 'fas fa-circle';
            
            $output .= '<a href="' . esc_url( $item->url ) . '" class="accordion-item">';
            $output .= '<div class="accordion-item-icon ' . esc_attr( $bg_class ) . '">';
            $output .= '<i class="' . esc_attr( $item_icon ) . '"></i>';
            $output .= '</div>';
            $output .= '<div>';
            $output .= '<strong>' . esc_html( $item->title ) . '</strong>';
            if ( ! empty( $description ) ) {
                $output .= '<span>' . esc_html( $description ) . '</span>';
            }
            $output .= '</div>';
            $output .= '</a>';
        }
    }

    /**
     * End the element output.
     */
    public function end_el( &$output, $item, $depth = 0, $args = null ) {
        // Nothing needed here for mobile menu
    }
}
