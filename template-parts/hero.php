<?php
/**
 * Homepage hero.
 *
 * @package NanoCat
 */

$nanocat_eyebrow      = get_theme_mod( 'nanocat_hero_eyebrow', nanocat_get_default( 'hero_eyebrow' ) );
$nanocat_title        = get_theme_mod( 'nanocat_hero_title', nanocat_get_default( 'hero_title' ) );
$nanocat_lead         = get_theme_mod( 'nanocat_hero_text', nanocat_get_default( 'hero_text' ) );
$nanocat_primary_text = get_theme_mod( 'nanocat_hero_primary_label', nanocat_get_default( 'hero_primary_label' ) );
$nanocat_primary_url  = get_theme_mod( 'nanocat_hero_primary_url', '' );
$nanocat_second_text  = get_theme_mod( 'nanocat_hero_secondary_label', nanocat_get_default( 'hero_secondary_label' ) );
$nanocat_second_url   = get_theme_mod( 'nanocat_hero_secondary_url', '' );
$nanocat_hero_image_dark  = get_theme_mod( 'nanocat_hero_image', get_template_directory_uri() . '/assets/images/hero-night.svg' );
$nanocat_hero_image_light = get_theme_mod( 'nanocat_hero_image_light', get_template_directory_uri() . '/assets/images/hero-light.svg' );
$nanocat_hero_image_light = $nanocat_hero_image_light ? $nanocat_hero_image_light : $nanocat_hero_image_dark;
$nanocat_role             = get_theme_mod( 'nanocat_profile_role', nanocat_get_default( 'profile_role' ) );
$nanocat_site_name        = get_bloginfo( 'name', 'display' );
$nanocat_avatar           = get_site_icon_url( 96 );
$nanocat_avatar           = $nanocat_avatar ? $nanocat_avatar : get_template_directory_uri() . '/assets/images/mascot.svg';

