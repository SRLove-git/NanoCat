<?php
/**
 * AI-first homepage: the archive begins as a question.
 *
 * @package NanoCat
 */

$nanocat_eyebrow     = get_theme_mod( 'nanocat_hero_eyebrow', nanocat_get_default( 'hero_eyebrow' ) );
$nanocat_title       = get_theme_mod( 'nanocat_hero_title', nanocat_get_default( 'hero_title' ) );
$nanocat_lead        = get_theme_mod( 'nanocat_hero_text', nanocat_get_default( 'hero_text' ) );
$nanocat_ai_url      = function_exists( 'nanocat_ai_page_url' ) ? nanocat_ai_page_url() : home_url( '/ai/' );
$nanocat_primary_txt = get_theme_mod( 'nanocat_hero_primary_label', nanocat_get_default( 'hero_primary_label' ) );
$nanocat_primary_url = get_theme_mod( 'nanocat_hero_primary_url', '' );
$nanocat_second_txt  = get_theme_mod( 'nanocat_hero_secondary_label', nanocat_get_default( 'hero_secondary_label' ) );
$nanocat_second_url  = get_theme_mod( 'nanocat_hero_secondary_url', '' );
$nanocat_primary_url = $nanocat_primary_url ? $nanocat_primary_url : '#latest-posts';
$nanocat_second_url  = $nanocat_second_url ? $nanocat_second_url : get_feed_link();

$nanocat_counts      = wp_count_posts( 'post' );
$nanocat_total_posts = isset( $nanocat_counts->publish ) ? (int) $nanocat_counts->publish : 0;
$nanocat_topic_count = wp_count_terms(
	array(
		'taxonomy'   => 'nanocat_topic',
		'hide_empty' => true,
	)
);
$nanocat_topic_count = is_wp_error( $nanocat_topic_count ) ? 0 : (int) $nanocat_topic_count;

