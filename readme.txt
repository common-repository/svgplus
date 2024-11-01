=== SVGPlus ===
Contributors: Rizonepress
Tags: svg, vector graphics, media upload, sanitization
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 1.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Home Page: https://rizonepress.com

Short Description: Upload, sanitize, and display SVG files securely in WordPress with role-based upload permissions and custom CSS support.

== Description ==

**SVGPlus** is a WordPress plugin designed to securely manage SVG (Scalable Vector Graphics) files on your website. It allows for safe SVG uploads, automatic sanitization, and provides options to control which user roles can upload SVGs.

### Key Features

1. **Secure SVG Uploads with Automatic Sanitization**: Upload SVG files directly to your WordPress media library, with automatic sanitization to remove potentially harmful code.
2. **Role-Based Upload Permissions**: Control which user roles are permitted to upload SVG files. Only administrators can modify these settings.
3. **Option to Enable or Disable SVG Support**: Easily enable or disable SVG support across your site with a single switch in the settings.
4. **Centralized Settings for Consistency and Control**: Access a dedicated settings page (`Settings > SVGPlus`) in the WordPress admin dashboard to configure plugin options.
5. **Custom CSS Support**: Add global custom CSS to style all SVGs managed by SVGPlus, maintaining a consistent design aesthetic across your site.

== Installation ==

1. **Upload the Plugin:**
   - Upload the `svgplus` folder to the `/wp-content/plugins/` directory.
2. **Activate the Plugin:**
   - Activate the plugin through the 'Plugins' menu in WordPress.
3. **Configure Settings:**
   - Navigate to `Settings > SVGPlus` in the WordPress admin dashboard to configure your SVG preferences.

### Usage

#### Uploading and Managing SVGs

1. **Upload SVGs:** Go to the WordPress media library (`Media > Add New`) and upload your SVG files as you would with any other media type.
2. **Sanitized SVGs:** SVGPlus automatically sanitizes your SVG uploads to ensure they are safe and optimized for use on your website.

#### Configuring Plugin Settings

1. **Access Settings:** Navigate to `Settings > SVGPlus` in the WordPress admin dashboard.
2. **Enable SVG Support:** Toggle the option to enable or disable SVG support across your site.
3. **Select Allowed User Roles:** (Administrators only) Choose which user roles are permitted to upload SVG files to your site.
4. **Add Custom CSS:** Input any custom CSS to style your SVGs globally.

## Changelog

= 1.1.0 =

- Fixed issue where the blue background of switches did not change to orange when SVG support was disabled.
- Restricted modification of allowed roles to Administrators only.
- Removed the "Custom CSS" main label and adjusted the codebox to span the full width of the row.
- Updated plugin version and aligned documentation to reflect current features.

= 1.0.14 =

- General UI Enhancements
- Improved responsiveness of the settings page to ensure better usability across different devices.

= 1.0.13 =

- Switched to using the `enshrined/svg-sanitize` library for SVG sanitization.
- Ensured "Allow SVG Animations" setting functions correctly with the new sanitizer.
- Default allowed roles for SVG uploads are Administrator, Editor, and Author.
- Confirmed custom CSS settings are applied correctly to SVG images.

= 1.0.12 =

- Comprehensive SVG Element Support: Expanded the list of allowed SVG elements and attributes in the sanitizer to include a complete set of SVG elements, including all filter elements and animation elements. This ensures compatibility with a wider range of SVG files and features.
- Enhanced Animation Support: Completed the inclusion of all animation elements and their attributes, allowing for full support of SVG animations when enabled in the settings.
- Improved Sanitization Logic: Updated the SVG sanitizer to support advanced SVG features like filters and animations while maintaining strict security measures. The sanitizer now properly handles a more extensive range of elements and attributes.
- Security Enhancements: Ensured that the expanded support does not compromise security by maintaining robust sanitization and validation of SVG content.

= 1.0.8 =

* Shortcode Enhancement: Added support for the lazy attribute in the shortcode to control lazy loading of SVGs. Users can now enable or disable lazy loading per SVG by setting lazy="true" or lazy="false" in the shortcode.
* Global Custom CSS Application: Modified the plugin to enqueue custom CSS globally from the settings page. This ensures that custom styles are applied consistently to all SVGs without needing to append CSS in each shortcode instance.
* Conditional Animation Support: Updated the SVG sanitization process to conditionally allow animation elements and attributes based on the 'Allow SVG Animations' setting in the plugin settings. When enabled, the sanitizer permits elements like <animate>, <animateTransform>, and their associated attributes.
* Improved Sanitization Logic: Enhanced the SVG sanitizer to dynamically adjust allowed elements and attributes based on settings, improving security and flexibility.
* Code Optimizations: Refactored code for better performance and maintainability, including optimizing the shortcode rendering process and reducing redundant code.
* Documentation Updates: Updated the readme file and usage instructions to reflect the new features and provide clearer guidance on how to use the plugin.

= 1.0.7 =

* Security Enhancements: Escaped output functions to prevent security vulnerabilities.
* Filesystem Operations: Replaced `file_get_contents()` with `WP_Filesystem::get_contents()` and Replaced `file_put_contents()` with `WP_Filesystem::put_contents()`.

= 1.0.6 =

* Refined plugin to remove the dedicated Elementor widget while enhancing SVG upload compatibility with Elementor's native widgets.
* Improved sanitization process for SVG uploads.
* Enhanced shortcode functionality with additional customization options.
* Updated settings page for better user experience.
* Fixed minor bugs and improved performance.

= 1.0.3 =

* Added lazy loading support for SVG images.
* Introduced custom CSS options in the settings page.
* Enhanced compatibility with the latest WordPress and Elementor versions.

= 1.0.2 =

* Initial release with core functionalities.

## Upgrade Notice

= 1.1.0 =

Please update to this version to fix the switch background color issue, improve settings notifications, and enhance security by restricting role modifications to administrators.

== License ==

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License version 2.