$nanocat_primary_url = $nanocat_primary_url ? $nanocat_primary_url : '#latest-posts';
$nanocat_second_url  = $nanocat_second_url ? $nanocat_second_url : get_feed_link();
$nanocat_counts      = wp_count_posts( 'post' );
$nanocat_total_posts = isset( $nanocat_counts->publish ) ? (int) $nanocat_counts->publish : 0;
$nanocat_category_count = wp_count_terms(
	array(
		'taxonomy'   => 'category',
		'hide_empty' => true,
	)
);
$nanocat_total_categories = is_wp_error( $nanocat_category_count ) ? 0 : (int) $nanocat_category_count;
?>
<section class="hero hero--cover" aria-labelledby="hero-title">
	<div class="hero__backdrop" aria-hidden="true">
		<img class="hero__image hero__image--light" src="<?php echo esc_url( $nanocat_hero_image_light ); ?>" alt="" fetchpriority="high" decoding="async">
		<img class="hero__image hero__image--dark" src="<?php echo esc_url( $nanocat_hero_image_dark ); ?>" alt="" decoding="async">
		<span class="hero__scrim"></span>
		<span class="hero__scanlines"></span>
	</div>

	<div class="container hero__inner">
		<div class="hero__masthead" data-reveal>
			<span class="hero__issue"><?php esc_html_e( 'ISSUE', 'nanocat' ); ?> <strong>01</strong></span>
			<span class="hero__masthead-rule" aria-hidden="true"></span>
			<span class="hero__masthead-label"><?php esc_html_e( 'PERSONAL LOG / 2026', 'nanocat' ); ?></span>
			<span class="hero__masthead-status">
				<span class="status-dot" aria-hidden="true"></span>
				<?php esc_html_e( 'ONLINE', 'nanocat' ); ?>
			</span>
		</div>

		<div class="hero__grid">
			<div class="hero__copy" data-reveal>
				<p class="hero__eyebrow">
					<span class="status-dot" aria-hidden="true"></span>
					<span class="hero__prompt"><?php echo esc_html( $nanocat_eyebrow ); ?></span>
					<span class="hero__eyebrow-rule" aria-hidden="true"></span>
					<span class="hero__edition hero__edition--light"><?php esc_html_e( 'MORNING SHIFT / 2026', 'nanocat' ); ?></span>
					<span class="hero__edition hero__edition--dark"><?php esc_html_e( 'NIGHT SHIFT / 2026', 'nanocat' ); ?></span>
				</p>

				<h1 id="hero-title" class="hero__title">
					<?php echo wp_kses_post( $nanocat_title ); ?>
				</h1>

				<p class="hero__lead"><?php echo esc_html( $nanocat_lead ); ?></p>

				<div class="hero__actions">
					<?php if ( $nanocat_primary_text ) : ?>
						<a class="button button--primary" href="<?php echo esc_url( $nanocat_primary_url ); ?>">
							<span><?php echo esc_html( $nanocat_primary_text ); ?></span>
							<?php nanocat_icon( 'arrow-right', 17 ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $nanocat_second_text ) : ?>
						<a class="button button--ghost" href="<?php echo esc_url( $nanocat_second_url ); ?>">
							<?php nanocat_icon( 'rss', 16 ); ?>
							<span><?php echo esc_html( $nanocat_second_text ); ?></span>
						</a>
					<?php endif; ?>
				</div>

				<ul class="hero__facts" aria-label="<?php esc_attr_e( '站点数据', 'nanocat' ); ?>">
					<li>
						<strong><?php echo esc_html( number_format_i18n( $nanocat_total_posts ) ); ?></strong>
						<span><?php esc_html_e( '篇笔记', 'nanocat' ); ?></span>
					</li>
					<li>
						<strong><?php echo esc_html( number_format_i18n( $nanocat_total_categories ) ); ?></strong>
						<span><?php esc_html_e( '个分类', 'nanocat' ); ?></span>
					</li>
					<li>
						<strong>∞</strong>
						<span><?php esc_html_e( '杯咖啡', 'nanocat' ); ?></span>
					</li>
				</ul>
			</div>

			<aside class="hero__stack" aria-label="<?php esc_attr_e( '站点速览', 'nanocat' ); ?>" data-reveal>
				<article class="hero-card hero-card--profile">
					<div class="hero-card__head">
						<span class="terminal__dots" aria-hidden="true"><i></i><i></i><i></i></span>
						<span>~/profile.json</span>
						<span class="hero-card__branch">main</span>
					</div>

					<div class="hero-card__identity">
						<span class="hero-card__avatar">
							<img src="<?php echo esc_url( $nanocat_avatar ); ?>" alt="" width="64" height="64" loading="lazy" decoding="async">
						</span>
						<div class="hero-card__who">
							<strong><?php echo esc_html( $nanocat_site_name ); ?></strong>
							<span><?php echo esc_html( $nanocat_role ); ?></span>
						</div>
						<span class="hero-card__live"><i aria-hidden="true"></i><?php esc_html_e( 'ONLINE', 'nanocat' ); ?></span>
					</div>

					<div class="hero-card__rows">
						<span><b>stack</b>CSS / PHP / JS</span>
						<span><b>mode</b><?php esc_html_e( 'building in public', 'nanocat' ); ?></span>
					</div>

					<a class="hero-card__link" href="#latest-posts">
						<span><?php esc_html_e( '打开最新一篇', 'nanocat' ); ?></span>
						<?php nanocat_icon( 'arrow-right', 15 ); ?>
					</a>
				</article>

				<article class="hero-card hero-card--code">
					<div class="hero-card__head">
						<span class="terminal__dots" aria-hidden="true"><i></i><i></i><i></i></span>
						<span>~/hello.sh</span>
						<span class="hero-card__branch">zsh</span>
					</div>
					<pre class="hero-card__code"><code><span class="hero-card__prompt">$</span> whoami
<span class="hero-card__out">NanoCat</span>
<span class="hero-card__prompt">$</span> echo "keep building"
<span class="hero-card__out">keep building<span class="terminal__cursor" aria-hidden="true"></span></span></code></pre>
				</article>

				<span class="hero__sticker" aria-hidden="true">
					<span>NYA~</span>
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/mascot.svg' ); ?>" alt="" width="76" height="76" loading="lazy" decoding="async">
				</span>
			</aside>
		</div>

		<div class="hero__footer">
			<a class="hero__scroll" href="#latest-posts" aria-label="<?php esc_attr_e( '向下滚动到最新文章', 'nanocat' ); ?>">
				<span><?php esc_html_e( 'SCROLL TO READ', 'nanocat' ); ?></span>
				<i aria-hidden="true"></i>
			</a>
			<span class="hero__footer-meta"><?php esc_html_e( 'WORDPRESS · AZURE · MADE WITH 猫毛', 'nanocat' ); ?></span>
			<a class="hero__footer-link" href="#latest-posts">
				<?php
				printf(
					/* translators: %s: number of published posts. */
					esc_html__( '%s NOTES', 'nanocat' ),
					esc_html( number_format_i18n( $nanocat_total_posts ) )
				);
				?>
				<?php nanocat_icon( 'arrow-right', 14 ); ?>
			</a>
		</div>
	</div>
</section>
