<?php
/**
 * Compact topic strip shown on the homepage.
 *
 * @package NanoCat
 */

$nanocat_strip_topics = nanocat_get_topics( 4, true );

if ( ! $nanocat_strip_topics ) {
	return;
}

$nanocat_topics_page = get_page_by_path( 'topics' );
$nanocat_topics_url  = $nanocat_topics_page instanceof WP_Post ? get_permalink( $nanocat_topics_page ) : home_url( '/topics/' );
?>
<section class="topics-band" aria-labelledby="topics-band-title">
	<div class="container">
		<div class="topics-band__head">
			<div>
				<p class="section-kicker"><?php esc_html_e( '// READING_LISTS', 'nanocat' ); ?></p>
				<h2 class="section-title" id="topics-band-title"><?php esc_html_e( '专栏', 'nanocat' ); ?></h2>
			</div>
			<a class="text-link" href="<?php echo esc_url( $nanocat_topics_url ); ?>">
				<?php esc_html_e( '全部专栏', 'nanocat' ); ?>
				<?php nanocat_icon( 'arrow-right', 16 ); ?>
			</a>
		</div>

		<div class="topics-band__grid">
			<?php foreach ( $nanocat_strip_topics as $nanocat_strip_topic ) : ?>
				<a class="topic-card topic-card--strip" href="<?php echo esc_url( get_term_link( $nanocat_strip_topic ) ); ?>">
					<div class="topic-card__top">
						<?php nanocat_topic_icon( $nanocat_strip_topic ); ?>
						<span class="topic-card__count">
							<?php
							printf(
								/* translators: %s: post count. */
								esc_html__( '%s 篇', 'nanocat' ),
								esc_html( number_format_i18n( $nanocat_strip_topic->count ) )
							);
							?>
						</span>
					</div>
					<h3 class="topic-card__title"><?php echo esc_html( $nanocat_strip_topic->name ); ?></h3>
					<p class="topic-card__desc">
						<?php
						echo $nanocat_strip_topic->description
							? esc_html( $nanocat_strip_topic->description )
							: esc_html__( '一个还在慢慢生长的文章系列。', 'nanocat' );
						?>
					</p>
					<span class="topic-card__arrow" aria-hidden="true"><?php nanocat_icon( 'arrow-right', 16 ); ?></span>
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>
