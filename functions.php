<?php
/**
 * NanoCat functions and definitions.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'NANOCAT_VERSION' ) ) {
	define( 'NANOCAT_VERSION', '3.2.0' );
}

/**
 * Theme setup.
 */
function nanocat_setup() {
	load_theme_textdomain( 'nanocat', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'navigation-widgets',
		)
	);

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 240,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_editor_style( 'assets/css/editor.css' );

	set_post_thumbnail_size( 1200, 675, true );
	add_image_size( 'nanocat-card', 960, 640, true );
	add_image_size( 'nanocat-wide', 1440, 810, true );

	register_nav_menus(
		array(
			'primary' => __( '主导航', 'nanocat' ),
			'footer'  => __( '页脚导航', 'nanocat' ),
		)
	);
}
add_action( 'after_setup_theme', 'nanocat_setup' );

/**
 * Set the content width in pixels.
 */
function nanocat_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'nanocat_content_width', 760 );
}
add_action( 'after_setup_theme', 'nanocat_content_width', 0 );

/**
 * Enqueue styles and scripts.
 */
function nanocat_scripts() {
	$theme     = wp_get_theme();
	$main_css  = get_template_directory() . '/assets/css/main.css';
	$main_js   = get_template_directory() . '/assets/js/main.js';
	$css_ver   = file_exists( $main_css ) ? (string) filemtime( $main_css ) : NANOCAT_VERSION;
	$js_ver    = file_exists( $main_js ) ? (string) filemtime( $main_js ) : NANOCAT_VERSION;

	wp_enqueue_style( 'nanocat-style', get_stylesheet_uri(), array(), $theme->get( 'Version' ) );
	wp_enqueue_style( 'nanocat-main', get_template_directory_uri() . '/assets/css/main.css', array( 'nanocat-style' ), $css_ver );

	$accent = sanitize_hex_color( get_theme_mod( 'nanocat_accent_color', '#2f7df6' ) );
	if ( $accent ) {
		wp_add_inline_style( 'nanocat-main', ':root{--accent-base:' . $accent . ';}' );
	}

	wp_enqueue_script( 'nanocat-main', get_template_directory_uri() . '/assets/js/main.js', array(), $js_ver, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'nanocat_scripts' );

/**
 * Avoid a flash of the wrong color scheme and mark that JavaScript is available.
 */
function nanocat_color_mode_script() {
	?>
	<script>
	(function () {
		var root = document.documentElement;
		root.classList.remove('no-js');
		root.classList.add('js');
		var mode = 'light';
		try {
			var saved = window.localStorage.getItem('nanocat-theme');
			if (saved === 'light' || saved === 'dark') {
				mode = saved;
			} else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
				mode = 'dark';
			}
		} catch (error) {
			if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
				mode = 'dark';
			}
		}
		root.setAttribute('data-theme', mode);

		var meta = document.getElementById('nanocat-theme-color');
		if (meta) {
			meta.setAttribute('content', mode === 'dark' ? '#060e19' : '#f2f7fd');
		}
	}());
	</script>
	<?php
}
add_action( 'wp_head', 'nanocat_color_mode_script', 1 );

/**
 * Register widget areas.
 */
function nanocat_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( '博客侧边栏', 'nanocat' ),
			'id'            => 'sidebar-1',
			'description'   => __( '显示在文章列表、归档与搜索结果旁。', 'nanocat' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( '首页左栏', 'nanocat' ),
			'id'            => 'sidebar-left',
			'description'   => __( '显示在首页文章列表左侧。留空时使用主题内置的个人卡片、分类与专栏。', 'nanocat' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	register_sidebar(
		array(
			'name'          => __( '首页右栏', 'nanocat' ),
			'id'            => 'sidebar-right',
			'description'   => __( '显示在首页文章列表右侧。留空时使用主题内置的热门文章、Wiki、标签与一言。', 'nanocat' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);

	for ( $i = 1; $i <= 3; $i++ ) {
		register_sidebar(
			array(
				/* translators: %d: footer column number. */
				'name'          => sprintf( __( '页脚 %d', 'nanocat' ), $i ),
				'id'            => 'footer-' . $i,
				'description'   => __( '可选的三栏页脚小工具区域。', 'nanocat' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			)
		);
	}
}
add_action( 'widgets_init', 'nanocat_widgets_init' );

/**
 * Add helpful body classes.
 *
 * @param array $classes Body classes.
 * @return array
 */
function nanocat_body_classes( $classes ) {
	if ( is_active_sidebar( 'sidebar-1' ) && ( is_home() || is_archive() || is_search() ) ) {
		$classes[] = 'has-widget-sidebar';
	}

	if ( is_singular() ) {
		$classes[] = 'is-reader-view';
	}

	if ( is_home() ) {
		$classes[] = 'is-ai-home';
	}

	return $classes;
}
add_filter( 'body_class', 'nanocat_body_classes' );

/**
 * Remove the default ellipsis from excerpts; cards use their own trimming helper.
 *
 * @return string
 */
function nanocat_excerpt_more() {
	return '…';
}
add_filter( 'excerpt_more', 'nanocat_excerpt_more' );

/**
 * Add a pingback header when needed.
 */
function nanocat_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'nanocat_pingback_header' );

/**
 * Use a localized label for the comment cookie consent checkbox.
 *
 * @param string $field The default checkbox markup.
 * @return string
 */
function nanocat_comment_cookie_field( $field ) {
	$commenter = wp_get_current_commenter();
	$checked   = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';

	return '<p class="comment-form-cookies-consent">' .
		'<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $checked . ' />' .
		' <label for="wp-comment-cookies-consent">' . esc_html__( '在此浏览器中保存我的昵称、邮箱和网站，下次评论时自动填写。', 'nanocat' ) . '</label>' .
		'</p>';
}
add_filter( 'comment_form_field_cookies', 'nanocat_comment_cookie_field' );

require get_template_directory() . '/inc/icons.php';
require get_template_directory() . '/inc/ai.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/topics.php';
require get_template_directory() . '/inc/wiki.php';
require get_template_directory() . '/inc/links.php';
require get_template_directory() . '/inc/content-admin.php';
require get_template_directory() . '/inc/customizer.php';


