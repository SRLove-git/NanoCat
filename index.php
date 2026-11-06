<?php
/**
 * The main blog index.
 *
 * @package NanoCat
 */

get_header();

$nanocat_show_hero = is_home() && ! is_paged() && get_theme_mod( 'nanocat_hero_enabled', true );

if ( $nanocat_show_hero ) {
	get_template_part( 'template-parts/hero' );
}
?>

<main id="primary" class="site-main">
	<div class="container">
		<div class="content-shell content-shell--home">
			<?php get_sidebar( 'left' ); ?>

			<div id="latest-posts" class="content-primary">
				<?php if ( have_posts() ) : ?>
					<div class="section-heading">
						<div>
							<p class="section-kicker"><?php esc_html_e( '// LATEST NOTES', 'nanocat' ); ?></p>
							<h2 class="section-title"><?php esc_html_e( '最新文章', 'nanocat' ); ?></h2>
						</div>
						<p class="section-count">
							<?php
							printf(
								/* translators: %s: total number of posts. */
								esc_html__( '共 %s 篇', 'nanocat' ),
								esc_html( number_format_i18n( $wp_query->found_posts ) )
							);
							?>
						</p>
					</div>

					<div class="post-list post-list--home">
						<?php
						$nanocat_index = 0;
						while ( have_posts() ) :
							the_post();
							get_template_part(
								'template-parts/content',
								get_post_type(),
								array(
									'featured' => false,
									'index'    => $nanocat_index,
								)
							);
							$nanocat_index++;
						endwhile;
						?>
					</div>

					<?php nanocat_pagination(); ?>
				<?php else : ?>
					<div class="section-heading">
						<div>
							<p class="section-kicker"><?php esc_html_e( '// HELLO?', 'nanocat' ); ?></p>
							<h2 class="section-title"><?php esc_html_e( '最新文章', 'nanocat' ); ?></h2>
						</div>
					</div>
					<?php get_template_part( 'template-parts/content', 'none' ); ?>
				<?php endif; ?>
			</div>

			<?php get_sidebar( 'right' ); ?>
		</div>
	</div>

	<?php
	if ( is_home() && ! is_paged() ) {
		get_template_part( 'template-parts/topics-strip' );
	}
	?>
</main>

<?php
get_footer();
