<?php
/**
 * Customizer settings.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize a checkbox.
 *
 * @param mixed $value Raw value.
 * @return bool
 */
function nanocat_sanitize_checkbox( $value ) {
	return (bool) $value;
}

/**
 * Allow limited inline formatting in the hero heading.
 *
 * @param string $value Raw value.
 * @return string
 */
function nanocat_sanitize_hero_title( $value ) {
	return wp_kses(
		$value,
		array(
			'br'     => array(),
			'span'   => array( 'class' => array() ),
			'em'     => array(),
			'strong' => array(),
			'b'      => array(),
		)
	);
}

/**
 * Sanitize a URL that may also be a same-page anchor.
 *
 * @param string $value Raw value.
 * @return string
 */
function nanocat_sanitize_link( $value ) {
	$value = trim( (string) $value );

	if ( '' === $value ) {
		return '';
	}

	if ( 0 === strpos( $value, '#' ) ) {
		return '#' . sanitize_title( ltrim( $value, '#' ) );
	}

	return esc_url_raw( $value );
}

/**
 * Register theme options.
 *
 * @param WP_Customize_Manager $wp_customize Customizer instance.
 */
function nanocat_customize_register( $wp_customize ) {
	$wp_customize->add_panel(
		'nanocat_options',
		array(
			'title'       => __( 'NanoCat 主题设置', 'nanocat' ),
			'description' => __( '调整首页文案、个人资料卡片与主题强调色。所有设置都可以留空以使用内置样式。', 'nanocat' ),
			'priority'    => 30,
		)
	);

	$wp_customize->add_section(
		'nanocat_hero_section',
		array(
			'title'       => __( '首页 Hero', 'nanocat' ),
			'description' => __( '仅在默认博客首页且位于第一页时显示。', 'nanocat' ),
			'panel'       => 'nanocat_options',
			'priority'    => 10,
		)
	);

	$wp_customize->add_setting(
		'nanocat_hero_enabled',
		array(
			'default'           => true,
			'sanitize_callback' => 'nanocat_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'nanocat_hero_enabled',
		array(
			'label'   => __( '显示首页 Hero', 'nanocat' ),
			'section' => 'nanocat_hero_section',
			'type'    => 'checkbox',
		)
	);

	$wp_customize->add_setting(
		'nanocat_hero_image',
		array(
			'default'           => get_template_directory_uri() . '/assets/images/hero-night.svg',
			'sanitize_callback' => 'esc_url_raw',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'nanocat_hero_image',
			array(
				'label'       => __( '首页大图', 'nanocat' ),
				'description' => __( '建议使用 1920×1080 以上的横向图片；左侧会叠加深色蒙版以承载标题。', 'nanocat' ),
				'section'     => 'nanocat_hero_section',
			)
		)
	);

	$text_controls = array(
		'nanocat_hero_eyebrow' => array(
			'label'   => __( '眉题 / 终端提示', 'nanocat' ),
			'default' => nanocat_get_default( 'hero_eyebrow' ),
			'type'    => 'text',
		),
		'nanocat_hero_title'   => array(
			'label'       => __( '大标题', 'nanocat' ),
			'description' => __( '允许使用 <br>、<span class="hero-mark">、<em> 与 <strong>。', 'nanocat' ),
			'default'     => nanocat_get_default( 'hero_title' ),
			'type'        => 'textarea',
			'sanitize'    => 'nanocat_sanitize_hero_title',
		),
		'nanocat_hero_text'    => array(
			'label'   => __( '介绍文字', 'nanocat' ),
			'default' => nanocat_get_default( 'hero_text' ),
			'type'    => 'textarea',
		),
		'nanocat_hero_primary_label' => array(
			'label'   => __( '主按钮文字', 'nanocat' ),
			'default' => nanocat_get_default( 'hero_primary_label' ),
			'type'    => 'text',
		),
		'nanocat_hero_primary_url' => array(
			'label'       => __( '主按钮链接', 'nanocat' ),
			'description' => __( '留空则滚动到文章列表；支持 #锚点。', 'nanocat' ),
			'default'     => '',
			'type'        => 'text',
			'sanitize'    => 'nanocat_sanitize_link',
		),
		'nanocat_hero_secondary_label' => array(
			'label'   => __( '次按钮文字', 'nanocat' ),
			'default' => nanocat_get_default( 'hero_secondary_label' ),
			'type'    => 'text',
		),
		'nanocat_hero_secondary_url' => array(
			'label'       => __( '次按钮链接', 'nanocat' ),
			'description' => __( '留空则使用本站 RSS 地址。', 'nanocat' ),
			'default'     => '',
			'type'        => 'text',
			'sanitize'    => 'nanocat_sanitize_link',
		),
	);

	foreach ( $text_controls as $setting_id => $control ) {
		$sanitize = isset( $control['sanitize'] ) ? $control['sanitize'] : ( 'textarea' === $control['type'] ? 'sanitize_textarea_field' : 'sanitize_text_field' );

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $control['default'],
				'sanitize_callback' => $sanitize,
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'       => $control['label'],
				'description' => isset( $control['description'] ) ? $control['description'] : '',
				'section'     => 'nanocat_hero_section',
				'type'        => $control['type'],
				'input_attrs' => 'textarea' === $control['type'] ? array( 'rows' => 2 ) : array(),
			)
		);
	}

	$wp_customize->add_setting(
		'nanocat_accent_color',
		array(
			'default'           => '#2f7df6',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'nanocat_accent_color',
			array(
				'label'       => __( '品牌强调色', 'nanocat' ),
				'section'     => 'nanocat_hero_section',
				'description' => __( '用于链接、按钮和细节装饰。', 'nanocat' ),
			)
		)
	);

	$wp_customize->add_section(
		'nanocat_profile_section',
		array(
			'title'    => __( '个人卡片与社交', 'nanocat' ),
			'panel'    => 'nanocat_options',
			'priority' => 20,
		)
	);

	$profile_controls = array(
		'nanocat_profile_role' => array(
			'label'   => __( '身份标签', 'nanocat' ),
			'default' => nanocat_get_default( 'profile_role' ),
			'type'    => 'text',
		),
		'nanocat_profile_bio'  => array(
			'label'   => __( '个人简介', 'nanocat' ),
			'default' => nanocat_get_default( 'profile_bio' ),
			'type'    => 'textarea',
		),
		'nanocat_social_github' => array(
			'label'   => __( 'GitHub 链接', 'nanocat' ),
			'default' => '',
			'type'    => 'url',
			'sanitize' => 'esc_url_raw',
		),
		'nanocat_social_bilibili' => array(
			'label'   => __( '哔哩哔哩链接', 'nanocat' ),
			'default' => '',
			'type'    => 'url',
			'sanitize' => 'esc_url_raw',
		),
		'nanocat_social_email' => array(
			'label'   => __( '联系邮箱', 'nanocat' ),
			'default' => '',
			'type'    => 'email',
			'sanitize' => 'sanitize_email',
		),
	);

	foreach ( $profile_controls as $setting_id => $control ) {
		$sanitize = isset( $control['sanitize'] ) ? $control['sanitize'] : ( 'textarea' === $control['type'] ? 'sanitize_textarea_field' : 'sanitize_text_field' );

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $control['default'],
				'sanitize_callback' => $sanitize,
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $control['label'],
				'section' => 'nanocat_profile_section',
				'type'    => $control['type'],
			)
		);
	}

	$wp_customize->add_section(
		'nanocat_footer_section',
		array(
			'title'    => __( '页脚', 'nanocat' ),
			'panel'    => 'nanocat_options',
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'nanocat_footer_text',
		array(
			'default'           => nanocat_get_default( 'footer_text' ),
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'nanocat_footer_text',
		array(
			'label'       => __( '页脚标语', 'nanocat' ),
			'description' => __( '显示在页脚品牌区。', 'nanocat' ),
			'section'     => 'nanocat_footer_section',
			'type'        => 'text',
		)
	);
}
add_action( 'customize_register', 'nanocat_customize_register' );
