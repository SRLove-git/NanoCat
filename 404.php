<?php
/**
 * 404 template.
 *
 * @package NanoCat
 */

get_header();
?>
<main id="primary" class="site-main error-view">
	<div class="container error-page">
		<div class="error-visual" aria-hidden="true">
			<p class="error-visual__kicker">// ERROR_NOT_FOUND</p>
			<div class="error-number">
				<span>4</span>
				<span class="error-number__cat">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/mascot.svg' ); ?>" alt="" width="150" height="150">
				</span>
				<span>4</span>
			</div>
			<span class="error-visual__sticker">NYA?</span>
			<span class="error-visual__spark error-visual__spark--one"><?php nanocat_icon( 'sparkle', 26 ); ?></span>
			<span class="error-visual__spark error-visual__spark--two"><?php nanocat_icon( 'sparkle', 16 ); ?></span>
		</div>

		<div class="error-copy">
			<p class="section-kicker"><?php esc_html_e( '// 404_PAGE_NOT_FOUND', 'nanocat' ); ?></p>
			<h1 class="error-title"><?php esc_html_e( '喵？这个页面被猫叼走了。', 'nanocat' ); ?></h1>
			<p class="error-text">
				<?php esc_html_e( '你访问的链接可能已经搬家、被删掉，或者从一开始就只是一场猫的幻觉。检查一下地址，或者从下面重新出发。', 'nanocat' ); ?>
			</p>

			<div class="error-actions">
				<a class="button button--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php nanocat_icon( 'home', 17 ); ?>
					<?php esc_html_e( '回到首页', 'nanocat' ); ?>
				</a>
				<button class="button button--outline" type="button" data-search-open>
					<?php nanocat_icon( 'search', 17 ); ?>
					<?php esc_html_e( '搜索文章', 'nanocat' ); ?>
				</button>
			</div>

			<pre class="error-terminal" aria-hidden="true"><span class="error-terminal__prompt">cat@nanocat:~$</span> curl -I /lost-page
<span class="error-terminal__warn">HTTP/2 404</span>
<span class="error-terminal__prompt">cat@nanocat:~$</span> cat /lost-page
cat: /lost-page: No such file or directory
<span class="error-terminal__prompt">cat@nanocat:~$</span> <span class="error-terminal__cursor"></span></pre>

			<div class="error-hints">
				<span><?php esc_html_e( '或者试试：', 'nanocat' ); ?></span>
				<a href="<?php echo esc_url( get_feed_link() ); ?>"><?php nanocat_icon( 'rss', 15 ); ?>RSS</a>
				<a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>"><?php nanocat_icon( 'terminal', 15 ); ?>grep</a>
			</div>
		</div>
	</div>
</main>

<?php
get_footer();
