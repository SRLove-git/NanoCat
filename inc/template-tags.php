<?php
/**
 * Reusable template helpers.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Theme defaults used by the Customizer and templates.
 *
 * @return array
 */
function nanocat_get_defaults() {
	$defaults = array(
		'hero_enabled'        => true,
		'hero_eyebrow'        => 'NANOCAT INTELLIGENCE',
		'hero_title'          => '让整座博客，<br><span class="hero-mark">回答你</span>。',
		'hero_text'           => '站内 AI 会翻专栏、Wiki 和每一篇文章，再带着来源回答你。',
		'hero_primary_label'  => '开始阅读',
		'hero_primary_url'    => '',
		'hero_secondary_label' => '订阅 RSS',
		'hero_secondary_url'  => '',
		'profile_role'        => 'Front-end developer / 猫奴',
		'profile_bio'         => '白天写界面，晚上写 bug。相信好的设计会呼吸，也相信猫能治愈一切。',
		'social_github'       => '',
		'social_bilibili'     => '',
		'social_email'        => '',
		'footer_text'         => '保持好奇，持续构建。',
	);

	return apply_filters( 'nanocat_defaults', $defaults );
}

/**
 * Get one theme default.
 *
 * @param string $key Setting key.
 * @return mixed
 */
function nanocat_get_default( $key ) {
	$defaults = nanocat_get_defaults();

	return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
}

/**
 * Inline NanoCat cat mark.
 *
 * @param string $class Extra classes.
 * @return string
 */
function nanocat_get_mark( $class = '' ) {
	static $instance = 0;
	$instance++;
	$gradient_id = 'nanocat-mark-gradient-' . $instance;
	$class       = trim( 'brand-mark ' . $class );

	return '<svg class="' . esc_attr( $class ) . '" viewBox="0 0 48 48" fill="none" aria-hidden="true" focusable="false">'
		. '<defs><linearGradient id="' . esc_attr( $gradient_id ) . '" x1="4" y1="3" x2="45" y2="46" gradientUnits="userSpaceOnUse">'
		. '<stop stop-color="#B9EEE5"></stop><stop offset=".52" stop-color="#2F9E8A"></stop><stop offset="1" stop-color="#4F8F88"></stop>'
		. '</linearGradient></defs>'
		. '<rect x="1" y="1" width="46" height="46" rx="14" fill="url(#' . esc_attr( $gradient_id ) . ')"></rect>'
		. '<path d="M13.4 21.2 10.3 10.8l10.2 4.4h7l10.2-4.4-3.1 10.4v8a7.8 7.8 0 0 1-7.8 7.8h-5.6a7.8 7.8 0 0 1-7.8-7.8v-8Z" fill="#F4F8FB"></path>'
		. '<path d="M18.4 24.4h.01M29.6 24.4h.01" stroke="#2B2335" stroke-width="2.7" stroke-linecap="round"></path>'
		. '<path d="M21.2 29.1c1.5 1.7 4.1 1.7 5.6 0" stroke="#2B2335" stroke-width="1.7" stroke-linecap="round"></path>'
		. '<path d="M8.3 23.2h5M8.3 26.8h5M34.7 23.2h5M34.7 26.8h5" stroke="#2B2335" stroke-width="1.2" stroke-linecap="round" opacity=".48"></path>'
		. '</svg>';
}

/**
 * Estimated reading time.
 *
 * @param int|null $post_id Post ID.
 * @return int Minutes.
 */
function nanocat_get_reading_time( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$content = get_post_field( 'post_content', $post_id );
	$content = wp_strip_all_tags( strip_shortcodes( (string) $content ) );

	$cjk   = preg_match_all( '/[\x{3040}-\x{30ff}\x{3400}-\x{9fff}]/u', $content );
	$words = preg_match_all( '/[A-Za-z0-9_]+/', $content );
	$mins  = (int) ceil( ( $cjk / 400 ) + ( $words / 200 ) );

	return max( 1, $mins );
}

/**
 * Multibyte-safe string length with a dependency-free fallback.
 *
 * @param string $text Text to measure.
 * @return int
 */
function nanocat_strlen( $text ) {
	if ( function_exists( 'mb_strlen' ) ) {
		return (int) mb_strlen( $text, 'UTF-8' );
	}

	$count = preg_match_all( '/./us', (string) $text, $matches );

	return false === $count ? strlen( (string) $text ) : (int) $count;
}

/**
 * Multibyte-safe substring with a dependency-free fallback.
 *
 * @param string $text   Source text.
 * @param int    $start  Start offset.
 * @param int    $length Number of characters.
 * @return string
 */
