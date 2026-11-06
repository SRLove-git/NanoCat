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

$nanocat_primary_url = $nanocat_primary_url ? $nanocat_primary_url : '#latest-posts';
$nanocat_second_url  = $nanocat_second_url ? $nanocat_second_url : get_feed_link();
$nanocat_counts      = wp_count_posts( 'post' );
$nanocat_total_posts = isset( $nanocat_counts->publish ) ? (int) $nanocat_counts->publish : 0;
?>
<section class="hero hero--cover" aria-labelledby="hero-title">
	<div class="hero__backdrop" aria-hidden="true">
		<img class="hero__image hero__image--light" src="<?php echo esc_url( $nanocat_hero_image_light ); ?>" alt="" fetchpriority="high" decoding="async">
		<img class="hero__image hero__image--dark" src="<?php echo esc_url( $nanocat_hero_image_dark ); ?>" alt="" decoding="async">
		<span class="hero__scrim"></span>
		<span class="hero__scanlines"></span>
	</div>

	<div class="container hero__inner">
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

			<p class="hero__micro">
				<span class="hero__micro-line" aria-hidden="true"></span>
				<?php
				printf(
					/* translators: %s: number of published posts. */
					esc_html__( '已经写下 %s 篇笔记，仍在持续更新', 'nanocat' ),
					'<strong>' . esc_html( number_format_i18n( $nanocat_total_posts ) ) . '</strong>'
				);
				?>
			</p>
		</div>

		<aside class="hero__panel" aria-label="<?php esc_attr_e( '站点速览', 'nanocat' ); ?>" data-reveal>
			<div class="hero__panel-head">
				<span class="terminal__dots" aria-hidden="true"><i></i><i></i><i></i></span>
				<span>~/profile.json</span>
				<span class="hero__panel-branch">main</span>
			</div>

			<dl class="hero__panel-list">
				<div>
					<dt>role</dt>
					<dd><?php echo esc_html( $nanocat_role ); ?></dd>
				</div>
				<div>
					<dt>stack</dt>
					<dd>CSS / PHP / vanilla JS</dd>
				</div>
				<div>
					<dt>status</dt>
					<dd><span class="status-dot" aria-hidden="true"></span><?php esc_html_e( 'building in public', 'nanocat' ); ?></dd>
				</div>
			</dl>

			<p class="hero__panel-note"><?php esc_html_e( '// 代码、猫，以及把日子过成喜欢的样子。', 'nanocat' ); ?></p>

			<a class="hero__panel-link" href="#latest-posts">
				<span><?php esc_html_e( '跳到最新文章', 'nanocat' ); ?></span>
				<?php nanocat_icon( 'arrow-right', 15 ); ?>
			</a>
		</aside>
	</div>

	<a class="hero__scroll" href="#latest-posts" aria-label="<?php esc_attr_e( '向下滚动到最新文章', 'nanocat' ); ?>">
		<span><?php esc_html_e( 'SCROLL TO READ', 'nanocat' ); ?></span>
		<i aria-hidden="true"></i>
	</a>
</section>
