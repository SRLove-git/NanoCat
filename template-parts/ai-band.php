<?php
/**
 * Homepage AI entry band.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) || ! function_exists( 'nanocat_ai_is_active' ) || ! nanocat_ai_is_active() ) {
	return;
}

$nanocat_ai_counts  = wp_count_posts( 'post' );
$nanocat_ai_total   = isset( $nanocat_ai_counts->publish ) ? (int) $nanocat_ai_counts->publish : 0;
$nanocat_ai_page    = nanocat_ai_page_url();
$nanocat_ai_prompts = array(
	__( '这个博客适合先读哪篇？', 'nanocat' ),
	__( '推荐一条从零写内核的路线', 'nanocat' ),
	__( '最近在折腾什么？', 'nanocat' ),
);
?>
<section class="ai-band" aria-labelledby="ai-band-title">
	<div class="container ai-band__inner">
		<div class="ai-band__copy">
			<p class="ai-band__kicker"><span aria-hidden="true">✦</span> <?php esc_html_e( 'NANOCAT INTELLIGENCE', 'nanocat' ); ?></p>
			<h2 id="ai-band-title" class="ai-band__title"><?php esc_html_e( '不只翻标题，直接问整个博客。', 'nanocat' ); ?></h2>
		</div>

		<div class="ai-band__console">
			<button class="ai-band__ask" type="button" data-ai-open data-ai-prompt="<?php esc_attr_e( '这个博客主要写什么？我应该从哪篇开始读？', 'nanocat' ); ?>">
				<span class="ai-band__spark" aria-hidden="true">✦</span>
				<span class="ai-band__placeholder"><?php esc_html_e( '问 AI：这个博客适合先读哪篇？', 'nanocat' ); ?></span>
				<kbd>Ctrl K</kbd>
			</button>
			<div class="ai-band__suggestions">
				<?php foreach ( $nanocat_ai_prompts as $nanocat_ai_prompt ) : ?>
					<button type="button" data-ai-open data-ai-prompt="<?php echo esc_attr( $nanocat_ai_prompt ); ?>"><?php echo esc_html( $nanocat_ai_prompt ); ?></button>
				<?php endforeach; ?>
			</div>
		</div>

		<a class="ai-band__more" href="<?php echo esc_url( $nanocat_ai_page ); ?>">
			<span>
				<?php
				printf(
					/* translators: %s: number of posts. */
					esc_html__( '%s 篇笔记 · AI 工作台', 'nanocat' ),
					esc_html( number_format_i18n( $nanocat_ai_total ) )
				);
				?>
			</span>
			<?php nanocat_icon( 'arrow-right', 15 ); ?>
		</a>
	</div>
</section>