function nanocat_substr( $text, $start, $length ) {
	if ( function_exists( 'mb_substr' ) ) {
		return (string) mb_substr( (string) $text, $start, $length, 'UTF-8' );
	}

	$count = preg_match_all( '/./us', (string) $text, $matches );
	if ( false === $count ) {
		return (string) substr( (string) $text, $start, $length );
	}

	return implode( '', array_slice( $matches[0], $start, $length ) );
}

/**
 * Rough character count shown in the article aside.
 *
 * @param int|null $post_id Post ID.
 * @return int
 */
function nanocat_get_character_count( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$content = wp_strip_all_tags( strip_shortcodes( (string) get_post_field( 'post_content', $post_id ) ) );
	$content = preg_replace( '/\s+/u', '', $content );

	return max( 0, nanocat_strlen( $content ) );
}

/**
 * Trimmed, HTML-free excerpt for cards.
 *
 * @param int $length  Maximum characters.
 * @param int $post_id Post ID.
 * @return string
 */
function nanocat_get_excerpt( $length = 88, $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	if ( post_password_required( $post_id ) ) {
		return __( '这是一篇受密码保护的文章，输入密码后才能阅读。', 'nanocat' );
	}

	$excerpt = (string) get_post_field( 'post_excerpt', $post_id );
	if ( '' === trim( $excerpt ) ) {
		$excerpt = (string) get_post_field( 'post_content', $post_id );
	}

	$excerpt = strip_shortcodes( $excerpt );
	$excerpt = wp_strip_all_tags( $excerpt );
	$excerpt = preg_replace( '/\s+/u', ' ', $excerpt );
	$excerpt = trim( $excerpt );
	$length  = max( 1, absint( $length ) );

	if ( nanocat_strlen( $excerpt ) > $length ) {
		$excerpt = rtrim( nanocat_substr( $excerpt, 0, $length ) ) . '…';
	}

	return $excerpt;
}

/**
 * First category for a post.
 *
 * @param int $post_id Post ID.
 * @return WP_Term|null
 */
function nanocat_get_primary_category( $post_id = 0 ) {
	$categories = get_the_category( $post_id );

	return ! empty( $categories ) ? $categories[0] : null;
}

/**
 * URL of the decorative fallback cover.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function nanocat_get_fallback_cover( $post_id = 0 ) {
	$post_id = $post_id ? absint( $post_id ) : get_the_ID();
	$variant = ( $post_id % 3 ) + 1;

	return get_template_directory_uri() . '/assets/images/fallback-' . $variant . '.svg';
}

/**
 * Print a post cover, falling back to one of the bundled illustrations.
 *
 * @param string $size       Image size.
 * @param string $loading    Loading attribute.
 * @param string $extra_class Extra class on the image.
 * @param int    $post_id    Post ID.
 */
function nanocat_cover( $size = 'nanocat-card', $loading = 'lazy', $extra_class = '', $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$class   = trim( 'post-cover__img ' . $extra_class );

	if ( has_post_thumbnail( $post_id ) ) {
		echo get_the_post_thumbnail(
			$post_id,
			$size,
			array(
				'class'    => $class,
				'loading'  => $loading,
				'decoding' => 'async',
				'alt'      => '',
			)
		);
		return;
	}

	printf(
		'<img class="%1$s" src="%2$s" width="1200" height="800" alt="" loading="%3$s" decoding="async">',
		esc_attr( $class . ' is-fallback' ),
		esc_url( nanocat_get_fallback_cover( $post_id ) ),
		esc_attr( $loading )
	);
}

/**
 * Social links built from Customizer fields.
 *
 * @return array
 */
function nanocat_get_social_links() {
	$links = array();

	$github = get_theme_mod( 'nanocat_social_github', nanocat_get_default( 'social_github' ) );
	if ( $github ) {
		$links[] = array(
			'label'    => 'GitHub',
			'url'      => $github,
			'icon'     => 'github',
			'external' => true,
		);
	}

	$bilibili = get_theme_mod( 'nanocat_social_bilibili', nanocat_get_default( 'social_bilibili' ) );
	if ( $bilibili ) {
		$links[] = array(
			'label'    => '哔哩哔哩',
			'url'      => $bilibili,
			'icon'     => 'bilibili',
			'external' => true,
		);
	}

	$email = sanitize_email( get_theme_mod( 'nanocat_social_email', nanocat_get_default( 'social_email' ) ) );
	if ( $email ) {
		$links[] = array(
			'label'    => 'Email',
			'url'      => 'mailto:' . antispambot( $email ),
			'icon'     => 'mail',
			'external' => false,
		);
	}

	$links[] = array(
		'label'    => 'RSS',
		'url'      => get_feed_link(),
		'icon'     => 'rss',
		'external' => true,
	);

	return apply_filters( 'nanocat_social_links', $links );
}

