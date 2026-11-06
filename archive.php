<?php
/**
 * Archive template for categories, tags, authors and date archives.
 *
 * @package NanoCat
 */

get_header();

$nanocat_archive_code  = 'ARCHIVE';
$nanocat_archive_label = __( '归档', 'nanocat' );
$nanocat_archive_title = wp_strip_all_tags( get_the_archive_title() );

if ( is_category() ) {
	$nanocat_archive_code  = 'CATEGORY';
	$nanocat_archive_label = __( '分类', 'nanocat' );
	$nanocat_archive_title = single_cat_title( '', false );
} elseif ( is_tag() ) {
	$nanocat_archive_code  = 'TAG';
	$nanocat_archive_label = __( '标签', 'nanocat' );
	$nanocat_archive_title = single_tag_title( '', false );
} elseif ( is_author() ) {
	$nanocat_archive_code  = 'AUTHOR';
	$nanocat_archive_label = __( '作者', 'nanocat' );
	$nanocat_archive_title = get_the_author_meta( 'display_name', get_queried_object_id() );
} elseif ( is_tax() ) {
	$nanocat_archive_code  = 'TOPIC';
	$nanocat_archive_label = __( '专题', 'nanocat' );
	$nanocat_archive_title = single_term_title( '', false );
} elseif ( is_post_type_archive() ) {
	$nanocat_archive_code  = 'COLLECTION';
	$nanocat_archive_label = __( '内容类型', 'nanocat' );
	$nanocat_archive_title = post_type_archive_title( '', false );
} elseif ( is_year() ) {
	$nanocat_archive_code  = 'YEAR';
	$nanocat_archive_label = __( '年份', 'nanocat' );
	$nanocat_archive_title = sprintf(
		/* translators: %s: archive year. */
		__( '%s 年', 'nanocat' ),
		get_query_var( 'year' )
	);
} elseif ( is_month() ) {
	$nanocat_archive_code  = 'MONTH';
	$nanocat_archive_label = __( '月份', 'nanocat' );
	$nanocat_archive_title = sprintf(
		/* translators: 1: archive year, 2: archive month. */
		__( '%1$s 年 %2$s 月', 'nanocat' ),
		get_query_var( 'year' ),
		get_query_var( 'monthnum' )
	);
} elseif ( is_day() ) {
	$nanocat_archive_code  = 'DAY';
	$nanocat_archive_label = __( '日期', 'nanocat' );
	$nanocat_archive_title = sprintf(
		/* translators: 1: archive year, 2: archive month, 3: archive day. */
		__( '%1$s 年 %2$s 月 %3$s 日', 'nanocat' ),
		get_query_var( 'year' ),
		get_query_var( 'monthnum' ),
		get_query_var( 'day' )
	);
}

$nanocat_archive_description = get_the_archive_description();
$nanocat_archive_glyph       = preg_match( '/./u', $nanocat_archive_label, $nanocat_archive_match ) ? $nanocat_archive_match[0] : '#';
?>
<main id="primary" class="site-main archive-view">
	<header class="archive-hero">
		<div class="container archive-hero__inner">
			<?php nanocat_breadcrumbs(); ?>

			<div class="archive-hero__main">
				<div class="archive-hero__copy">
					<p class="section-kicker">// <?php echo esc_html( $nanocat_archive_code ); ?>_ARCHIVE</p>
					<p class="archive-hero__label"><?php echo esc_html( $nanocat_archive_label ); ?></p>
					<h1 class="archive-title"><?php echo esc_html( $nanocat_archive_title ); ?></h1>

					<?php if ( $nanocat_archive_description ) : ?>
						<div class="archive-description"><?php echo wp_kses_post( $nanocat_archive_description ); ?></div>
					<?php endif; ?>
				</div>

				<?php if ( is_author() ) : ?>
					<div class="archive-hero__avatar" aria-hidden="true">
						<?php echo get_avatar( get_queried_object_id(), 112, '', get_the_author_meta( 'display_name', get_queried_object_id() ) ); ?>
					</div>
				<?php else : ?>
					<span class="archive-hero__glyph" aria-hidden="true"><?php echo esc_html( $nanocat_archive_glyph ); ?></span>
				<?php endif; ?>
			</div>

			<div class="archive-hero__meta">
				<span class="archive-count">
					<span class="status-dot" aria-hidden="true"></span>
					<?php
					printf(
						/* translators: %s: number of posts in the archive. */
						esc_html__( '共 %s 篇文章', 'nanocat' ),
						esc_html( number_format_i18n( $wp_query->found_posts ) )
					);
					?>
				</span>
				<a class="archive-rss" href="<?php echo esc_url( get_feed_link() ); ?>">
					<?php nanocat_icon( 'rss', 15 ); ?>
					<?php esc_html_e( '订阅 RSS', 'nanocat' ); ?>
				</a>
			</div>
		</div>
	</header>

	<div class="container archive-shell">
		<div class="content-shell">
			<div class="content-primary">
				<?php if ( have_posts() ) : ?>
					<div class="post-grid post-grid--archive">
						<?php
						$nanocat_index = 0;
						while ( have_posts() ) :
							the_post();
							get_template_part(
								'template-parts/content',
								get_post_type(),
								array(
									'featured' => ( 0 === $nanocat_index && ! is_paged() ),
									'index'    => $nanocat_index,
								)
							);
							$nanocat_index++;
						endwhile;
						?>
					</div>

					<?php nanocat_pagination(); ?>
				<?php else : ?>
					<?php get_template_part( 'template-parts/content', 'none' ); ?>
				<?php endif; ?>
			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>
</main>

<?php
get_footer();
