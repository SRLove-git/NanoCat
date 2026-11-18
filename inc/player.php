<?php
/**
 * Global bottom-left NetEase Cloud Music launcher.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Player script.
 */
function nanocat_music_assets() {
	if ( is_admin() || is_feed() ) {
		return;
	}

	$path = get_template_directory() . '/assets/js/player.js';
	if ( ! file_exists( $path ) ) {
		return;
	}

	wp_enqueue_script(
		'nanocat-player',
		get_template_directory_uri() . '/assets/js/player.js',
		array(),
		(string) filemtime( $path ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'nanocat_music_assets', 80 );

/**
 * Print the mini launcher and the NetEase playlist overlay.
 */
function nanocat_music_player() {
	if ( is_admin() || is_feed() ) {
		return;
	}

	$url = get_theme_mod( 'nanocat_netease_playlist', '' );
	if ( ! $url ) {
		return;
	}

	$playlist_id = '';
	if ( preg_match( '/[?&]id=(\d+)/', $url, $matches ) ) {
		$playlist_id = $matches[1];
	}

	if ( ! $playlist_id ) {
		return;
	}

	$embed_url = 'https://music.163.com/outchain/player?type=0&id=' . $playlist_id . '&auto=0&height=480';
	$title     = get_theme_mod( 'nanocat_netease_playlist_title', __( '网易云音乐歌单', 'nanocat' ) );
	?>
	<div id="nanocat-player" class="music-player" data-netease-url="<?php echo esc_url( $url ); ?>" aria-label="<?php esc_attr_e( '网易云音乐歌单', 'nanocat' ); ?>">
		<div class="music-player__body">
			<span class="music-player__disc" aria-hidden="true"></span>
			<div class="music-player__meta">
				<span class="music-player__title"><?php echo esc_html( $title ); ?></span>
				<span class="music-player__artist"><?php esc_html_e( '网易云音乐', 'nanocat' ); ?></span>
			</div>
			<div class="music-player__controls">
				<button class="music-player__btn music-player__btn--play" type="button" data-player-open aria-label="<?php esc_attr_e( '打开网易云歌单', 'nanocat' ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"></path></svg>
				</button>
				<button class="music-player__btn" type="button" data-player-open aria-label="<?php esc_attr_e( '打开网易云歌单', 'nanocat' ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v2H4zM4 11h10v2H4zM4 16h16v2H4z"></path></svg>
				</button>
			</div>
		</div>
	</div>

	<div id="nanocat-netease" class="music-netease" aria-hidden="true">
		<div class="music-netease__backdrop" data-netease-close></div>
		<div class="music-netease__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( '网易云音乐歌单', 'nanocat' ); ?>">
			<header class="music-netease__head">
				<span><?php echo esc_html( $title ); ?></span>
				<button class="music-netease__close" type="button" data-netease-close aria-label="<?php esc_attr_e( '关闭', 'nanocat' ); ?>">×</button>
			</header>
			<div class="music-netease__frame">
				<iframe data-netease-frame data-src="<?php echo esc_url( $embed_url ); ?>" title="<?php esc_attr_e( '网易云音乐歌单', 'nanocat' ); ?>" loading="lazy" allow="autoplay; encrypted-media"></iframe>
			</div>
			<footer class="music-netease__foot">
				<a href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '在网易云打开 ↗', 'nanocat' ); ?></a>
			</footer>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'nanocat_music_player', 5 );