/**
 * Print the social links list.
 *
 * @param string $class Extra class on the list.
 */
function nanocat_social_links( $class = '' ) {
	$links = nanocat_get_social_links();

	if ( empty( $links ) ) {
		return;
	}

	$class = trim( 'social-links ' . $class );
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $links as $link ) {
		echo '<li>';
		printf(
			'<a class="social-link" href="%1$s"%2$s>%3$s<span>%4$s</span>%5$s</a>',
			esc_url( $link['url'] ),
			$link['external'] ? ' target="_blank" rel="noopener noreferrer"' : '',
			nanocat_get_icon( $link['icon'], 15 ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted icon markup.
			esc_html( $link['label'] ),
			$link['external'] ? '<span class="screen-reader-text">' . esc_html__( '（在新窗口打开）', 'nanocat' ) . '</span>' : ''
		);
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Fallback primary navigation when no menu is assigned.
 */
function nanocat_primary_menu_fallback() {
	$links = array(
		array(
			'label'   => __( '首页', 'nanocat' ),
			'url'     => home_url( '/' ),
			'current' => is_front_page(),
		),
	);

	$posts_page = (int) get_option( 'page_for_posts' );
	if ( $posts_page ) {
		$links[] = array(
			'label'   => __( '文章', 'nanocat' ),
			'url'     => get_permalink( $posts_page ),
			'current' => is_home(),
		);
	}

	$topics_page = get_page_by_path( 'topics' );
	if ( $topics_page instanceof WP_Post && 'publish' === $topics_page->post_status ) {
		$links[] = array(
			'label'   => __( '专栏', 'nanocat' ),
			'url'     => get_permalink( $topics_page ),
			'current' => is_page( $topics_page->ID ) || is_tax( 'nanocat_topic' ),
		);
	}

	$wiki_archive = get_post_type_archive_link( 'nanocat_wiki' );
	if ( $wiki_archive ) {
		$links[] = array(
			'label'   => __( 'Wiki', 'nanocat' ),
			'url'     => $wiki_archive,
			'current' => is_post_type_archive( 'nanocat_wiki' ) || is_singular( 'nanocat_wiki' ),
		);
	}

	$links_page = get_page_by_path( 'links' );
	if ( $links_page instanceof WP_Post && 'publish' === $links_page->post_status ) {
		$links[] = array(
			'label'   => __( '友链', 'nanocat' ),
			'url'     => get_permalink( $links_page ),
			'current' => is_page( $links_page->ID ),
		);
	}

	$ai_page = get_page_by_path( 'ai' );
	if ( $ai_page instanceof WP_Post && 'publish' === $ai_page->post_status ) {
		$links[] = array(
			'label'   => __( 'AI', 'nanocat' ),
			'url'     => get_permalink( $ai_page ),
			'current' => is_page( $ai_page->ID ),
		);
	}

	foreach ( array( 'about', 'about-me' ) as $slug ) {
		$about = get_page_by_path( $slug );
		if ( $about instanceof WP_Post && 'publish' === $about->post_status ) {
			$links[] = array(
				'label'   => __( '关于', 'nanocat' ),
				'url'     => get_permalink( $about ),
				'current' => is_page( $about->ID ),
			);
			break;
		}
	}

	echo '<ul id="primary-menu" class="primary-menu menu">';
	foreach ( $links as $link ) {
		printf(
			'<li class="menu-item%1$s"><a href="%2$s"%3$s>%4$s</a></li>',
			$link['current'] ? ' current-menu-item' : '',
			esc_url( $link['url'] ),
			$link['current'] ? ' aria-current="page"' : '',
			esc_html( $link['label'] )
		);
	}
	echo '</ul>';
}

/**
 * Fallback footer navigation.
 */
function nanocat_footer_menu_fallback() {
	echo '<ul class="footer-menu">';
	printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/' ) ), esc_html__( '首页', 'nanocat' ) );

	$posts_page = (int) get_option( 'page_for_posts' );
	if ( $posts_page ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $posts_page ) ), esc_html__( '文章', 'nanocat' ) );
	}

	$topics_page = get_page_by_path( 'topics' );
	if ( $topics_page instanceof WP_Post && 'publish' === $topics_page->post_status ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $topics_page ) ), esc_html__( '专栏', 'nanocat' ) );
	}

	$wiki_archive = get_post_type_archive_link( 'nanocat_wiki' );
	if ( $wiki_archive ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( $wiki_archive ), esc_html__( 'Wiki', 'nanocat' ) );
	}

	$links_page = get_page_by_path( 'links' );
	if ( $links_page instanceof WP_Post && 'publish' === $links_page->post_status ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $links_page ) ), esc_html__( '友链', 'nanocat' ) );
	}

	$ai_page = get_page_by_path( 'ai' );
	if ( $ai_page instanceof WP_Post && 'publish' === $ai_page->post_status ) {
		printf( '<li><a href="%s">%s</a></li>', esc_url( get_permalink( $ai_page ) ), esc_html__( 'AI 工作台', 'nanocat' ) );
	}

	printf( '<li><a href="%s">%s</a></li>', esc_url( get_feed_link() ), esc_html__( 'RSS 订阅', 'nanocat' ) );
	echo '</ul>';
}

