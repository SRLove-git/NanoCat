<?php
/**
 * Global bottom-left music player.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Playlist. Filter `nanocat_music_tracks` to replace it.
 *
 * @return array
 */
function nanocat_music_tracks() {
	$base = get_template_directory_uri() . '/assets/audio/';

	$tracks = array(
		array(
			'title'  => __( '夜航', 'nanocat' ),
			'artist' => __( 'NanoCat Ambient', 'nanocat' ),
			'src'    => $base . 'night-flight.mp3',
		),
		array(
			'title'  => __( '窗边的猫', 'nanocat' ),
			'artist' => __( 'NanoCat Ambient', 'nanocat' ),
			'src'    => $base . 'window-cat.mp3',
		),
		array(
			'title'  => __( '雨后的服务器', 'nanocat' ),
			'artist' => __( 'NanoCat Ambient', 'nanocat' ),
			'src'    => $base . 'after-rain.mp3',
		),
	);

	return apply_filters( 'nanocat_music_tracks', $tracks );
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
 * Print the player.
 */
function nanocat_music_player() {
	if ( is_admin() || is_feed() ) {
		return;
	}

	$tracks = nanocat_music_tracks();
	if ( empty( $tracks ) ) {
		return;
	}

	$first  = reset( $tracks );
	$qq_url = get_theme_mod( 'nanocat_qq_playlist', '' );
	$labels = array(
		'prev'  => __( '上一首', 'nanocat' ),
		'play'  => __( '播放', 'nanocat' ),
		'pause' => __( '暂停', 'nanocat' ),
		'next'  => __( '下一首', 'nanocat' ),
		'list'  => __( '播放列表', 'nanocat' ),
	);
	?>
	<div id="nanocat-player" class="music-player" aria-label="<?php esc_attr_e( '站内歌单', 'nanocat' ); ?>">
		<div class="music-player__body">
			<span class="music-player__disc" aria-hidden="true"></span>
			<div class="music-player__meta">
				<span class="music-player__title"><?php echo esc_html( $first['title'] ); ?></span>
				<span class="music-player__artist"><?php echo esc_html( $first['artist'] ); ?></span>
			</div>
			<div class="music-player__controls">
				<button class="music-player__btn" type="button" data-player-prev aria-label="<?php echo esc_attr( $labels['prev'] ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 5h2v14H6zM19 5v14l-10-7z"></path></svg>
				</button>
				<button class="music-player__btn music-player__btn--play" type="button" data-player-play aria-label="<?php echo esc_attr( $labels['play'] ); ?>">
					<svg class="music-player__icon-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z"></path></svg>
					<svg class="music-player__icon-pause" viewBox="0 0 24 24" aria-hidden="true"><path d="M7 5h4v14H7zM13 5h4v14h-4z"></path></svg>
				</button>
				<button class="music-player__btn" type="button" data-player-next aria-label="<?php echo esc_attr( $labels['next'] ); ?>">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M16 5h2v14h-2zM5 5v14l10-7z"></path></svg>
				</button>
				<?php if ( $qq_url ) : ?>
					<button class="music-player__btn" type="button" data-player-qq data-qq-url="<?php echo esc_url( $qq_url ); ?>" aria-label="<?php esc_attr_e( 'QQ 音乐歌单', 'nanocat' ); ?>">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 18V6l10-2v12a3 3 0 1 1-2-2.83V8.5L11 10v8a3 3 0 1 1-2-2.83z"></path></svg>
					</button>
				<?php endif; ?>
				<button class="music-player__btn" type="button" data-player-list aria-label="<?php echo esc_attr( $labels['list'] ); ?>" aria-expanded="false">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v2H4zM4 11h10v2H4zM4 16h16v2H4z"></path></svg>
				</button>
			</div>
		</div>

		<div class="music-player__progress" data-player-progress role="slider" aria-label="<?php esc_attr_e( '播放进度', 'nanocat' ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" tabindex="0">
			<span></span>
		</div>

		<div class="music-player__playlist" hidden>
			<?php foreach ( $tracks as $nanocat_index => $nanocat_track ) : ?>
				<button
					class="music-player__track<?php echo 0 === $nanocat_index ? ' is-active' : ''; ?>"
					type="button"
					data-src="<?php echo esc_url( $nanocat_track['src'] ); ?>"
					data-title="<?php echo esc_attr( $nanocat_track['title'] ); ?>"
					data-artist="<?php echo esc_attr( $nanocat_track['artist'] ); ?>"
				>
					<span class="music-player__track-index"><?php echo esc_html( str_pad( (string) ( $nanocat_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<span class="music-player__track-title"><?php echo esc_html( $nanocat_track['title'] ); ?></span>
					<span class="music-player__track-artist"><?php echo esc_html( $nanocat_track['artist'] ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<audio preload="metadata"></audio>
	</div>

	<?php if ( $qq_url ) : ?>
		<div id="nanocat-qq" class="music-qq" aria-hidden="true">
			<div class="music-qq__backdrop" data-qq-close></div>
			<div class="music-qq__panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'QQ 音乐歌单', 'nanocat' ); ?>">
				<header class="music-qq__head">
					<span><?php esc_html_e( 'QQ 音乐歌单', 'nanocat' ); ?></span>
					<button class="music-qq__close" type="button" data-qq-close aria-label="<?php esc_attr_e( '关闭', 'nanocat' ); ?>">×</button>
				</header>
				<div class="music-qq__frame">
					<iframe data-qq-frame data-src="<?php echo esc_url( $qq_url ); ?>" title="<?php esc_attr_e( 'QQ 音乐歌单', 'nanocat' ); ?>" loading="lazy" allow="autoplay; encrypted-media"></iframe>
				</div>
				<footer class="music-qq__foot">
					<a href="<?php echo esc_url( $qq_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( '在 QQ 音乐打开 ↗', 'nanocat' ); ?></a>
				</footer>
			</div>
		</div>
	<?php endif; ?>
	<?php
}
add_action( 'wp_footer', 'nanocat_music_player', 5 );
