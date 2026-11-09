<?php
/**
 * Template Name: 文章归档
 * Description: A complete, year-grouped index of every published note.
 *
 * @package NanoCat
 */

get_header();

$nanocat_archive_posts = get_posts(
	array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => -1,
		'ignore_sticky_posts' => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
	)
);

$nanocat_archive_groups = array();
foreach ( $nanocat_archive_posts as $nanocat_archive_post ) {
	$nanocat_year  = get_the_date( 'Y', $nanocat_archive_post );
	$nanocat_month = get_the_date( 'n', $nanocat_archive_post );
	if ( ! isset( $nanocat_archive_groups[ $nanocat_year ][ $nanocat_month ] ) ) {
		$nanocat_archive_groups[ $nanocat_year ][ $nanocat_month ] = array();
	}
	$nanocat_archive_groups[ $nanocat_year ][ $nanocat_month ][] = $nanocat_archive_post;
}
?>
<main id="primary" class="site-main archive-library">
	<header class="archive-library__hero">
		<div class="container archive-library__hero-inner">
			<?php nanocat_breadcrumbs(); ?>

			<div class="archive-library__title-row">
				<div>
					<p class="section-kicker"><?php esc_html_e( '// COMPLETE_ARCHIVE', 'nanocat' ); ?></p>
					<h1 class="archive-library__title"><?php esc_html_e( '所有笔记', 'nanocat' ); ?></h1>
					<p class="archive-library__deck"><?php esc_html_e( '按时间排好的全部文字。想找某个主题，也可以直接问 AI。', 'nanocat' ); ?></p>
				</div>

				<div class="archive-library__meta" aria-label="<?php esc_attr_e( '归档统计', 'nanocat' ); ?>">
					<span class="status-dot" aria-hidden="true"></span>
					<span>
						<?php
						printf(
							/* translators: 1: number of posts, 2: number of years. */
							esc_html__( '%1$s 篇 · %2$s 年', 'nanocat' ),
							esc_html( number_format_i18n( count( $nanocat_archive_posts ) ) ),
							esc_html( number_format_i18n( count( $nanocat_archive_groups ) ) )
						);
						?>
					</span>
				</div>
			</div>
		</div>
	</header>

	<div class="container archive-library__wrap">
		<div class="content-shell content-shell--home archive-library__shell">
			<?php get_sidebar( 'left' ); ?>

			<div class="content-primary archive-library__main">
				<?php if ( $nanocat_archive_groups ) : ?>
					<nav class="archive-library__jump" aria-label="<?php esc_attr_e( '按年份跳转', 'nanocat' ); ?>">
						<span class="section-kicker"><?php esc_html_e( '// JUMP_TO', 'nanocat' ); ?></span>
						<?php foreach ( array_keys( $nanocat_archive_groups ) as $nanocat_year ) : ?>
							<a href="#year-<?php echo esc_attr( $nanocat_year ); ?>"><?php echo esc_html( $nanocat_year ); ?></a>
						<?php endforeach; ?>
					</nav>

					<?php foreach ( $nanocat_archive_groups as $nanocat_year => $nanocat_months ) : ?>
						<section class="archive-year" id="year-<?php echo esc_attr( $nanocat_year ); ?>" aria-labelledby="year-title-<?php echo esc_attr( $nanocat_year ); ?>">
							<header class="archive-year__head">
								<h2 class="archive-year__title" id="year-title-<?php echo esc_attr( $nanocat_year ); ?>"><?php echo esc_html( $nanocat_year ); ?></h2>
								<span class="archive-year__count">
									<?php
									printf(
										/* translators: %s: number of posts. */
										esc_html__( '%s 篇', 'nanocat' ),
										esc_html( number_format_i18n( array_sum( array_map( 'count', $nanocat_months ) ) ) )
									);
									?>
								</span>
							</header>

							<?php foreach ( $nanocat_months as $nanocat_month => $nanocat_posts ) : ?>
								<div class="archive-month">
									<h3 class="archive-month__title"><?php echo esc_html( sprintf( '%02d 月', $nanocat_month ) ); ?></h3>
									<ol class="archive-month__list">
										<?php foreach ( $nanocat_posts as $nanocat_post ) : ?>
											<?php
											$nanocat_category = nanocat_get_primary_category( $nanocat_post->ID );
											$nanocat_minutes  = nanocat_get_reading_time( $nanocat_post->ID );
											?>
											<li class="archive-month__item">
												<a href="<?php echo esc_url( get_permalink( $nanocat_post ) ); ?>">
													<time class="archive-month__date" datetime="<?php echo esc_attr( get_the_date( DATE_W3C, $nanocat_post ) ); ?>">
														<?php echo esc_html( get_the_date( 'd', $nanocat_post ) ); ?>
													</time>
													<span class="archive-month__body">
														<span class="archive-month__meta">
															<?php if ( $nanocat_category ) : ?>
																<span><?php echo esc_html( $nanocat_category->name ); ?></span>
																<span aria-hidden="true">/</span>
															<?php endif; ?>
															<span><?php echo esc_html( sprintf( '%d 分钟', $nanocat_minutes ) ); ?></span>
														</span>
														<strong class="archive-month__title-link"><?php echo esc_html( get_the_title( $nanocat_post ) ); ?></strong>
													</span>
													<?php nanocat_icon( 'arrow-up-right', 16 ); ?>
												</a>
											</li>
										<?php endforeach; ?>
									</ol>
								</div>
							<?php endforeach; ?>
						</section>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="content-none">
						<p class="section-kicker"><?php esc_html_e( '// EMPTY_ARCHIVE', 'nanocat' ); ?></p>
						<h2><?php esc_html_e( '还没有可以归档的文章。', 'nanocat' ); ?></h2>
						<p><?php esc_html_e( '发布第一篇笔记之后，这里会自动长成一条时间线。', 'nanocat' ); ?></p>
					</div>
				<?php endif; ?>
			</div>

			<?php get_sidebar( 'right' ); ?>
		</div>
	</div>
</main>
<?php
get_footer();
