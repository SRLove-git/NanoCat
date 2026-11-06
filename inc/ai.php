<?php
/**
 * NanoCat AI: native front-end for the site knowledge assistant.
 *
 * The site-level Feiyu MU plugin provides the /api/ai endpoints. NanoCat owns
 * the interface so the assistant feels like part of the theme instead of a
 * floating third-party widget.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the native AI interface is enabled.
 *
 * @return bool
 */
function nanocat_ai_is_active() {
	return (bool) apply_filters( 'nanocat_ai_active', true );
}

/**
 * URL of the AI workspace page, falling back to /ai/.
 *
 * @return string
 */
function nanocat_ai_page_url() {
	$page = get_page_by_path( 'ai' );

	if ( $page instanceof WP_Post && 'publish' === $page->post_status ) {
		return (string) get_permalink( $page );
	}

	return home_url( '/ai/' );
}

/**
 * Configuration handed to assets/js/ai.js.
 *
 * @return array
 */
function nanocat_ai_config() {
	$post_id = is_singular( 'post' ) ? (int) get_queried_object_id() : 0;

	$config = array(
		'api'       => home_url( '/api/ai' ),
		'page'      => nanocat_ai_page_url(),
		'home'      => home_url( '/' ),
		'postId'    => $post_id,
		'postTitle' => $post_id ? wp_strip_all_tags( get_the_title( $post_id ) ) : '',
		'postUrl'   => $post_id ? get_permalink( $post_id ) : '',
		'isPage'    => is_page_template( 'page-ai.php' ),
		'strings'   => array(
			'searching' => __( '正在检索站内笔记…', 'nanocat' ),
			'grounded'  => __( '基于站内文章生成', 'nanocat' ),
			'error'     => __( 'AI 暂时没有接上。稍后再问一次，或者换个更具体的词。', 'nanocat' ),
			'stopped'   => __( '已停止生成。', 'nanocat' ),
			'sources'   => __( '参考来源', 'nanocat' ),
			'clear'     => __( '清空对话', 'nanocat' ),
			'copy'      => __( '复制', 'nanocat' ),
			'copied'    => __( '已复制', 'nanocat' ),
			'empty'     => __( '问一个具体问题，比如“窗口服务是怎么实现的？”', 'nanocat' ),
		),
	);

	return (array) apply_filters( 'nanocat_ai_config', $config );
}

/**
 * Enqueue AI styles and the native client.
 */
function nanocat_ai_scripts() {
	if ( ! nanocat_ai_is_active() ) {
		return;
	}

	$css_path = get_template_directory() . '/assets/css/ai.css';
	$js_path  = get_template_directory() . '/assets/js/ai.js';
	$css_ver  = file_exists( $css_path ) ? (string) filemtime( $css_path ) : NANOCAT_VERSION;
	$js_ver   = file_exists( $js_path ) ? (string) filemtime( $js_path ) : NANOCAT_VERSION;

	wp_enqueue_style(
		'nanocat-ai',
		get_template_directory_uri() . '/assets/css/ai.css',
		array( 'nanocat-main' ),
		$css_ver
	);

	wp_enqueue_script(
		'nanocat-ai',
		get_template_directory_uri() . '/assets/js/ai.js',
		array( 'nanocat-main' ),
		$js_ver,
		true
	);

	wp_add_inline_script( 'nanocat-ai', 'window.NanoCatAI=' . wp_json_encode( nanocat_ai_config() ) . ';', 'before' );
}
add_action( 'wp_enqueue_scripts', 'nanocat_ai_scripts', 20 );

/**
 * Retire the Feiyu front-end widget. Its API stays available at /api/ai and is
 * consumed by the theme-native console.
 */
function nanocat_disable_legacy_ai() {
	if ( ! nanocat_ai_is_active() ) {
		return;
	}

	wp_dequeue_script( 'feiyu-ai' );
	wp_deregister_script( 'feiyu-ai' );
}
add_action( 'wp_enqueue_scripts', 'nanocat_disable_legacy_ai', 100 );

/**
 * Body class for AI-aware styling.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function nanocat_ai_body_class( $classes ) {
	if ( nanocat_ai_is_active() ) {
		$classes[] = 'has-ai';
	}

	return $classes;
}
add_filter( 'body_class', 'nanocat_ai_body_class' );
