<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SVGPlus_Shortcode {

    public static function init() {
        add_shortcode('svgplus', array(__CLASS__, 'render_shortcode'));
    }

    public static function render_shortcode($atts) {
        $atts = shortcode_atts(
            array(
                'id' => '',
                'class' => 'svgplus-widget',
                'alt' => __('SVG Image', 'svgplus'),
                'lazy' => 'true',
            ),
            $atts,
            'svgplus'
        );

        if (empty($atts['id'])) {
            return esc_html__('No SVG ID provided.', 'svgplus');
        }

        $svg_url = wp_get_attachment_url($atts['id']);
        if (!$svg_url) {
            return esc_html__('SVG URL not found.', 'svgplus');
        }

        $settings = get_option('svgplus_settings');
        $allow_animations = isset($settings['allow_animations']) ? $settings['allow_animations'] : 0;
        $classes = esc_attr($atts['class']);
        $alt_text = esc_attr($atts['alt']);
        $loading = ($atts['lazy'] === 'false' || $atts['lazy'] === '0') ? 'eager' : 'lazy';

        // Add animation class if enabled
        if ($allow_animations) {
            $classes .= ' svgplus-animate';
        }

        // Build the output
        $output = '<div class="' . $classes . '">';
        $output .= '<img src="' . esc_url($svg_url) . '" alt="' . $alt_text . '" loading="' . esc_attr($loading) . '" />';
        $output .= '</div>';

        return $output;
    }
}

?>