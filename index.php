<?php
/**
 * The main blog index: AI first, index second.
 *
 * @package NanoCat
 */

get_header();

if ( is_home() && ! is_paged() ) {
	get_template_part( 'template-parts/home-ai' );
}
?>

<main id="primary" class="site-main home-index-main">
	<div class="container home-index__wrap">
		<div class="content-shell content-shell--home">
			<?php get_sidebar( 'left' ); ?>

			<div class="content-primary home-index" id="latest-posts">
				<section aria-labelledby="home-index-title">
					<header class="home-index__head">
						<div>
							<p class="section-kicker"><?php esc_html_e( '// THE INDEX', 'nanocat' ); ?></p>
							<h2 class="home-index__title" id="home-index-title">
								<?php
								if ( is_paged() ) {
									printf(
										/* translators: %s: page number. */
										esc_html__( '笔记索引 / 第 %s 页', 'nanocat' ),
										esc_html( number_format_i18n( get_query_var( 'paged' ) ) )
									);
								} else {
									esc_html_e( '慢慢翻，也可以。', 'nanocat' );
								}
								?>
							</h2>
						</div>
						<p class="home-index__count">
							<?php
							printf(
								/* translators: %s: total number of posts. */
								esc_html__( '共 %s 篇', 'nanocat' ),
								esc_html( number_format_i18n( $wp_query->found_posts ) )
							);
							?>
						</p>
					</header>

					<?php if ( have_posts() ) : ?>
						<div class="home-index__list">
							<?php
							$nanocat_index = 0;
							while ( have_posts() ) :
								the_post();
								get_template_part(
									'template-parts/content',
									'index',
									array(
										'index' => $nanocat_index,
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
				</section>
			</div>

			<?php get_sidebar( 'right' ); ?>
		</div>
	</div>
</main>

<?php
get_footer();
