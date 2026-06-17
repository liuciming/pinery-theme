<?php
if (!defined("ABSPATH")) exit;

// Pinery Theme — Customizer Settings

add_action('customize_register', 'pinery_customize_register');
function pinery_customize_register($wp_customize) {

    // ── Colors ──
    $wp_customize->add_panel('pinery_colors', [
        'title'    => __('Colors', 'pinery'),
        'priority' => 30,
    ]);

    $colors = [
        'accent_color'   => [__('Accent Color', 'pinery'), '#b07d62'],
        'bg_color'       => [__('Background Color', 'pinery'), '#faf7f4'],
        'text_color'     => [__('Text Color', 'pinery'), '#3d3028'],
        'heading_color'  => [__('Heading Color', 'pinery'), '#2c2420'],
        'card_bg_color'  => [__('Card Background', 'pinery'), '#fff9f5'],
    ];

    foreach ($colors as $id => $cfg) {
        $wp_customize->add_setting("pinery_{$id}", [
            'default'           => $cfg[1],
            'sanitize_callback' => 'sanitize_hex_color',
            'transport'         => 'postMessage',
        ]);
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, "pinery_{$id}", [
            'label'   => $cfg[0],
            'section' => 'colors',
            'settings'=> "pinery_{$id}",
        ]));
    }

    // Move core colors section into our panel
    $colors_section = $wp_customize->get_section('colors');
    if ($colors_section) {
        $colors_section->panel = 'pinery_colors';
    }

    // ── Typography ──
    $wp_customize->add_section('pinery_typography', [
        'title'    => __('Typography', 'pinery'),
        'priority' => 31,
    ]);

    // Heading and body fonts are bundled locally (Cormorant Garamond + Jost)
    // and defined in style.css, so only the base size is adjustable here.
    $wp_customize->add_setting('pinery_font_size', [
        'default'           => 16,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('pinery_font_size', [
        'label'       => __('Base Font Size (px)', 'pinery'),
        'section'     => 'pinery_typography',
        'type'        => 'range',
        'input_attrs' => ['min' => 14, 'max' => 20, 'step' => 1],
    ]);

    // ── Layout ──
    $wp_customize->add_section('pinery_layout', [
        'title'    => __('Layout', 'pinery'),
        'priority' => 32,
    ]);

    $wp_customize->add_setting('pinery_layout_style', [
        'default'           => 'masonry',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('pinery_layout_style', [
        'label'   => __('Layout Style', 'pinery'),
        'section' => 'pinery_layout',
        'type'    => 'radio',
        'choices' => [
            'masonry' => __('Masonry (Pinterest-style)', 'pinery'),
            'grid'    => __('Grid (Equal height rows)', 'pinery'),
            'mixed'   => __('Mixed (First post large)', 'pinery'),
        ],
    ]);

    $wp_customize->add_setting('pinery_columns_desktop', [
        'default'           => 3,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('pinery_columns_desktop', [
        'label'       => __('Desktop Columns', 'pinery'),
        'section'     => 'pinery_layout',
        'type'        => 'range',
        'input_attrs' => ['min' => 2, 'max' => 5, 'step' => 1],
    ]);

    $wp_customize->add_setting('pinery_columns_tablet', [
        'default'           => 2,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('pinery_columns_tablet', [
        'label'       => __('Tablet Columns', 'pinery'),
        'section'     => 'pinery_layout',
        'type'        => 'range',
        'input_attrs' => ['min' => 1, 'max' => 3, 'step' => 1],
    ]);

    $wp_customize->add_setting('pinery_columns_mobile', [
        'default'           => 2,
        'sanitize_callback' => 'absint',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('pinery_columns_mobile', [
        'label'       => __('Mobile Columns', 'pinery'),
        'section'     => 'pinery_layout',
        'type'        => 'range',
        'input_attrs' => ['min' => 1, 'max' => 2, 'step' => 1],
    ]);

    $wp_customize->add_setting('pinery_show_excerpt', [
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('pinery_show_excerpt', [
        'label'   => __('Show excerpt on cards', 'pinery'),
        'section' => 'pinery_layout',
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('pinery_show_meta', [
        'default'           => false,
        'sanitize_callback' => 'wp_validate_boolean',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('pinery_show_meta', [
        'label'   => __('Show date & read time on cards', 'pinery'),
        'section' => 'pinery_layout',
        'type'    => 'checkbox',
    ]);

    // ── Homepage ──
    $wp_customize->add_section('pinery_homepage', [
        'title'    => __('Homepage', 'pinery'),
        'priority' => 33,
    ]);

    $wp_customize->add_setting('pinery_show_hero', [
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ]);
    $wp_customize->add_control('pinery_show_hero', [
        'label'   => __('Show hero section', 'pinery'),
        'section' => 'pinery_homepage',
        'type'    => 'checkbox',
    ]);

    $wp_customize->add_setting('pinery_hero_title', [
        'default'           => 'Discover <em>amazing</em> content.',
        'sanitize_callback' => 'wp_kses_post',
    ]);
    $wp_customize->add_control('pinery_hero_title', [
        'label'   => __('Hero Title (HTML allowed)', 'pinery'),
        'section' => 'pinery_homepage',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('pinery_hero_desc', [
        'default'           => 'Curated reviews and recommendations. We find the best products so you don\'t have to.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ]);
    $wp_customize->add_control('pinery_hero_desc', [
        'label'   => __('Hero Description', 'pinery'),
        'section' => 'pinery_homepage',
        'type'    => 'textarea',
    ]);

    $wp_customize->add_setting('pinery_hero_btn_text', [
        'default'           => "Browse Latest Posts",
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('pinery_hero_btn_text', [
        'label'   => __('Hero Button Text', 'pinery'),
        'section' => 'pinery_homepage',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('pinery_hero_btn_url', [
        'default'           => home_url('/category/new-arrivals'),
        'sanitize_callback' => 'esc_url_raw',
    ]);
    $wp_customize->add_control('pinery_hero_btn_url', [
        'label'   => __('Hero Button URL', 'pinery'),
        'section' => 'pinery_homepage',
        'type'    => 'url',
    ]);

    // ── Affiliate ──
    $wp_customize->add_section('pinery_affiliate', [
        'title'    => __('Affiliate Settings', 'pinery'),
        'priority' => 34,
    ]);

    $wp_customize->add_setting('pinery_affiliate_btn_text', [
        'default'           => 'Buy on Amazon',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'postMessage',
    ]);
    $wp_customize->add_control('pinery_affiliate_btn_text', [
        'label'   => __('Buy Button Text', 'pinery'),
        'section' => 'pinery_affiliate',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('pinery_affiliate_btn_style', [
        'default'           => 'solid',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('pinery_affiliate_btn_style', [
        'label'   => __('Buy Button Style', 'pinery'),
        'section' => 'pinery_affiliate',
        'type'    => 'radio',
        'choices' => [
            'solid'  => __('Solid', 'pinery'),
            'outline'=> __('Outline', 'pinery'),
        ],
    ]);

    $wp_customize->add_setting('pinery_affiliate_default_tag', [
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    $wp_customize->add_control('pinery_affiliate_default_tag', [
        'label'       => __('Amazon Affiliate Tag', 'pinery'),
        'description' => __('e.g. yourtag-20. Will be appended to Amazon links.', 'pinery'),
        'section'     => 'pinery_affiliate',
        'type'        => 'text',
    ]);
}
