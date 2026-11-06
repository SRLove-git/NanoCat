<?php
/**
 * Friend links, link health checks and RSS article aggregation.
 *
 * WordPress bookmarks are the source of truth. We only add cached metadata
 * around them, so the feature remains compatible with the core Link Manager.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Keep the core Link Manager available even on newer WordPress installs.
 *
 * @param mixed $value Existing option value.
 * @return bool
 */
function nanocat_link_manager_enabled( $value ) { // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Required filter signature.
	return true;
}
add_filter( 'pre_option_link_manager_enabled', 'nanocat_link_manager_enabled' );

/**
 * Health transient key.
 *
 * @param string $url Link URL.
 * @return string
 */
function nanocat_link_health_key( $url ) {
	return 'nanocat_link_health_' . md5( (string) $url );
}

/**
 * Feed transient key.
 *
 * @param string $url Feed URL.
 * @return string
 */
function nanocat_link_feed_key( $url ) {
	return 'nanocat_link_feed_' . md5( (string) $url );
}

/**
 * Cached health data for a link. Never performs a network request.
 *
 * @param string $url Link URL.
 * @return array{status:string,code:int,checked:int}
 */
function nanocat_get_link_health( $url ) {
	$cached = get_transient( nanocat_link_health_key( $url ) );

	if ( is_array( $cached ) && isset( $cached['status'] ) ) {
		return wp_parse_args(
			$cached,
			array(
				'status'  => 'unknown',
				'code'    => 0,
				'checked' => 0,
			)
		);
	}

	return array(
		'status'  => 'unknown',
		'code'    => 0,
		'checked' => 0,
	);
}

/**
 * Cached RSS items for a feed URL. Never performs a network request.
 *
 * @param string $url Feed URL.
 * @return array
 */
function nanocat_get_link_feed( $url ) {
	$cached = get_transient( nanocat_link_feed_key( $url ) );

	return is_array( $cached ) ? $cached : array();
}

/**
 * Check whether a link responds successfully.
 *
 * @param string $url Link URL.
 * @return array{status:string,code:int,checked:int}
 */
function nanocat_check_link_health( $url ) {
	$url      = esc_url_raw( $url );
	$response = wp_remote_head(
		$url,
		array(
			'timeout'     => 5,
			'redirection' => 3,
			'user-agent'  => 'NanoCat/' . NANOCAT_VERSION . '; ' . home_url( '/' ),
		)
	);

	if ( is_wp_error( $response ) ) {
		$response = wp_remote_get(
			$url,
			array(
				'timeout'     => 6,
				'redirection' => 3,
				'user-agent'  => 'NanoCat/' . NANOCAT_VERSION . '; ' . home_url( '/' ),
			)
		);
	}

	$code = is_wp_error( $response ) ? 0 : (int) wp_remote_retrieve_response_code( $response );
	$live = $code >= 200 && $code < 400;

	return array(
		'status'  => $live ? 'live' : 'down',
		'code'    => $code,
		'checked' => time(),
	);
}

/**
 * Fetch and normalise the latest items from a friend's RSS/Atom feed.
 *
 * @param string $url   Feed URL.
 * @param int    $limit Maximum items.
 * @return array
 */
function nanocat_fetch_feed_items( $url, $limit = 3 ) {
	$url = esc_url_raw( $url );

	if ( ! $url ) {
		return array();
	}

	if ( ! function_exists( 'fetch_feed' ) ) {
		require_once ABSPATH . WPINC . '/feed.php';
	}

	$feed = fetch_feed( $url );
	if ( is_wp_error( $feed ) ) {
		return array();
	}

	$items = array();
	$max   = $feed->get_item_quantity( (int) $limit );

	for ( $i = 0; $i < $max; $i++ ) {
		$item = $feed->get_item( $i );
		if ( ! $item ) {
			continue;
		}

		$timestamp = (int) $item->get_date( 'U' );
		$items[]   = array(
			'title'     => wp_strip_all_tags( (string) $item->get_title() ),
			'permalink' => esc_url_raw( (string) $item->get_permalink() ),
			'date'      => $timestamp ? wp_date( get_option( 'date_format' ), $timestamp ) : '',
			'timestamp' => $timestamp,
		);
	}

	return $items;
}

/**
 * Refresh health and feed cache for one bookmark.
 *
 * @param int|object $bookmark Bookmark ID or object.
 * @return bool
 */
function nanocat_refresh_link( $bookmark ) {
	if ( is_numeric( $bookmark ) ) {
		$bookmark = get_bookmark( (int) $bookmark );
	}

	if ( ! is_object( $bookmark ) || empty( $bookmark->link_url ) ) {
		return false;
	}

	$health = nanocat_check_link_health( $bookmark->link_url );
	set_transient( nanocat_link_health_key( $bookmark->link_url ), $health, 6 * HOUR_IN_SECONDS );

	if ( ! empty( $bookmark->link_rss ) ) {
		$items = nanocat_fetch_feed_items( $bookmark->link_rss, 3 );
		set_transient( nanocat_link_feed_key( $bookmark->link_rss ), $items, HOUR_IN_SECONDS );
	}

	return true;
}

