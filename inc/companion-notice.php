<?php
if (!defined('ABSPATH')) exit;

/**
 * Pinery — optional companion-plugin notice.
 *
 * Shows ONE dismissible admin notice pointing to the Pinery Flow plugin (the
 * theme's directly-related automation companion). It is:
 *   - admin-only (never rendered on the front end),
 *   - shown only on the Themes and Dashboard screens,
 *   - hidden once dismissed (stored per user), and
 *   - hidden when the companion plugin is already active.
 *
 * This is the theme's only upsell, kept intentionally unobtrusive.
 */

if (!function_exists('pinery_companion_active')) {
    function pinery_companion_active() {
        // The plugin defines these; if present, no need to upsell.
        return function_exists('pinery_flow_price_overlay')
            || function_exists('pinery_flow_ad_zone');
    }
}

function pinery_companion_notice() {
    if (!function_exists('get_current_screen')) {
        return;
    }
    $screen = get_current_screen();
    if (!$screen || !in_array($screen->id, array('themes', 'dashboard'), true)) {
        return;
    }
    if (!current_user_can('install_plugins')) {
        return;
    }
    if (pinery_companion_active()) {
        return;
    }
    if (get_user_meta(get_current_user_id(), 'pinery_companion_dismissed', true)) {
        return;
    }

    $learn_url   = 'https://www.pinery.pro/';
    $dismiss_url = wp_nonce_url(
        add_query_arg('pinery_dismiss_companion', '1'),
        'pinery_dismiss_companion'
    );
    $icon_url = get_template_directory_uri() . '/assets/pinery-flow-icon.png';
    ?>
    <div class="notice pinery-flow-notice">
        <a class="pinery-flow-notice__close" href="<?php echo esc_url($dismiss_url); ?>" aria-label="<?php esc_attr_e('Dismiss this notice', 'pinery'); ?>">&times;</a>
        <img class="pinery-flow-notice__icon" src="<?php echo esc_url($icon_url); ?>" width="56" height="56" alt="<?php esc_attr_e('Pinery Flow', 'pinery'); ?>" />
        <div class="pinery-flow-notice__body">
            <h3 class="pinery-flow-notice__title"><?php esc_html_e('Put your shop on autopilot with Pinery Flow', 'pinery'); ?></h3>
            <p class="pinery-flow-notice__text"><?php esc_html_e('The optional companion plugin adds AI-generated posts, Amazon best-seller import, automatic price overlays, and ad placements — built to pair with this theme.', 'pinery'); ?></p>
            <p class="pinery-flow-notice__actions">
                <a class="pinery-flow-notice__cta button button-primary" href="<?php echo esc_url($learn_url); ?>" target="_blank" rel="noopener"><?php esc_html_e('Learn more', 'pinery'); ?> &rarr;</a>
                <a class="pinery-flow-notice__dismiss" href="<?php echo esc_url($dismiss_url); ?>"><?php esc_html_e('No thanks', 'pinery'); ?></a>
            </p>
        </div>
    </div>
    <style>
        .pinery-flow-notice{position:relative;display:flex;gap:16px;align-items:flex-start;
            border:1px solid #ece3d9;border-left:4px solid #b07d62;border-radius:8px;
            padding:18px 46px 18px 18px;margin:16px 0;
            background:linear-gradient(135deg,#fff9f5 0%,#f5ece3 100%);
            box-shadow:0 1px 3px rgba(44,36,32,.08);}
        .pinery-flow-notice__icon{flex:0 0 auto;width:56px;height:56px;border-radius:13px;
            box-shadow:0 3px 8px rgba(44,36,32,.22);}
        .pinery-flow-notice__body{flex:1 1 auto;min-width:0;}
        .pinery-flow-notice__title{margin:2px 0 5px;padding:0;font-size:15px;font-weight:700;
            line-height:1.3;color:#3d3028;}
        .pinery-flow-notice__text{margin:0 0 12px;color:#7a6a60;font-size:13px;
            line-height:1.55;max-width:780px;}
        .pinery-flow-notice__actions{margin:0;display:flex;flex-wrap:wrap;align-items:center;gap:16px;}
        .pinery-flow-notice__cta.button-primary{background:#b07d62;border-color:#9c6b52;
            color:#fff;box-shadow:none;text-shadow:none;border-radius:6px;
            padding:3px 18px;height:auto;font-weight:600;}
        .pinery-flow-notice__cta.button-primary:hover,
        .pinery-flow-notice__cta.button-primary:focus{background:#9c6b52;border-color:#8a5d46;color:#fff;}
        .pinery-flow-notice__dismiss{color:#9a8a7e;text-decoration:none;font-size:13px;}
        .pinery-flow-notice__dismiss:hover{color:#7a6a60;}
        .pinery-flow-notice__close{position:absolute;top:8px;right:13px;color:#c0b4a6;
            text-decoration:none;font-size:20px;line-height:1;font-weight:400;}
        .pinery-flow-notice__close:hover{color:#7a6a60;}
        @media (max-width:600px){.pinery-flow-notice{flex-direction:column;}}
    </style>
    <?php
}
add_action('admin_notices', 'pinery_companion_notice');

function pinery_companion_dismiss() {
    if (!isset($_GET['pinery_dismiss_companion'])) {
        return;
    }
    if (!current_user_can('install_plugins')) {
        return;
    }
    check_admin_referer('pinery_dismiss_companion');
    update_user_meta(get_current_user_id(), 'pinery_companion_dismissed', 1);
    wp_safe_redirect(remove_query_arg(array('pinery_dismiss_companion', '_wpnonce')));
    exit;
}
add_action('admin_init', 'pinery_companion_dismiss');
