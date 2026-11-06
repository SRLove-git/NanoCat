<?php
/**
 * Homepage hero: the blog as a conversation.
 *
 * @package NanoCat
 */

$nanocat_eyebrow     = get_theme_mod( 'nanocat_hero_eyebrow', nanocat_get_default( 'hero_eyebrow' ) );
$nanocat_title       = get_theme_mod( 'nanocat_hero_title', nanocat_get_default( 'hero_title' ) );
$nanocat_lead        = get_theme_mod( 'nanocat_hero_text', nanocat_get_default( 'hero_text' ) );
$nanocat_primary_txt = get_theme_mod( 'nanocat_hero_primary_label', nanocat_get_default( 'hero_primary_label' ) );
$nanocat_primary_url = get_theme_mod( 'nanocat_hero_primary_url', '' );
$nanocat_second_txt  = get_theme_mod( 'nanocat_hero_secondary_label', nanocat_get_default( 'hero_secondary_label' ) );
$nanocat_second_url  = get_theme_mod( 'nanocat_hero_secondary_url', '' );

$nanocat_hero_image_dark  = get_theme_mod( 'nanocat_hero_image', get_template_directory_uri() . '/assets/images/hero-night.svg' );
$nanocat_hero_image_light = get_theme_mod( 'nanocat_hero_image_light', get_template_directory_uri() . '/assets/images/hero-light.svg' );
$nanocat_hero_image_light = $nanocat_hero_image_light ? $nanocat_hero_image_light : $nanocat_hero_image_dark;

$nanocat_primary_url = $nanocat_primary_url ? $nanocat_primary_url : '#latest-posts';
$nanocat_second_url  = $nanocat_second_url ? $nanocat_second_url : get_feed_link();
$nanocat_ai_url      = function_exists( 'nanocat_ai_page_url' ) ? nanocat_ai_page_url() : home_url( '/ai/' );

$nanocat_counts      = wp_count_posts( 'post' );
$nanocat_total_posts = isset( $nanocat_counts->publish ) ? (int) $nanocat_counts->publish : 0;
$nanocat_topic_count = wp_count_terms(
	array(
		'taxonomy'   => 'nanocat_topic',
		'hide_empty' => true,
	)
);
$nanocat_topic_count = is_wp_error( $nanocat_topic_count ) ? 0 : (int) $nanocat_topic_count;

