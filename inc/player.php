<?php
/**
 * Global bottom-left direct NetEase player.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tracks that can be streamed directly from NetEase's free outer URLs.
 * Filter `nanocat_music_tracks` to replace the list.
 *
 * @return array
 */
function nanocat_music_tracks() {
	$songs = array(
		array( 'title' => '海屿你', 'artist' => '马也_Crabbit', 'id' => 1973665667 ),
		array( 'title' => '罗生门（Follow）', 'artist' => '梨冻紧/Wiz_H张子豪', 'id' => 1456890009 ),
		array( 'title' => '99', 'artist' => 'TizzyT', 'id' => 2741144144 ),
		array( 'title' => '嗜好', 'artist' => '颜人中', 'id' => 1391891631 ),
		array( 'title' => '明知故犯', 'artist' => 'Max李玄', 'id' => 3342319503 ),
		array( 'title' => '如果呢', 'artist' => '郑润泽', 'id' => 1842728629 ),
		array( 'title' => '于是', 'artist' => '郑润泽', 'id' => 1303464858 ),
		array( 'title' => '遐想', 'artist' => '郑润泽', 'id' => 2018733994 ),
		array( 'title' => '一半一半', 'artist' => 'Top Barry/INDEcompany', 'id' => 3333988321 ),
		array( 'title' => '宝船', 'artist' => 'DOUDOU/燕云十六声', 'id' => 3451003254 ),
		array( 'title' => '忘不掉的你', 'artist' => 'h3R3', 'id' => 1851652156 ),
		array( 'title' => '互删 (我走以后)', 'artist' => '江辰', 'id' => 3425638996 ),
		array( 'title' => '红', 'artist' => '罗言', 'id' => 1918576268 ),
		array( 'title' => '周旋', 'artist' => '王以太/艾热 AIR', 'id' => 3404238777 ),
		array( 'title' => '还是会想你', 'artist' => '林达浪/h3R3', 'id' => 1827600686 ),
		array( 'title' => '睡前别想太多', 'artist' => '李雨霏_晚饭', 'id' => 3440961675 ),
		array( 'title' => '两 难', 'artist' => '加木', 'id' => 2163210456 ),
		array( 'title' => '晚安', 'artist' => '颜人中', 'id' => 1359356908 ),
		array( 'title' => '时过境迁', 'artist' => '苏艺馨Chency', 'id' => 2078101758 ),
		array( 'title' => '苦茶子', 'artist' => 'Starling8/DRODUMANA/FIVESTAR', 'id' => 1922888354 ),
		array( 'title' => '一张相片', 'artist' => '连麻Swimming', 'id' => 3451531276 ),
		array( 'title' => '把回忆拼好给你', 'artist' => '王贰浪', 'id' => 1403318151 ),
		array( 'title' => '回到你身边', 'artist' => 'h3R3', 'id' => 3397415408 ),
		array( 'title' => '我只能离开', 'artist' => '颜人中', 'id' => 1398283847 ),
		array( 'title' => '再等冬天(Memories)', 'artist' => 'h3R3', 'id' => 1927693793 ),
		array( 'title' => 'Lost ', 'artist' => '满舒克/Jony J', 'id' => 479979029 ),
		array( 'title' => '予你', 'artist' => '队长', 'id' => 1895330088 ),
		array( 'title' => '锈 (我们两个都拉过勾)', 'artist' => '江辰', 'id' => 2648204040 ),
		array( 'title' => '小胡同', 'artist' => '郑润泽', 'id' => 1926623288 ),
		array( 'title' => '有些', 'artist' => '颜人中', 'id' => 1406649619 ),
		array( 'title' => '你', 'artist' => '郑润泽', 'id' => 1406083061 ),
		array( 'title' => '熄灭', 'artist' => 'TC', 'id' => 2040015902 ),
		array( 'title' => '背对背 (feat. scarletchoir)', 'artist' => 'User116/scarletchoir', 'id' => 3339257959 ),
		array( 'title' => '小孩', 'artist' => '罗森涛', 'id' => 2166584564 ),
		array( 'title' => '我爱你但是我要回家', 'artist' => 'ET/Happer/无面小生/口古口古', 'id' => 2647831829 ),
		array( 'title' => '心似烟火', 'artist' => '陈壹千', 'id' => 1399112638 ),
		array( 'title' => '他只是经过', 'artist' => 'h3R3/高旭', 'id' => 1443838552 ),
		array( 'title' => '绽放', 'artist' => '郑润泽', 'id' => 2025186889 ),
		array( 'title' => '幻痛药 Hurt And Hurt Ft.LSGCsikoriot四口', 'artist' => 'Tlatre特雷西/LSGCsikoriot', 'id' => 1903248130 ),
		array( 'title' => '讴歌', 'artist' => 'Vansdaddy', 'id' => 2108845007 ),
		array( 'title' => '哪里都是你', 'artist' => '队长', 'id' => 488249475 ),
		array( 'title' => '诀别书', 'artist' => '邓垚', 'id' => 2038191895 ),
		array( 'title' => '自由的你', 'artist' => 'G.E.M.邓紫棋', 'id' => 3436855494 ),
		array( 'title' => '和你', 'artist' => '余佳运', 'id' => 420401511 ),
		array( 'title' => '故里逢春', 'artist' => '阿YueYue', 'id' => 1457113272 ),
		array( 'title' => '沉沦与遐想', 'artist' => 'C.HØPE', 'id' => 1492584510 ),
		array( 'title' => 'Undefined', 'artist' => 'PerVade/第五人格', 'id' => 3451951517 ),
		array( 'title' => 'My love', 'artist' => '颜人中', 'id' => 1493976789 ),
		array( 'title' => 'Баллада', 'artist' => 'Xcho', 'id' => 2145353565 ),
		array( 'title' => 'Forget it （feat. Mac Ova Seas）', 'artist' => 'SETI/mac ova seas', 'id' => 2122615322 ),
		array( 'title' => 'Leave Me Alone（寂寞作伴）', 'artist' => 'TC', 'id' => 1806634682 ),
		array( 'title' => '拼接乌托邦', 'artist' => 'Ciyo/见过夏天P/乌托邦P', 'id' => 3379996850 ),
		array( 'title' => '还是分开', 'artist' => '张叶蕾', 'id' => 465921195 ),
		array( 'title' => '拉过勾的', 'artist' => '陆虎', 'id' => 26387325 ),
		array( 'title' => '我本将心向明月', 'artist' => '王朝1982/朱旭BooBoo', 'id' => 1964850580 ),
		array( 'title' => 'unhappy', 'artist' => 's0rrow', 'id' => 3332771263 ),
		array( 'title' => '偷心', 'artist' => 'SASIOVERLXRD/连麻Swimming', 'id' => 554322674 ),
		array( 'title' => '普通女孩', 'artist' => '沙一汀EL', 'id' => 2617646167 ),
		array( 'title' => '明天过后', 'artist' => '张杰', 'id' => 191248 ),
		array( 'title' => '无情画', 'artist' => '王唯旖', 'id' => 1311347661 ),
		array( 'title' => '第三人称', 'artist' => '冯沁苑(买辣椒也用券)', 'id' => 502043537 ),
		array( 'title' => '最后的借口', 'artist' => 'h3R3/罗可Lorkin', 'id' => 3394851493 ),
		array( 'title' => '该怎么办', 'artist' => 'TC', 'id' => 2032201541 ),
		array( 'title' => 'Butterflies', 'artist' => '艾志恒Asen/Maikon Flocka Flame', 'id' => 1437030961 ),
		array( 'title' => '精卫', 'artist' => '30年前，50年后', 'id' => 1951069525 ),
		array( 'title' => '隐藏相册', 'artist' => 'Top Barry/INDEcompany', 'id' => 3413127704 ),
		array( 'title' => '学功夫练武术', 'artist' => '概念黁', 'id' => 3372052257 ),
	);

	$tracks = array();
	foreach ( $songs as $song ) {
		$song['src'] = 'https://music.163.com/song/media/outer/url?id=' . $song['id'] . '.mp3';
		$tracks[]    = $song;
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

	$first  = reset( $tracks );
	$count  = count( $tracks );
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
				<span><?php esc_html_e( '网易云 · 2026爆火全网的歌曲', 'nanocat' ); ?></span>
				<span>
					<?php
					printf(
						/* translators: %s: number of directly playable tracks. */
						esc_html__( '%s 首可直接播放', 'nanocat' ),
						esc_html( number_format_i18n( $count ) )
					);
					?>
				</span>
			</div>
			<?php foreach ( $tracks as $index => $track ) : ?>
				<button
					class="music-player__track<?php echo 0 === $index ? ' is-active' : ''; ?>"
					type="button"
					data-src="<?php echo esc_url( $track['src'] ); ?>"
					data-title="<?php echo esc_attr( $track['title'] ); ?>"
					data-artist="<?php echo esc_attr( $track['artist'] ); ?>"
				>
					<span class="music-player__track-index"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<span class="music-player__track-title"><?php echo esc_html( $track['title'] ); ?></span>
					<span class="music-player__track-artist"><?php echo esc_html( $track['artist'] ); ?></span>
				</button>
			<?php endforeach; ?>
		</div>

		<audio preload="metadata"></audio>
	</div>
	<?php
}
add_action( 'wp_footer', 'nanocat_music_player', 5 );
