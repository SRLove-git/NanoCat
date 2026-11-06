<?php
/**
 * Curated profile card used by the home sidebars.
 *
 * @package NanoCat
 */

$nanocat_avatar = get_site_icon_url( 192 );
$nanocat_avatar = $nanocat_avatar ? $nanocat_avatar : get_template_directory_uri() . '/assets/images/mascot.svg';
$nanocat_counts = wp_count_posts( 'post' );
$nanocat_total  = isset( $nanocat_counts->publish ) ? (int) $nanocat_counts->publish : 0;
$nanocat_cats   = wp_count_terms( array( 'taxonomy' => 'category', 'hide_empty' => true ) );
$nanocat_cats   = is_wp_error( $nanocat_cats ) ? 0 : (int) $nanocat_cats;
$nanocat_role   = get_theme_mod( 'nanocat_profile_role', nanocat_get_default( 'profile_role' ) );
$nanocat_bio    = get_theme_mod( 'nanocat_profile_bio', nanocat_get_default( 'profile_bio' ) );
?>
<section class="widget profile-card">
	<div class="profile-card__banner" aria-hidden="true">
		<span>&lt;/&gt;</span>
		<span class="profile-card__banner-spark">✦</span>
	</div>
	<div class="profile-card__avatar">
		<img src="<?php echo esc_url( $nanocat_avatar ); ?>" width="192" height="192" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" loading="lazy">
		<span class="profile-card__status" title="<?php esc_attr_e( '在线', 'nanocat' ); ?>"><span class="screen-reader-text"><?php esc_html_e( '在线', 'nanocat' ); ?></span></span>
	</div>
	<div class="profile-card__body">
		<h2 class="profile-card__name"><?php bloginfo( 'name' ); ?></h2>
		<p class="profile-card__role"><?php echo esc_html( $nanocat_role ); ?></p>
		<p class="profile-card__bio"><?php echo esc_html( $nanocat_bio ); ?></p>

		<div class="profile-card__stats">
			<div><strong><?php echo esc_html( number_format_i18n( $nanocat_total ) ); ?></strong><span><?php esc_html_e( '文章', 'nanocat' ); ?></span></div>
			<div><strong><?php echo esc_html( number_format_i18n( $nanocat_cats ) ); ?></strong><span><?php esc_html_e( '分类', 'nanocat' ); ?></span></div>
			<div><strong>∞</strong><span><?php esc_html_e( '咖啡', 'nanocat' ); ?></span></div>
		</div>

		<?php nanocat_social_links( 'social-links--profile' ); ?>
	</div>
</section>
