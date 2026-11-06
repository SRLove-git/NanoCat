<?php
/**
 * Left sidebar on the blog home.
 *
 * @package NanoCat
 */
?>
<aside id="sidebar-left" class="sidebar home-side home-side--left widget-area" aria-label="<?php esc_attr_e( '首页左栏', 'nanocat' ); ?>">
	<?php
	if ( is_active_sidebar( 'sidebar-left' ) ) {
		dynamic_sidebar( 'sidebar-left' );
	} else {
		get_template_part( 'template-parts/sidebar-profile' );
		get_template_part( 'template-parts/sidebar-browse' );
		get_template_part( 'template-parts/sidebar-topics' );
	}
	?>
</aside>
