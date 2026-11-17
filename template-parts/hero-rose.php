<?php
/**
 * Full-bleed opening hero for the front page.
 *
 * @package NanoCat
 */

$nanocat_avatar = get_site_icon_url( 192 );
$nanocat_avatar = $nanocat_avatar ? $nanocat_avatar : get_template_directory_uri() . '/assets/images/mascot.svg';
$nanocat_tagline = get_bloginfo( 'description' );
if ( ! $nanocat_tagline ) {
	$nanocat_tagline = __( '保持好奇，持续构建。', 'nanocat' );
}
?>
<section class="rose-hero flec-hero" id="rose-hero" aria-labelledby="flec-hero-title">
	<div class="rose-hero__bg" aria-hidden="true"></div>
	<img class="rose-hero__girl" src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/rose-girl.jpg' ); ?>" alt="" width="853" height="1280" decoding="async" fetchpriority="low" aria-hidden="true" draggable="false">
	<div class="rose-hero__girl-tint" aria-hidden="true"></div>
	<div class="rose-hero__veil" aria-hidden="true"></div>
	<div class="rose-hero__grain" aria-hidden="true"></div>
	<div class="rose-hero__petals" aria-hidden="true">
		<i></i><i></i><i></i><i></i><i></i>
	</div>

	<div class="container flec-hero__inner">
		<p class="flec-hero__eyebrow"><?php esc_html_e( 'WELCOME TO MY BLOG', 'nanocat' ); ?></p>
		<h1 class="flec-hero__title" id="flec-hero-title"><?php bloginfo( 'name' ); ?></h1>
		<p class="flec-hero__desc"><?php echo esc_html( $nanocat_tagline ); ?></p>

		<div class="flec-hero__actions">
			<a class="flec-btn flec-btn--solid" href="#latest-posts">
				<?php esc_html_e( '开始阅读', 'nanocat' ); ?>
				<span aria-hidden="true">→</span>
			</a>
			<button class="flec-btn flec-btn--ghost" type="button" data-ai-open aria-haspopup="dialog" aria-controls="nanocat-ai-dialog" aria-expanded="false">
				<span aria-hidden="true">✦</span>
				<?php esc_html_e( '问 AI', 'nanocat' ); ?>
			</button>
		</div>

		<a class="flec-hero__scroll" href="#latest-posts">
			<span aria-hidden="true"></span>
			<?php esc_html_e( '向下滚动', 'nanocat' ); ?>
		</a>
	</div>
</section>
