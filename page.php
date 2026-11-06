<?php
/**
 * Page template.
 *
 * @package NanoCat
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<main id="primary" class="site-main page-view">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-page' ); ?>>
			<header class="page-hero">
				<div class="container container--narrow">
					<?php nanocat_breadcrumbs(); ?>

					<div class="page-hero__kicker">
						<p class="section-kicker"><?php esc_html_e( '// PAGE', 'nanocat' ); ?></p>
						<?php
						edit_post_link(
							__( '编辑页面', 'nanocat' ),
							'<span class="entry-edit">',
							'</span>'
						);
						?>
					</div>

					<h1 class="page-title"><?php the_title(); ?></h1>

					<?php if ( has_excerpt() ) : ?>
						<p class="page-deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>
				</div>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="container entry-cover-wrap">
					<figure class="entry-cover">
						<?php
						the_post_thumbnail(
							'nanocat-wide',
							array(
								'class'         => 'entry-cover__img',
								'loading'       => 'eager',
								'fetchpriority' => 'high',
							)
						);
						$nanocat_caption = get_the_post_thumbnail_caption();
						if ( $nanocat_caption ) :
							?>
							<figcaption class="entry-cover__caption"><?php echo wp_kses_post( $nanocat_caption ); ?></figcaption>
						<?php endif; ?>
					</figure>
				</div>
			<?php endif; ?>

			<div class="container container--narrow page-shell">
				<div class="entry-content" data-entry-content>
					<?php
					the_content();
					wp_link_pages(
						array(
							'before'      => '<nav class="page-links" aria-label="' . esc_attr__( '分页', 'nanocat' ) . '"><span class="page-links__label">' . esc_html__( '页面', 'nanocat' ) . '</span>',
							'after'       => '</nav>',
							'link_before' => '<span>',
							'link_after'  => '</span>',
						)
					);
					?>
				</div>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}
				?>
			</div>
		</article>
	</main>
	<?php
endwhile;

get_footer();
