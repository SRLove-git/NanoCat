<?php
/**
 * Wiki post type and helpers.
 *
 * The wiki is a hierarchical post type: top-level entries are projects and
 * their children are documentation pages. This keeps editing native while
 * still allowing a project tree.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the wiki post type.
 */
function nanocat_register_wiki_cpt() {
	register_post_type(
		'nanocat_wiki',
		array(
			'labels'             => array(
				'name'               => __( 'Wiki', 'nanocat' ),
				'singular_name'      => __( 'Wiki 页面', 'nanocat' ),
				'menu_name'          => __( 'Wiki', 'nanocat' ),
				'all_items'          => __( '全部 Wiki', 'nanocat' ),
				'add_new'            => __( '新建页面', 'nanocat' ),
				'add_new_item'       => __( '新建 Wiki 页面', 'nanocat' ),
				'edit_item'          => __( '编辑 Wiki 页面', 'nanocat' ),
				'new_item'           => __( '新 Wiki 页面', 'nanocat' ),
				'view_item'          => __( '查看 Wiki 页面', 'nanocat' ),
				'view_items'         => __( '查看 Wiki', 'nanocat' ),
				'search_items'       => __( '搜索 Wiki', 'nanocat' ),
				'not_found'          => __( '还没有 Wiki 页面。', 'nanocat' ),
				'not_found_in_trash' => __( '回收站里没有 Wiki 页面。', 'nanocat' ),
				'parent_item_colon'  => __( '父级 Wiki：', 'nanocat' ),
				'archives'           => __( 'Wiki 归档', 'nanocat' ),
			),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'show_in_nav_menus'  => true,
			'has_archive'        => true,
			'rewrite'            => array(
				'slug'       => 'wiki',
				'with_front' => false,
				'pages'      => true,
			),
			'hierarchical'       => true,
			'menu_icon'          => 'dashicons-book-alt',
			'menu_position'      => 21,
			'supports'           => array( 'title', 'editor', 'excerpt', 'page-attributes', 'thumbnail', 'revisions' ),
			'taxonomies'         => array(),
		)
	);
}
add_action( 'init', 'nanocat_register_wiki_cpt', 9 );

/**
 * Return top-level wiki projects.
 *
 * @param int $limit Maximum number, 0 for all.
 * @return WP_Post[]
 */
function nanocat_get_wiki_roots( $limit = 0 ) {
	$args = array(
		'post_type'      => 'nanocat_wiki',
		'post_status'    => 'publish',
		'posts_per_page' => $limit > 0 ? (int) $limit : -1,
		'orderby'        => array(
			'menu_order' => 'ASC',
			'title'      => 'ASC',
		),
		'no_found_rows'  => true,
	);

	if ( $limit > 0 ) {
		$args['posts_per_page'] = (int) $limit;
	}

	$roots = new WP_Query(
		array_merge(
			$args,
			array(
				'post_parent' => 0,
			)
		)
	);

	return $roots->posts;
}

/**
 * All published descendants of a wiki project.
 *
 * @param int $root_id Project ID.
 * @return WP_Post[]
 */
function nanocat_get_wiki_pages( $root_id ) {
	static $cache = array();
	$root_id     = (int) $root_id;

	if ( isset( $cache[ $root_id ] ) ) {
		return $cache[ $root_id ];
	}

	$pages = get_pages(
		array(
			'post_type'   => 'nanocat_wiki',
			'post_status' => 'publish',
			'child_of'    => $root_id,
			'sort_column' => 'menu_order,post_title',
			'sort_order'  => 'ASC',
		)
	);

	$cache[ $root_id ] = is_array( $pages ) ? $pages : array();

	return $cache[ $root_id ];
}

/**
 * Find the project (top-level ancestor) for a wiki page.
 *
 * @param int $post_id Wiki page ID.
 * @return WP_Post|null
 */
function nanocat_get_wiki_root( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$post    = get_post( $post_id );

	if ( ! $post || 'nanocat_wiki' !== $post->post_type ) {
		return null;
	}

	$ancestors = get_post_ancestors( $post_id );
	if ( $ancestors ) {
		$root_id = (int) end( $ancestors );
		$root    = get_post( $root_id );
		if ( $root ) {
			return $root;
		}
	}

	return $post;
}

