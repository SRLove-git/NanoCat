<?php
/**
 * Card used by the Flec-style index grid.
 *
 * @package NanoCat
 */

$nanocat_index        = isset( $args['index'] ) ? (int) $args['index'] : 0;
$nanocat_category     = nanocat_get_primary_category();
$nanocat_reading_time = nanocat_get_reading_time();
$nanocat_cover        = nanocat_get_post_cover();
$nanocat_comments     = (int) get_comments_number();
?>
<article <?php post_class( 'flec-card' ); ?>>
	<a class="flec-card__cover" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
		<img src="<?php echo esc_url( $nanocat_cover ); ?>" alt="" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='<?php echo esc_js( get_template_directory_uri() . '/assets/images/fallback-1.svg' ); ?>';">
		<?php if ( $nanocat_category ) : ?>
			<span class="flec-card__badge"><?php echo esc_html( $nanocat_category->name ); ?></span>
		<?php endif; ?>
		<?php if ( is_sticky() ) : ?>
			<span class="flec-card__pin"><?php esc_html_e( '置顶', 'nanocat' ); ?></span>
		<?php endif; ?>
		<span class="flec-card__cover-text">
			<span class="flec-card__cover-title"><?php the_title(); ?></span>
			<span class="flec-card__cover-by">@<?php bloginfo( 'name' ); ?></span>
		</span>
	</a>

	<div class="flec-card__body">
		<h2 class="flec-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

		<p class="flec-card__meta">
			<span><?php echo esc_html( get_the_date( 'Y-m-d' ) ); ?></span>
			<?php if ( $nanocat_category ) : ?>
				<span aria-hidden="true">·</span>
				<span><?php echo esc_html( $nanocat_category->name ); ?></span>
			<?php endif; ?>
			<span aria-hidden="true">·</span>
			<span><?php echo esc_html( $nanocat_reading_time ); ?></span>
			<?php if ( $nanocat_comments > 0 ) : ?>
				<span aria-hidden="true">·</span>
				<span>
					<?php
					printf(
						/* translators: %s: comment count. */
						esc_html__( '%s 条评论', 'nanocat' ),
						esc_html( number_format_i18n( $nanocat_comments ) )
					);
					?>
				</span>
			<?php endif; ?>
		</p>

		<p class="flec-card__excerpt"><?php echo esc_html( nanocat_get_excerpt( 96 ) ); ?></p>

		<a class="flec-card__more" href="<?php the_permalink(); ?>">
			<?php esc_html_e( '阅读全文', 'nanocat' ); ?>
			<span aria-hidden="true">→</span>
		</a>
	</div>
</article>
