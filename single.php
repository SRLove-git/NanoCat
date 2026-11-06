<?php
/**
 * Single post template.
 *
 * @package NanoCat
 */

get_header();

while ( have_posts() ) :
	the_post();

	$nanocat_category   = nanocat_get_primary_category();
	$nanocat_author_id  = (int) get_the_author_meta( 'ID' );
	$nanocat_author_bio = get_the_author_meta( 'description' );
	$nanocat_comments   = (int) get_comments_number();
	$nanocat_tags       = get_the_tags();
	$nanocat_topics     = function_exists( 'nanocat_get_post_topics' ) ? nanocat_get_post_topics() : array();
	$nanocat_issue      = str_pad( (string) get_the_ID(), 3, '0', STR_PAD_LEFT );
	$nanocat_words      = nanocat_get_character_count();
	?>
	<main id="primary" class="site-main single-view">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>
			<header class="entry-hero">
				<div class="container entry-hero__inner">
					<div class="entry-hero__masthead">
						<?php nanocat_breadcrumbs(); ?>
						<span class="entry-hero__issue">
							<span class="entry-hero__issue-dot" aria-hidden="true"></span>
							<?php esc_html_e( 'ARTICLE', 'nanocat' ); ?>
							<b><?php echo esc_html( $nanocat_issue ); ?></b>
							<span aria-hidden="true">/</span>
							<?php echo esc_html( get_the_date( 'Y' ) ); ?>
						</span>
					</div>

					<div class="entry-hero__grid">
						<div class="entry-hero__copy">
							<div class="entry-hero__kicker">
								<?php if ( $nanocat_category ) : ?>
									<a class="chip" href="<?php echo esc_url( get_category_link( $nanocat_category->term_id ) ); ?>">
										<span class="chip__dot" aria-hidden="true"></span>
										<?php echo esc_html( $nanocat_category->name ); ?>
									</a>
								<?php endif; ?>

								<?php foreach ( $nanocat_topics as $nanocat_topic ) : ?>
									<a class="chip chip--topic" href="<?php echo esc_url( get_term_link( $nanocat_topic ) ); ?>">
										<?php nanocat_icon( 'folder', 14 ); ?>
										<?php echo esc_html( $nanocat_topic->name ); ?>
									</a>
								<?php endforeach; ?>

								<?php if ( is_sticky() ) : ?>
									<span class="chip chip--muted"><?php esc_html_e( '置顶', 'nanocat' ); ?></span>
								<?php endif; ?>

								<?php
								edit_post_link(
									__( '编辑文章', 'nanocat' ),
									'<span class="entry-edit">',
									'</span>'
								);
								?>
							</div>

							<h1 class="entry-title"><?php the_title(); ?></h1>

							<?php if ( has_excerpt() ) : ?>
								<p class="entry-deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
							<?php endif; ?>

							<div class="entry-meta">
								<a class="entry-author" href="<?php echo esc_url( get_author_posts_url( $nanocat_author_id ) ); ?>">
									<?php
									echo get_avatar(
										$nanocat_author_id,
										44,
										'',
										get_the_author(),
										array(
											'class' => 'entry-author__avatar',
										)
									);
									?>
									<span class="entry-author__text">
										<span class="entry-meta__label"><?php esc_html_e( 'WRITTEN BY', 'nanocat' ); ?></span>
										<span class="entry-author__name"><?php the_author(); ?></span>
									</span>
								</a>

								<span class="entry-meta__sep" aria-hidden="true"></span>

								<span class="entry-meta__item">
									<?php nanocat_icon( 'calendar', 16 ); ?>
									<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
								</span>

								<span class="entry-meta__item">
									<?php nanocat_icon( 'clock', 16 ); ?>
									<?php
									printf(
										/* translators: %s: reading time in minutes. */
										esc_html__( '%s 分钟阅读', 'nanocat' ),
										esc_html( number_format_i18n( nanocat_get_reading_time() ) )
									);
									?>
								</span>

								<?php if ( comments_open() || $nanocat_comments > 0 ) : ?>
									<a class="entry-meta__item entry-meta__item--link" href="#comments">
										<?php nanocat_icon( 'comments', 16 ); ?>
										<?php
										printf(
											/* translators: %s: comment count. */
											esc_html( _n( '%s 条评论', '%s 条评论', $nanocat_comments, 'nanocat' ) ),
											esc_html( number_format_i18n( $nanocat_comments ) )
										);
										?>
									</a>
								<?php endif; ?>
							</div>
						</div>

						<aside class="entry-brief" aria-label="<?php esc_attr_e( '文章档案', 'nanocat' ); ?>">
							<span class="entry-brief__eyebrow">
								<span class="status-dot" aria-hidden="true"></span>
								<?php esc_html_e( 'ENTRY FILE', 'nanocat' ); ?>
							</span>

							<span class="entry-brief__number">#<?php echo esc_html( $nanocat_issue ); ?></span>
							<span class="entry-brief__caption"><?php echo esc_html( $nanocat_category ? $nanocat_category->name : __( 'NOTES', 'nanocat' ) ); ?></span>

							<dl class="entry-brief__rows">
								<div>
									<dt><?php esc_html_e( '发布', 'nanocat' ); ?></dt>
									<dd><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></dd>
								</div>
								<div>
									<dt><?php esc_html_e( '更新', 'nanocat' ); ?></dt>
									<dd><time datetime="<?php echo esc_attr( get_the_modified_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_modified_date() ); ?></time></dd>
								</div>
								<div>
									<dt><?php esc_html_e( '字数', 'nanocat' ); ?></dt>
									<dd><?php echo esc_html( number_format_i18n( $nanocat_words ) ); ?></dd>
								</div>
								<div>
									<dt><?php esc_html_e( '阅读', 'nanocat' ); ?></dt>
									<dd><?php echo esc_html( sprintf( __( '%s 分钟', 'nanocat' ), number_format_i18n( nanocat_get_reading_time() ) ) ); ?></dd>
								</div>
							</dl>

							<div class="entry-brief__foot">
								<span><?php echo esc_html( get_the_date( 'Y' ) ); ?> · <?php esc_html_e( 'ARCHIVE', 'nanocat' ); ?></span>
								<?php if ( comments_open() || $nanocat_comments > 0 ) : ?>
									<a href="#comments">
										<?php esc_html_e( '参与讨论', 'nanocat' ); ?>
										<?php nanocat_icon( 'arrow-right', 14 ); ?>
									</a>
								<?php endif; ?>
							</div>
						</aside>
					</div>
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

			<div class="container entry-shell">
				<div class="entry-main">
					<details class="toc-mobile" data-toc-mobile hidden>
						<summary>
							<span class="toc-mobile__title"><?php nanocat_icon( 'terminal', 18 ); ?><?php esc_html_e( '文章目录', 'nanocat' ); ?></span>
							<span class="toc-mobile__hint" data-toc-count></span>
						</summary>
						<nav class="toc-nav" data-toc-nav-mobile aria-label="<?php esc_attr_e( '文章目录', 'nanocat' ); ?>"></nav>
					</details>

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

					<footer class="entry-footer">
						<?php if ( $nanocat_tags ) : ?>
							<div class="entry-tags">
								<span class="entry-tags__label"><?php nanocat_icon( 'tag', 16 ); ?><?php esc_html_e( '标签', 'nanocat' ); ?></span>
								<ul class="tag-list tag-list--inline">
									<?php foreach ( $nanocat_tags as $nanocat_tag ) : ?>
										<li>
											<a class="tag-chip" href="<?php echo esc_url( get_tag_link( $nanocat_tag->term_id ) ); ?>" rel="tag">#<?php echo esc_html( $nanocat_tag->name ); ?></a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<div class="entry-share">
							<span class="entry-share__label"><?php esc_html_e( 'SHARE', 'nanocat' ); ?></span>
							<button class="share-action" type="button" data-share-native hidden>
								<?php nanocat_icon( 'share', 17 ); ?>
								<span><?php esc_html_e( '分享文章', 'nanocat' ); ?></span>
							</button>
							<button class="share-action" type="button" data-share-copy>
								<?php nanocat_icon( 'link', 17 ); ?>
								<span data-share-label><?php esc_html_e( '复制链接', 'nanocat' ); ?></span>
							</button>
						</div>
					</footer>

					<?php nanocat_post_navigation(); ?>

					<section class="author-card" aria-labelledby="author-card-name">
						<div class="author-card__avatar">
							<?php echo get_avatar( $nanocat_author_id, 88, '', get_the_author() ); ?>
						</div>
						<div class="author-card__body">
							<p class="section-kicker"><?php esc_html_e( '// ABOUT THE AUTHOR', 'nanocat' ); ?></p>
							<h2 class="author-card__name" id="author-card-name">
								<a href="<?php echo esc_url( get_author_posts_url( $nanocat_author_id ) ); ?>"><?php the_author(); ?></a>
							</h2>
							<p class="author-card__bio">
								<?php
								echo $nanocat_author_bio
									? esc_html( $nanocat_author_bio )
									: esc_html__( '这个家伙很懒，只留下了一堆代码和一只猫。', 'nanocat' );
								?>
							</p>
							<a class="text-link" href="<?php echo esc_url( get_author_posts_url( $nanocat_author_id ) ); ?>">
								<?php esc_html_e( '查看全部文章', 'nanocat' ); ?>
								<?php nanocat_icon( 'arrow-right', 16 ); ?>
							</a>
						</div>
						<span class="author-card__stamp" aria-hidden="true">MEOW</span>
					</section>

					<?php nanocat_related_posts(); ?>

					<?php
					if ( comments_open() || $nanocat_comments > 0 ) {
						comments_template();
					}
					?>
				</div>

				<aside class="entry-aside" aria-label="<?php esc_attr_e( '文章附加信息', 'nanocat' ); ?>">
					<div class="entry-aside__sticky">
						<div class="toc-card" data-toc hidden>
							<p class="section-kicker"><?php esc_html_e( '// ON THIS PAGE', 'nanocat' ); ?></p>
							<h2 class="toc-card__title"><?php esc_html_e( '目录', 'nanocat' ); ?></h2>
							<nav class="toc-nav" data-toc-nav aria-label="<?php esc_attr_e( '文章目录', 'nanocat' ); ?>"></nav>
						</div>

						<?php if ( $nanocat_topics ) : ?>
							<div class="toc-card topic-aside-card">
								<p class="section-kicker"><?php esc_html_e( '// SERIES', 'nanocat' ); ?></p>
								<h2 class="toc-card__title"><?php esc_html_e( '专栏', 'nanocat' ); ?></h2>
								<ul class="topic-aside-list">
									<?php foreach ( $nanocat_topics as $nanocat_topic ) : ?>
										<li>
											<a href="<?php echo esc_url( get_term_link( $nanocat_topic ) ); ?>">
												<?php nanocat_topic_icon( $nanocat_topic ); ?>
												<span><?php echo esc_html( $nanocat_topic->name ); ?></span>
												<strong><?php echo esc_html( number_format_i18n( $nanocat_topic->count ) ); ?></strong>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>

						<a class="aside-top-link" href="#primary">
							<?php nanocat_icon( 'arrow-up', 15 ); ?>
							<?php esc_html_e( '回到顶部', 'nanocat' ); ?>
						</a>
					</div>
				</aside>
			</div>
		</article>
	</main>
	<?php
endwhile;

get_footer();
