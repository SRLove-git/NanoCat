<?php
/**
 * Template Name: Status
 *
 * Service status board, modelled on the reference theme's /status page.
 *
 * @package NanoCat
 */

get_header();

$nanocat_force  = isset( $_GET['refresh'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only cache bust.
$nanocat_report = nanocat_status_report( $nanocat_force );
$nanocat_labels = array(
	'operational' => __( '正常运行', 'nanocat' ),
	'degraded'    => __( '性能下降', 'nanocat' ),
	'down'        => __( '服务中断', 'nanocat' ),
	'unknown'     => __( '待检测', 'nanocat' ),
	'none'        => __( '暂无数据', 'nanocat' ),
);
?>
<main id="primary" class="site-main page-view status-view">
	<div class="vivia-page-grid">
		<?php get_sidebar( 'right' ); ?>
		<div class="vivia-page-main">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<header class="v-panel-heading status-heading">
					<div>
						<p class="section-kicker"><?php esc_html_e( '// STATUS', 'nanocat' ); ?></p>
						<h1><?php esc_html_e( 'Status', 'nanocat' ); ?></h1>
					</div>
					<a class="status-refresh" href="<?php echo esc_url( add_query_arg( 'refresh', time(), get_permalink() ) ); ?>">
						<?php esc_html_e( '刷新', 'nanocat' ); ?>
					</a>
				</header>

				<div class="status-summary">
					<span><i class="status-dot is-operational"></i><?php echo esc_html( $nanocat_report['summary']['operational'] ); ?> <?php esc_html_e( '正常运行', 'nanocat' ); ?></span>
					<span><i class="status-dot is-degraded"></i><?php echo esc_html( $nanocat_report['summary']['degraded'] ); ?> <?php esc_html_e( '性能下降', 'nanocat' ); ?></span>
					<span><i class="status-dot is-down"></i><?php echo esc_html( $nanocat_report['summary']['down'] ); ?> <?php esc_html_e( '服务中断', 'nanocat' ); ?></span>
					<?php if ( $nanocat_report['summary']['unknown'] ) : ?>
						<span><i class="status-dot is-unknown"></i><?php echo esc_html( $nanocat_report['summary']['unknown'] ); ?> <?php esc_html_e( '待检测', 'nanocat' ); ?></span>
					<?php endif; ?>
				</div>

				<?php if ( $nanocat_report['checks'] < 30 ) : ?>
					<p class="status-note">
						<?php
						if ( $nanocat_report['first_check'] ) {
							printf(
								/* translators: %s: datetime. */
								esc_html__( '检测从 %s 开始记录，灰色格子表示还没有历史数据；之后每检测一次就会点亮一格。', 'nanocat' ),
								esc_html( wp_date( 'Y-m-d H:i', $nanocat_report['first_check'] ) )
							);
						} else {
							esc_html_e( '检测刚刚开始，灰色格子表示还没有历史数据；之后每检测一次就会点亮一格。', 'nanocat' );
						}
						?>
					</p>
				<?php endif; ?>

				<div class="status-cards">
					<?php foreach ( $nanocat_report['services'] as $nanocat_service ) : ?>
						<article class="status-card">
							<div class="status-card__head">
								<h2 class="status-card__name"><?php echo esc_html( $nanocat_service['label'] ); ?></h2>
								<span class="status-card__label is-<?php echo esc_attr( $nanocat_service['status'] ); ?>">
									<?php echo esc_html( isset( $nanocat_labels[ $nanocat_service['status'] ] ) ? $nanocat_labels[ $nanocat_service['status'] ] : $nanocat_labels['unknown'] ); ?>
								</span>
							</div>

							<p class="status-card__meta">
								<?php echo $nanocat_service['code'] ? esc_html( $nanocat_service['code'] ) : '—'; ?>
								<span aria-hidden="true">·</span>
								<?php echo esc_html( number_format_i18n( $nanocat_service['avg_ms'] ) ); ?> ms
								<span aria-hidden="true">·</span>
								<?php
								if ( null === $nanocat_service['uptime'] ) {
									esc_html_e( '30 天可用率 —', 'nanocat' );
								} else {
									printf(
										/* translators: 1: uptime percentage, 2: number of checks. */
										esc_html__( '30 天可用率 %1$s%% · %2$s 次检测', 'nanocat' ),
										esc_html( number_format_i18n( $nanocat_service['uptime'], 1 ) ),
										esc_html( number_format_i18n( $nanocat_service['checks'] ) )
									);
								}
								?>
							</p>

							<div class="status-card__bars" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: service name. */ __( '%s 最近 30 次检测', 'nanocat' ), $nanocat_service['label'] ) ); ?>">
								<?php foreach ( $nanocat_service['bars'] as $nanocat_bar ) : ?>
									<?php
									$nanocat_state = $nanocat_bar['status'];
									$nanocat_title = $nanocat_bar['time']
										? wp_date( 'Y-m-d H:i', $nanocat_bar['time'] ) . ' · ' . ( isset( $nanocat_labels[ $nanocat_state ] ) ? $nanocat_labels[ $nanocat_state ] : $nanocat_labels['none'] )
										: $nanocat_labels['none'];
									?>
									<span
										class="status-card__bar is-<?php echo esc_attr( $nanocat_state ); ?>"
										title="<?php echo esc_attr( $nanocat_title ); ?>"
									></span>
								<?php endforeach; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<footer class="status-footer">
					<div class="status-legend">
						<span><i class="status-dot is-operational"></i><?php esc_html_e( '正常', 'nanocat' ); ?></span>
						<span><i class="status-dot is-degraded"></i><?php esc_html_e( '变慢 / 4xx', 'nanocat' ); ?></span>
						<span><i class="status-dot is-down"></i><?php esc_html_e( '中断', 'nanocat' ); ?></span>
						<span><i class="status-dot is-none"></i><?php esc_html_e( '暂无数据', 'nanocat' ); ?></span>
					</div>
					<p class="status-updated">
						<?php
						if ( $nanocat_report['generated_at'] ) {
							printf(
								/* translators: %s: datetime. */
								esc_html__( '最近检测 %s', 'nanocat' ),
								esc_html( wp_date( 'Y-m-d H:i', $nanocat_report['generated_at'] ) )
							);
						} else {
							esc_html_e( '等待第一次检测', 'nanocat' );
						}
						?>
					</p>
				</footer>
				<?php
			endwhile;
			?>
		</div>
	</div>
</main>
<?php
get_footer();
