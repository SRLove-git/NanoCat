<?php
/**
 * Topic list for the left home sidebar.
 *
 * @package NanoCat
 */

$nanocat_sidebar_topics = function_exists( 'nanocat_get_topics' ) ? nanocat_get_topics( 4, true ) : array();
$nanocat_topics_page    = get_page_by_path( 'topics' );

if ( ! $nanocat_sidebar_topics ) {
	return;
}
?>
<section class="widget sidebar-topics">
	<div class="widget-heading">
		<p class="section-kicker"><?php esc_html_e( '// READING_LISTS', 'nanocat' ); ?></p>
		<h2 class="widget-title"><?php esc_html_e( '专栏', 'nanocat' ); ?></h2>
	</div>
	<ul class="sidebar-topic-list">
		<?php foreach ( $nanocat_sidebar_topics as $nanocat_sidebar_topic ) : ?>
			<?php $nanocat_sidebar_topic_link = get_term_link( $nanocat_sidebar_topic ); ?>
			<?php if ( is_wp_error( $nanocat_sidebar_topic_link ) ) : ?>
				<?php continue; ?>
			<?php endif; ?>
			<li>
				<a href="<?php echo esc_url( $nanocat_sidebar_topic_link ); ?>">
					<?php nanocat_topic_icon( $nanocat_sidebar_topic ); ?>
					<span><?php echo esc_html( $nanocat_sidebar_topic->name ); ?></span>
					<strong><?php echo esc_html( number_format_i18n( $nanocat_sidebar_topic->count ) ); ?></strong>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php if ( $nanocat_topics_page instanceof WP_Post ) : ?>
		<a class="text-link sidebar-widget-link" href="<?php echo esc_url( get_permalink( $nanocat_topics_page ) ); ?>">
			<?php esc_html_e( '全部专栏', 'nanocat' ); ?>
			<?php nanocat_icon( 'arrow-right', 16 ); ?>
		</a>
	<?php endif; ?>
</section>
