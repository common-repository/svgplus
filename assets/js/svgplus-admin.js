// JavaScript for handling switch background colors based on SVG support
jQuery(document).ready(function($) {
    function updateSwitches() {
        var svgSupportEnabled = $('input[name="svgplus_settings[enable_svg_support]"]').is(':checked');
        if (!svgSupportEnabled) {
            // Add 'orange-background' class to the slider spans of allowed roles switches
            $('input[name^="svgplus_settings[allowed_roles]"]').each(function() {
                $(this).closest('.svgplus-switch').find('.svgplus-slider').addClass('orange-background');
            });
        } else {
            // Remove 'orange-background' class
            $('input[name^="svgplus_settings[allowed_roles]"]').each(function() {
                $(this).closest('.svgplus-switch').find('.svgplus-slider').removeClass('orange-background');
            });
        }
    }

    // Initial update
    updateSwitches();

    // Update when 'Enable SVG Support' is toggled
    $('input[name="svgplus_settings[enable_svg_support]"]').on('change', function() {
        updateSwitches();
    });
});
