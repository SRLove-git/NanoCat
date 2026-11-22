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
	define( 'NANOCAT_VERSION', '3.5.1' );
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
 * Refinement layer: loaded after main.css and ai.css so it can quiet the
 * palette and spacing without touching the base styles.
 */
function nanocat_polish_assets() {
	$path = get_template_directory() . '/assets/css/polish.css';

	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'nanocat-polish',
		get_template_directory_uri() . '/assets/css/polish.css',
		array( 'nanocat-main' ),
		(string) filemtime( $path )
	);
}
add_action( 'wp_enqueue_scripts', 'nanocat_polish_assets', 30 );

/**
 * Flec layout skin: the light, card-based restructuring layer.
 */
function nanocat_flec_assets() {
	$path = get_template_directory() . '/assets/css/flec.css';

	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'nanocat-flec',
		get_template_directory_uri() . '/assets/css/flec.css',
		array( 'nanocat-main' ),
		(string) filemtime( $path )
	);
}
add_action( 'wp_enqueue_scripts', 'nanocat_flec_assets', 40 );

/**
 * Vivia glass skin: transparent panels over a fixed wallpaper.
 */
function nanocat_vivia_assets() {
	$path = get_template_directory() . '/assets/css/vivia.css';

	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'nanocat-vivia',
		get_template_directory_uri() . '/assets/css/vivia.css',
		array( 'nanocat-main' ),
		(string) filemtime( $path )
	);
}
add_action( 'wp_enqueue_scripts', 'nanocat_vivia_assets', 50 );

/**
 * Swup: client-side navigation for the front end.
 */
