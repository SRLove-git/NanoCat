<?php
/**
 * Site header.
 *
 * @package NanoCat
 */

$nanocat_logo_id = get_theme_mod( 'custom_logo' );
$nanocat_logo    = $nanocat_logo_id ? wp_get_attachment_image(
	$nanocat_logo_id,
	'full',
	false,
	array(
		'class' => 'custom-logo',
		'alt'   => get_bloginfo( 'name' ),
	)
) : '';
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#f2f7fd" id="nanocat-theme-color">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#primary"><?php esc_html_e( '跳到主要内容', 'nanocat' ); ?></a>

<?php if ( is_singular( 'post' ) ) : ?>
	<div class="reading-progress" aria-hidden="true">
		<span class="reading-progress__bar" data-reading-progress></span>
	</div>
<?php endif; ?>

<header id="masthead" class="site-header" data-site-header>
	<div class="container header-inner">
		<div class="site-branding">
			<a class="site-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php if ( $nanocat_logo ) : ?>
					<?php echo $nanocat_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core image markup. ?>
				<?php else : ?>
					<?php echo nanocat_get_mark( 'site-brand__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?>
				<?php endif; ?>
				<span class="site-brand__copy">
					<span class="site-brand__name"><?php bloginfo( 'name' ); ?></span>
					<?php if ( get_bloginfo( 'description' ) ) : ?>
						<span class="site-brand__tag"><?php bloginfo( 'description' ); ?></span>
					<?php endif; ?>
				</span>
			</a>
		</div>

		<nav id="site-navigation" class="site-navigation" aria-label="<?php esc_attr_e( '主导航', 'nanocat' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'primary-menu',
					'menu_id'        => 'primary-menu',
					'fallback_cb'    => 'nanocat_primary_menu_fallback',
					'depth'          => 1,
				)
			);
			?>
		</nav>

		<div class="header-actions">
			<button class="ai-trigger" type="button" data-ai-open aria-haspopup="dialog" aria-controls="nanocat-ai-dialog" aria-expanded="false" aria-keyshortcuts="Control+K Meta+K" aria-label="<?php esc_attr_e( '打开 NanoCat AI', 'nanocat' ); ?>">
				<span class="ai-trigger__spark" aria-hidden="true">✦</span>
				<span class="ai-trigger__label"><?php esc_html_e( '问 AI', 'nanocat' ); ?></span>
				<kbd class="ai-trigger__kbd">Ctrl K</kbd>
			</button>

			<button class="icon-button search-toggle" type="button" data-search-open aria-controls="nanocat-search-dialog" aria-haspopup="dialog">
				<?php nanocat_icon( 'search', 19 ); ?>
				<span class="screen-reader-text"><?php esc_html_e( '打开搜索', 'nanocat' ); ?></span>
			</button>

			<button class="icon-button theme-toggle" type="button" data-theme-toggle aria-label="<?php esc_attr_e( '切换明暗主题', 'nanocat' ); ?>">
				<span class="theme-toggle__icon theme-toggle__icon--sun"><?php nanocat_icon( 'sun', 19 ); ?></span>
				<span class="theme-toggle__icon theme-toggle__icon--moon"><?php nanocat_icon( 'moon', 19 ); ?></span>
			</button>

			<button class="icon-button menu-toggle" type="button" data-menu-toggle aria-controls="site-navigation" aria-expanded="false">
				<span class="menu-toggle__open"><?php nanocat_icon( 'menu', 21 ); ?></span>
				<span class="menu-toggle__close"><?php nanocat_icon( 'close', 21 ); ?></span>
				<span class="screen-reader-text"><?php esc_html_e( '菜单', 'nanocat' ); ?></span>
			</button>
		</div>
	</div>
</header>
