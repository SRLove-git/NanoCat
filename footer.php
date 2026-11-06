<?php
/**
 * Site footer.
 *
 * @package NanoCat
 */

$nanocat_has_footer_widgets = is_active_sidebar( 'footer-1' ) || is_active_sidebar( 'footer-2' ) || is_active_sidebar( 'footer-3' );
$nanocat_footer_text        = get_theme_mod( 'nanocat_footer_text', nanocat_get_default( 'footer_text' ) );
?>
<footer id="colophon" class="site-footer">
	<?php if ( $nanocat_has_footer_widgets ) : ?>
		<div class="container footer-widgets">
			<?php for ( $i = 1; $i <= 3; $i++ ) : ?>
				<?php if ( is_active_sidebar( 'footer-' . $i ) ) : ?>
					<div class="footer-widgets__column"><?php dynamic_sidebar( 'footer-' . $i ); ?></div>
				<?php endif; ?>
			<?php endfor; ?>
		</div>
	<?php endif; ?>

	<div class="container footer-main">
		<div class="footer-brand">
			<a class="footer-brand__link" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php echo nanocat_get_mark( 'footer-brand__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static SVG. ?>
				<span><?php bloginfo( 'name' ); ?></span>
			</a>
			<p class="footer-brand__text"><?php echo esc_html( $nanocat_footer_text ); ?></p>
			<p class="footer-brand__meta"><span class="status-dot" aria-hidden="true"></span><?php esc_html_e( '保持在线，偶尔断网去撸猫', 'nanocat' ); ?></p>
		</div>

		<div class="footer-column">
			<h2 class="footer-heading"><?php esc_html_e( '探索', 'nanocat' ); ?></h2>
			<nav aria-label="<?php esc_attr_e( '页脚导航', 'nanocat' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-menu',
						'fallback_cb'    => 'nanocat_footer_menu_fallback',
						'depth'          => 1,
					)
				);
				?>
			</nav>
		</div>

		<div class="footer-column">
			<h2 class="footer-heading"><?php esc_html_e( '保持联系', 'nanocat' ); ?></h2>
			<?php nanocat_social_links( 'social-links--footer' ); ?>
			<p class="footer-note"><?php esc_html_e( '写邮件通常比私信更快。', 'nanocat' ); ?></p>
		</div>
	</div>

	<div class="container footer-bottom">
		<p class="footer-copyright">
			<?php
			printf(
				/* translators: 1: current year, 2: site name. */
				esc_html__( '© %1$s %2$s', 'nanocat' ),
				esc_html( wp_date( 'Y' ) ),
				esc_html( get_bloginfo( 'name' ) )
			);
			?>
			<span class="footer-dot" aria-hidden="true">·</span>
			<span><?php esc_html_e( '由 WordPress 驱动', 'nanocat' ); ?></span>
		</p>
		<p class="footer-signature">
			<span class="footer-prompt" aria-hidden="true">$ echo</span>
			<?php esc_html_e( '“谢谢你看到这里。”', 'nanocat' ); ?>
			<span class="footer-heart" aria-hidden="true">♥</span>
		</p>
	</div>
</footer>

<button class="back-to-top" type="button" data-back-to-top aria-label="<?php esc_attr_e( '回到顶部', 'nanocat' ); ?>" hidden>
	<?php nanocat_icon( 'arrow-up', 18 ); ?>
</button>

<dialog id="nanocat-ai-dialog" class="ai-dialog" aria-labelledby="nanocat-ai-dialog-title">
	<div class="ai-dialog__shell">
		<header class="ai-dialog__head">
			<div class="ai-dialog__brand">
				<span class="ai-dialog__mark" aria-hidden="true">✦</span>
				<span class="ai-dialog__brand-copy">
					<span class="ai-dialog__kicker"><?php esc_html_e( 'NANOCAT / INTELLIGENCE', 'nanocat' ); ?></span>
					<strong id="nanocat-ai-dialog-title"><?php esc_html_e( '问 NanoCat AI', 'nanocat' ); ?></strong>
				</span>
			</div>
			<div class="ai-dialog__actions">
				<a class="ai-dialog__workspace" href="<?php echo esc_url( nanocat_ai_page_url() ); ?>"><?php esc_html_e( '工作台', 'nanocat' ); ?> <?php nanocat_icon( 'arrow-right', 13 ); ?></a>
				<button class="ai-dialog__close" type="button" data-ai-close aria-label="<?php esc_attr_e( '关闭 AI 对话', 'nanocat' ); ?>">
					<?php nanocat_icon( 'close', 18 ); ?>
				</button>
			</div>
		</header>

		<?php
		get_template_part(
			'template-parts/ai-console',
			null,
			array(
				'context' => 'dialog',
				'title'   => __( '从站内笔记里，找一条线索。', 'nanocat' ),
				'lead'    => __( '问具体一点：一个技术点、一条阅读路线，或者一篇文章。回答会带来源。', 'nanocat' ),
			)
		);
		?>
	</div>
</dialog>

<dialog id="nanocat-search-dialog" class="search-dialog" aria-labelledby="search-dialog-title">
	<div class="search-dialog__inner">
		<button class="search-dialog__close" type="button" data-search-close aria-label="<?php esc_attr_e( '关闭搜索', 'nanocat' ); ?>">
			<?php nanocat_icon( 'close', 20 ); ?>
		</button>
		<p class="section-kicker"><?php esc_html_e( '// SEARCH THE ARCHIVE', 'nanocat' ); ?></p>
		<h2 id="search-dialog-title" class="search-dialog__title"><?php esc_html_e( '今天想找什么？', 'nanocat' ); ?></h2>
		<p class="search-dialog__lead"><?php esc_html_e( '技术笔记、猫片，或者某个深夜写下的小情绪。', 'nanocat' ); ?></p>
		<?php get_search_form(); ?>
		<p class="search-dialog__hint"><kbd>Esc</kbd> <?php esc_html_e( '关闭', 'nanocat' ); ?><span aria-hidden="true">/</span><kbd>Enter</kbd> <?php esc_html_e( '搜索', 'nanocat' ); ?></p>
	</div>
</dialog>

<?php wp_footer(); ?>
</body>
</html>
