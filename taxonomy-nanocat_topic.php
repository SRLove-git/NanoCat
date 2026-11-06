<?php
/**
 * Topic archive.
 *
 * @package NanoCat
 */

get_header();

$nanocat_term = get_queried_object();

if ( ! $nanocat_term instanceof WP_Term ) {
	get_footer();
	return;
}

$nanocat_order    = nanocat_get_topic_sort( $nanocat_term );
$nanocat_topics   = get_page_by_path( 'topics' );
$nanocat_all      = $nanocat_topics instanceof WP_Post ? get_permalink( $nanocat_topics ) : home_url( '/topics/' );
$nanocat_feed_url = get_term_feed_link( $nanocat_term->term_id, 'nanocat_topic', 'rss2' );
$nanocat_page     = max( 1, (int) get_query_var( 'paged' ) );
$nanocat_per_page = (int) get_option( 'posts_per_page' );
?>
<main id="primary" class="site-main topic-view">
	<header class="hub-hero hub-hero--topic">
		<div class="container hub-hero__inner">
			<div class="hub-hero__copy">
				<nav class="breadcrumbs" aria-label="<?php esc_attr_e( '面包屑导航', 'nanocat' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '首页', 'nanocat' ); ?></a>
					<span class="breadcrumbs__sep" aria-hidden="true">/</span>
					<a href="<?php echo esc_url( $nanocat_all ); ?>"><?php esc_html_e( '专栏', 'nanocat' ); ?></a>
					<span class="breadcrumbs__sep" aria-hidden="true">/</span>
					<span aria-current="page"><?php echo esc_html( $nanocat_term->name ); ?></span>
				</nav>

				<p class="section-kicker"><?php esc_html_e( '// COLUMN', 'nanocat' ); ?></p>
				<h1 class="hub-hero__title"><?php echo esc_html( $nanocat_term->name ); ?></h1>
				<p class="hub-hero__deck">
					<?php
					echo $nanocat_term->description
						? esc_html( $nanocat_term->description )
						: esc_html__( '一个还在持续更新的文章系列。', 'nanocat' );
					?>
				</p>

				<div class="hub-hero__meta">
					<span class="status-dot" aria-hidden="true"></span>
					<span>
						<?php
						printf(
							/* translators: %s: number of posts. */
							esc_html__( '共 %s 篇文章', 'nanocat' ),
							esc_html( number_format_i18n( $nanocat_term->count ) )
						);
						?>
					</span>
					<span class="meta-dot" aria-hidden="true"></span>
					<span><?php echo 'date' === $nanocat_order ? esc_html__( '从旧到新', 'nanocat' ) : esc_html__( '最新在前', 'nanocat' ); ?></span>
					<?php if ( ! is_wp_error( $nanocat_feed_url ) ) : ?>
						<a class="hub-hero__feed" href="<?php echo esc_url( $nanocat_feed_url ); ?>">
							<?php nanocat_icon( 'rss', 14 ); ?>
							<?php esc_html_e( '专栏 RSS', 'nanocat' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>

			<div class="topic-hero__icon-wrap">
				<?php nanocat_topic_icon( $nanocat_term, 'topic-icon--hero' ); ?>
				<span class="topic-hero__spark" aria-hidden="true">✦</span>
				<span class="topic-hero__label" aria-hidden="true"><?php esc_html_e( 'READ IN ORDER', 'nanocat' ); ?></span>
			</div>
		</div>
	</header>

	<div class="container topic-shell">
		<div class="topic-timeline__head">
			<p class="section-kicker"><?php esc_html_e( '// READING_ORDER', 'nanocat' ); ?></p>
			<p class="topic-timeline__hint">
				<?php
				echo 'date' === $nanocat_order
					? esc_html__( '按连载顺序排列，适合从第一篇读起。', 'nanocat' )
					: esc_html__( '新文章在最前面。', 'nanocat' );
				?>
			</p>
		</div>

		<?php if ( have_posts() ) : ?>
			<ol class="topic-timeline">
				<?php
				while ( have_posts() ) :
					the_post();
					$nanocat_index  = ( $nanocat_page - 1 ) * $nanocat_per_page + (int) $GLOBALS['wp_query']->current_post + 1;
					$nanocat_number = str_pad( (string) $nanocat_index, 2, '0', STR_PAD_LEFT );
					?>
					<li class="topic-entry">
						<span class="topic-entry__index" aria-hidden="true"><?php echo esc_html( $nanocat_number ); ?></span>
						<div class="topic-entry__body">
							<div class="topic-entry__meta">
								<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								<span class="meta-dot" aria-hidden="true"></span>
								<span>
									<?php
									printf(
										/* translators: %s: reading time in minutes. */
										esc_html__( '%s 分钟', 'nanocat' ),
										esc_html( number_format_i18n( nanocat_get_reading_time() ) )
									);
									?>
								</span>
							</div>
							<h2 class="topic-entry__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<p class="topic-entry__excerpt"><?php echo esc_html( nanocat_get_excerpt( 96 ) ); ?></p>
						</div>
						<a class="topic-entry__arrow" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( '阅读：%s', 'nanocat' ), get_the_title() ) ); ?>">
							<?php nanocat_icon( 'arrow-right', 18 ); ?>
						</a>
					</li>
				<?php endwhile; ?>
			</ol>

			<?php nanocat_pagination(); ?>
		<?php else : ?>
			<div class="content-none">
				<p class="section-kicker"><?php esc_html_e( '// EMPTY_COLUMN', 'nanocat' ); ?></p>
				<h2><?php esc_html_e( '这个专栏还没有文章。', 'nanocat' ); ?></h2>
				<p><?php esc_html_e( '给文章勾选这个专栏，目录就会自动出现。', 'nanocat' ); ?></p>
			</div>
		<?php endif; ?>

		<a class="back-link" href="<?php echo esc_url( $nanocat_all ); ?>">
			<?php nanocat_icon( 'arrow-left', 16 ); ?>
			<?php esc_html_e( '返回全部专栏', 'nanocat' ); ?>
		</a>
	</div>
</main>
<?php
get_footer();
