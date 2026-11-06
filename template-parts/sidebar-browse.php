<?php
/**
 * Category navigation for the left home sidebar.
 *
 * @package NanoCat
 */
?>
<section class="widget browse-widget">
	<div class="widget-heading">
		<p class="section-kicker"><?php esc_html_e( '// BROWSE', 'nanocat' ); ?></p>
		<h2 class="widget-title"><?php esc_html_e( '分类', 'nanocat' ); ?></h2>
	</div>
	<ul class="category-list">
		<?php
		wp_list_categories(
			array(
				'title_li'   => '',
				'show_count' => true,
				'hide_empty' => true,
			)
		);
		?>
	</ul>
</section>
