<?php
/**
 * Template Name: 专栏首页
 *
 * @package NanoCat
 */

get_header();

while ( have_posts() ) :
	the_post();

	$nanocat_topics      = nanocat_get_topics( 0, true );
	$nanocat_topic_posts = 0;

	foreach ( $nanocat_topics as $nanocat_topic ) {
		$nanocat_topic_posts += (int) $nanocat_topic->count;
	}

	$nanocat_intro   = trim( get_the_content() );
	$nanocat_links   = get_page_by_path( 'links' );
	$nanocat_wiki    = get_post_type_archive_link( 'nanocat_wiki' );
	$nanocat_wikis   = get_posts(
		array(
			'post_type'      => 'nanocat_wiki',
			'post_parent'    => 0,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	?>
	<main id="primary" class="site-main topics-view">
		<header class="hub-hero">
			<div class="container hub-hero__inner">
				<div class="hub-hero__copy">
					<?php nanocat_breadcrumbs(); ?>

					<p class="section-kicker"><?php esc_html_e( '// COLUMNS', 'nanocat' ); ?></p>
					<h1 class="hub-hero__title"><?php the_title(); ?></h1>
					<p class="hub-hero__deck"><?php esc_html_e( '把零散的文章，串成一条可以慢慢读完的线。选一个专栏，从第一篇开始。', 'nanocat' ); ?></p>

					<div class="hub-hero__meta">
						<span class="status-dot" aria-hidden="true"></span>
						<span>
							<?php
							printf(
								/* translators: 1: number of topics, 2: number of posts. */
								esc_html__( '%1$s 个专栏 · %2$s 次收录', 'nanocat' ),
								esc_html( number_format_i18n( count( $nanocat_topics ) ) ),
								esc_html( number_format_i18n( $nanocat_topic_posts ) )
							);
							?>
						</span>
					</div>
				</div>

				<div class="hub-hero__terminal" aria-hidden="true">
					<div class="hub-hero__terminal-bar">
						<span class="terminal__dots"><i></i><i></i><i></i></span>
						<span>cat@nanocat: ~/topics</span>
					</div>
					<div class="hub-hero__terminal-body">
						<p><span class="t-prompt">$</span> tree topics/ -L 1</p>
						<p class="t-out">topics/</p>
						<?php foreach ( array_slice( $nanocat_topics, 0, 4 ) as $nanocat_topic ) : ?>
							<p class="t-out t-out--branch">├── <?php echo esc_html( $nanocat_topic->slug ); ?>/</p>
						<?php endforeach; ?>
						<p><span class="t-prompt">$</span> echo "pick a rabbit hole"<span class="terminal__cursor"></span></p>
					</div>
				</div>
			</div>
		</header>

		<div class="container topics-shell">
			<?php if ( $nanocat_intro ) : ?>
				<div class="topic-intro entry-content"><?php echo apply_filters( 'the_content', $nanocat_intro ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>

			<?php if ( $nanocat_topics ) : ?>
				<div class="topic-grid">
					<?php
					$nanocat_topic_index = 0;
					foreach ( $nanocat_topics as $nanocat_topic ) :
						$nanocat_topic_index++;
						$nanocat_topic_sort = nanocat_get_topic_sort( $nanocat_topic );
						$nanocat_latest     = nanocat_get_topic_posts( $nanocat_topic, 1 );
						$nanocat_topic_link = get_term_link( $nanocat_topic );

						if ( is_wp_error( $nanocat_topic_link ) ) {
							continue;
						}
						?>
						<a class="topic-card" href="<?php echo esc_url( $nanocat_topic_link ); ?>">
							<div class="topic-card__top">
								<?php nanocat_topic_icon( $nanocat_topic ); ?>
								<span class="topic-card__count">
									<?php
									printf(
										/* translators: %s: post count. */
										esc_html__( '%s 篇', 'nanocat' ),
										esc_html( number_format_i18n( $nanocat_topic->count ) )
									);
									?>
								</span>
							</div>

							<p class="topic-card__kicker"><?php printf( '// SERIES %02d', (int) $nanocat_topic_index ); ?></p>
							<h2 class="topic-card__title"><?php echo esc_html( $nanocat_topic->name ); ?></h2>
							<p class="topic-card__desc">
								<?php
								echo $nanocat_topic->description
									? esc_html( $nanocat_topic->description )
									: esc_html__( '一个还在慢慢生长的文章系列。', 'nanocat' );
								?>
							</p>

							<?php if ( $nanocat_latest->have_posts() ) : ?>
								<div class="topic-card__latest">
									<span><?php echo 'date' === $nanocat_topic_sort ? esc_html__( '开篇', 'nanocat' ) : esc_html__( '最新', 'nanocat' ); ?></span>
									<?php echo esc_html( get_the_title( $nanocat_latest->posts[0] ) ); ?>
								</div>
								<?php wp_reset_postdata(); ?>
							<?php endif; ?>

							<span class="text-link">
								<?php esc_html_e( '进入专栏', 'nanocat' ); ?>
								<?php nanocat_icon( 'arrow-right', 16 ); ?>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="content-none">
					<p class="section-kicker"><?php esc_html_e( '// EMPTY_SHELF', 'nanocat' ); ?></p>
					<h2><?php esc_html_e( '专栏还空着。', 'nanocat' ); ?></h2>
					<p><?php esc_html_e( '在后台给文章勾选一个专栏，这里就会自动长出目录。', 'nanocat' ); ?></p>
				</div>
			<?php endif; ?>

			<nav class="hub-crosslinks" aria-label="<?php esc_attr_e( '继续探索', 'nanocat' ); ?>">
				<?php if ( $nanocat_wiki && $nanocat_wikis ) : ?>
					<a class="hub-crosslink" href="<?php echo esc_url( $nanocat_wiki ); ?>">
						<span class="hub-crosslink__kicker"><?php esc_html_e( '// WIKI', 'nanocat' ); ?></span>
						<strong><?php esc_html_e( '打开知识库', 'nanocat' ); ?></strong>
						<?php nanocat_icon( 'arrow-right', 16 ); ?>
					</a>
				<?php endif; ?>
				<?php if ( $nanocat_links instanceof WP_Post ) : ?>
					<a class="hub-crosslink" href="<?php echo esc_url( get_permalink( $nanocat_links ) ); ?>">
						<span class="hub-crosslink__kicker"><?php esc_html_e( '// FRIENDS', 'nanocat' ); ?></span>
						<strong><?php esc_html_e( '看看友链', 'nanocat' ); ?></strong>
						<?php nanocat_icon( 'arrow-right', 16 ); ?>
					</a>
				<?php endif; ?>
			</nav>
		</div>
	</main>
	<?php
endwhile;

get_footer();
