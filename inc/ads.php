<?php
if (!defined("ABSPATH")) exit;

// Pinery Theme — Ad Zone Compatibility
//
// All ad-zone logic lives in Pinery Flow plugin.
// These thin wrappers keep the theme working without the plugin
// (ads simply won't render) and delegate to the plugin when it's active.

if (!function_exists('pinery_ad_zone')):
function pinery_ad_zone($zone_id) {
    if (function_exists('pinery_flow_ad_zone')) {
        pinery_flow_ad_zone($zone_id);
    }
}
endif;

if (!function_exists('pinery_maybe_between_ad')):
function pinery_maybe_between_ad($counter) {
    if (function_exists('pinery_flow_maybe_between_ad')) {
        pinery_flow_maybe_between_ad($counter);
    }
}
endif;

// Customizer section is registered by the plugin.
// Keep the hook registration in the theme for when the plugin isn't active.
add_action('customize_register', function($wp_customize) {
    if (function_exists('pinery_flow_register_ad_customizer_settings')) {
        pinery_flow_register_ad_customizer_settings($wp_customize);
    }
}, 20);