/**
 * Minimal breadcrumbs.
 */
function nanocat_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( '面包屑导航', 'nanocat' ) . '">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( '首页', 'nanocat' ) . '</a>';
	echo '<span class="breadcrumbs__sep" aria-hidden="true">/</span>';

	if ( is_single() ) {
		$category = nanocat_get_primary_category();
		if ( $category ) {
			printf(
				'<a href="%1$s">%2$s</a><span class="breadcrumbs__sep" aria-hidden="true">/</span>',
				esc_url( get_category_link( $category->term_id ) ),
				esc_html( $category->name )
			);
		}
		echo '<span aria-current="page">' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_page() ) {
		$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
		foreach ( $ancestors as $ancestor_id ) {
			printf(
				'<a href="%1$s">%2$s</a><span class="breadcrumbs__sep" aria-hidden="true">/</span>',
				esc_url( get_permalink( $ancestor_id ) ),
				esc_html( get_the_title( $ancestor_id ) )
			);
		}
		echo '<span aria-current="page">' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_category() ) {
		echo '<span aria-current="page">' . esc_html( single_cat_title( '', false ) ) . '</span>';
	} elseif ( is_tag() ) {
		echo '<span aria-current="page">' . esc_html( single_tag_title( '', false ) ) . '</span>';
	} elseif ( is_tax() ) {
		echo '<span aria-current="page">' . esc_html( single_term_title( '', false ) ) . '</span>';
	} elseif ( is_author() ) {
		echo '<span aria-current="page">' . esc_html( get_the_author() ) . '</span>';
	} elseif ( is_search() ) {
		/* translators: %s: search query. */
		echo '<span aria-current="page">' . esc_html( sprintf( __( '搜索：%s', 'nanocat' ), get_search_query() ) ) . '</span>';
	} elseif ( is_404() ) {
		echo '<span aria-current="page">' . esc_html__( '404', 'nanocat' ) . '</span>';
	} elseif ( is_home() ) {
		echo '<span aria-current="page">' . esc_html__( '文章', 'nanocat' ) . '</span>';
	} elseif ( is_archive() ) {
		echo '<span aria-current="page">' . esc_html( wp_strip_all_tags( get_the_archive_title() ) ) . '</span>';
	}

	echo '</nav>';
}

/**
 * Article meta row for cards.
 */
function nanocat_card_meta() {
	$category = nanocat_get_primary_category();
	?>
	<div class="post-card__meta">
		<?php if ( $category ) : ?>
			<a class="post-card__category" href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
		<?php endif; ?>
		<span class="meta-dot" aria-hidden="true"></span>
		<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
	</div>
	<?php
}

/**
 * Numbered pagination used on the blog, archives and search.
 */
function nanocat_pagination() {
	the_posts_pagination(
		array(
			'mid_size'           => 1,
			'prev_text'          => nanocat_get_icon( 'arrow-left', 16 ) . '<span>' . esc_html__( '上一页', 'nanocat' ) . '</span>',
			'next_text'          => '<span>' . esc_html__( '下一页', 'nanocat' ) . '</span>' . nanocat_get_icon( 'arrow-right', 16 ),
			'screen_reader_text' => __( '文章分页', 'nanocat' ),
			'aria_label'         => __( '文章分页', 'nanocat' ),
		)
	);
}

/**
 * Previous/next post cards.
 */
