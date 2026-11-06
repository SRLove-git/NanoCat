<?php
/**
 * Topic (column) taxonomy, term fields and helpers.
 *
 * A topic is a curated series of posts. It intentionally uses a taxonomy
 * instead of a second post type so posts can belong to a topic without
 * duplicating content.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the topic taxonomy.
 */
function nanocat_register_topic_taxonomy() {
	register_taxonomy(
		'nanocat_topic',
		array( 'post' ),
		array(
			'labels'            => array(
				'name'                       => __( '专栏', 'nanocat' ),
				'singular_name'              => __( '专栏', 'nanocat' ),
				'menu_name'                  => __( '专栏', 'nanocat' ),
				'all_items'                  => __( '全部专栏', 'nanocat' ),
				'edit_item'                  => __( '编辑专栏', 'nanocat' ),
				'view_item'                  => __( '查看专栏', 'nanocat' ),
				'update_item'                => __( '更新专栏', 'nanocat' ),
				'add_new_item'               => __( '新建专栏', 'nanocat' ),
				'new_item_name'              => __( '新专栏名称', 'nanocat' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'search_items'               => __( '搜索专栏', 'nanocat' ),
				'popular_items'              => __( '常用专栏', 'nanocat' ),
				'separate_items_with_commas' => __( '多个专栏用逗号分隔', 'nanocat' ),
				'add_or_remove_items'        => __( '添加或移除专栏', 'nanocat' ),
				'choose_from_most_used'      => __( '从常用专栏中选择', 'nanocat' ),
				'not_found'                  => __( '没有找到专栏。', 'nanocat' ),
				'back_to_items'              => __( '返回专栏列表', 'nanocat' ),
			),
			'public'            => true,
			'publicly_queryable' => true,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_nav_menus' => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'hierarchical'      => false,
			'rewrite'           => array(
				'slug'       => 'topics',
				'with_front' => false,
			),
		)
	);
}
add_action( 'init', 'nanocat_register_topic_taxonomy', 9 );

/**
 * Keep topic term links working even when the /topics/ landing page exists.
 *
 * WordPress page rules can otherwise claim /topics/my-series/ before the
 * taxonomy rule. These explicit top-level rules keep the taxonomy canonical.
 */
function nanocat_topic_rewrite_rules() {
	add_rewrite_rule( '^topics/([^/]+)/page/([0-9]{1,})/?$', 'index.php?nanocat_topic=$matches[1]&paged=$matches[2]', 'top' );
	add_rewrite_rule( '^topics/([^/]+)/?$', 'index.php?nanocat_topic=$matches[1]', 'top' );
}
add_action( 'init', 'nanocat_topic_rewrite_rules', 11 );

/**
 * Honour the topic's chosen reading order on its archive.
 *
 * @param WP_Query $query Main query.
 */
function nanocat_topic_archive_order( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_tax( 'nanocat_topic' ) ) {
		return;
	}

	$term = get_queried_object();
	if ( ! $term instanceof WP_Term ) {
		return;
	}

	$query->set( 'orderby', 'date' );
	$query->set( 'order', 'date' === nanocat_get_topic_sort( $term ) ? 'ASC' : 'DESC' );
}
add_action( 'pre_get_posts', 'nanocat_topic_archive_order' );

/**
 * Return topics ordered by the optional manual order, then name.
 *
 * @param int  $limit      Maximum number of terms, 0 for all.
 * @param bool $hide_empty Only return topics containing posts.
 * @return WP_Term[]
 */
