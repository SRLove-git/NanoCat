<?php
/**
 * Template Name: AI 工作台
 *
 * A full-page, conversational interface to the site archive.
 *
 * @package NanoCat
 */

get_header();

$nanocat_ai_counts = wp_count_posts( 'post' );
$nanocat_ai_total  = isset( $nanocat_ai_counts->publish ) ? (int) $nanocat_ai_counts->publish : 0;
$nanocat_ai_topics = function_exists( 'nanocat_get_topics' ) ? nanocat_get_topics( 6 ) : array();
$nanocat_ai_wikis  = function_exists( 'nanocat_get_wiki_roots' ) ? nanocat_get_wiki_roots( 4 ) : array();
$nanocat_ai_latest = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 4,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
$nanocat_ai_blog_url = get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/' );
$nanocat_ai_wiki_url = get_post_type_archive_link( 'nanocat_wiki' );
?>
<main id="primary" class="site-main ai-page-view">
	<section class="ai-page-hero">
		<div class="container ai-page-hero__inner">
			<div class="ai-page-hero__copy" data-reveal>
				<p class="section-kicker"><span aria-hidden="true">✦</span> <?php esc_html_e( '// NANOCAT INTELLIGENCE', 'nanocat' ); ?></p>
				<h1 class="ai-page-hero__title"><?php esc_html_e( '把博客，', 'nanocat' ); ?><br><span><?php esc_html_e( '变成对话。', 'nanocat' ); ?></span></h1>
				<p class="ai-page-hero__lead"><?php esc_html_e( '一个只读本站的 AI。它会翻专栏、Wiki 和每篇文章，再把线索拼成回答。问具体一点，它会更聪明。', 'nanocat' ); ?></p>
				<ul class="ai-page-hero__facts">
					<li><strong><?php echo esc_html( number_format_i18n( $nanocat_ai_total ) ); ?></strong><span><?php esc_html_e( '篇文章', 'nanocat' ); ?></span></li>
					<li><strong><?php echo esc_html( number_format_i18n( count( $nanocat_ai_topics ) ) ); ?></strong><span><?php esc_html_e( '个专栏', 'nanocat' ); ?></span></li>
					<li><strong><?php echo esc_html( number_format_i18n( count( $nanocat_ai_wikis ) ) ); ?></strong><span><?php esc_html_e( '个 Wiki 项目', 'nanocat' ); ?></span></li>
				</ul>
			</div>

			<div class="ai-page-hero__visual" aria-hidden="true" data-reveal>
				<div class="ai-orbit">
					<span class="ai-orbit__ring ai-orbit__ring--one"></span>
					<span class="ai-orbit__ring ai-orbit__ring--two"></span>
					<span class="ai-orbit__core"><span>✦</span></span>
					<span class="ai-orbit__label ai-orbit__label--one">TOPICS</span>
					<span class="ai-orbit__label ai-orbit__label--two">WIKI</span>
					<span class="ai-orbit__label ai-orbit__label--three">POSTS</span>
					<img class="ai-orbit__mascot" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/mascot.svg' ); ?>" alt="" width="88" height="88" loading="lazy" decoding="async">
				</div>
				<p class="ai-page-hero__visual-note"><span class="status-dot" aria-hidden="true"></span><?php esc_html_e( 'RETRIEVAL AUGMENTED · ALWAYS ON', 'nanocat' ); ?></p>
			</div>
		</div>
	</section>

	<div class="container ai-page-shell">
		<div class="ai-page-main">
			<?php
			get_template_part(
				'template-parts/ai-console',
				null,
				array(
					'context' => 'page',
					'title'   => __( '今天想从博客里知道什么？', 'nanocat' ),
					'lead'    => __( '例如：窗口服务怎么实现、推荐一条内核学习路线、或者让 AI 用三句话总结一篇文章。', 'nanocat' ),
				)
			);
			?>
		</div>

		<aside class="ai-page-side" aria-label="<?php esc_attr_e( 'AI 工作台侧栏', 'nanocat' ); ?>">
			<section class="ai-side-card">
				<p class="section-kicker">// KNOWLEDGE MAP</p>
				<h2><?php esc_html_e( '知识地图', 'nanocat' ); ?></h2>

				<?php if ( $nanocat_ai_topics ) : ?>
					<div class="ai-side-group">
						<span class="ai-side-label"><?php esc_html_e( '专栏', 'nanocat' ); ?></span>
						<ul class="ai-side-list">
							<?php foreach ( $nanocat_ai_topics as $nanocat_ai_topic ) : ?>
								<?php
								$nanocat_ai_topic_link = get_term_link( $nanocat_ai_topic );
								if ( is_wp_error( $nanocat_ai_topic_link ) ) {
									continue;
								}
								?>
								<li>
									<a href="<?php echo esc_url( $nanocat_ai_topic_link ); ?>">
										<span><?php echo esc_html( $nanocat_ai_topic->name ); ?></span>
										<em><?php echo esc_html( number_format_i18n( $nanocat_ai_topic->count ) ); ?></em>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<?php if ( $nanocat_ai_wikis ) : ?>
					<div class="ai-side-group">
						<span class="ai-side-label"><?php esc_html_e( 'Wiki', 'nanocat' ); ?></span>
						<ul class="ai-side-list">
							<?php foreach ( $nanocat_ai_wikis as $nanocat_ai_wiki ) : ?>
								<li>
									<a href="<?php echo esc_url( get_permalink( $nanocat_ai_wiki ) ); ?>">
										<span><?php echo esc_html( get_the_title( $nanocat_ai_wiki ) ); ?></span>
										<em><?php echo esc_html( number_format_i18n( nanocat_get_wiki_count( $nanocat_ai_wiki->ID ) ) ); ?></em>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</section>

			<section class="ai-side-card">
				<p class="section-kicker">// RECENT QUESTIONS</p>
				<h2><?php esc_html_e( '最近提问', 'nanocat' ); ?></h2>
				<div class="ai-history" data-ai-history>
					<p class="ai-history__empty"><?php esc_html_e( '还没有提问。上面的问题会保存在这台设备上。', 'nanocat' ); ?></p>
				</div>
			</section>

			<section class="ai-side-card ai-side-card--note">
				<p class="section-kicker">// HOW IT WORKS</p>
				<h2><?php esc_html_e( '它不是通用聊天机器人', 'nanocat' ); ?></h2>
				<p><?php esc_html_e( '每次回答都会先在站内检索相关文章，再生成带出处的答案。没有找到依据时，它应该承认不知道。', 'nanocat' ); ?></p>
				<?php if ( $nanocat_ai_wiki_url ) : ?>
					<a class="text-link" href="<?php echo esc_url( $nanocat_ai_wiki_url ); ?>"><?php esc_html_e( '翻翻 Wiki', 'nanocat' ); ?> <?php nanocat_icon( 'arrow-right', 14 ); ?></a>
				<?php endif; ?>
			</section>
		</aside>
	</div>

	<section class="container ai-page-latest">
		<div class="section-heading">
			<div>
				<p class="section-kicker">// FRESH CONTEXT</p>
				<h2 class="section-title"><?php esc_html_e( '可以拿来问的最新文章', 'nanocat' ); ?></h2>
			</div>
			<a class="text-link" href="<?php echo esc_url( $nanocat_ai_blog_url ); ?>"><?php esc_html_e( '全部文章', 'nanocat' ); ?> <?php nanocat_icon( 'arrow-right', 14 ); ?></a>
		</div>

		<?php if ( $nanocat_ai_latest->have_posts() ) : ?>
			<div class="ai-latest-grid">
				<?php
				while ( $nanocat_ai_latest->have_posts() ) :
					$nanocat_ai_latest->the_post();
					?>
					<a class="ai-latest-card" href="<?php the_permalink(); ?>">
						<span class="ai-latest-card__meta"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?> · <?php echo esc_html( nanocat_get_reading_time() ); ?> min</span>
						<strong><?php the_title(); ?></strong>
						<span class="ai-latest-card__arrow"><?php nanocat_icon( 'arrow-right', 14 ); ?></span>
					</a>
				<?php endwhile; ?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php endif; ?>
	</section>
</main>
<?php get_footer(); ?>
