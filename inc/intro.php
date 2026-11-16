<?php
/**
 * Sakura-inspired opening animation.
 *
 * A quiet two-dot curtain that plays once per browser session, before the
 * page is revealed. It is deliberately dependency-free so a slow footer
 * script can never leave the screen covered.
 *
 * @package NanoCat
 */

/**
 * Print the tiny boot gate in the head.
 *
 * Runs before the stylesheets are printed so the overlay is present on the
 * very first paint. Storage failures simply mean the curtain is skipped.
 */
function nanocat_intro_head() {
	if ( is_customize_preview() ) {
		return;
	}
	?>
	<script>
	(function () {
		var key = 'nanocat-intro-seen';
		try {
			if (window.sessionStorage.getItem(key) === '1') {
				return;
			}
			if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
				return;
			}
			window.sessionStorage.setItem(key, '1');
		} catch (error) {
			/* Private browsing can block storage; showing the intro is harmless. */
		}
		document.documentElement.classList.add('has-intro');
		window.setTimeout(function () {
			if (!document.documentElement.classList.contains('has-intro')) {
				return;
			}
			document.documentElement.classList.remove('has-intro');
			var intro = document.getElementById('nanocat-intro');
			if (intro && intro.parentNode) {
				intro.parentNode.removeChild(intro);
			}
		}, 5000);
	}());
	</script>
	<?php
}
add_action( 'wp_head', 'nanocat_intro_head', 2 );

/**
 * Print the curtain and its self-contained exit timer.
 */
function nanocat_intro_markup() {
	if ( is_customize_preview() ) {
		return;
	}
	$site_name = get_bloginfo( 'name' );
	$tagline   = get_bloginfo( 'description' );
	if ( ! $tagline ) {
		$tagline = __( 'AI-NATIVE JOURNAL', 'nanocat' );
	}
	?>
	<div id="nanocat-intro" class="nanocat-intro" aria-hidden="true">
		<div class="nanocat-intro__inner">
			<div class="nanocat-intro__loader">
				<span class="nanocat-intro__dot nanocat-intro__dot--a"></span>
				<span class="nanocat-intro__dot nanocat-intro__dot--b"></span>
			</div>
			<div class="nanocat-intro__brand">
				<span class="nanocat-intro__name"><?php echo esc_html( $site_name ); ?></span>
				<span class="nanocat-intro__tag"><?php echo esc_html( $tagline ); ?></span>
			</div>
		</div>
	</div>
	<script>
	(function () {
		var intro = document.getElementById('nanocat-intro');
		if (!intro || !document.documentElement.classList.contains('has-intro')) {
			return;
		}
		var finished = false;
		function finish() {
			if (finished) {
				return;
			}
			finished = true;
			intro.classList.add('is-out');
			window.setTimeout(function () {
				if (intro.parentNode) {
					intro.parentNode.removeChild(intro);
				}
				document.documentElement.classList.remove('has-intro');
			}, 460);
		}
		if (document.readyState === 'complete') {
			window.setTimeout(finish, 1100);
		} else {
			window.addEventListener('load', function () {
				window.setTimeout(finish, 1100);
			}, { once: true });
			window.setTimeout(finish, 2500);
		}
	}());
	</script>
	<?php
}
add_action( 'wp_body_open', 'nanocat_intro_markup', 1 );