function nanocat_get_topics( $limit = 0, $hide_empty = true ) {
	$terms = get_terms(
		array(
			'taxonomy'   => 'nanocat_topic',
			'hide_empty' => (bool) $hide_empty,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	usort(
		$terms,
		function ( $a, $b ) {
			$order_a = (int) get_term_meta( $a->term_id, 'nanocat_topic_order', true );
			$order_b = (int) get_term_meta( $b->term_id, 'nanocat_topic_order', true );

			if ( $order_a === $order_b ) {
				return strcasecmp( $a->name, $b->name );
			}

			return $order_a - $order_b;
		}
	);

	if ( $limit > 0 ) {
		$terms = array_slice( $terms, 0, (int) $limit );
	}

	return $terms;
}

/**
 * Get a topic icon URL.
 *
 * @param WP_Term $term Topic term.
 * @return string
 */
function nanocat_get_topic_icon( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '';
	}

	return esc_url( (string) get_term_meta( $term->term_id, 'nanocat_topic_icon', true ) );
}

/**
 * Get the post order for a topic.
 *
 * @param WP_Term $term Topic term.
 * @return string Either "date" or "-date".
 */
function nanocat_get_topic_sort( $term ) {
	if ( ! $term instanceof WP_Term ) {
		return '-date';
	}

	$sort = (string) get_term_meta( $term->term_id, 'nanocat_topic_sort', true );

	return 'date' === $sort ? 'date' : '-date';
}

/**
 * Get the topics attached to a post.
 *
 * @param int $post_id Post ID.
 * @return WP_Term[]
 */
function nanocat_get_post_topics( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : get_the_ID();
	$terms   = get_the_terms( $post_id, 'nanocat_topic' );

	return is_array( $terms ) ? $terms : array();
}

/**
 * Get posts belonging to a topic in the configured order.
 *
 * @param WP_Term $term  Topic term.
 * @param int     $limit Number of posts, -1 for all.
 * @param array   $extra Extra WP_Query args.
 * @return WP_Query
 */
function nanocat_get_topic_posts( $term, $limit = -1, $extra = array() ) {
	$args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => (int) $limit,
		'ignore_sticky_posts' => true,
		'orderby'             => 'date',
		'order'               => 'date' === nanocat_get_topic_sort( $term ) ? 'ASC' : 'DESC',
		'tax_query'           => array(
			array(
				'taxonomy' => 'nanocat_topic',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		),
	);

	if ( -1 !== (int) $limit ) {
		$args['no_found_rows'] = true;
	}

	return new WP_Query( array_merge( $args, $extra ) );
}

/**
 * Render the term icon, or a typographic fallback.
 *
 * @param WP_Term $term  Topic term.
 * @param string  $class Extra class.
 */
function nanocat_topic_icon( $term, $class = '' ) {
	$icon  = nanocat_get_topic_icon( $term );
	$class = trim( 'topic-icon ' . $class );

	if ( $icon ) {
		printf(
			'<span class="%1$s"><img src="%2$s" alt="" width="56" height="56" loading="lazy"></span>',
			esc_attr( $class ),
			esc_url( $icon )
		);
		return;
	}

	$letter = function_exists( 'nanocat_substr' ) ? nanocat_substr( $term->name, 0, 1 ) : substr( $term->name, 0, 1 );

	printf(
		'<span class="%1$s topic-icon--letter" aria-hidden="true">%2$s</span>',
		esc_attr( $class ),
		esc_html( $letter )
	);
}

/**
 * Add the icon field to the "new topic" form.
 */
function nanocat_topic_add_form_fields() {
	wp_nonce_field( 'nanocat_topic_meta', 'nanocat_topic_meta_nonce' );
	?>
	<div class="form-field">
		<label for="nanocat-topic-icon"><?php esc_html_e( '专栏图标 URL', 'nanocat' ); ?></label>
		<input type="url" name="nanocat_topic_icon" id="nanocat-topic-icon" value="" placeholder="https://">
		<p><?php esc_html_e( '可选。建议使用正方形图片；留空则显示专栏首字。', 'nanocat' ); ?></p>
	</div>
	<div class="form-field">
		<label for="nanocat-topic-order"><?php esc_html_e( '排序值', 'nanocat' ); ?></label>
		<input type="number" name="nanocat_topic_order" id="nanocat-topic-order" value="0" step="1">
		<p><?php esc_html_e( '数字越小越靠前。', 'nanocat' ); ?></p>
	</div>
	<div class="form-field">
		<label for="nanocat-topic-sort"><?php esc_html_e( '文章顺序', 'nanocat' ); ?></label>
		<select name="nanocat_topic_sort" id="nanocat-topic-sort">
			<option value="-date"><?php esc_html_e( '最新发布在前', 'nanocat' ); ?></option>
			<option value="date"><?php esc_html_e( '从旧到新（连载顺序）', 'nanocat' ); ?></option>
		</select>
	</div>
	<?php
}
add_action( 'nanocat_topic_add_form_fields', 'nanocat_topic_add_form_fields' );

/**
 * Add the icon fields to the "edit topic" form.
 *
 * @param WP_Term $term Topic term.
 */
function nanocat_topic_edit_form_fields( $term ) {
	$icon  = (string) get_term_meta( $term->term_id, 'nanocat_topic_icon', true );
	$order = (int) get_term_meta( $term->term_id, 'nanocat_topic_order', true );
	$sort  = nanocat_get_topic_sort( $term );
	wp_nonce_field( 'nanocat_topic_meta', 'nanocat_topic_meta_nonce' );
	?>
	<tr class="form-field">
		<th scope="row"><label for="nanocat-topic-icon"><?php esc_html_e( '专栏图标 URL', 'nanocat' ); ?></label></th>
		<td>
			<input type="url" name="nanocat_topic_icon" id="nanocat-topic-icon" value="<?php echo esc_attr( $icon ); ?>" placeholder="https://">
			<p class="description"><?php esc_html_e( '建议使用正方形图片；留空则显示专栏首字。', 'nanocat' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="nanocat-topic-order"><?php esc_html_e( '排序值', 'nanocat' ); ?></label></th>
		<td>
			<input type="number" name="nanocat_topic_order" id="nanocat-topic-order" value="<?php echo esc_attr( (string) $order ); ?>" step="1">
			<p class="description"><?php esc_html_e( '数字越小越靠前。', 'nanocat' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="nanocat-topic-sort"><?php esc_html_e( '文章顺序', 'nanocat' ); ?></label></th>
		<td>
			<select name="nanocat_topic_sort" id="nanocat-topic-sort">
				<option value="-date" <?php selected( $sort, '-date' ); ?>><?php esc_html_e( '最新发布在前', 'nanocat' ); ?></option>
				<option value="date" <?php selected( $sort, 'date' ); ?>><?php esc_html_e( '从旧到新（连载顺序）', 'nanocat' ); ?></option>
			</select>
		</td>
	</tr>
	<?php
}
add_action( 'nanocat_topic_edit_form_fields', 'nanocat_topic_edit_form_fields' );

/**
 * Save topic term fields.
 *
 * @param int $term_id Term ID.
 */
function nanocat_save_topic_meta( $term_id ) {
	if ( ! isset( $_POST['nanocat_topic_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nanocat_topic_meta_nonce'] ) ), 'nanocat_topic_meta' ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_categories' ) ) {
		return;
	}

	$icon = isset( $_POST['nanocat_topic_icon'] ) ? esc_url_raw( wp_unslash( $_POST['nanocat_topic_icon'] ) ) : '';
	if ( $icon ) {
		update_term_meta( $term_id, 'nanocat_topic_icon', $icon );
	} else {
		delete_term_meta( $term_id, 'nanocat_topic_icon' );
	}

	$order = isset( $_POST['nanocat_topic_order'] ) ? (int) $_POST['nanocat_topic_order'] : 0;
	update_term_meta( $term_id, 'nanocat_topic_order', $order );

	$sort = isset( $_POST['nanocat_topic_sort'] ) ? sanitize_key( wp_unslash( $_POST['nanocat_topic_sort'] ) ) : '-date';
	update_term_meta( $term_id, 'nanocat_topic_sort', 'date' === $sort ? 'date' : '-date' );
}
add_action( 'created_nanocat_topic', 'nanocat_save_topic_meta' );
add_action( 'edited_nanocat_topic', 'nanocat_save_topic_meta' );
