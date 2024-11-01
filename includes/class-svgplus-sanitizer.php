<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

use enshrined\svgSanitize\Sanitizer;

class SVGPlus_Sanitizer {

    /**
     * Sanitizes the uploaded SVG content using the enshrined/svg-sanitize library.
     *
     * @param string $svg_content The raw SVG content.
     * @return string|false The sanitized SVG content or false on failure.
     */
    public static function sanitize_svg($svg_content) {
        // Initialize the sanitizer
        $sanitizer = new Sanitizer();

        // Sanitize the SVG
        $sanitized_svg = $sanitizer->sanitize($svg_content);

        if ($sanitized_svg === false) {
            error_log('SVGPlus_Sanitizer: Failed to sanitize SVG.');
            return false;
        }

        return $sanitized_svg;
    }
}