/**
 * Number of documents in a project, including the project overview.
 *
 * @param int $root_id Project ID.
 * @return int
 */
function nanocat_get_wiki_count( $root_id ) {
	return count( nanocat_get_wiki_pages( $root_id ) ) + 1;
}

/**
 * Pre-order flattening of a project tree, used for previous/next links.
 *
 * @param int $root_id Project ID.
 * @return WP_Post[]
 */
function nanocat_get_wiki_flat( $root_id ) {
	$root  = get_post( $root_id );
	$pages = nanocat_get_wiki_pages( $root_id );

	if ( ! $root ) {
		return array();
	}

	$by_parent = array();
	foreach ( $pages as $page ) {
		$by_parent[ (int) $page->post_parent ][] = $page;
	}

	$flat = array();
	$walk = function ( $parent_id ) use ( &$walk, &$flat, $by_parent ) {
		if ( empty( $by_parent[ $parent_id ] ) ) {
			return;
		}

		foreach ( $by_parent[ $parent_id ] as $page ) {
			$flat[] = $page;
			$walk( $page->ID );
		}
	};

	$flat[] = $root;
	$walk( $root->ID );

	return $flat;
}

/**
 * Render one level of the wiki tree.
 *
 * @param int   $parent_id Parent page ID.
 * @param array $by_parent Pages grouped by parent.
 * @param int   $current_id Current page ID.
 * @param array $ancestors Ancestor IDs of the current page.
 */
function nanocat_wiki_tree_branch( $parent_id, $by_parent, $current_id, $ancestors ) {
	if ( empty( $by_parent[ $parent_id ] ) ) {
		return;
	}

	echo '<ul class="wiki-tree__list">';
	foreach ( $by_parent[ $parent_id ] as $page ) {
		$is_current  = (int) $page->ID === (int) $current_id;
		$is_ancestor = in_array( (int) $page->ID, $ancestors, true );
		$classes     = 'wiki-tree__item';

		if ( $is_current ) {
			$classes .= ' is-current';
		}
		if ( $is_ancestor ) {
			$classes .= ' is-ancestor';
		}

		echo '<li class="' . esc_attr( $classes ) . '">';
		printf(
			'<a href="%1$s"%2$s><span>%3$s</span>%4$s</a>',
			esc_url( get_permalink( $page ) ),
			$is_current ? ' aria-current="page"' : '',
			esc_html( get_the_title( $page ) ),
			! empty( $by_parent[ $page->ID ] ) ? nanocat_get_icon( 'chevron-down', 14 ) : '' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted icon markup.
		);
		nanocat_wiki_tree_branch( $page->ID, $by_parent, $current_id, $ancestors );
		echo '</li>';
	}
	echo '</ul>';
}

/**
 * Print a project's page tree.
 *
 * @param int $root_id Project ID.
 * @param int $current_id Current page ID.
 */
function nanocat_wiki_tree( $root_id, $current_id = 0 ) {
	$pages = nanocat_get_wiki_pages( $root_id );

	if ( empty( $pages ) ) {
		return;
	}

	$by_parent = array();
	foreach ( $pages as $page ) {
		$by_parent[ (int) $page->post_parent ][] = $page;
	}

	$ancestors = $current_id ? array_map( 'intval', get_post_ancestors( $current_id ) ) : array();
	nanocat_wiki_tree_branch( (int) $root_id, $by_parent, (int) $current_id, $ancestors );
}

/**
 * Previous and next document in a project.
 *
 * @param int $root_id Project ID.
 * @param int $current_id Current page ID.
 * @return array{previous: WP_Post|null, next: WP_Post|null}
 */
function nanocat_get_wiki_siblings( $root_id, $current_id ) {
	$flat     = nanocat_get_wiki_flat( $root_id );
	$ids      = wp_list_pluck( $flat, 'ID' );
	$position = array_search( (int) $current_id, array_map( 'intval', $ids ), true );

	return array(
		'previous' => false !== $position && isset( $flat[ $position - 1 ] ) ? $flat[ $position - 1 ] : null,
		'next'     => false !== $position && isset( $flat[ $position + 1 ] ) ? $flat[ $position + 1 ] : null,
	);
}
