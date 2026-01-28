<?php
/**
 * Schema Markup Class
 *
 * Outputs JSON-LD structured data for Organization, Services, and ItemList.
 *
 * @package TugasinWP
 * @since 2.8.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tugasin_Schema {

    /**
     * Service page slugs mapped to option prefixes
     */
    private $service_pages = array(
        'joki-skripsi'   => 'joki_skripsi',
        'joki-makalah'   => 'joki_makalah',
        'joki-tugas'     => 'joki_tugas',
        'cek-plagiarism' => 'cek_plagiarism',
    );

    /**
     * Constructor
     */
    public function __construct() {
        // Only add hooks if schema is enabled
        add_action( 'wp_head', array( $this, 'maybe_output_schema' ), 5 );
    }

    /**
     * Check if schema output is enabled
     */
    private function is_enabled() {
        return (bool) get_option( 'tugasin_schema_enabled', true );
    }

    /**
     * Main entry point for schema output
     */
    public function maybe_output_schema() {
        if ( ! $this->is_enabled() ) {
            return;
        }

        // Organization schema on homepage
        $this->output_organization_schema();

        // Service schema on service pages
        $this->output_service_schema();

        // ItemList schema on Layanan page
        $this->output_itemlist_schema();
        
        // CollegeOrUniversity schema on single university pages
        $this->output_university_schema();
    }

    /**
     * Output Organization schema (homepage only)
     */
    private function output_organization_schema() {
        if ( ! is_front_page() ) {
            return;
        }

        $org_name = get_option( 'tugasin_schema_org_name', get_bloginfo( 'name' ) );
        $org_logo_id = get_option( 'tugasin_schema_org_logo', 0 );
        $org_phone = get_option( 'tugasin_schema_org_phone', '' );

        // Fallback to site logo if no schema logo is set
        if ( ! $org_logo_id ) {
            $org_logo_id = get_option( 'tugasin_logo', 0 );
        }

        $org_logo_url = $org_logo_id ? wp_get_attachment_image_url( $org_logo_id, 'full' ) : '';

        $schema = array(
            '@context' => 'https://schema.org',
            '@type'    => 'Organization',
            '@id'      => home_url( '/#organization' ),
            'name'     => $org_name,
            'url'      => home_url( '/' ),
        );

        if ( $org_logo_url ) {
            $schema['logo'] = array(
                '@type'  => 'ImageObject',
                'url'    => $org_logo_url,
            );
        }

        if ( $org_phone ) {
            $schema['contactPoint'] = array(
                '@type'             => 'ContactPoint',
                'telephone'         => $org_phone,
                'contactType'       => 'customer service',
                'areaServed'        => 'ID',
                'availableLanguage' => 'Indonesian',
            );
        }

        $this->output_json_ld( $schema );
    }

    /**
     * Output Service schema on service pages
     */
    private function output_service_schema() {
        if ( ! is_page() ) {
            return;
        }

        $current_slug = get_post_field( 'post_name', get_queried_object_id() );
        $matched_slug = null;
        
        // Check if current page is a service page by slug
        if ( isset( $this->service_pages[ $current_slug ] ) ) {
            $matched_slug = $current_slug;
        } else {
            // Check by page mapping
            foreach ( $this->service_pages as $slug => $prefix ) {
                $mapped_page_id = get_option( 'tugasin_page_' . str_replace( '-', '_', $slug ), 0 );
                if ( $mapped_page_id && $mapped_page_id == get_queried_object_id() ) {
                    $matched_slug = $slug;
                    break;
                }
            }
        }
        
        if ( ! $matched_slug ) {
            return;
        }

        $prefix = 'tugasin_schema_service_' . str_replace( '-', '_', $matched_slug );
        
        $service_name = get_option( $prefix . '_name', ucwords( str_replace( '-', ' ', $matched_slug ) ) );
        $service_desc = get_option( $prefix . '_desc', '' );
        $rating_enabled = get_option( $prefix . '_rating_enabled', false );
        $rating_value = floatval( get_option( $prefix . '_rating_value', '4.9' ) );
        $rating_count = intval( get_option( $prefix . '_rating_count', 100 ) );
        $price_from = get_option( $prefix . '_price_from', '' );

        $org_name = get_option( 'tugasin_schema_org_name', get_bloginfo( 'name' ) );
        
        // Get image from theme settings (logo)
        $logo_id = get_option( 'tugasin_schema_org_logo', 0 );
        if ( ! $logo_id ) {
            $logo_id = get_option( 'tugasin_logo', 0 );
        }
        $image_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';

        // Use Product type for services - has best Google Rich Results support
        $schema = array(
            '@context'    => 'https://schema.org',
            '@type'       => 'Product',
            '@id'         => get_permalink() . '#product',
            'name'        => $service_name,
            'url'         => get_permalink(),
            'brand'       => array(
                '@type' => 'Brand',
                'name'  => $org_name,
            ),
            'category'    => 'Academic Writing Assistance',
        );

        // Add image (required for merchant listing)
        if ( $image_url ) {
            $schema['image'] = $image_url;
        }

        if ( $service_desc ) {
            $schema['description'] = $service_desc;
        }

        // Add Offer with price - always include priceCurrency
        if ( $price_from && is_numeric( $price_from ) ) {
            // priceValidUntil = last day of current month (auto-updates)
            $last_day_of_month = date( 'Y-m-t' );
            
            $schema['offers'] = array(
                '@type'           => 'Offer',
                'url'             => get_permalink(),
                'price'           => floatval( $price_from ),
                'priceCurrency'   => 'IDR',
                'availability'    => 'https://schema.org/InStock',
                'priceValidUntil' => $last_day_of_month,
            );
        }

        // Add AggregateRating if enabled - Product type fully supports this
        if ( $rating_enabled && $rating_value > 0 && $rating_count > 0 ) {
            $schema['aggregateRating'] = array(
                '@type'       => 'AggregateRating',
                'ratingValue' => $rating_value,
                'ratingCount' => $rating_count,
                'bestRating'  => 5,
                'worstRating' => 1,
            );
        }

        $this->output_json_ld( $schema );
    }

    /**
     * Output ItemList schema on Layanan page
     */
    private function output_itemlist_schema() {
        if ( ! is_page() ) {
            return;
        }

        // Check if this is the Layanan page
        $layanan_page_id = get_option( 'tugasin_page_layanan', 0 );
        $is_layanan = false;

        if ( $layanan_page_id && $layanan_page_id == get_queried_object_id() ) {
            $is_layanan = true;
        } else {
            // Fallback: check by slug
            $current_slug = get_post_field( 'post_name', get_queried_object_id() );
            if ( $current_slug === 'layanan' ) {
                $is_layanan = true;
            }
        }

        if ( ! $is_layanan ) {
            return;
        }

        // Get Layanan archive settings
        $layanan_name = get_option( 'tugasin_schema_layanan_name', __( 'Layanan Tugasin', 'tugasin' ) );
        $layanan_desc = get_option( 'tugasin_schema_layanan_desc', __( 'Daftar layanan jasa akademik dari Tugasin', 'tugasin' ) );
        $layanan_rating_enabled = get_option( 'tugasin_schema_layanan_rating_enabled', false );
        $layanan_rating_value = floatval( get_option( 'tugasin_schema_layanan_rating_value', '4.9' ) );
        $layanan_rating_count = intval( get_option( 'tugasin_schema_layanan_rating_count', 500 ) );

        $items = array();
        $position = 1;

        foreach ( $this->service_pages as $slug => $prefix ) {
            // Get the page URL
            $page_id = get_option( 'tugasin_page_' . str_replace( '-', '_', $slug ), 0 );
            $page_url = '';

            if ( $page_id ) {
                $page_url = get_permalink( $page_id );
            } else {
                // Fallback to slug
                $page = get_page_by_path( $slug );
                if ( $page ) {
                    $page_url = get_permalink( $page->ID );
                }
            }

            if ( $page_url ) {
                $schema_prefix = 'tugasin_schema_service_' . str_replace( '-', '_', $slug );
                $service_name = get_option( $schema_prefix . '_name', ucwords( str_replace( '-', ' ', $slug ) ) );

                $items[] = array(
                    '@type'    => 'ListItem',
                    'position' => $position,
                    'name'     => $service_name,
                    'url'      => $page_url,
                );
                $position++;
            }
        }

        if ( empty( $items ) ) {
            return;
        }

        $org_name = get_option( 'tugasin_schema_org_name', get_bloginfo( 'name' ) );

        // Build provider Organization object
        $provider = array(
            '@type' => 'Organization',
            '@id'   => home_url( '/#organization' ),
            'name'  => $org_name,
            'url'   => home_url( '/' ),
        );

        // Add AggregateRating to PROVIDER (Organization), NOT to ItemList
        // ItemList doesn't support aggregateRating directly - Google rejects it
        if ( $layanan_rating_enabled && $layanan_rating_value > 0 && $layanan_rating_count > 0 ) {
            $provider['aggregateRating'] = array(
                '@type'       => 'AggregateRating',
                'ratingValue' => $layanan_rating_value,
                'reviewCount' => $layanan_rating_count,
                'bestRating'  => 5,
                'worstRating' => 1,
            );
        }

        $schema = array(
            '@context'        => 'https://schema.org',
            '@type'           => 'ItemList',
            '@id'             => get_permalink() . '#itemlist',
            'name'            => $layanan_name,
            'description'     => $layanan_desc,
            'url'             => get_permalink(),
            'numberOfItems'   => count( $items ),
            'itemListElement' => $items,
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id'   => get_permalink(),
            ),
            'provider'        => $provider,
        );

        $this->output_json_ld( $schema );
    }

    /**
     * Output CollegeOrUniversity schema on single university pages
     *
     * Uses Schema.org CollegeOrUniversity type for educational institutions.
     *
     * @since 2.14.0
     */
    private function output_university_schema() {
        // Only on single university pages
        if ( ! is_singular( 'university' ) ) {
            return;
        }

        $post_id = get_the_ID();
        
        // Get ACF fields
        $logo      = get_field( 'uni_logo', $post_id );
        $location  = get_field( 'uni_location', $post_id );
        $website   = get_field( 'uni_website', $post_id );
        $phone     = get_field( 'uni_phone', $post_id );
        
        // Get taxonomies
        $uni_type = get_the_terms( $post_id, 'university_type' );
        $accred   = get_the_terms( $post_id, 'accreditation' );
        
        // Get featured image for schema image
        $featured_image = get_the_post_thumbnail_url( $post_id, 'large' );
        
        // Build schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type'    => 'CollegeOrUniversity',
            '@id'      => get_permalink( $post_id ) . '#organization',
            'name'     => get_the_title( $post_id ),
            'url'      => $website ?: get_permalink( $post_id ),
        );
        
        // Add description from post content
        $content = get_the_content( null, false, $post_id );
        if ( $content ) {
            $description = wp_trim_words( wp_strip_all_tags( $content ), 30, '...' );
            $schema['description'] = $description;
        }
        
        // Add logo
        if ( $logo ) {
            $schema['logo'] = array(
                '@type'  => 'ImageObject',
                'url'    => $logo,
            );
        }
        
        // Add featured image
        if ( $featured_image ) {
            $schema['image'] = $featured_image;
        }
        
        // Add address
        if ( $location ) {
            $schema['address'] = array(
                '@type'           => 'PostalAddress',
                'addressLocality' => $location,
                'addressCountry'  => 'ID',
            );
        }
        
        // Add phone
        if ( $phone ) {
            $schema['telephone'] = $phone;
        }
        
        // Add university type (PTN, PTS, etc.)
        if ( $uni_type && ! is_wp_error( $uni_type ) ) {
            // Map to Schema.org educational organization types where applicable
            $type_name = $uni_type[0]->name;
            $schema['additionalType'] = $type_name;
        }
        
        // Add accreditation
        if ( $accred && ! is_wp_error( $accred ) ) {
            $schema['hasCredential'] = array(
                '@type' => 'EducationalOccupationalCredential',
                'name'  => __( 'Akreditasi', 'tugasin' ) . ' ' . $accred[0]->name,
            );
        }
        
        // Add same as (external website)
        if ( $website ) {
            $schema['sameAs'] = array( $website );
        }

        $this->output_json_ld( $schema );
    }

    /**
     * Output JSON-LD script tag
     */
    private function output_json_ld( $schema ) {
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
        echo "\n" . '</script>' . "\n";
    }
}
