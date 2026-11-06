<?php
/**
 * Blog sidebar.
 *
 * @package NanoCat
 */
?>
<aside id="secondary" class="sidebar widget-area" aria-label="<?php esc_attr_e( '博客侧边栏', 'nanocat' ); ?>">
	<?php
	if ( is_active_sidebar( 'sidebar-1' ) ) {
		dynamic_sidebar( 'sidebar-1' );
	} else {
		$nanocat_avatar = get_site_icon_url( 192 );
		$nanocat_avatar = $nanocat_avatar ? $nanocat_avatar : get_template_directory_uri() . '/assets/images/mascot.svg';
		$nanocat_counts = wp_count_posts( 'post' );
		$nanocat_total  = isset( $nanocat_counts->publish ) ? (int) $nanocat_counts->publish : 0;
		$nanocat_cats   = wp_count_terms( array( 'taxonomy' => 'category', 'hide_empty' => true ) );
		$nanocat_cats   = is_wp_error( $nanocat_cats ) ? 0 : (int) $nanocat_cats;
		$nanocat_role   = get_theme_mod( 'nanocat_profile_role', nanocat_get_default( 'profile_role' ) );
		$nanocat_bio    = get_theme_mod( 'nanocat_profile_bio', nanocat_get_default( 'profile_bio' ) );
		?>
		<section class="widget profile-card">
			<div class="profile-card__banner" aria-hidden="true">
				<span>&lt;/&gt;</span>
				<span class="profile-card__banner-spark">✦</span>
			</div>
			<div class="profile-card__avatar">
				<img src="<?php echo esc_url( $nanocat_avatar ); ?>" width="192" height="192" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy">
				<span class="profile-card__status" title="<?php esc_attr_e( '在线', 'nanocat' ); ?>"><span class="screen-reader-text"><?php esc_html_e( '在线', 'nanocat' ); ?></span></span>
			</div>
			<div class="profile-card__body">
				<h2 class="profile-card__name"><?php bloginfo( 'name' ); ?></h2>
				<p class="profile-card__role"><?php echo esc_html( $nanocat_role ); ?></p>
				<p class="profile-card__bio"><?php echo esc_html( $nanocat_bio ); ?></p>

				<div class="profile-card__stats">
					<div><strong><?php echo esc_html( number_format_i18n( $nanocat_total ) ); ?></strong><span><?php esc_html_e( '文章', 'nanocat' ); ?></span></div>
					<div><strong><?php echo esc_html( number_format_i18n( $nanocat_cats ) ); ?></strong><span><?php esc_html_e( '分类', 'nanocat' ); ?></span></div>
					<div><strong>∞</strong><span><?php esc_html_e( '咖啡', 'nanocat' ); ?></span></div>
				</div>

				<?php nanocat_social_links( 'social-links--profile' ); ?>
			</div>
		</section>

		<?php
		$nanocat_popular = new WP_Query(
			array(
				'posts_per_page'      => 4,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'orderby'             => array(
					'comment_count' => 'DESC',
					'date'          => 'DESC',
				),
			)
		);

		if ( $nanocat_popular->have_posts() ) :
			?>
			<section class="widget popular-widget">
				<div class="widget-heading">
					<p class="section-kicker"><?php esc_html_e( '// TRENDING', 'nanocat' ); ?></p>
					<h2 class="widget-title"><?php esc_html_e( '热门文章', 'nanocat' ); ?></h2>
				</div>
				<ol class="popular-list">
					<?php
					$nanocat_rank = 0;
					while ( $nanocat_popular->have_posts() ) :
						$nanocat_popular->the_post();
						$nanocat_rank++;
						$nanocat_comments = (int) get_comments_number();
						?>
						<li class="popular-item">
							<a href="<?php the_permalink(); ?>">
								<span class="popular-item__rank"><?php echo esc_html( str_pad( (string) $nanocat_rank, 2, '0', STR_PAD_LEFT ) ); ?></span>
								<span class="popular-item__copy">
									<span class="popular-item__title"><?php the_title(); ?></span>
									<span class="popular-item__meta">
										<?php
										if ( $nanocat_comments > 0 ) {
											printf(
												/* translators: %s: number of comments. */
												esc_html( _n( '%s 条评论', '%s 条评论', $nanocat_comments, 'nanocat' ) ),
												esc_html( number_format_i18n( $nanocat_comments ) )
											);
										} else {
											echo esc_html( get_the_date() );
										}
										?>
									</span>
								</span>
							</a>
						</li>
					<?php endwhile; ?>
				</ol>
				<?php wp_reset_postdata(); ?>
			</section>
		<?php endif; ?>

		<?php
		$nanocat_sidebar_topics = function_exists( 'nanocat_get_topics' ) ? nanocat_get_topics( 4, true ) : array();
		$nanocat_topics_page    = get_page_by_path( 'topics' );

		if ( $nanocat_sidebar_topics ) :
			?>
			<section class="widget sidebar-topics">
				<div class="widget-heading">
					<p class="section-kicker"><?php esc_html_e( '// READING_LISTS', 'nanocat' ); ?></p>
					<h2 class="widget-title"><?php esc_html_e( '专栏', 'nanocat' ); ?></h2>
				</div>
				<ul class="sidebar-topic-list">
					<?php foreach ( $nanocat_sidebar_topics as $nanocat_sidebar_topic ) : ?>
						<?php $nanocat_sidebar_topic_link = get_term_link( $nanocat_sidebar_topic ); ?>
						<?php if ( is_wp_error( $nanocat_sidebar_topic_link ) ) : ?>
							<?php continue; ?>
						<?php endif; ?>
						<li>
							<a href="<?php echo esc_url( $nanocat_sidebar_topic_link ); ?>">
								<?php nanocat_topic_icon( $nanocat_sidebar_topic ); ?>
								<span><?php echo esc_html( $nanocat_sidebar_topic->name ); ?></span>
								<strong><?php echo esc_html( number_format_i18n( $nanocat_sidebar_topic->count ) ); ?></strong>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ( $nanocat_topics_page instanceof WP_Post ) : ?>
					<a class="text-link sidebar-widget-link" href="<?php echo esc_url( get_permalink( $nanocat_topics_page ) ); ?>">
						<?php esc_html_e( '全部专栏', 'nanocat' ); ?>
						<?php nanocat_icon( 'arrow-right', 16 ); ?>
					</a>
				<?php endif; ?>
			</section>
		<?php endif; ?>

		<?php
		$nanocat_sidebar_wikis = function_exists( 'nanocat_get_wiki_roots' ) ? nanocat_get_wiki_roots( 3 ) : array();

		if ( $nanocat_sidebar_wikis && ! is_wp_error( get_post_type_archive_link( 'nanocat_wiki' ) ) ) :
			?>
			<section class="widget sidebar-wiki">
				<div class="widget-heading">
					<p class="section-kicker"><?php esc_html_e( '// WIKI', 'nanocat' ); ?></p>
					<h2 class="widget-title"><?php esc_html_e( '知识库', 'nanocat' ); ?></h2>
				</div>
				<ul class="sidebar-wiki-list">
					<?php foreach ( $nanocat_sidebar_wikis as $nanocat_sidebar_wiki ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $nanocat_sidebar_wiki ) ); ?>">
								<span><?php echo esc_html( get_the_title( $nanocat_sidebar_wiki ) ); ?></span>
								<strong><?php echo esc_html( number_format_i18n( nanocat_get_wiki_count( $nanocat_sidebar_wiki->ID ) ) ); ?><?php esc_html_e( ' 页', 'nanocat' ); ?></strong>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
				<a class="text-link sidebar-widget-link" href="<?php echo esc_url( get_post_type_archive_link( 'nanocat_wiki' ) ); ?>">
					<?php esc_html_e( '打开 Wiki', 'nanocat' ); ?>
					<?php nanocat_icon( 'arrow-right', 16 ); ?>
				</a>
			</section>
		<?php endif; ?>

		<?php
		ob_start();
		wp_tag_cloud(
			array(
				'number'  => 16,
				'smallest' => 12,
				'largest'  => 12,
				'unit'     => 'px',
				'orderby'  => 'count',
				'order'    => 'DESC',
			)
		);
		$nanocat_tags = trim( ob_get_clean() );

		if ( $nanocat_tags ) :
			?>
			<section class="widget tags-widget">
				<div class="widget-heading">
					<p class="section-kicker"><?php esc_html_e( '// TAGS', 'nanocat' ); ?></p>
					<h2 class="widget-title"><?php esc_html_e( '标签云', 'nanocat' ); ?></h2>
				</div>
				<div class="tag-cloud"><?php echo $nanocat_tags; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Generated by core. ?></div>
			</section>
		<?php endif; ?>

		<section class="sidebar-quote" aria-label="<?php esc_attr_e( '一言', 'nanocat' ); ?>">
			<span class="sidebar-quote__mark" aria-hidden="true">“</span>
			<p><?php esc_html_e( '不是所有代码都要有意义，有时只是为了逗猫。', 'nanocat' ); ?></p>
			<span class="sidebar-quote__byline">— NanoCat</span>
		</section>
		<?php
	}
	?>
</aside>
