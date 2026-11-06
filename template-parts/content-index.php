<?php
/**
 * Minimal index row for the AI-first homepage.
 *
 * @package NanoCat
 */

$nanocat_index        = isset( $args['index'] ) ? (int) $args['index'] : 0;
$nanocat_category     = nanocat_get_primary_category();
$nanocat_reading_time = nanocat_get_reading_time();
?>
<article <?php post_class( 'index-entry' ); ?>>
	<a class="index-entry__link" href="<?php the_permalink(); ?>">
		<span class="index-entry__number"><?php echo esc_html( str_pad( (string) ( $nanocat_index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>

		<span class="index-entry__body">
			<span class="index-entry__meta">
				<?php if ( $nanocat_category ) : ?>
					<span><?php echo esc_html( $nanocat_category->name ); ?></span>
					<span aria-hidden="true">/</span>
				<?php endif; ?>
				<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date( 'Y.m.d' ) ); ?></time>
				<?php if ( is_sticky() ) : ?>
					<span class="index-entry__sticky"><?php esc_html_e( '置顶', 'nanocat' ); ?></span>
				<?php endif; ?>
			</span>

			<h2 class="index-entry__title"><?php the_title(); ?></h2>
			<p class="index-entry__excerpt"><?php echo esc_html( nanocat_get_excerpt( 120 ) ); ?></p>
		</span>

		<span class="index-entry__side">
			<span><?php echo esc_html( $nanocat_reading_time ); ?></span>
			<?php nanocat_icon( 'arrow-up-right', 17 ); ?>
		</span>
	</a>
</article>
