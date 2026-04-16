<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * D5TM_Skeleton_Generator
 *
 * Returns a dynamic, monochrome skeleton SVG placeholder based on category colors.
 */
class D5TM_Skeleton_Generator {

    /**
     * Generate a dynamic skeleton SVG for the card thumbnail.
     *
     * @param WP_Post $post The layout post object.
     * @return string Inline SVG HTML.
     */
    public static function generate( $post ) {
        if ( ! $post ) return self::generic_skeleton( '#cbd5e1' );

        $branding_enabled = get_option( 'd5tm_skeleton_branding', 'off' ) === 'on'; // Default to OFF now
        $color = '#cbd5e1'; // Default to a High-Contrast Gray

        if ( $branding_enabled ) {
            $categories = get_the_terms( $post->ID, 'layout_category' );
            if ( ! is_wp_error( $categories ) && ! empty( $categories ) ) {
                $cat = $categories[0];
                $stored_color = get_term_meta( $cat->term_id, 'd5tm_color', true );
                if ( $stored_color ) {
                    $color = $stored_color;
                } else {
                    $color = '#6366f1'; // Default brand purple fallback if branding on but no cat color
                }
            }
        }

        return self::generic_skeleton( $color );
    }

    /**
     * A clean, modern wireframe skeleton using monochrome shades of a base color.
     */
    private static function generic_skeleton( $base_hex ) {
        $shades = self::generate_shades( $base_hex );

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300" width="100%" style="display:block;">
            <!-- Background -->
            <rect width="400" height="300" fill="' . $shades['bg'] . '"/>

            <!-- Section 1: Hero / Header band -->
            <rect x="0" y="0" width="400" height="90" fill="' . $shades['primary'] . '"/>
            <rect x="20" y="18" width="200" height="16" rx="8" fill="' . $shades['accent'] . '"/>
            <rect x="20" y="42" width="150" height="10" rx="5" fill="' . $shades['accent'] . '"/>
            <rect x="20" y="60" width="80" height="20" rx="10" fill="' . $shades['accent'] . '"/>

            <!-- Section 2: Two-column text block -->
            <rect x="20" y="108" width="170" height="10" rx="5" fill="' . $shades['accent'] . '"/>
            <rect x="20" y="124" width="140" height="8" rx="4" fill="' . $shades['accent'] . '"/>
            <rect x="20" y="138" width="155" height="8" rx="4" fill="' . $shades['accent'] . '"/>

            <rect x="210" y="108" width="170" height="10" rx="5" fill="' . $shades['accent'] . '"/>
            <rect x="210" y="124" width="140" height="8" rx="4" fill="' . $shades['accent'] . '"/>
            <rect x="210" y="138" width="155" height="8" rx="4" fill="' . $shades['accent'] . '"/>

            <!-- Section divider -->
            <line x1="20" y1="165" x2="380" y2="165" stroke="' . $shades['border'] . '" stroke-width="1"/>

            <!-- Section 3: Three-card grid -->
            <rect x="20"  y="178" width="108" height="70" rx="4" fill="' . $shades['primary'] . '"/>
            <rect x="146" y="178" width="108" height="70" rx="4" fill="' . $shades['primary'] . '"/>
            <rect x="272" y="178" width="108" height="70" rx="4" fill="' . $shades['primary'] . '"/>

            <!-- Card labels -->
            <rect x="20"  y="256" width="70" height="8" rx="4" fill="' . $shades['accent'] . '"/>
            <rect x="20"  y="270" width="50" height="6" rx="3" fill="' . $shades['accent'] . '"/>
            <rect x="146" y="256" width="70" height="8" rx="4" fill="' . $shades['accent'] . '"/>
            <rect x="146" y="270" width="50" height="6" rx="3" fill="' . $shades['accent'] . '"/>
            <rect x="272" y="256" width="70" height="8" rx="4" fill="' . $shades['accent'] . '"/>
            <rect x="272" y="270" width="50" height="6" rx="3" fill="' . $shades['accent'] . '"/>
        </svg>';
    }

    /**
     * Generate monochrome shades from a base hex color.
     * High-contrast logic: Light BG, Darker Accents.
     */
    private static function generate_shades( $hex ) {
        $hex = ltrim($hex, '#');

        if (strlen($hex) == 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return [
            'bg'      => self::adjust_color($r, $g, $b, 1.1),  // Slightly Lighter than base
            'primary' => self::adjust_color($r, $g, $b, 0.95), // Slightly Darker
            'accent'  => self::adjust_color($r, $g, $b, 0.8),  // Much Darker
            'border'  => self::adjust_color($r, $g, $b, 0.9),  // Subtle Divider
        ];
    }

    /**
     * Adjust color brightness: >1 is lighten, <1 is darken.
     */
    private static function adjust_color($r, $g, $b, $factor) {
        $r = min(255, max(0, round($r * $factor)));
        $g = min(255, max(0, round($g * $factor)));
        $b = min(255, max(0, round($b * $factor)));

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}
