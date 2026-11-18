<?php
/**
 * Global bottom-left music player, backed by a NetEase Cloud Music playlist.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Playlist. Filter `nanocat_music_tracks` to replace it.
 *
 * Free tracks stream through NetEase's outer URL; VIP tracks link out.
 *
 * @return array
 */
function nanocat_music_tracks() {
	$songs = array(
		array(
			'title'  => '10%',
			'artist' => 'SynBlazer',
			'id'     => 425100824,
			'vip'    => true,
		),
		array(
			'title'  => '盛夏',
			'artist' => 'Alisa',
			'id'     => 2043637342,
			'vip'    => false,
		),
		array(
			'title'  => 'The Way I Still Love You',
			'artist' => 'Reynard Silva',
			'id'     => 28718313,
			'vip'    => true,
		),
		array(
			'title'  => 'May I See U Again (治愈版)',
			'artist' => 'ALisa',
			'id'     => 1843672390,
			'vip'    => false,
		),
		// 愿世界温柔以待 — 网易云未收录，跳过。
		array(
			'title'  => 'Glow Me',
			'artist' => 'Magdy Haddad',
			'id'     => 1447797075,
			'vip'    => true,
		),
		array(
			'title'  => '青空',
			'artist' => 'Candy_Wind',
			'id'     => 32717172,
			'vip'    => false,
		),
		array(
			'title'  => 'Una Mattina',
			'artist' => 'Ludovico Einaudi',
			'id'     => 20952171,
			'vip'    => true,
		),
		array(
			'title'  => 'Subwoofer Lullaby',
			'artist' => 'C418',
			'id'     => 4010187,
			'vip'    => true,
		),
		array(
			'title'  => 'Minecraft',
			'artist' => 'C418',
			'id'     => 4010201,
			'vip'    => true,
		),
	);

	$tracks = array();

	foreach ( $songs as $song ) {
		$song['external'] = 'https://music.163.com/#/song?id=' . $song['id'];
		$song['src']      = $song['vip'] ? '' : 'https://music.163.com/song/media/outer/url?id=' . $song['id'] . '.mp3';
		$tracks[]         = $song;
	}

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

	$start = 0;
	foreach ( $tracks as $index => $track ) {
		if ( ! empty( $track['src'] ) ) {
			$start = $index;
			break;
		}
	}

	$playable = 0;
	$vip      = 0;
	foreach ( $tracks as $track ) {
		if ( empty( $track['src'] ) ) {
			$vip++;
		} else {
			$playable++;
		}
	}

	$first  = $tracks[ $start ];
	$labels = array(
		'prev'  => __( '上一首', 'nanocat' ),
		'play'  => __( '播放', 'nanocat' ),
		'pause' => __( '暂停', 'nanocat' ),
		'next'  => __( '下一首', 'nanocat' ),
		'list'  => __( '播放列表', 'nanocat' ),
	);
	?>
	<div id="nanocat-player" class="music-player" data-start="<?php echo esc_attr( (string) $start ); ?>" aria-label="<?php esc_attr_e( '站内歌单', 'nanocat' ); ?>">
		<div class="music-player__body">
			<span class="music-player__disc" aria-hidden="true"></span>
			<div class="music-player__meta">
				<span class="music-player__title-row">
					<span class="music-player__title"><?php echo esc_html( $first['title'] ); ?></span>
					<span class="music-player__vip" hidden>VIP</span>
				</span>
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
				<button class="music-player__btn" type="button" data-player-list aria-label="<?php echo esc_attr( $labels['list'] ); ?>" aria-expanded="false">
					<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v2H4zM4 11h10v2H4zM4 16h16v2H4z"></path></svg>
				</button>
			</div>
		</div>

		<div class="music-player__progress" data-player-progress role="slider" aria-label="<?php esc_attr_e( '播放进度', 'nanocat' ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" tabindex="0">
			<span></span>
		</div>

		<div class="music-player__playlist" hidden>
			<div class="music-player__playlist-head">
				<span><?php esc_html_e( '网易云 · 慢品人间烟火色', 'nanocat' ); ?></span>
				<span>
					<?php
					printf(
						/* translators: 1: playable count, 2: VIP count. */
						esc_html__( '%1$s 首可播 / %2$s 首 VIP', 'nanocat' ),
						esc_html( number_format_i18n( $playable ) ),
						esc_html( number_format_i18n( $vip ) )
					);
					?>
				</span>
			</div>
			<?php foreach ( $tracks as $nanocat_index => $nanocat_track ) : ?>
				<button
					class="music-player__track<?php echo (int) $nanocat_index === (int) $start ? ' is-active' : ''; ?><?php echo $nanocat_track['vip'] ? ' is-vip' : ''; ?>"
					type="button"
					data-src="<?php echo esc_url( $nanocat_track['src'] ); ?>"
					data-external="<?php echo esc_url( $nanocat_track['external'] ); ?>"
					data-title="<?php echo esc_attr( $nanocat_track['title'] ); ?>"
					data-artist="<?php echo esc_attr( $nanocat_track['artist'] ); ?>"
					data-vip="<?php echo $nanocat_track['vip'] ? '1' : '0'; ?>"
				>
					<span class="music-player__track-index"><?php echo esc_html( str_pad( (string) ( $nanocat_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<span class="music-player__track-title"><?php echo esc_html( $nanocat_track['title'] ); ?></span>
					<span class="music-player__track-artist"><?php echo esc_html( $nanocat_track['artist'] ); ?></span>
					<?php if ( $nanocat_track['vip'] ) : ?>
						<span class="music-player__track-vip">VIP</span>
					<?php endif; ?>
				</button>
			<?php endforeach; ?>
		</div>

		<audio preload="metadata"></audio>
	</div>
	<?php
}
add_action( 'wp_footer', 'nanocat_music_player', 5 );
