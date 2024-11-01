<?php
/**
 * Plugin Name: SVGPlus
 * Description: Upload, sanitize, and display SVG files securely in WordPress.
 * Version: 1.1.0
 * Author: Rizonepress
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit;
}

// Include Composer's autoloader if it exists
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
} else {
    error_log('SVGPlus: Composer autoloader not found. Please ensure dependencies are installed.');
    return;
}

// Include necessary classes
$required_classes = [
    'includes/class-svgplus-sanitizer.php',
    'includes/class-svgplus-upload.php',
    'includes/class-svgplus-settings.php',  // Ensure settings class is included
];

foreach ($required_classes as $class_file) {
    $file_path = plugin_dir_path(__FILE__) . $class_file;
    if (file_exists($file_path)) {
        require_once $file_path;
    } else {
        error_log('SVGPlus: ' . basename($class_file) . ' file not found.');
        return;
    }
}

// Add the default settings function
function svgplus_default_settings() {
    return [
        'allowed_roles' => ['administrator', 'editor'],  // Default allowed roles for uploads
        'allow_animations' => true,  // Enable animations by default
        'custom_css' => '',  // Custom CSS field, initially empty
        'enable_svg_support' => 1, // Added default setting for SVG support
    ];
}

// Set up the plugin activation hook to ensure default settings are added
function svgplus_activate_plugin() {
    $default_settings = svgplus_default_settings();
    if (!get_option('svgplus_settings')) {
        add_option('svgplus_settings', $default_settings);
    }
}
register_activation_hook(__FILE__, 'svgplus_activate_plugin');

// Initialize the Settings class to make sure the settings page is loaded
if (class_exists('SVGPlus_Settings')) {
    new SVGPlus_Settings();  // Load the settings page
}

// Initialize the upload process
SVGPlus_Upload::init();

// Force allow SVG uploads based on the 'Enable SVG Support' setting
function svgplus_allow_svg_uploads($existing_mimes) {
    $options = get_option('svgplus_settings');
    $is_svg_enabled = isset($options['enable_svg_support']) ? $options['enable_svg_support'] : 0;

    if ($is_svg_enabled) {
        // Add the SVG mime type
        $existing_mimes['svg'] = 'image/svg+xml';
        $existing_mimes['svgz'] = 'image/svg+xml'; // For compressed SVG
    } else {
        // Remove SVG mime types if present
        unset($existing_mimes['svg']);
        unset($existing_mimes['svgz']);
    }

    return $existing_mimes;
}
add_filter('upload_mimes', 'svgplus_allow_svg_uploads');

// Bypass MIME type checks only when SVG support is enabled
function svgplus_disable_real_mime_check($data, $file, $filename, $mimes) {
    $options = get_option('svgplus_settings');
    $is_svg_enabled = isset($options['enable_svg_support']) ? $options['enable_svg_support'] : 0;

    if (!$is_svg_enabled) {
        return $data;
    }

    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    
    if ($ext === 'svg' || $ext === 'svgz') {
        $data['ext'] = 'svg';
        $data['type'] = 'image/svg+xml';
    }

    return $data;
}
add_filter('wp_check_filetype_and_ext', 'svgplus_disable_real_mime_check', 10, 4);

// Update user roles based on settings
function svgplus_user_roles_can_upload($user) {
    $options = get_option('svgplus_settings');
    $allowed_roles = isset($options['allowed_roles']) ? $options['allowed_roles'] : array();
    
    foreach ($allowed_roles as $role) {
        if (in_array($role, $user->roles)) {
            return true;
        }
    }
    
    return false;
}

// Adjust permissions only when SVG support is enabled
add_action('admin_init', function() {
    $options = get_option('svgplus_settings');
    $is_svg_enabled = isset($options['enable_svg_support']) ? $options['enable_svg_support'] : 0;

    if ($is_svg_enabled) {
        if (!current_user_can('upload_files')) {
            add_filter('user_has_cap', function($caps, $cap, $user_id) {
                $user = new WP_User($user_id);
                if (svgplus_user_roles_can_upload($user)) {
                    $caps['upload_files'] = true;
                }
                return $caps;
            }, 10, 3);
        }
    }
});