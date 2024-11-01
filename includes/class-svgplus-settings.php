<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class SVGPlus_Settings {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * Adds the settings page under the "Settings" menu.
     */
    public function add_settings_menu() {
        add_options_page(
            __('SVGPlus Settings', 'svgplus'), // Page title
            __('SVGPlus', 'svgplus'),          // Menu title
            'manage_options',                  // Capability
            'svgplus-settings',                // Menu slug
            array($this, 'render_settings_page') // Callback
        );
    }

    /**
     * Registers the plugin settings, sections, and fields.
     */
    public function register_settings() {
        // Register main settings group
        register_setting('svgplus_settings_group', 'svgplus_settings', array($this, 'sanitize_settings'));

        // Main Settings Section
        add_settings_section(
            'svgplus_main_section',
            __('Main Settings', 'svgplus'),
            array($this, 'main_section_callback'),
            'svgplus-settings'
        );

        // SVG Support Toggle
        add_settings_field(
            'enable_svg_support',
            __('Enable SVG Support', 'svgplus'),
            array($this, 'enable_svg_support_callback'),
            'svgplus-settings',
            'svgplus_main_section'
        );

        // Allowed Roles
        add_settings_field(
            'allowed_roles',
            __('Allowed Roles for SVG Uploads', 'svgplus'),
            array($this, 'allowed_roles_callback'),
            'svgplus-settings',
            'svgplus_main_section'
        );

        // Custom CSS Section
        register_setting('svgplus_custom_css_group', 'svgplus_custom_css', array($this, 'sanitize_custom_css'));

        add_settings_section(
            'svgplus_custom_css_section',
            __('Custom CSS', 'svgplus'),
            null,
            'svgplus-settings-custom-css'
        );

        add_settings_field(
            'custom_css',
            '', // Removed the main label
            array($this, 'custom_css_callback'),
            'svgplus-settings-custom-css',
            'svgplus_custom_css_section'
        );
    }

    /**
     * Sanitizes the main settings input.
     *
     * @param array $input The input array from the settings form.
     * @return array The sanitized settings array.
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        $sanitized['enable_svg_support'] = isset($input['enable_svg_support']) ? 1 : 0;

        // Sanitize allowed roles
        if (isset($input['allowed_roles']) && is_array($input['allowed_roles'])) {
            $available_roles = array_keys(get_editable_roles());
            $sanitized['allowed_roles'] = array_map('sanitize_text_field', array_intersect($input['allowed_roles'], $available_roles));
        } else {
            $sanitized['allowed_roles'] = array();
        }

        return $sanitized;
    }

    /**
     * Sanitizes the custom CSS input.
     *
     * @param string $input The custom CSS string from the form.
     * @return string The sanitized custom CSS string.
     */
    public function sanitize_custom_css($input) {
        return wp_strip_all_tags($input);
    }

    /**
     * Callback for the main settings section.
     */
    public function main_section_callback() {
        echo esc_html__('Configure the main settings for SVGPlus.', 'svgplus');
    }

    /**
     * Callback for the "Enable SVG Support" field.
     */
    public function enable_svg_support_callback() {
        $options = get_option('svgplus_settings');
        $is_svg_enabled = isset($options['enable_svg_support']) ? $options['enable_svg_support'] : 0;

        ?>
        <label class="svgplus-switch">
            <input type="checkbox" name="svgplus_settings[enable_svg_support]" value="1" <?php checked(1, $is_svg_enabled); ?> />
            <span class="svgplus-slider"></span>
        </label>
        <?php
    }

    /**
     * Callback for the "Allowed Roles for SVG Uploads" field.
     */
    public function allowed_roles_callback() {
        $options = get_option('svgplus_settings');
        $selected_roles = isset($options['allowed_roles']) ? $options['allowed_roles'] : array();
        $is_svg_enabled = isset($options['enable_svg_support']) ? $options['enable_svg_support'] : 0;
        $roles = get_editable_roles();

        // Check if the current user is an administrator
        if (!current_user_can('administrator')) {
            echo '<p>' . esc_html__('You do not have permission to change allowed roles.', 'svgplus') . '</p>';
            return;
        }

        foreach ($roles as $role_key => $role) {
            $checked = in_array($role_key, $selected_roles) ? 'checked' : '';
            ?>
            <label>
                <span class="svgplus-switch-wrapper">
                    <label class="svgplus-switch">
                        <input type="checkbox" name="svgplus_settings[allowed_roles][]" value="<?php echo esc_attr($role_key); ?>" <?php echo $checked; ?>>
                        <span class="svgplus-slider"></span>
                    </label>
                </span>
                <span class="svgplus-label"><?php echo esc_html($role['name']); ?></span>
            </label><br>
            <?php
        }
        echo '<p class="description">' . esc_html__('Select the user roles that are allowed to upload SVG files.', 'svgplus') . '</p>';
    }

    /**
     * Callback for the "Custom CSS" field.
     */
    public function custom_css_callback() {
        $custom_css = get_option('svgplus_custom_css', '');
        ?>
        <textarea
            id="svgplus_custom_css"
            name="svgplus_custom_css"
            class="large-text code"
            rows="10"
            aria-label="<?php esc_attr_e('Custom CSS', 'svgplus'); ?>"
        ><?php echo esc_textarea($custom_css); ?></textarea>
        <p class="description"><?php esc_html_e('Add custom CSS to style SVGs.', 'svgplus'); ?></p>
        <?php
    }

    /**
     * Enqueue admin styles and scripts for settings page.
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_svgplus-settings') {
            return;
        }

        // Enqueue the admin-specific CSS for switches and layout
        wp_enqueue_style('svgplus-admin-style', plugin_dir_url(__FILE__) . '../assets/css/svgplus-admin.css', array(), '1.1.0');

        // Enqueue the admin-specific JavaScript
        wp_enqueue_script('svgplus-admin-script', plugin_dir_url(__FILE__) . '../assets/js/svgplus-admin.js', array('jquery'), '1.1.0', true);

        // Enqueue CodeMirror for the custom CSS editor
        $settings = wp_enqueue_code_editor(array('type' => 'text/css'));
        if ($settings === false) {
            return;
        }

        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');

        // Localize the script to initialize CodeMirror for the textarea
        wp_add_inline_script(
            'wp-theme-plugin-editor',
            sprintf(
                'jQuery(function($) { wp.codeEditor.initialize($("#svgplus_custom_css"), %s); });',
                wp_json_encode($settings)
            )
        );
    }

    /**
     * Renders the settings page content.
     */
    public function render_settings_page() {
        // Dynamically get the plugin directory URL to load the icon
        $icon_url = plugin_dir_url(__FILE__) . '../icon.svg';

        ?>
        <div class="wrap">
            <h1>
                <img src="<?php echo esc_url($icon_url); ?>" alt="SVGPlus Icon" class="svgplus-settings-icon" />
                <?php esc_html_e('SVGPlus Settings', 'svgplus'); ?>
            </h1>

            <!-- Main Settings Form -->
            <form method="post" action="options.php" class="svgplus-main-settings">
                <?php
                settings_fields('svgplus_settings_group');
                do_settings_sections('svgplus-settings');
                // Display settings errors for main settings
                settings_errors('svgplus_settings_group');
                submit_button(__('Save Settings', 'svgplus'));
                ?>
            </form>

            <!-- Custom CSS Form -->
            <form method="post" action="options.php" class="svgplus-custom-css-settings">
                <?php
                settings_fields('svgplus_custom_css_group');
                do_settings_sections('svgplus-settings-custom-css');
                // Display settings errors for custom CSS
                settings_errors('svgplus_custom_css_group');
                submit_button(__('Save Custom CSS', 'svgplus'));
                ?>
            </form>
        </div>
        <?php
    }
}
