<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SVGPlus_Render {

    public function __construct() {
        // Hook to modify SVG image attributes when using wp_get_attachment_image()
        add_filter('wp_get_attachment_image_attributes', [$this, 'add_custom_class_to_svg_image'], 10, 2);

        // Hook to modify SVGs that are embedded directly in post content
        add_filter('the_content', [$this, 'add_custom_class_to_embedded_svgs'], 10);
    }

    /**
     * Adds a custom class to SVG attachment images for easier customization.
     *
     * @param array $attr The existing attributes of the attachment.
     * @param WP_Post $attachment The attachment post object.
     * @return array Modified attributes with a custom class added.
     */
    public function add_custom_class_to_svg_image($attr, $attachment) {
        // Check if the attachment is an SVG
        if ('image/svg+xml' === get_post_mime_type($attachment->ID)) {
            // Check if a class attribute already exists and append our custom class
            if (isset($attr['class'])) {
                $attr['class'] .= ' svgplus-custom';
            } else {
                $attr['class'] = 'svgplus-custom';
            }
        }
        
        return $attr;
    }

    /**
     * Adds a custom class to SVGs embedded directly in the content.
     *
     * @param string $content The post content.
     * @return string Modified content with SVGs including a custom class.
     */
    public function add_custom_class_to_embedded_svgs($content) {
        // Use regex to find <img> tags that contain SVGs and add a class
        $content = preg_replace_callback(
            '/<img[^>]+src=[\'"]([^\'"]+\.svg)[\'"][^>]*>/i',
            function ($matches) {
                $img_tag = $matches[0];
                // Check if the class attribute is present
                if (strpos($img_tag, 'class=') !== false) {
                    // Append the custom class to the existing class attribute
                    $img_tag = preg_replace('/class=["\']([^"\']*)["\']/', 'class="$1 svgplus-custom"', $img_tag);
                } else {
                    // Add a new class attribute if none exists
                    $img_tag = str_replace('<img', '<img class="svgplus-custom"', $img_tag);
                }
                return $img_tag;
            },
            $content
        );

        return $content;
    }
}

// Instantiate the class to apply the filter
new SVGPlus_Render();

?>