$nanocat_prompts = array(
	__( '推荐一条从零写内核的路线', 'nanocat' ),
	__( '窗口服务是怎么实现的？', 'nanocat' ),
	__( '从构建到引导要读哪几篇？', 'nanocat' ),
	__( '用三句话总结窗口服务这一篇', 'nanocat' ),
);
?>
<section class="home-ai" id="ask" aria-labelledby="home-ai-title">
	<div class="container home-ai__inner">
		<div class="home-ai__masthead">
			<span class="home-ai__issue"><?php esc_html_e( 'NANOCAT / ISSUE 03', 'nanocat' ); ?></span>
			<span class="home-ai__rule" aria-hidden="true"></span>
			<span class="home-ai__stamp"><?php esc_html_e( 'AN AI-NATIVE JOURNAL', 'nanocat' ); ?></span>
			<span class="home-ai__live"><i class="status-dot" aria-hidden="true"></i><?php esc_html_e( 'RETRIEVAL ONLINE', 'nanocat' ); ?></span>
		</div>

		<div class="home-ai__stage">
			<p class="home-ai__kicker">
				<?php nanocat_icon( 'sparkle', 15 ); ?>
				<span><?php echo esc_html( $nanocat_eyebrow ); ?></span>
				<span class="home-ai__kicker-rule" aria-hidden="true"></span>
				<span><?php esc_html_e( 'ASK THE ARCHIVE', 'nanocat' ); ?></span>
			</p>

			<h1 class="home-ai__title" id="home-ai-title"><?php echo wp_kses_post( $nanocat_title ); ?></h1>
			<p class="home-ai__lead"><?php echo esc_html( $nanocat_lead ); ?></p>

			<div class="ai-console home-ai__console" data-ai-console data-ai-context="home">
				<form class="ai-composer home-ai__composer" data-ai-form>
					<label class="screen-reader-text" for="nanocat-home-question"><?php esc_html_e( '问这个博客', 'nanocat' ); ?></label>
					<div class="ai-composer__field">
						<span class="ai-composer__spark" aria-hidden="true">✦</span>
						<textarea id="nanocat-home-question" data-ai-input rows="1" maxlength="800" placeholder="<?php esc_attr_e( '问点什么？', 'nanocat' ); ?>" required></textarea>
						<button class="ai-composer__send" type="submit" data-ai-send aria-label="<?php esc_attr_e( '向 NanoCat AI 提问', 'nanocat' ); ?>">
							<?php nanocat_icon( 'arrow-up', 18 ); ?>
						</button>
						<button class="ai-composer__stop" type="button" data-ai-stop hidden><?php esc_html_e( '停止', 'nanocat' ); ?></button>
					</div>
					<div class="ai-composer__meta">
						<span><kbd>Enter</kbd> <?php esc_html_e( '发送', 'nanocat' ); ?> <span aria-hidden="true">·</span> <kbd>Shift</kbd> + <kbd>Enter</kbd> <?php esc_html_e( '换行', 'nanocat' ); ?></span>
						<span class="ai-composer__note"><?php esc_html_e( '只检索本站 · 回答带来源', 'nanocat' ); ?></span>
					</div>
				</form>

				<div class="home-ai__suggestions" aria-label="<?php esc_attr_e( '试试这些问题', 'nanocat' ); ?>">
					<?php foreach ( $nanocat_prompts as $nanocat_prompt ) : ?>
						<button type="button" class="home-ai__suggestion" data-ai-suggest="<?php echo esc_attr( $nanocat_prompt ); ?>">
							<span aria-hidden="true">↗</span>
							<?php echo esc_html( $nanocat_prompt ); ?>
						</button>
					<?php endforeach; ?>
				</div>

				<div class="ai-console__stream home-ai__stream">
					<div class="ai-thread" data-ai-thread aria-live="polite" aria-relevant="additions text"></div>
				</div>

				<div class="ai-console__foot home-ai__foot">
					<span><i class="status-dot" aria-hidden="true"></i><?php esc_html_e( 'NanoCat AI / retrieval online', 'nanocat' ); ?></span>
					<button class="ai-console__clear" type="button" data-ai-clear><?php esc_html_e( '清空对话', 'nanocat' ); ?></button>
				</div>
			</div>

			<div class="home-ai__meta">
				<div class="home-ai__links">
					<a href="<?php echo esc_url( $nanocat_primary_url ); ?>"><?php echo esc_html( $nanocat_primary_txt ); ?> <?php nanocat_icon( 'arrow-right', 13 ); ?></a>
					<a href="<?php echo esc_url( $nanocat_ai_url ); ?>"><?php esc_html_e( 'AI 工作台', 'nanocat' ); ?> <?php nanocat_icon( 'arrow-up-right', 13 ); ?></a>
					<a href="<?php echo esc_url( $nanocat_second_url ); ?>"><?php nanocat_icon( 'rss', 13 ); ?> <?php echo esc_html( $nanocat_second_txt ); ?></a>
				</div>
				<span class="home-ai__count">
					<?php
					printf(
						/* translators: 1: post count, 2: topic count. */
						esc_html__( '%1$s 篇笔记 · %2$s 个专栏 · 1 个 AI', 'nanocat' ),
						esc_html( number_format_i18n( $nanocat_total_posts ) ),
						esc_html( number_format_i18n( $nanocat_topic_count ) )
					);
					?>
				</span>
			</div>
		</div>

		<div class="home-ai__footer">
			<a class="home-ai__scroll" href="#latest-posts"><?php esc_html_e( 'INDEX OF NOTES', 'nanocat' ); ?> <span aria-hidden="true">↓</span></a>
			<span><?php esc_html_e( 'WORDPRESS · SEA GLASS · RETRIEVAL AUGMENTED', 'nanocat' ); ?></span>
			<a href="<?php echo esc_url( $nanocat_ai_url ); ?>"><?php esc_html_e( 'OPEN NANOCAT AI', 'nanocat' ); ?> <?php nanocat_icon( 'arrow-right', 14 ); ?></a>
		</div>
	</div>
</section>

