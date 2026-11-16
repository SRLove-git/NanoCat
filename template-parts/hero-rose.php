<?php
/**
 * Rose-garden opening hero for the front page.
 *
 * @package NanoCat
 */

$nanocat_avatar = get_site_icon_url( 192 );
$nanocat_avatar = $nanocat_avatar ? $nanocat_avatar : get_template_directory_uri() . '/assets/images/mascot.svg';
$nanocat_role   = get_theme_mod( 'nanocat_profile_role', nanocat_get_default( 'profile_role' ) );
$nanocat_quote  = get_theme_mod(
	'nanocat_rose_quote',
	__( '在玫瑰、旧打字机与一杯凉掉的茶之间，写一点还愿意被记住的东西。', 'nanocat' )
);
$nanocat_socials = nanocat_get_social_links();
$nanocat_extras  = array(
	array(
		'label'    => __( '归档', 'nanocat' ),
		'url'      => home_url( '/archives/' ),
		'icon'     => 'calendar',
		'external' => false,
	),
	array(
		'label'    => __( 'Wiki', 'nanocat' ),
		'url'      => home_url( '/wiki/' ),
		'icon'     => 'folder',
		'external' => false,
	),
);
if ( count( $nanocat_socials ) < 3 ) {
	$nanocat_socials = array_merge(
		$nanocat_socials,
		array_slice( $nanocat_extras, 0, 3 - count( $nanocat_socials ) )
	);
}
?>
<section class="rose-hero" id="rose-hero" aria-labelledby="rose-hero-name">
	<div class="rose-hero__bg" aria-hidden="true"></div>
	<img class="rose-hero__girl" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/rose-girl.jpg' ); ?>" alt="" width="853" height="1280" decoding="async" fetchpriority="low" aria-hidden="true" draggable="false">
	<div class="rose-hero__girl-tint" aria-hidden="true"></div>
	<div class="rose-hero__veil" aria-hidden="true"></div>
	<div class="rose-hero__grain" aria-hidden="true"></div>
	<div class="rose-hero__petals" aria-hidden="true">
		<i></i><i></i><i></i><i></i><i></i>
	</div>

	<div class="container rose-hero__inner">
		<header class="rose-hero__masthead">
			<span class="rose-hero__issue"><i></i><?php esc_html_e( 'NANOCAT / ROSE STUDY', 'nanocat' ); ?></span>
			<span class="rose-hero__rule" aria-hidden="true"></span>
			<span class="rose-hero__stamp"><?php esc_html_e( 'WRITE · READ · BREATHE', 'nanocat' ); ?></span>
			<span class="rose-hero__live"><i></i><?php esc_html_e( 'AI ONLINE', 'nanocat' ); ?></span>
		</header>

		<article class="rose-card">
			<div class="rose-card__avatar">
				<img src="<?php echo esc_url( $nanocat_avatar ); ?>" width="192" height="192" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			</div>
			<p class="rose-card__kicker"><?php esc_html_e( 'A QUIET CORNER ON THE INTERNET', 'nanocat' ); ?></p>
			<h1 class="rose-card__name" id="rose-hero-name"><?php bloginfo( 'name' ); ?></h1>
			<p class="rose-card__role"><?php echo esc_html( $nanocat_role ); ?></p>
			<blockquote class="rose-card__quote">
				<?php echo esc_html( $nanocat_quote ); ?>
			</blockquote>
			<p class="rose-card__note"><?php esc_html_e( '有些话不适合放进时间线，于是留在这里。', 'nanocat' ); ?></p>

			<ul class="social-links social-links--rose">
				<?php foreach ( $nanocat_socials as $nanocat_social ) : ?>
					<li>
						<a class="social-link" href="<?php echo esc_url( $nanocat_social['url'] ); ?>"<?php echo $nanocat_social['external'] ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
							<?php echo nanocat_get_icon( $nanocat_social['icon'], 15 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted icon markup. ?>
							<span><?php echo esc_html( $nanocat_social['label'] ); ?></span>
							<?php if ( $nanocat_social['external'] ) : ?>
								<span class="screen-reader-text"><?php esc_html_e( '（在新窗口打开）', 'nanocat' ); ?></span>
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<div class="rose-card__actions">
				<a class="rose-card__button rose-card__button--solid" href="#latest-posts">
					<?php esc_html_e( '开始阅读', 'nanocat' ); ?>
					<span aria-hidden="true">→</span>
				</a>
				<button class="rose-card__button rose-card__button--ghost" type="button" data-ai-open aria-haspopup="dialog" aria-controls="nanocat-ai-dialog" aria-expanded="false">
					<span class="rose-card__spark" aria-hidden="true">✦</span>
					<?php esc_html_e( '问 AI', 'nanocat' ); ?>
				</button>
			</div>
		</article>

		<footer class="rose-hero__footer">
			<a href="#latest-posts"><?php esc_html_e( 'SCROLL TO READ', 'nanocat' ); ?> <span aria-hidden="true">↓</span></a>
			<span><?php esc_html_e( 'WORDPRESS · AZURE · RETRIEVAL AUGMENTED', 'nanocat' ); ?></span>
			<a href="<?php echo esc_url( home_url( '/ai/' ) ); ?>"><?php esc_html_e( 'OPEN NANOCAT AI', 'nanocat' ); ?> <span aria-hidden="true">→</span></a>
		</footer>
	</div>
</section>
