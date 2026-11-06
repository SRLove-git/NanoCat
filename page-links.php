<?php
/**
 * Template Name: 友链
 *
 * @package NanoCat
 */

get_header();

while ( have_posts() ) :
	the_post();

	$nanocat_friends      = nanocat_get_friend_links();
	$nanocat_friend_posts = nanocat_get_friend_posts( 6 );
	$nanocat_intro        = trim( get_the_content() );
	$nanocat_last_refresh = (int) get_option( 'nanocat_links_refreshed', 0 );
	$nanocat_topics       = get_page_by_path( 'topics' );
	$nanocat_wiki         = get_post_type_archive_link( 'nanocat_wiki' );
	$nanocat_wikis        = get_posts(
		array(
			'post_type'      => 'nanocat_wiki',
			'post_parent'    => 0,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	?>
	<main id="primary" class="site-main links-view">
		<header class="hub-hero hub-hero--links">
			<div class="container hub-hero__inner">
				<div class="hub-hero__copy">
					<?php nanocat_breadcrumbs(); ?>

					<p class="section-kicker"><?php esc_html_e( '// BLOGROLL', 'nanocat' ); ?></p>
					<h1 class="hub-hero__title"><?php the_title(); ?></h1>
					<p class="hub-hero__deck"><?php esc_html_e( '互联网很大，但常去的地方就那么几个。这里住着一群写字、画画、折腾代码的人。', 'nanocat' ); ?></p>

					<div class="hub-hero__meta">
						<span class="status-dot" aria-hidden="true"></span>
						<span>
							<?php
							printf(
								/* translators: %s: number of friends. */
								esc_html__( '%s 位朋友', 'nanocat' ),
								esc_html( number_format_i18n( count( $nanocat_friends ) ) )
							);
							?>
						</span>
						<?php if ( $nanocat_last_refresh ) : ?>
							<span class="meta-dot" aria-hidden="true"></span>
							<span>
								<?php
								printf(
									/* translators: %s: human-readable time difference. */
									esc_html__( 'RSS 更新于 %s前', 'nanocat' ),
									esc_html( human_time_diff( $nanocat_last_refresh, time() ) )
								);
								?>
							</span>
						<?php endif; ?>
					</div>
				</div>

				<div class="hub-hero__terminal" aria-hidden="true">
					<div class="hub-hero__terminal-bar">
						<span class="terminal__dots"><i></i><i></i><i></i></span>
						<span>cat@nanocat: ~/friends</span>
					</div>
					<div class="hub-hero__terminal-body">
						<p><span class="t-prompt">$</span> curl -s friends.json | jq '.[].name'</p>
						<?php if ( $nanocat_friends ) : ?>
							<?php foreach ( array_slice( $nanocat_friends, 0, 4 ) as $nanocat_friend ) : ?>
								<p class="t-out t-out--branch">"<?php echo esc_html( $nanocat_friend['name'] ); ?>"</p>
							<?php endforeach; ?>
						<?php else : ?>
							<p class="t-out">[] <span class="t-comment"># waiting for friends</span></p>
						<?php endif; ?>
						<p><span class="t-prompt">$</span> echo "hello, neighbor"<span class="terminal__cursor"></span></p>
					</div>
				</div>
			</div>
		</header>

		<div class="container links-shell">
			<?php if ( $nanocat_intro ) : ?>
				<div class="links-intro entry-content"><?php echo apply_filters( 'the_content', $nanocat_intro ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<?php endif; ?>

			<?php if ( current_user_can( 'manage_options' ) ) : ?>
				<div class="links-admin">
					<div>
						<p class="section-kicker"><?php esc_html_e( '// ADMIN', 'nanocat' ); ?></p>
						<p><?php esc_html_e( '友链健康状态和 RSS 文章默认缓存 1–6 小时，也可以在这里立即刷新。', 'nanocat' ); ?></p>
					</div>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="nanocat_refresh_links">
						<?php wp_nonce_field( 'nanocat_refresh_links' ); ?>
						<button class="button button--small button--outline" type="submit">
							<?php nanocat_icon( 'rss', 15 ); ?>
							<?php esc_html_e( '刷新 RSS', 'nanocat' ); ?>
						</button>
					</form>
				</div>
			<?php endif; ?>

			<?php if ( $nanocat_friend_posts ) : ?>
				<section class="friend-feed" aria-labelledby="friend-feed-title">
					<div class="section-heading">
						<div>
							<p class="section-kicker"><?php esc_html_e( '// FRIENDS_ARE_WRITING', 'nanocat' ); ?></p>
							<h2 class="section-title" id="friend-feed-title"><?php esc_html_e( '友链新文', 'nanocat' ); ?></h2>
						</div>
						<p class="section-count"><?php esc_html_e( '来自邻居的最近更新', 'nanocat' ); ?></p>
					</div>

					<div class="friend-feed__list">
						<?php foreach ( $nanocat_friend_posts as $nanocat_item ) : ?>
							<a class="friend-feed__item" href="<?php echo esc_url( $nanocat_item['permalink'] ); ?>" target="_blank" rel="noopener noreferrer">
								<span class="friend-feed__friend"><?php echo esc_html( $nanocat_item['friend'] ); ?></span>
								<span class="friend-feed__title"><?php echo esc_html( $nanocat_item['title'] ); ?></span>
								<time class="friend-feed__date" datetime="<?php echo esc_attr( gmdate( 'c', (int) $nanocat_item['timestamp'] ) ); ?>"><?php echo esc_html( $nanocat_item['date'] ); ?></time>
								<?php nanocat_icon( 'arrow-right', 15 ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $nanocat_friends ) : ?>
				<div class="friend-section-head">
					<div>
						<p class="section-kicker"><?php esc_html_e( '// NEIGHBORS', 'nanocat' ); ?></p>
						<h2 class="section-title"><?php esc_html_e( '常去的地方', 'nanocat' ); ?></h2>
					</div>
					<span class="section-count"><?php esc_html_e( '排名按后台评分排序', 'nanocat' ); ?></span>
				</div>

				<div class="friend-grid">
					<?php
					foreach ( $nanocat_friends as $nanocat_friend ) :
						$nanocat_host = wp_parse_url( $nanocat_friend['url'], PHP_URL_HOST );
						$nanocat_host = $nanocat_host ? $nanocat_host : $nanocat_friend['url'];
						$nanocat_state = isset( $nanocat_friend['health']['status'] ) ? $nanocat_friend['health']['status'] : 'unknown';
						$nanocat_label = 'live' === $nanocat_state ? __( '在线', 'nanocat' ) : ( 'down' === $nanocat_state ? __( '失联', 'nanocat' ) : __( '待检测', 'nanocat' ) );
						?>
						<article class="friend-card">
							<header class="friend-card__head">
								<a class="friend-card__avatar" href="<?php echo esc_url( $nanocat_friend['url'] ); ?>" target="_blank" rel="noopener noreferrer" tabindex="-1" aria-hidden="true">
									<?php if ( $nanocat_friend['image'] ) : ?>
										<img src="<?php echo esc_url( $nanocat_friend['image'] ); ?>" alt="" width="56" height="56" loading="lazy" referrerpolicy="no-referrer">
									<?php else : ?>
										<span><?php echo esc_html( $nanocat_friend['initial'] ); ?></span>
									<?php endif; ?>
								</a>

								<div class="friend-card__identity">
									<h3 class="friend-card__name">
										<a href="<?php echo esc_url( $nanocat_friend['url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $nanocat_friend['name'] ); ?></a>
									</h3>
									<p class="friend-card__host"><?php echo esc_html( $nanocat_host ); ?></p>
								</div>

								<span class="link-status link-status--<?php echo esc_attr( $nanocat_state ); ?>" title="<?php echo esc_attr( $nanocat_friend['health']['code'] ? 'HTTP ' . $nanocat_friend['health']['code'] : '' ); ?>">
									<span class="status-dot" aria-hidden="true"></span>
									<?php echo esc_html( $nanocat_label ); ?>
								</span>
							</header>

							<?php if ( $nanocat_friend['description'] ) : ?>
								<p class="friend-card__desc"><?php echo esc_html( $nanocat_friend['description'] ); ?></p>
							<?php endif; ?>

							<?php if ( $nanocat_friend['categories'] ) : ?>
								<ul class="friend-card__tags" aria-label="<?php esc_attr_e( '友链分类', 'nanocat' ); ?>">
									<?php foreach ( $nanocat_friend['categories'] as $nanocat_link_cat ) : ?>
										<li><?php echo esc_html( $nanocat_link_cat->name ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<?php if ( $nanocat_friend['feed'] ) : ?>
								<ul class="friend-card__posts">
									<?php foreach ( $nanocat_friend['feed'] as $nanocat_post ) : ?>
										<li>
											<a href="<?php echo esc_url( $nanocat_post['permalink'] ); ?>" target="_blank" rel="noopener noreferrer">
												<span><?php echo esc_html( $nanocat_post['title'] ); ?></span>
												<time datetime="<?php echo esc_attr( gmdate( 'c', (int) $nanocat_post['timestamp'] ) ); ?>"><?php echo esc_html( $nanocat_post['date'] ); ?></time>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php elseif ( $nanocat_friend['rss'] ) : ?>
								<p class="friend-card__empty"><?php esc_html_e( 'RSS 还在路上，稍后刷新就会有了。', 'nanocat' ); ?></p>
							<?php else : ?>
								<p class="friend-card__empty"><?php esc_html_e( '这位朋友没有留下 RSS，直接去串门吧。', 'nanocat' ); ?></p>
							<?php endif; ?>

							<a class="friend-card__visit" href="<?php echo esc_url( $nanocat_friend['url'] ); ?>" target="_blank" rel="noopener noreferrer">
								<?php esc_html_e( '访问网站', 'nanocat' ); ?>
								<?php nanocat_icon( 'arrow-right', 15 ); ?>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="content-none">
					<p class="section-kicker"><?php esc_html_e( '// NO_NEIGHBORS_YET', 'nanocat' ); ?></p>
					<h2><?php esc_html_e( '门口还没有朋友。', 'nanocat' ); ?></h2>
					<?php if ( current_user_can( 'manage_options' ) ) : ?>
						<p><?php esc_html_e( '在后台“链接”中添加第一位朋友，并填写 RSS 地址，这里就会热闹起来。', 'nanocat' ); ?></p>
						<p><a class="button button--primary" href="<?php echo esc_url( admin_url( 'link-add.php' ) ); ?>"><?php esc_html_e( '添加友链', 'nanocat' ); ?></a></p>
					<?php else : ?>
						<p><?php esc_html_e( '这里还空着，晚点再来看看吧。', 'nanocat' ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="friend-invite">
				<p class="section-kicker"><?php esc_html_e( '// SAY_HELLO', 'nanocat' ); ?></p>
				<h2><?php esc_html_e( '想交换友链？', 'nanocat' ); ?></h2>
				<p><?php esc_html_e( '留个言，或者直接发邮件。聊聊你在写什么，比交换链接本身更重要。', 'nanocat' ); ?></p>
				<?php nanocat_social_links( 'social-links--inline' ); ?>
			</div>

			<nav class="hub-crosslinks" aria-label="<?php esc_attr_e( '继续探索', 'nanocat' ); ?>">
				<?php if ( $nanocat_topics instanceof WP_Post ) : ?>
					<a class="hub-crosslink" href="<?php echo esc_url( get_permalink( $nanocat_topics ) ); ?>">
						<span class="hub-crosslink__kicker"><?php esc_html_e( '// COLUMNS', 'nanocat' ); ?></span>
						<strong><?php esc_html_e( '去读专栏', 'nanocat' ); ?></strong>
						<?php nanocat_icon( 'arrow-right', 16 ); ?>
					</a>
				<?php endif; ?>
				<?php if ( $nanocat_wiki && $nanocat_wikis ) : ?>
					<a class="hub-crosslink" href="<?php echo esc_url( $nanocat_wiki ); ?>">
						<span class="hub-crosslink__kicker"><?php esc_html_e( '// WIKI', 'nanocat' ); ?></span>
						<strong><?php esc_html_e( '打开知识库', 'nanocat' ); ?></strong>
						<?php nanocat_icon( 'arrow-right', 16 ); ?>
					</a>
				<?php endif; ?>
			</nav>
		</div>
	</main>
	<?php
endwhile;

get_footer();
