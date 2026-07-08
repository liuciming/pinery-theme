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
    <?php
}
add_action('admin_notices', 'pinery_companion_notice');

// Notice styles live in a proper stylesheet (cacheable), enqueued only where the notice can render.
function pinery_companion_notice_styles($hook) {
    if (!in_array($hook, array('index.php', 'themes.php'), true)) {
        return;
    }
    if (!current_user_can('install_plugins') || pinery_companion_active()) {
        return;
    }
    if (get_user_meta(get_current_user_id(), 'pinery_companion_dismissed', true)) {
        return;
    }
    wp_enqueue_style(
        'pinery-companion-notice',
        get_template_directory_uri() . '/assets/companion-notice.css',
        array(),
        wp_get_theme()->get('Version')
    );
}
add_action('admin_enqueue_scripts', 'pinery_companion_notice_styles');

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
