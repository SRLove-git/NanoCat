<?php
/**
 * Lightweight service status page.
 *
 * Checks a handful of endpoints on the site, keeps 30 days of history in an
 * option and feeds the Status page template. No external uptime service.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Services shown on the status page.
 *
 * @return array
 */
function nanocat_status_services() {
	$icon = get_site_icon_url( 192 );

	return array(
		'home'     => array(
			'label' => __( '博客主站', 'nanocat' ),
			'url'   => home_url( '/' ),
		),
		'feed'     => array(
			'label' => __( 'RSS 订阅', 'nanocat' ),
			'url'   => home_url( '/feed/' ),
		),
		'rest'     => array(
			'label' => __( 'WordPress REST', 'nanocat' ),
			'url'   => rest_url(),
		),
		'ai'       => array(
			'label' => __( 'AI 接口', 'nanocat' ),
			'url'   => home_url( '/api/ai/' ),
		),
		'static'   => array(
			'label' => __( '静态资源', 'nanocat' ),
			'url'   => $icon ? $icon : get_template_directory_uri() . '/assets/images/mascot.svg',
		),
		'database' => array(
			'label' => __( '数据库', 'nanocat' ),
			'local' => true,
		),
	);
}

/**
 * Probe one service.
 *
 * @param array $service Service definition.
 * @return array{status:string,code:int,ms:int}
 */
function nanocat_status_probe( $service ) {
	$start = microtime( true );

	if ( ! empty( $service['local'] ) ) {
		global $wpdb;
		$ok   = '1' === (string) $wpdb->get_var( 'SELECT 1' ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$code = $ok ? 200 : 0;
	} else {
		$response = wp_remote_get(
			$service['url'],
			array(
				'timeout'     => 6,
				'redirection' => 3,
				'user-agent'  => 'Mozilla/5.0 (compatible; NanoCat-Status/' . NANOCAT_VERSION . '; ' . home_url( '/' ) . ')',
			)
		);

		$code = is_wp_error( $response ) ? 0 : (int) wp_remote_retrieve_response_code( $response );
	}

	$ms = (int) round( ( microtime( true ) - $start ) * 1000 );

	if ( $code >= 200 && $code < 400 ) {
		$status = $ms >= 3000 ? 'degraded' : 'operational';
	} elseif ( $code >= 400 && $code < 500 ) {
		$status = 'degraded';
	} else {
		$status = 'down';
	}

	return array(
		'status' => $status,
		'code'   => $code,
		'ms'     => $ms,
	);
}

/**
 * Run all probes and append the result to the 30-day history.
 *
 * @param bool $force Ignore the five-minute throttle.
 * @return array Latest results keyed by service id.
 */
function nanocat_status_run( $force = false ) {
	$last = (int) get_option( 'nanocat_status_last_run', 0 );

	if ( ! $force && ( time() - $last ) < 5 * MINUTE_IN_SECONDS ) {
		$latest = get_option( 'nanocat_status_latest', array() );
		if ( is_array( $latest ) && $latest ) {
			return $latest;
		}
	}

	$results = array();
	foreach ( nanocat_status_services() as $id => $service ) {
		$results[ $id ] = nanocat_status_probe( $service );
	}

	$history = get_option( 'nanocat_status_history', array() );
	if ( ! is_array( $history ) ) {
		$history = array();
	}

	$history[ time() ] = $results;

	$cutoff = time() - 30 * DAY_IN_SECONDS;
	foreach ( array_keys( $history ) as $timestamp ) {
		if ( (int) $timestamp < $cutoff ) {
			unset( $history[ $timestamp ] );
		}
	}

	update_option( 'nanocat_status_history', $history, false );
	update_option( 'nanocat_status_latest', $results, false );
	update_option( 'nanocat_status_last_run', time(), false );

	delete_transient( 'nanocat_status_report' );

	return $results;
}

/**
 * Pick the worse of two statuses.
 *
 * @param string $a Status.
 * @param string $b Status.
 * @return string
 */
function nanocat_status_worst( $a, $b ) {
	$rank = array(
		'none'        => 0,
		'operational' => 1,
		'degraded'    => 2,
		'down'        => 3,
	);

	return $rank[ $b ] > $rank[ $a ] ? $b : $a;
}

/**
 * Build the report consumed by page-status.php.
 *
 * @param bool $force Force a fresh probe run.
 * @return array
 */
function nanocat_status_report( $force = false ) {
	$cached = get_transient( 'nanocat_status_report' );
	if ( ! $force && is_array( $cached ) ) {
		return $cached;
	}

	$latest = nanocat_status_run( $force );
	$history = get_option( 'nanocat_status_history', array() );
	if ( ! is_array( $history ) ) {
		$history = array();
	}

	ksort( $history );

	$days = array();
	for ( $i = 29; $i >= 0; $i-- ) {
		$days[ wp_date( 'Y-m-d', time() - $i * DAY_IN_SECONDS ) ] = 'none';
	}

	$summary = array(
		'operational' => 0,
		'degraded'    => 0,
		'down'        => 0,
		'unknown'     => 0,
	);

	$services = array();

	foreach ( nanocat_status_services() as $id => $service ) {
		$current = isset( $latest[ $id ] ) ? $latest[ $id ] : array(
			'status' => 'unknown',
			'code'   => 0,
			'ms'     => 0,
		);

		$summary_key = isset( $summary[ $current['status'] ] ) ? $current['status'] : 'unknown';
		$summary[ $summary_key ]++;

		$bars = $days;
		$checks = 0;
		$good   = 0;
		$ms_sum = 0;
		$ms_n   = 0;
		$day_cutoff = time() - 30 * DAY_IN_SECONDS;

		foreach ( $history as $timestamp => $results ) {
			if ( ! isset( $results[ $id ] ) || (int) $timestamp < $day_cutoff ) {
				continue;
			}

			$entry = $results[ $id ];
			$checks++;
			$good  += 'operational' === $entry['status'] ? 1 : 0;
			$ms_sum += (int) $entry['ms'];
			$ms_n++;

			$day = wp_date( 'Y-m-d', (int) $timestamp );
			if ( isset( $bars[ $day ] ) ) {
				$bars[ $day ] = nanocat_status_worst( $bars[ $day ], $entry['status'] );
			}
		}

		$services[] = array(
			'id'         => $id,
			'label'      => $service['label'],
			'status'     => $current['status'],
			'code'       => (int) $current['code'],
			'ms'         => (int) $current['ms'],
			'uptime'     => $checks ? round( $good / $checks * 100, 1 ) : null,
			'avg_ms'     => $ms_n ? (int) round( $ms_sum / $ms_n ) : (int) $current['ms'],
			'bars'       => $bars,
		);
	}

	$report = array(
		'generated_at' => (int) get_option( 'nanocat_status_last_run', 0 ),
		'summary'      => $summary,
		'services'     => $services,
	);

	set_transient( 'nanocat_status_report', $report, MINUTE_IN_SECONDS );

	return $report;
}

/**
 * Hourly probe so the history fills up without page views.
 */
function nanocat_status_schedule() {
	if ( ! wp_next_scheduled( 'nanocat_status_probe' ) ) {
		wp_schedule_event( time() + 2 * MINUTE_IN_SECONDS, 'hourly', 'nanocat_status_probe' );
	}
}
add_action( 'init', 'nanocat_status_schedule' );
add_action( 'nanocat_status_probe', 'nanocat_status_run' );