/**
 * Refresh every bookmark. Intended for cron and the admin refresh action.
 *
 * @return int Number of links processed.
 */
function nanocat_refresh_all_links() {
	$links   = get_bookmarks(
		array(
			'orderby' => 'rating',
			'order'   => 'DESC',
		)
	);
	$count   = 0;

	foreach ( $links as $link ) {
		if ( nanocat_refresh_link( $link ) ) {
			$count++;
		}
	}

	update_option( 'nanocat_links_refreshed', time(), false );

	return $count;
}

/**
 * Rebuild link caches after the admin refresh button is submitted.
 */
function nanocat_handle_refresh_links() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( '你没有权限刷新友链。', 'nanocat' ), '', array( 'response' => 403 ) );
	}

	check_admin_referer( 'nanocat_refresh_links' );

	$count = nanocat_refresh_all_links();
	$url   = add_query_arg(
		array(
			'nanocat_links_refreshed' => $count,
		),
		wp_get_referer() ? wp_get_referer() : home_url( '/' )
	);

	wp_safe_redirect( $url );
	exit;
}
add_action( 'admin_post_nanocat_refresh_links', 'nanocat_handle_refresh_links' );

/**
 * Prepare one bookmark for a template.
 *
 * @param object $bookmark Bookmark object.
 * @return array
 */
function nanocat_prepare_link( $bookmark ) {
	$categories = wp_get_object_terms( (int) $bookmark->link_id, 'link_category' );
	$categories = is_array( $categories ) ? $categories : array();
	$health     = nanocat_get_link_health( $bookmark->link_url );
	$feed       = $bookmark->link_rss ? nanocat_get_link_feed( $bookmark->link_rss ) : array();
	$name       = wp_strip_all_tags( (string) $bookmark->link_name );
	$initial    = nanocat_substr( $name, 0, 1 );

	return array(
		'id'          => (int) $bookmark->link_id,
		'name'        => $name,
		'url'         => esc_url( $bookmark->link_url ),
		'image'       => esc_url( $bookmark->link_image ),
		'description' => wp_trim_words( wp_strip_all_tags( (string) $bookmark->link_description ), 34, '…' ),
		'rss'         => esc_url( $bookmark->link_rss ),
		'initial'     => $initial ? $initial : '·',
		'categories'  => $categories,
		'health'      => $health,
		'feed'        => $feed,
	);
}

/**
 * All prepared friend links.
 *
 * @return array
 */
function nanocat_get_friend_links() {
	static $prepared = null;

	if ( null !== $prepared ) {
		return $prepared;
	}

	$bookmarks = get_bookmarks(
		array(
			'orderby' => 'rating',
			'order'   => 'DESC',
		)
	);

	$prepared = array();
	foreach ( $bookmarks as $bookmark ) {
		$prepared[] = nanocat_prepare_link( $bookmark );
	}

	return $prepared;
}

/**
 * Latest posts across all cached friend feeds.
 *
 * @param int $limit Maximum items.
 * @return array
 */
function nanocat_get_friend_posts( $limit = 6 ) {
	$items = array();

	foreach ( nanocat_get_friend_links() as $link ) {
		foreach ( $link['feed'] as $item ) {
			if ( empty( $item['timestamp'] ) ) {
				continue;
			}

			$item['friend']     = $link['name'];
			$item['friend_url'] = $link['url'];
			$items[]            = $item;
		}
	}

	usort(
		$items,
		function ( $a, $b ) {
			return (int) $b['timestamp'] - (int) $a['timestamp'];
		}
	);

	return array_slice( $items, 0, (int) $limit );
}

/**
 * Warm link caches twice a day.
 */
function nanocat_schedule_link_refresh() {
	if ( ! wp_next_scheduled( 'nanocat_refresh_links_event' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'twicedaily', 'nanocat_refresh_links_event' );
	}
}
add_action( 'init', 'nanocat_schedule_link_refresh', 20 );
add_action( 'nanocat_refresh_links_event', 'nanocat_refresh_all_links' );

/**
 * Show the result of a manual refresh.
 */
function nanocat_link_refresh_notice() {
	if ( ! isset( $_GET['nanocat_links_refreshed'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$count = absint( $_GET['nanocat_links_refreshed'] );
	printf(
		'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
		esc_html(
			sprintf(
				/* translators: %d: number of friend links. */
				_n( '已刷新 %d 个友链的缓存。', '已刷新 %d 个友链的缓存。', $count, 'nanocat' ),
				$count
			)
		)
	);
}
add_action( 'admin_notices', 'nanocat_link_refresh_notice' );
