<?php
/**
 * Search form.
 *
 * @package NanoCat
 */

$nanocat_search_id = wp_unique_id( 'search-field-' );
?>
<form role="search" method="get" class="search-form" aria-label="<?php esc_attr_e( '站内搜索', 'nanocat' ); ?>" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="<?php echo esc_attr( $nanocat_search_id ); ?>"><?php esc_html_e( '搜索文章', 'nanocat' ); ?></label>
	<div class="search-form__inner">
		<?php nanocat_icon( 'search', 18, 'search-form__icon' ); ?>
		<input
			type="search"
			id="<?php echo esc_attr( $nanocat_search_id ); ?>"
			class="search-form__field"
			name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php esc_attr_e( '搜索代码、猫和日常…', 'nanocat' ); ?>"
			autocomplete="off"
		>
		<button type="submit" class="search-form__submit" aria-label="<?php esc_attr_e( '搜索', 'nanocat' ); ?>">
			<span><?php esc_html_e( '搜索', 'nanocat' ); ?></span>
			<?php nanocat_icon( 'arrow-right', 16 ); ?>
		</button>
	</div>
</form>
