<?php
/**
 * Post card used in archives and the home feed.
 *
 * @package NanoCat
 */

$nanocat_featured = isset( $args['featured'] ) && $args['featured'];
$nanocat_index    = isset( $args['index'] ) ? (int) $args['index'] : 0;
$nanocat_classes  = array( 'post-card' );

if ( $nanocat_featured ) {
	$nanocat_classes[] = 'post-card--featured';
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $nanocat_classes ); ?>>
	<a class="post-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php nanocat_cover( $nanocat_featured ? 'nanocat-wide' : 'nanocat-card', $nanocat_featured ? 'eager' : 'lazy' ); ?>
		<?php if ( $nanocat_featured ) : ?>
			<span class="post-card__flag"><?php esc_html_e( 'FEATURED', 'nanocat' ); ?></span>
		<?php endif; ?>
		<span class="post-card__read" aria-hidden="true">
			<?php nanocat_icon( 'arrow-up', 14 ); ?>
		</span>
	</a>

	<div class="post-card__body">
		<?php if ( is_sticky() && ! is_paged() ) : ?>
			<span class="post-card__sticky"><?php nanocat_icon( 'sparkle', 13 ); ?><?php esc_html_e( '置顶', 'nanocat' ); ?></span>
		<?php endif; ?>

		<?php nanocat_card_meta(); ?>

		<h3 class="post-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<p class="post-card__excerpt"><?php echo esc_html( nanocat_get_excerpt( $nanocat_featured ? 110 : 76 ) ); ?></p>

		<div class="post-card__footer">
			<span class="post-card__reading">
				<?php nanocat_icon( 'clock', 15 ); ?>
				<?php
				printf(
					/* translators: %s: reading time in minutes. */
					esc_html__( '%s 分钟阅读', 'nanocat' ),
					esc_html( number_format_i18n( nanocat_get_reading_time() ) )
				);
				?>
			</span>
			<a class="post-card__more" href="<?php the_permalink(); ?>">
				<span><?php esc_html_e( '阅读全文', 'nanocat' ); ?></span>
				<?php nanocat_icon( 'arrow-right', 15 ); ?>
			</a>
		</div>
	</div>
</article>
