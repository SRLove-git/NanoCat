<?php
/**
 * Empty state.
 *
 * @package NanoCat
 */
?>
<section class="no-results">
	<p class="section-kicker"><?php esc_html_e( '// NOTHING HERE BUT CAT HAIR', 'nanocat' ); ?></p>
	<h2 class="no-results__title"><?php esc_html_e( '这里暂时安静得只剩下猫毛。', 'nanocat' ); ?></h2>
	<p class="no-results__text"><?php esc_html_e( '没有找到需要的内容。换个关键词，或者回到首页继续逛逛吧。', 'nanocat' ); ?></p>
	<?php get_search_form(); ?>
	<a class="text-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		<?php nanocat_icon( 'arrow-left', 15 ); ?>
		<?php esc_html_e( '返回首页', 'nanocat' ); ?>
	</a>
</section>
