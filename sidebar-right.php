<?php
/**
 * Sidebar: the profile card (home only) and the category list.
 *
 * @package NanoCat
 */

$nanocat_categories = get_categories(
	array(
		'hide_empty' => true,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
?>
<aside id="sidebar-right" class="sidebar flec-side widget-area" aria-label="<?php esc_attr_e( '侧栏', 'nanocat' ); ?>">
	<?php get_template_part( 'template-parts/sidebar-profile' ); ?>

	<?php if ( $nanocat_categories ) : ?>
		<section class="widget flec-widget">
			<h2 class="widget-title"><?php esc_html_e( '分类', 'nanocat' ); ?></h2>
			<ul class="flec-list">
				<?php foreach ( $nanocat_categories as $nanocat_category ) : ?>
					<li>
						<a href="<?php echo esc_url( get_category_link( $nanocat_category ) ); ?>"><?php echo esc_html( $nanocat_category->name ); ?></a>
						<span class="flec-list__count"><?php echo esc_html( number_format_i18n( $nanocat_category->count ) ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endif; ?>
</aside>
