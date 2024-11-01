<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class SVGPlus_Upload {

    public static function init() {
        // Modified section starts here
        $options = get_option('svgplus_settings');
        $is_svg_enabled = isset($options['enable_svg_support']) ? $options['enable_svg_support'] : 0;

        if ($is_svg_enabled) {
            // Allow SVG mime types
            add_filter('upload_mimes', array(__CLASS__, 'add_svg_mime_type'));
            // Fix MIME type and file extension checks for SVGs
            add_filter('wp_check_filetype_and_ext', array(__CLASS__, 'fix_mime_type_svg'), 10, 4);
            // Handle file upload prefilter for SVG sanitization
            add_filter('wp_handle_upload_prefilter', array(__CLASS__, 'handle_upload_prefilter'));
        }
        // Modified section ends here
    }

    /**
     * Adds SVG to the list of allowed mime types with the custom MIME type svgplus/svg+xml.
     *
     * @param array $mimes Existing mime types.
     * @return array Modified mime types.
     */
    public static function add_svg_mime_type($mimes) {
        // Add the custom MIME type for SVGPlus
        $mimes['svg'] = 'image/svg+xml';  // Standard SVG MIME type
        $mimes['svgz'] = 'image/svg+xml'; // Compressed SVG
        return $mimes;
    }

    /**
     * Fixes the MIME type and extension checks for SVG files to ensure they pass WordPress validation.
     *
     * @param array  $data
     * @param string $file
     * @param string $filename
     * @param array  $mimes
     * @return array
     */
    public static function fix_mime_type_svg($data, $file, $filename, $mimes) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        if ($ext === 'svg' || $ext === 'svgz') {
            $data['ext'] = 'svg';
            $data['type'] = 'image/svg+xml';  // Ensure the proper MIME type is set
            $data['proper_filename'] = $data['proper_filename'] ?? $filename;
        }

        return $data;
    }

    /**
     * Handles the upload prefilter for SVGs, sanitizing the uploaded file.
     *
     * @param array $file The uploaded file data.
     * @return array Modified file data.
     */
    public static function handle_upload_prefilter($file) {
        // Make sure we're dealing with an SVG
        if ($file['type'] === 'image/svg+xml') {

            // Check user permissions
            $current_user = wp_get_current_user();
            $settings = get_option('svgplus_settings');
            $allowed_roles = isset($settings['allowed_roles']) ? $settings['allowed_roles'] : array();

            $has_allowed_role = false;
            foreach ($current_user->roles as $role) {
                if (in_array($role, $allowed_roles)) {
                    $has_allowed_role = true;
                    break;
                }
            }

            if (!$has_allowed_role) {
                $file['error'] = __('You do not have permission to upload SVG files.', 'svgplus');
                return $file;
            }

            global $wp_filesystem;

            // Initialize WP_Filesystem if not already done
            if (empty($wp_filesystem)) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                WP_Filesystem();
            }

            // Get SVG content using WP_Filesystem
            $svg_content = $wp_filesystem->get_contents($file['tmp_name']);

            if ($svg_content === false) {
                $file['error'] = __('Unable to read SVG file.', 'svgplus');
                return $file;
            }

            // Sanitize SVG
            $sanitized_svg = SVGPlus_Sanitizer::sanitize_svg($svg_content);

            if ($sanitized_svg === false) {
                $file['error'] = __('Invalid SVG file.', 'svgplus');
                return $file;
            }

            // Overwrite the temporary file with sanitized SVG using WP_Filesystem
            $result = $wp_filesystem->put_contents($file['tmp_name'], $sanitized_svg, FS_CHMOD_FILE);

            if ($result === false) {
                $file['error'] = __('Failed to sanitize SVG file.', 'svgplus');
                return $file;
            }
        }

        return $file;
    }
}
