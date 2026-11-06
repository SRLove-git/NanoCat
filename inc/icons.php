<?php
/**
 * Small, dependency-free SVG icon set.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return an inline SVG icon.
 *
 * @param string $name  Icon name.
 * @param int    $size  Pixel size.
 * @param string $class Extra CSS classes.
 * @return string
 */
function nanocat_get_icon( $name, $size = 18, $class = '' ) {
	$icons = array(
		'search'      => '<circle cx="11" cy="11" r="7"></circle><path d="m20 20-3.6-3.6"></path>',
		'sun'         => '<circle cx="12" cy="12" r="4"></circle><path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41"></path>',
		'moon'        => '<path d="M20.8 13.1A8.5 8.5 0 0 1 10.9 3.2a8.5 8.5 0 1 0 9.9 9.9Z"></path>',
		'arrow-right' => '<path d="M5 12h14"></path><path d="m13 6 6 6-6 6"></path>',
		'arrow-left'  => '<path d="M19 12H5"></path><path d="m11 6-6 6 6 6"></path>',
		'arrow-up'    => '<path d="M12 19V5"></path><path d="m6 11 6-6 6 6"></path>',
		'arrow-up-right' => '<path d="M7 17 17 7"></path><path d="M8 7h9v9"></path>',
		'menu'        => '<path d="M4 7h16M4 12h16M4 17h10"></path>',
		'close'       => '<path d="m6 6 12 12M18 6 6 18"></path>',
		'clock'       => '<circle cx="12" cy="12" r="8.5"></circle><path d="M12 7v5l3 2"></path>',
		'calendar'    => '<rect x="3.5" y="5" width="17" height="15.5" rx="2.5"></rect><path d="M8 3v4M16 3v4M3.5 10h17"></path>',
		'tag'         => '<path d="M20 13 11 4H4v7l9 9 7-7Z"></path><circle cx="7.5" cy="7.5" r="1.2"></circle>',
		'folder'      => '<path d="M3.5 6.5h6l2 2h9v9.5a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2Z"></path>',
		'comments'    => '<path d="M21 11.5a8.2 8.2 0 0 1-8.5 8.2 9 9 0 0 1-3.4-.7L4 21l1.8-4.6A8.2 8.2 0 1 1 21 11.5Z"></path><path d="M8.5 11.5h.01M12 11.5h.01M15.5 11.5h.01"></path>',
		'rss'         => '<circle cx="5.5" cy="18.5" r="1.5" fill="currentColor" stroke="none"></circle><path d="M4 4a16 16 0 0 1 16 16M4 10a10 10 0 0 1 10 10"></path>',
		'mail'        => '<rect x="3" y="5" width="18" height="14" rx="2.5"></rect><path d="m4 7 8 6 8-6"></path>',
		'github'      => '<path fill="currentColor" stroke="none" d="M12 .9A11.2 11.2 0 0 0 8.45 22.7c.56.1.77-.24.77-.54v-2.1c-3.12.68-3.78-1.32-3.78-1.32-.51-1.3-1.25-1.64-1.25-1.64-1.02-.7.08-.68.08-.68 1.13.08 1.72 1.16 1.72 1.16 1 1.72 2.63 1.22 3.27.93.1-.73.4-1.22.71-1.5-2.49-.29-5.11-1.25-5.11-5.55 0-1.23.44-2.23 1.16-3.02-.12-.28-.5-1.43.11-2.98 0 0 .94-.3 3.08 1.15a10.7 10.7 0 0 1 5.62 0c2.14-1.45 3.08-1.15 3.08-1.15.61 1.55.23 2.7.11 2.98.72.79 1.16 1.79 1.16 3.02 0 4.31-2.63 5.26-5.13 5.54.4.35.76 1.03.76 2.08v3.09c0 .3.2.65.78.54A11.2 11.2 0 0 0 12 .9Z"></path>',
		'bilibili'    => '<rect x="2.8" y="6.8" width="18.4" height="13.4" rx="3.2"></rect><path d="m7.5 3 3.2 3.8M16.5 3l-3.2 3.8M8.3 11.6v1.7M15.7 11.6v1.7M8.5 16.7h7"></path>',
		'link'        => '<path d="M10 13a5 5 0 0 0 7.5.5l1.5-1.5A5 5 0 0 0 12 5l-1 1"></path><path d="M14 11a5 5 0 0 0-7.5-.5L5 12a5 5 0 0 0 7 7l1-1"></path>',
		'copy'        => '<rect x="8" y="8" width="12" height="12" rx="2"></rect><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"></path>',
		'check'       => '<path d="m5 12 4.5 4.5L19 6.5"></path>',
		'share'       => '<circle cx="18" cy="5" r="2.6"></circle><circle cx="6" cy="12" r="2.6"></circle><circle cx="18" cy="19" r="2.6"></circle><path d="m8.4 10.7 7.2-4.2M8.4 13.3l7.2 4.2"></path>',
		'chevron-down' => '<path d="m6 9 6 6 6-6"></path>',
		'terminal'    => '<rect x="3" y="4.5" width="18" height="15" rx="2.5"></rect><path d="m7 9 3 3-3 3M13 15h4"></path>',
		'sparkle'     => '<path d="M12 3c.5 4.6 3.4 7.5 8 8-4.6.5-7.5 3.4-8 8-.5-4.6-3.4-7.5-8-8 4.6-.5 7.5-3.4 8-8Z"></path>',
		'edit'        => '<path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path>',
		'home'        => '<path d="m3 10 9-7 9 7v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"></path><path d="M9 21v-7h6v7"></path>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	$size  = absint( $size );
	$size  = $size ? $size : 18;
	$class = trim( 'icon ' . $class );

	return sprintf(
		'<svg class="%1$s" width="%2$d" height="%2$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%3$s</svg>',
		esc_attr( $class ),
		$size,
		$icons[ $name ]
	);
}

/**
 * Print an inline SVG icon.
 *
 * @param string $name  Icon name.
 * @param int    $size  Pixel size.
 * @param string $class Extra CSS classes.
 */
function nanocat_icon( $name, $size = 18, $class = '' ) {
	echo nanocat_get_icon( $name, $size, $class ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static, trusted SVG markup.
}