$nanocat_suggestions = array(
	__( '推荐一条从零写内核的路线', 'nanocat' ),
	__( '窗口服务是怎么实现的？', 'nanocat' ),
	__( '这个博客最近在写什么？', 'nanocat' ),
	__( 'WordPress 主题开发从哪里开始？', 'nanocat' ),
);
?>
<section class="hero hero--cover hero--ai" aria-labelledby="hero-title">
	<div class="hero__backdrop" aria-hidden="true">
		<img class="hero__image hero__image--light" src="<?php echo esc_url( $nanocat_hero_image_light ); ?>" alt="" fetchpriority="high" decoding="async">
		<img class="hero__image hero__image--dark" src="<?php echo esc_url( $nanocat_hero_image_dark ); ?>" alt="" decoding="async">
		<span class="hero__scrim"></span>
		<span class="hero__scanlines"></span>
	</div>

	<div class="container hero__inner hero__inner--ai">
		<div class="hero__masthead" data-reveal>
			<span class="hero__issue"><?php esc_html_e( 'ISSUE', 'nanocat' ); ?> <strong>02</strong></span>
			<span class="hero__masthead-rule" aria-hidden="true"></span>
			<span class="hero__masthead-label"><?php esc_html_e( 'NANOCAT INTELLIGENCE / 2026', 'nanocat' ); ?></span>
			<span class="hero__masthead-status">
				<span class="status-dot" aria-hidden="true"></span>
				<?php esc_html_e( 'AI ONLINE', 'nanocat' ); ?>
			</span>
		</div>

		<div class="hero-ai" data-reveal>
			<p class="hero-ai__eyebrow">
				<?php nanocat_icon( 'sparkle', 15 ); ?>
				<span><?php echo esc_html( $nanocat_eyebrow ); ?></span>
				<span class="hero-ai__eyebrow-rule" aria-hidden="true"></span>
				<span class="hero-ai__eyebrow-note"><?php esc_html_e( 'ASK THE ARCHIVE', 'nanocat' ); ?></span>
			</p>

			<h1 id="hero-title" class="hero-ai__title">
				<?php echo wp_kses_post( $nanocat_title ); ?>
			</h1>

			<p class="hero-ai__lead"><?php echo esc_html( $nanocat_lead ); ?></p>

			<form class="hero-ai__ask" data-ai-hero-form>
				<span class="hero-ai__ask-icon" aria-hidden="true"><?php nanocat_icon( 'sparkle', 22 ); ?></span>
				<label class="screen-reader-text" for="nanocat-hero-question"><?php esc_html_e( '问整个博客', 'nanocat' ); ?></label>
				<input id="nanocat-hero-question" type="search" name="q" data-ai-hero-input autocomplete="off" placeholder="<?php esc_attr_e( '问点什么：窗口服务怎么实现？/ 最近在写什么？', 'nanocat' ); ?>">
				<button type="submit" class="hero-ai__submit" aria-label="<?php esc_attr_e( '向 NanoCat AI 提问', 'nanocat' ); ?>">
					<span><?php esc_html_e( '提问', 'nanocat' ); ?></span>
					<?php nanocat_icon( 'arrow-right', 18 ); ?>
				</button>
			</form>

			<div class="hero-ai__suggestions" aria-label="<?php esc_attr_e( '试试这些问题', 'nanocat' ); ?>">
				<?php foreach ( $nanocat_suggestions as $nanocat_suggestion ) : ?>
					<button type="button" class="hero-ai__suggestion" data-ai-hero-prompt="<?php echo esc_attr( $nanocat_suggestion ); ?>">
						<?php nanocat_icon( 'arrow-right', 13 ); ?>
						<span><?php echo esc_html( $nanocat_suggestion ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="hero-ai__meta">
				<div class="hero-ai__links">
					<?php if ( $nanocat_primary_txt ) : ?>
						<a href="<?php echo esc_url( $nanocat_primary_url ); ?>"><?php echo esc_html( $nanocat_primary_txt ); ?> <?php nanocat_icon( 'arrow-right', 13 ); ?></a>
					<?php endif; ?>
					<a href="<?php echo esc_url( $nanocat_ai_url ); ?>"><?php esc_html_e( 'AI 工作台', 'nanocat' ); ?> <?php nanocat_icon( 'arrow-up-right', 13 ); ?></a>
					<?php if ( $nanocat_second_txt ) : ?>
						<a href="<?php echo esc_url( $nanocat_second_url ); ?>"><?php nanocat_icon( 'rss', 13 ); ?> <?php echo esc_html( $nanocat_second_txt ); ?></a>
					<?php endif; ?>
				</div>
				<span class="hero-ai__count">
					<?php
					printf(
						/* translators: 1: post count, 2: topic count. */
						esc_html__( '%1$s 篇笔记 · %2$s 个专栏', 'nanocat' ),
						esc_html( number_format_i18n( $nanocat_total_posts ) ),
						esc_html( number_format_i18n( $nanocat_topic_count ) )
					);
					?>
				</span>
			</div>
		</div>

		<div class="hero__footer">
			<a class="hero__scroll" href="#latest-posts" aria-label="<?php esc_attr_e( '向下滚动到最新文章', 'nanocat' ); ?>">
				<span><?php esc_html_e( 'SCROLL TO READ', 'nanocat' ); ?></span>
				<i aria-hidden="true"></i>
			</a>
			<span class="hero__footer-meta"><?php esc_html_e( 'WORDPRESS · AZURE · RETRIEVAL AUGMENTED', 'nanocat' ); ?></span>
			<a class="hero__footer-link" href="<?php echo esc_url( $nanocat_ai_url ); ?>">
				<?php esc_html_e( 'OPEN NANOCAT AI', 'nanocat' ); ?>
				<?php nanocat_icon( 'arrow-right', 14 ); ?>
			</a>
		</div>
	</div>
</section>
