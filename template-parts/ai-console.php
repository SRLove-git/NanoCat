<?php
/**
 * Reusable AI console: stream, starter prompts and composer.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nanocat_ai_console_title = isset( $args['title'] ) ? $args['title'] : __( '从一个具体问题开始。', 'nanocat' );
$nanocat_ai_console_lead  = isset( $args['lead'] ) ? $args['lead'] : __( '它只从本站文章里找线索，回答会附上出处。问技术、问路线，也可以让它把一篇长文讲短。', 'nanocat' );
$nanocat_ai_context       = isset( $args['context'] ) ? $args['context'] : 'dialog';
$nanocat_ai_suggestions   = isset( $args['suggestions'] ) && is_array( $args['suggestions'] ) ? $args['suggestions'] : array(
	__( '推荐一条从零写内核的阅读路线', 'nanocat' ),
	__( '窗口服务是怎么实现的？', 'nanocat' ),
	__( '这个博客最近在写什么？', 'nanocat' ),
	__( 'WordPress 主题开发从哪里开始？', 'nanocat' ),
);
$nanocat_ai_field_id      = wp_unique_id( 'nanocat-ai-field-' );
?>
<div class="ai-console" data-ai-console data-ai-context="<?php echo esc_attr( $nanocat_ai_context ); ?>">
	<div class="ai-console__stream">
		<div class="ai-console__intro" data-ai-intro>
			<p class="ai-console__kicker"><span aria-hidden="true">✦</span> <?php esc_html_e( '// ASK THE ARCHIVE', 'nanocat' ); ?></p>
			<h2 class="ai-console__title"><?php echo esc_html( $nanocat_ai_console_title ); ?></h2>
			<p class="ai-console__lead"><?php echo esc_html( $nanocat_ai_console_lead ); ?></p>

			<div class="ai-suggestions" aria-label="<?php esc_attr_e( '试试这样问', 'nanocat' ); ?>">
				<?php foreach ( $nanocat_ai_suggestions as $nanocat_ai_suggestion ) : ?>
					<button class="ai-suggestion" type="button" data-ai-suggest="<?php echo esc_attr( $nanocat_ai_suggestion ); ?>">
						<span aria-hidden="true">↗</span>
						<?php echo esc_html( $nanocat_ai_suggestion ); ?>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<div class="ai-thread" data-ai-thread aria-live="polite" aria-relevant="additions text"></div>
	</div>

	<form class="ai-composer" data-ai-form>
		<label class="screen-reader-text" for="<?php echo esc_attr( $nanocat_ai_field_id ); ?>"><?php esc_html_e( '向 NanoCat AI 提问', 'nanocat' ); ?></label>
		<div class="ai-composer__field">
			<span class="ai-composer__spark" aria-hidden="true">✦</span>
			<textarea id="<?php echo esc_attr( $nanocat_ai_field_id ); ?>" data-ai-input rows="1" maxlength="800" placeholder="<?php esc_attr_e( '问点什么，比如：窗口服务是怎么实现的？', 'nanocat' ); ?>" required></textarea>
			<button class="ai-composer__send" type="submit" data-ai-send aria-label="<?php esc_attr_e( '发送问题', 'nanocat' ); ?>">
				<?php nanocat_icon( 'arrow-up', 17 ); ?>
			</button>
			<button class="ai-composer__stop" type="button" data-ai-stop hidden><?php esc_html_e( '停止', 'nanocat' ); ?></button>
		</div>
		<div class="ai-composer__meta">
			<span><kbd>Enter</kbd> <?php esc_html_e( '发送', 'nanocat' ); ?> <span aria-hidden="true">·</span> <kbd>Shift</kbd> + <kbd>Enter</kbd> <?php esc_html_e( '换行', 'nanocat' ); ?></span>
			<span class="ai-composer__note"><?php esc_html_e( '基于站内文章 · AI 也会犯错', 'nanocat' ); ?></span>
		</div>
	</form>

	<div class="ai-console__foot">
		<span><span class="status-dot" aria-hidden="true"></span><?php esc_html_e( 'NanoCat AI / retrieval online', 'nanocat' ); ?></span>
		<button class="ai-console__clear" type="button" data-ai-clear><?php esc_html_e( '清空对话', 'nanocat' ); ?></button>
	</div>
</div>
