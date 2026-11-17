<?php
/**
 * The main blog index: hero, then the article card stream.
 *
 * @package NanoCat
 */

get_header();

if ( is_home() && ! is_paged() ) {
	get_template_part( 'template-parts/hero-rose' );
}
?>

<main id="primary" class="site-main flec-main">
	<div class="container flec-layout" id="latest-posts">
		<div class="flec-content">
			<header class="flec-section-head">
				<div>
					<p class="flec-section-head__kicker"><?php esc_html_e( 'LATEST NOTES', 'nanocat' ); ?></p>
					<h2 class="flec-section-head__title">
						<?php
						if ( is_paged() ) {
							printf(
								/* translators: %s: page number. */
								esc_html__( '第 %s 页', 'nanocat' ),
								esc_html( number_format_i18n( get_query_var( 'paged' ) ) )
							);
						} else {
							esc_html_e( '最新文章', 'nanocat' );
						}
						?>
					</h2>
				</div>
				<p class="flec-section-head__count">
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
				<div class="flec-grid">
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
		</div>

		<?php get_sidebar( 'right' ); ?>
	</div>
</main>

<?php
get_footer();