function nanocat_swup_assets() {
	if ( is_admin() || is_feed() ) {
		return;
	}

	$dir  = get_template_directory() . '/assets/js/vendor/swup/';
	$base = get_template_directory_uri() . '/assets/js/vendor/swup/';

	$files = array(
		'nanocat-swup'         => array( 'swup.umd.js', array() ),
		'nanocat-swup-head'    => array( 'head-plugin.umd.js', array( 'nanocat-swup' ) ),
		'nanocat-swup-body'    => array( 'body-class-plugin.umd.js', array( 'nanocat-swup' ) ),
		'nanocat-swup-preload' => array( 'preload-plugin.umd.js', array( 'nanocat-swup' ) ),
	);

	foreach ( $files as $handle => $spec ) {
		$path = $dir . $spec[0];
		if ( ! file_exists( $path ) ) {
			return;
		}
		wp_enqueue_script( $handle, $base . $spec[0], $spec[1], (string) filemtime( $path ), true );
	}

	$init = get_template_directory() . '/assets/js/swup-init.js';
	if ( file_exists( $init ) ) {
		wp_enqueue_script(
			'nanocat-swup-init',
			get_template_directory_uri() . '/assets/js/swup-init.js',
			array( 'nanocat-swup', 'nanocat-swup-head', 'nanocat-swup-body', 'nanocat-swup-preload' ),
			(string) filemtime( $init ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'nanocat_swup_assets', 70 );

/**
 * Sakana widget and the random wallpaper used by the reference theme.
 */
function nanocat_vivia_sakana() {
	if ( is_admin() || is_feed() ) {
		return;
	}

	$path = get_template_directory() . '/assets/js/vendor/sakana-widget.min.js';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'nanocat-sakana',
		get_template_directory_uri() . '/assets/js/vendor/sakana-widget.min.js',
		array(),
		(string) filemtime( $path ),
		true
	);

}
add_action( 'wp_enqueue_scripts', 'nanocat_vivia_sakana', 60 );

/**
 * Mount point for the Sakana widget.
 */
function nanocat_vivia_sakana_mount() {
	if ( is_admin() || is_feed() ) {
		return;
	}
	echo '<div id="sakana-widget"></div>';
	?>
	<script>
	(function () {
		var tries = 0;

		function mount() {
			var el = document.getElementById('sakana-widget');
			if (!el) {
				return;
			}
			if (typeof SakanaWidget === 'undefined') {
				if (tries++ < 100) {
					window.setTimeout(mount, 40);
				}
				return;
			}
			if (el.dataset.mounted) {
				return;
			}
			el.dataset.mounted = '1';
			new SakanaWidget({
				size: 200,
				autoFit: true,
				controls: true,
				rod: true,
				draggable: true,
				character: 'chisato'
			}).mount(el);
		}

		mount();
	}());
	</script>
	<?php
}
add_action( 'wp_footer', 'nanocat_vivia_sakana_mount', 30 );

/**
 * Critical loading screen: hides the page until the random wallpaper is ready,
 * so the local fallback never flashes first.
 */
function nanocat_vivia_loader_head() {
	if ( is_admin() || is_feed() ) {
		return;
	}
	$avatar = get_site_icon_url( 96 );
	$name   = get_bloginfo( 'name' );

	$wallpapers = glob( get_template_directory() . '/assets/images/wallpapers/*.jpg' );
	if ( $wallpapers ) {
		$GLOBALS['nanocat_loader_wallpaper'] = get_template_directory_uri() . '/assets/images/wallpapers/' . basename( $wallpapers[ array_rand( $wallpapers ) ] );
	} else {
		$GLOBALS['nanocat_loader_wallpaper'] = get_template_directory_uri() . '/assets/images/vivia-bg.jpg';
	}
	?>
	<script>document.documentElement.classList.add('vivia-loading');</script>
	<link rel="preload" as="image" href="<?php echo esc_url( $GLOBALS['nanocat_loader_wallpaper'] ); ?>" fetchpriority="high">
	<style id="vivia-loader-style">
		html.vivia-loading { overflow: hidden; }
		html.vivia-loading body { background-image: none !important; background-color: #0a0a0e !important; }
		#vivia-loader {
			position: fixed;
			inset: 0;
			z-index: 9999;
			display: flex;
			align-items: center;
			justify-content: center;
			background: #0a0a0e;
			transition: opacity 0.5s ease, visibility 0.5s ease;
		}
		#vivia-loader.is-hidden { opacity: 0; visibility: hidden; }
		.vivia-loader__card {
			width: min(320px, 78vw);
			padding: 26px 26px 22px;
			border: 1px solid rgba(255, 255, 255, 0.08);
			border-radius: 16px;
			background: rgba(28, 28, 34, 0.6);
			text-align: center;
			color: #edf2f8;
			font-family: "Roboto", "PingFang SC", "Microsoft YaHei", sans-serif;
		}
		.vivia-loader__avatar {
			display: block;
			width: 76px;
			height: 76px;
			margin: 0 auto 14px;
			border: 1px solid rgba(255, 255, 255, 0.18);
			border-radius: 50%;
			box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.04), 0 12px 30px rgba(0, 0, 0, 0.35);
			object-fit: cover;
		}
		.vivia-loader__name {
			font-size: 20px;
			font-weight: 700;
			letter-spacing: 0.01em;
		}
		.vivia-loader__hint {
			margin-top: 4px;
			color: #a0a5ab;
			font-size: 12px;
			letter-spacing: 0.08em;
		}
		.vivia-loader__track {
			position: relative;
			overflow: hidden;
			height: 2px;
			margin-top: 18px;
			border-radius: 2px;
			background: rgba(255, 255, 255, 0.1);
		}
		.vivia-loader__track span {
			position: absolute;
			top: 0;
			left: -40%;
			width: 40%;
			height: 100%;
			border-radius: 2px;
			background: #63b3ff;
			animation: vivia-loading-slide 1.1s ease-in-out infinite;
		}
		@keyframes vivia-loading-slide {
			0% { left: -40%; }
			100% { left: 100%; }
		}
		@media (prefers-reduced-motion: reduce) {
			.vivia-loader__track span { animation: none; left: 30%; width: 40%; }
		}
	</style>
	<?php
}
add_action( 'wp_head', 'nanocat_vivia_loader_head', 1 );

/**
 * Loading markup plus the wallpaper preload that dismisses it.
 */
function nanocat_vivia_loader_body() {
	if ( is_admin() || is_feed() ) {
		return;
	}
	$avatar = get_site_icon_url( 96 );
	$name   = get_bloginfo( 'name' );

	$wallpaper = isset( $GLOBALS['nanocat_loader_wallpaper'] ) ? $GLOBALS['nanocat_loader_wallpaper'] : get_template_directory_uri() . '/assets/images/vivia-bg.jpg';
	?>
	<div id="vivia-loader" aria-hidden="true">
		<div class="vivia-loader__card">
			<?php if ( $avatar ) : ?>
				<img class="vivia-loader__avatar" src="<?php echo esc_url( $avatar ); ?>" alt="" width="64" height="64">
			<?php endif; ?>
			<div class="vivia-loader__name"><?php echo esc_html( $name ); ?></div>
			<div class="vivia-loader__hint"><?php esc_html_e( '少女祈祷中~', 'nanocat' ); ?></div>
			<div class="vivia-loader__track"><span></span></div>
		</div>
	</div>
	<script>
	(function () {
		var url = <?php echo wp_json_encode( $wallpaper ); ?>;
		var img = new Image();
		img.fetchPriority = 'high';
		var applied = false;
		var revealed = false;

		function applyWallpaper() {
			if (applied) { return; }
			applied = true;
			document.documentElement.style.setProperty('--vivia-wallpaper', 'url("' + url + '")');
		}

		function reveal() {
			if (revealed) { return; }
			revealed = true;
			document.documentElement.classList.remove('vivia-loading');
			var loader = document.getElementById('vivia-loader');
			if (loader) {
				loader.classList.add('is-hidden');
				setTimeout(function () {
					if (loader.parentNode) { loader.parentNode.removeChild(loader); }
				}, 600);
			}
		}

		img.onload = function () { applyWallpaper(); reveal(); };
		img.onerror = function () { reveal(); };
		img.src = url;
		setTimeout(reveal, 5000);
	}());
	</script>
	<?php
}
add_action( 'wp_body_open', 'nanocat_vivia_loader_body', 1 );


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
		var mode = 'dark';
		try {
			window.localStorage.removeItem('nanocat-theme');
		} catch (error) {
			mode = 'dark';
		}
		root.setAttribute('data-theme', mode);

		var meta = document.getElementById('nanocat-theme-color');
		if (meta) {
			meta.setAttribute('content', mode === 'dark' ? '#0a0a0e' : '#f6f3ee');
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
require get_template_directory() . '/inc/intro.php';
require get_template_directory() . '/inc/ai.php';
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/topics.php';
require get_template_directory() . '/inc/wiki.php';
require get_template_directory() . '/inc/links.php';
require get_template_directory() . '/inc/status.php';
require get_template_directory() . '/inc/player.php';
require get_template_directory() . '/inc/content-admin.php';
require get_template_directory() . '/inc/customizer.php';
