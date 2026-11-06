<?php
/**
 * Search results template.
 *
 * @package NanoCat
 */

get_header();
?>
<main id="primary" class="site-main search-view">
	<header class="search-hero">
		<div class="container search-hero__inner">
			<?php nanocat_breadcrumbs(); ?>

			<div class="search-hero__grid">
				<div class="search-hero__copy">
					<p class="section-kicker">// GREP_RESULTS</p>
					<h1 class="search-title"><?php esc_html_e( '搜索结果', 'nanocat' ); ?></h1>

					<?php if ( get_search_query() ) : ?>
						<p class="search-query">
							<span class="search-query__label"><?php esc_html_e( '关键词', 'nanocat' ); ?></span>
							<mark><?php echo esc_html( get_search_query() ); ?></mark>
						</p>
					<?php endif; ?>

					<p class="search-count">
						<span class="status-dot" aria-hidden="true"></span>
						<?php
						printf(
							/* translators: %s: number of search results. */
							esc_html__( '找到 %s 篇相关文章', 'nanocat' ),
							esc_html( number_format_i18n( $wp_query->found_posts ) )
						);
						?>
					</p>
				</div>

				<div class="search-hero__form">
					<p class="search-hero__form-label"><?php esc_html_e( '再搜一次', 'nanocat' ); ?></p>
					<?php get_search_form(); ?>
				</div>
			</div>
		</div>
	</header>

	<div class="container search-shell">
		<div class="content-shell">
			<div class="content-primary">
				<?php if ( have_posts() ) : ?>
					<div class="post-grid post-grid--search">
						<?php
						while ( have_posts() ) :
							the_post();
							get_template_part(
								'template-parts/content',
								get_post_type(),
								array(
									'featured' => false,
									'index'    => 0,
								)
							);
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
