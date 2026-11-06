<?php
/**
 * Article AI companion.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) || ! function_exists( 'nanocat_ai_is_active' ) || ! nanocat_ai_is_active() ) {
	return;
}

if ( ! is_singular( 'post' ) ) {
	return;
}

$nanocat_ai_post_id    = (int) get_queried_object_id();
$nanocat_ai_post_title = wp_strip_all_tags( get_the_title( $nanocat_ai_post_id ) );
$nanocat_ai_prompts    = array(
	array(
		'label'  => __( '三句话总结', 'nanocat' ),
		'prompt' => sprintf( __( '用三句话总结《%s》的核心内容。', 'nanocat' ), $nanocat_ai_post_title ),
	),
	array(
		'label'  => __( '列出要点', 'nanocat' ),
		'prompt' => sprintf( __( '列出《%s》最重要的 5 个要点。', 'nanocat' ), $nanocat_ai_post_title ),
	),
	array(
		'label'  => __( '讲人话', 'nanocat' ),
		'prompt' => sprintf( __( '用更通俗、更少术语的话解释《%s》。', 'nanocat' ), $nanocat_ai_post_title ),
	),
	array(
		'label'  => __( '考考我', 'nanocat' ),
		'prompt' => sprintf( __( '围绕《%s》出 3 个问题考考我，先不要给答案。', 'nanocat' ), $nanocat_ai_post_title ),
	),
);
?>
<section class="ai-companion" data-ai-companion data-post-id="<?php echo esc_attr( $nanocat_ai_post_id ); ?>" data-post-title="<?php echo esc_attr( $nanocat_ai_post_title ); ?>" aria-labelledby="ai-companion-title">
	<div class="ai-companion__head">
		<div class="ai-companion__brand">
			<span class="ai-companion__mark" aria-hidden="true">✦</span>
			<span class="ai-companion__kicker"><?php esc_html_e( 'NANO AI / 伴读', 'nanocat' ); ?></span>
			<h2 id="ai-companion-title" class="ai-companion__title"><?php esc_html_e( '先读这一段，再决定要不要读全文。', 'nanocat' ); ?></h2>
		</div>
		<span class="ai-companion__engine"><i aria-hidden="true"></i><?php esc_html_e( 'RAG ONLINE', 'nanocat' ); ?></span>
	</div>

	<div class="ai-companion__summary" data-ai-summary>
		<span class="ai-skeleton" aria-hidden="true"></span>
		<span class="ai-skeleton ai-skeleton--short" aria-hidden="true"></span>
		<span class="screen-reader-text"><?php esc_html_e( 'AI 正在阅读这篇文章', 'nanocat' ); ?></span>
	</div>

	<div class="ai-companion__actions" aria-label="<?php esc_attr_e( 'AI 伴读操作', 'nanocat' ); ?>">
		<?php foreach ( $nanocat_ai_prompts as $nanocat_ai_prompt ) : ?>
			<button class="ai-chip" type="button" data-ai-prompt="<?php echo esc_attr( $nanocat_ai_prompt['prompt'] ); ?>">
				<?php echo esc_html( $nanocat_ai_prompt['label'] ); ?>
			</button>
		<?php endforeach; ?>
		<button class="ai-chip ai-chip--ghost" type="button" data-ai-prompt="<?php echo esc_attr( sprintf( __( '关于《%s》，我还想问：', 'nanocat' ), $nanocat_ai_post_title ) ); ?>">
			<?php esc_html_e( '继续追问', 'nanocat' ); ?>
			<?php nanocat_icon( 'arrow-right', 13 ); ?>
		</button>
	</div>
</section>