function nanocat_post_navigation() {
	$previous = get_previous_post();
	$next     = get_next_post();

	if ( ! $previous && ! $next ) {
		return;
	}
	?>
	<nav class="post-navigation" aria-label="<?php esc_attr_e( '文章导航', 'nanocat' ); ?>">
		<?php if ( $previous ) : ?>
			<a class="post-nav-card post-nav-card--prev" href="<?php echo esc_url( get_permalink( $previous ) ); ?>">
				<span class="post-nav-card__label"><?php nanocat_icon( 'arrow-left', 16 ); ?><?php esc_html_e( '上一篇', 'nanocat' ); ?></span>
				<strong><?php echo esc_html( get_the_title( $previous ) ); ?></strong>
			</a>
		<?php else : ?>
			<span class="post-nav-card is-empty"></span>
		<?php endif; ?>

		<?php if ( $next ) : ?>
			<a class="post-nav-card post-nav-card--next" href="<?php echo esc_url( get_permalink( $next ) ); ?>">
				<span class="post-nav-card__label"><?php esc_html_e( '下一篇', 'nanocat' ); ?><?php nanocat_icon( 'arrow-right', 16 ); ?></span>
				<strong><?php echo esc_html( get_the_title( $next ) ); ?></strong>
			</a>
		<?php endif; ?>
	</nav>
	<?php
}

/**
 * Related posts based on the current post's categories.
 */
function nanocat_related_posts() {
	$post_id    = get_the_ID();
	$categories = wp_get_post_categories( $post_id );
	$args       = array(
		'post__not_in'        => array( $post_id ),
		'posts_per_page'      => 3,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	if ( ! empty( $categories ) ) {
		$args['category__in'] = $categories;
	}

	$related = new WP_Query( $args );

	if ( ! $related->have_posts() ) {
		return;
	}
	?>
	<section class="related-posts" aria-labelledby="related-title">
		<div class="section-heading section-heading--small">
			<div>
				<p class="section-kicker"><?php esc_html_e( 'KEEP READING', 'nanocat' ); ?></p>
				<h2 id="related-title" class="section-title"><?php esc_html_e( '你可能还想读', 'nanocat' ); ?></h2>
			</div>
		</div>
		<div class="related-grid">
			<?php
			while ( $related->have_posts() ) :
				$related->the_post();
				?>
				<article class="related-card">
					<a class="related-card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
						<?php nanocat_cover( 'nanocat-card' ); ?>
					</a>
					<div class="related-card__body">
						<span class="related-card__meta"><?php echo esc_html( get_the_date() ); ?> · <?php echo esc_html( sprintf( _n( '%s 分钟', '%s 分钟', nanocat_get_reading_time(), 'nanocat' ), number_format_i18n( nanocat_get_reading_time() ) ) ); ?></span>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	</section>
	<?php
	wp_reset_postdata();
}

/**
 * Custom comment markup.
 *
 * @param WP_Comment $comment Comment object.
 * @param array      $args    Comment arguments.
 * @param int        $depth   Nesting depth.
 */
function nanocat_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
					if ( 0 !== (int) $args['avatar_size'] ) {
						echo get_avatar( $comment, (int) $args['avatar_size'], '', '', array( 'class' => 'avatar' ) );
					}
					?>
					<div class="comment-author__info">
						<cite class="fn"><?php echo get_comment_author_link( $comment ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core escapes this link. ?></cite>
						<a class="comment-date" href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
							<time datetime="<?php echo esc_attr( get_comment_date( DATE_W3C, $comment ) ); ?>">
								<?php echo esc_html( get_comment_date( '', $comment ) ); ?>
							</time>
						</a>
					</div>
				</div>
				<?php
				if ( '0' === $comment->comment_approved ) {
					echo '<p class="comment-awaiting-moderation">' . esc_html__( '你的评论正在等待审核。', 'nanocat' ) . '</p>';
				}
				edit_comment_link( __( '编辑', 'nanocat' ), '<span class="comment-edit-link">', '</span>' );
				?>
			</footer>

			<div class="comment-content">
				<?php comment_text(); ?>
			</div>

			<?php
			comment_reply_link(
				array_merge(
					$args,
					array(
						'add_below' => 'div-comment',
						'depth'      => $depth,
						'max_depth'  => $args['max_depth'],
						'reply_text' => __( '回复', 'nanocat' ),
						'before'     => '<div class="comment-actions">',
						'after'      => '</div>',
					)
				)
			);
			?>
		</article>
	<?php
	// The closing </li> is intentionally left to Walker_Comment.
}
