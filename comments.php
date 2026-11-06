<?php
/**
 * Comments template.
 *
 * @package NanoCat
 */

if ( post_password_required() ) {
	return;
}

$nanocat_comment_count = (int) get_comments_number();
?>
<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<div class="comments-heading">
			<div>
				<p class="section-kicker"><?php esc_html_e( '// DISCUSS', 'nanocat' ); ?></p>
				<h2 class="comments-title">
					<?php esc_html_e( '评论', 'nanocat' ); ?>
					<span class="comments-title__count"><?php echo esc_html( number_format_i18n( $nanocat_comment_count ) ); ?></span>
				</h2>
			</div>
			<a class="comments-feed" href="<?php echo esc_url( get_post_comments_feed_link() ); ?>">
				<?php nanocat_icon( 'rss', 15 ); ?>
				<span><?php esc_html_e( '评论 RSS', 'nanocat' ); ?></span>
			</a>
		</div>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 48,
					'callback'    => 'nanocat_comment',
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text'          => nanocat_get_icon( 'arrow-left', 16 ) . '<span>' . esc_html__( '较早评论', 'nanocat' ) . '</span>',
				'next_text'          => '<span>' . esc_html__( '较新评论', 'nanocat' ) . '</span>' . nanocat_get_icon( 'arrow-right', 16 ),
				'screen_reader_text' => __( '评论分页', 'nanocat' ),
				'aria_label'         => __( '评论分页', 'nanocat' ),
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && $nanocat_comment_count > 0 && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( '评论已关闭，但欢迎你随时回来看看。', 'nanocat' ); ?></p>
	<?php endif; ?>

	<?php
	$nanocat_commenter  = wp_get_current_commenter();
	$nanocat_required   = get_option( 'require_name_email' );
	$nanocat_required_mark = $nanocat_required ? ' <span class="required" aria-hidden="true">*</span>' : '';
	$nanocat_aria_required = $nanocat_required ? ' aria-required="true" required' : '';

	$nanocat_fields = array(
		'author' => '<p class="comment-form-author">' .
			'<label for="author">' . esc_html__( '昵称', 'nanocat' ) . $nanocat_required_mark . '</label>' .
			'<input id="author" name="author" type="text" value="' . esc_attr( $nanocat_commenter['comment_author'] ) . '" size="30" maxlength="245" autocomplete="name"' . $nanocat_aria_required . ' placeholder="' . esc_attr__( '怎么称呼你？', 'nanocat' ) . '">' .
			'</p>',
		'email'  => '<p class="comment-form-email">' .
			'<label for="email">' . esc_html__( '邮箱', 'nanocat' ) . $nanocat_required_mark . '</label>' .
			'<input id="email" name="email" type="email" value="' . esc_attr( $nanocat_commenter['comment_author_email'] ) . '" size="30" maxlength="100" autocomplete="email"' . $nanocat_aria_required . ' placeholder="' . esc_attr__( 'you@example.com', 'nanocat' ) . '">' .
			'</p>',
		'url'    => '<p class="comment-form-url">' .
			'<label for="url">' . esc_html__( '网站', 'nanocat' ) . '</label>' .
			'<input id="url" name="url" type="url" value="' . esc_attr( $nanocat_commenter['comment_author_url'] ) . '" size="30" maxlength="200" autocomplete="url" placeholder="' . esc_attr__( 'https://', 'nanocat' ) . '">' .
			'</p>',
	);

	$nanocat_notes = __( '邮箱只用于接收回复通知，不会被公开。', 'nanocat' );
	if ( $nanocat_required ) {
		$nanocat_notes .= ' ' . __( '带 * 的项为必填项。', 'nanocat' );
	}

	comment_form(
		array(
			'class_form'           => 'comment-form',
			'title_reply'          => __( '留下评论', 'nanocat' ),
			'title_reply_before'   => '<div class="comment-reply-title-wrap"><p class="section-kicker">// LEAVE A NOTE</p><h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'    => '</h2></div>',
			'cancel_reply_before'  => '<span class="comment-cancel-reply">',
			'cancel_reply_after'   => '</span>',
			'comment_notes_before' => '<p class="comment-notes">' . esc_html( $nanocat_notes ) . '</p>',
			'comment_notes_after'  => '',
			'comment_field'        => '<p class="comment-form-comment">' .
				'<label for="comment">' . esc_html__( '评论', 'nanocat' ) . ' <span class="required" aria-hidden="true">*</span></label>' .
				'<textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" required placeholder="' . esc_attr__( '写点什么吧，友善的猫更受欢迎。', 'nanocat' ) . '"></textarea>' .
				'</p>',
			'fields'               => $nanocat_fields,
			'label_submit'         => __( '发表评论', 'nanocat' ),
			'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s">',
			'submit_field'         => '<p class="form-submit">%1$s %2$s</p>',
			'class_submit'         => 'button button--primary',
			'format'               => 'html5',
		)
	);
	?>
</div>
