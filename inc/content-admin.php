<?php
/**
 * Content setup, admin hub and one-time page installation.
 *
 * @package NanoCat
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a page for a content hub when it does not exist yet.
 *
 * @param string $slug     Page slug.
 * @param string $title    Page title.
 * @param string $template Page template filename.
 * @return int Page ID, or 0 on failure.
 */
function nanocat_create_content_page( $slug, $title, $template ) {
	$page = get_page_by_path( $slug );

	if ( $page instanceof WP_Post ) {
		if ( get_page_template_slug( $page->ID ) !== $template ) {
			update_post_meta( $page->ID, '_wp_page_template', $template );
		}
		return (int) $page->ID;
	}

	$page_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => '',
		),
		true
	);

	if ( is_wp_error( $page_id ) ) {
		return 0;
	}

	update_post_meta( $page_id, '_wp_page_template', $template );

	return (int) $page_id;
}

/**
 * Install pages and flush rules once per content version.
 *
 * Runs on admin_init so the taxonomy and post type are already registered.
 * It is intentionally idempotent: existing pages are reused.
 */
function nanocat_maybe_setup_content() {
	$version = (string) get_option( 'nanocat_content_version', '' );

	if ( NANOCAT_VERSION === $version ) {
		return;
	}

	nanocat_create_content_page( 'topics', __( '专栏', 'nanocat' ), 'page-topics.php' );
	nanocat_create_content_page( 'links', __( '友链', 'nanocat' ), 'page-links.php' );
	nanocat_create_content_page( 'ai', __( 'AI 工作台', 'nanocat' ), 'page-ai.php' );

	update_option( 'nanocat_content_version', NANOCAT_VERSION, false );
	nanocat_schedule_link_refresh();
	flush_rewrite_rules();
}
add_action( 'admin_init', 'nanocat_maybe_setup_content' );

/**
 * Flush rules and install pages when the theme is activated.
 */
function nanocat_activate_content() {
	if ( function_exists( 'nanocat_register_topic_taxonomy' ) ) {
		nanocat_register_topic_taxonomy();
	}
	if ( function_exists( 'nanocat_register_wiki_cpt' ) ) {
		nanocat_register_wiki_cpt();
	}
	if ( function_exists( 'nanocat_topic_rewrite_rules' ) ) {
		nanocat_topic_rewrite_rules();
	}

	nanocat_maybe_setup_content();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'nanocat_activate_content' );

/**
 * Add a small management hub under Appearance.
 */
function nanocat_content_admin_menu() {
	add_theme_page(
		__( 'NanoCat 内容', 'nanocat' ),
		__( 'NanoCat 内容', 'nanocat' ),
		'edit_theme_options',
		'nanocat-content',
		'nanocat_render_content_admin'
	);
}
add_action( 'admin_menu', 'nanocat_content_admin_menu' );

/**
 * Count published wiki pages.
 *
 * @return int
 */
function nanocat_count_wiki_pages() {
	$counts = wp_count_posts( 'nanocat_wiki' );

	return isset( $counts->publish ) ? (int) $counts->publish : 0;
}

/**
 * Render the content management hub.
 */
function nanocat_render_content_admin() {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	$topics       = wp_count_terms(
		array(
			'taxonomy'   => 'nanocat_topic',
			'hide_empty' => false,
		)
	);
	$topics       = is_wp_error( $topics ) ? 0 : (int) $topics;
	$wiki_count   = nanocat_count_wiki_pages();
	$links_count  = count( get_bookmarks() );
	$last_refresh = (int) get_option( 'nanocat_links_refreshed', 0 );
	$topics_page  = get_page_by_path( 'topics' );
	$links_page   = get_page_by_path( 'links' );
	$ai_page      = get_page_by_path( 'ai' );
	?>
	<div class="wrap nanocat-admin">
		<h1><?php esc_html_e( 'NanoCat 内容', 'nanocat' ); ?></h1>
		<p class="nanocat-admin__lead"><?php esc_html_e( '专栏、Wiki 和友链都使用 WordPress 原生内容结构。这里可以快速进入管理页面，并刷新友链的 RSS 缓存。', 'nanocat' ); ?></p>

		<div class="nanocat-admin__grid">
			<section class="nanocat-admin__card">
				<span class="nanocat-admin__kicker">// AI WORKSPACE</span>
				<h2><?php esc_html_e( 'AI 工作台', 'nanocat' ); ?></h2>
				<p class="nanocat-admin__number"><?php echo esc_html( number_format_i18n( $topics + $wiki_count + $links_count ) ); ?></p>
				<p><?php esc_html_e( '站内 AI 会检索专栏、Wiki 和文章后再回答。工作台页提供完整对话、知识地图和最近提问。', 'nanocat' ); ?></p>
				<p class="nanocat-admin__actions">
					<?php if ( $ai_page ) : ?>
						<a class="button button-primary" href="<?php echo esc_url( get_permalink( $ai_page ) ); ?>"><?php esc_html_e( '打开 AI 工作台', 'nanocat' ); ?></a>
					<?php endif; ?>
					<a class="button" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( '外观设置', 'nanocat' ); ?></a>
				</p>
			</section>

			<section class="nanocat-admin__card">
				<span class="nanocat-admin__kicker">// TOPICS</span>
				<h2><?php esc_html_e( '专栏', 'nanocat' ); ?></h2>
				<p class="nanocat-admin__number"><?php echo esc_html( number_format_i18n( $topics ) ); ?></p>
				<p><?php esc_html_e( '给文章勾选专栏，即可形成系列。支持图标、排序值和连载顺序。', 'nanocat' ); ?></p>
				<p class="nanocat-admin__actions">
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=nanocat_topic&post_type=post' ) ); ?>"><?php esc_html_e( '管理专栏', 'nanocat' ); ?></a>
					<?php if ( $topics_page ) : ?>
						<a class="button" href="<?php echo esc_url( get_permalink( $topics_page ) ); ?>"><?php esc_html_e( '查看专栏页', 'nanocat' ); ?></a>
					<?php endif; ?>
				</p>
			</section>

			<section class="nanocat-admin__card">
				<span class="nanocat-admin__kicker">// WIKI</span>
				<h2><?php esc_html_e( 'Wiki', 'nanocat' ); ?></h2>
				<p class="nanocat-admin__number"><?php echo esc_html( number_format_i18n( $wiki_count ) ); ?></p>
				<p><?php esc_html_e( '顶层页面是项目，子页面是文档。用“页面属性 → 排序”控制目录顺序。', 'nanocat' ); ?></p>
				<p class="nanocat-admin__actions">
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=nanocat_wiki' ) ); ?>"><?php esc_html_e( '管理 Wiki', 'nanocat' ); ?></a>
					<a class="button" href="<?php echo esc_url( get_post_type_archive_link( 'nanocat_wiki' ) ); ?>"><?php esc_html_e( '查看 Wiki', 'nanocat' ); ?></a>
				</p>
			</section>

			<section class="nanocat-admin__card">
				<span class="nanocat-admin__kicker">// FRIENDS</span>
				<h2><?php esc_html_e( '友链与 RSS', 'nanocat' ); ?></h2>
				<p class="nanocat-admin__number"><?php echo esc_html( number_format_i18n( $links_count ) ); ?></p>
				<p><?php esc_html_e( '在链接管理里填写网站地址、头像和 RSS 地址。前台会显示好友的最新文章。', 'nanocat' ); ?></p>
				<p class="nanocat-admin__actions">
					<a class="button button-primary" href="<?php echo esc_url( admin_url( 'link-manager.php' ) ); ?>"><?php esc_html_e( '管理友链', 'nanocat' ); ?></a>
					<?php if ( $links_page ) : ?>
						<a class="button" href="<?php echo esc_url( get_permalink( $links_page ) ); ?>"><?php esc_html_e( '查看友链页', 'nanocat' ); ?></a>
					<?php endif; ?>
				</p>
			</section>
		</div>

		<section class="nanocat-admin__panel">
			<div>
				<h2><?php esc_html_e( '刷新 RSS 缓存', 'nanocat' ); ?></h2>
				<p>
					<?php if ( $last_refresh ) : ?>
						<?php
						printf(
							/* translators: %s: human-readable time difference. */
							esc_html__( '上次刷新：%s 前。', 'nanocat' ),
							esc_html( human_time_diff( $last_refresh, time() ) )
						);
						?>
					<?php else : ?>
						<?php esc_html_e( '还没有刷新过。WordPress 会每 12 小时自动执行一次，也可以现在手动刷新。', 'nanocat' ); ?>
					<?php endif; ?>
				</p>
			</div>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="nanocat_refresh_links">
				<?php wp_nonce_field( 'nanocat_refresh_links' ); ?>
				<button type="submit" class="button button-secondary"><?php esc_html_e( '立即刷新', 'nanocat' ); ?></button>
			</form>
		</section>

		<p class="nanocat-admin__foot">
			<a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( '打开外观设置', 'nanocat' ); ?></a>
			<span aria-hidden="true">·</span>
			<?php esc_html_e( '首页与文章页会自动出现专栏入口；友链页和专栏页在首次启用主题时自动创建。', 'nanocat' ); ?>
		</p>
	</div>

	<style>
		.nanocat-admin{max-width:1080px}
		.nanocat-admin__lead{max-width:760px;color:#646970;font-size:15px;line-height:1.7}
		.nanocat-admin__grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px;margin:24px 0}
		.nanocat-admin__card,.nanocat-admin__panel{background:#fff;border:1px solid #dcdcde;border-radius:12px;padding:22px;box-sizing:border-box}
		.nanocat-admin__kicker{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:11px;letter-spacing:.08em;color:#5a9b95}
		.nanocat-admin__card h2{margin:.35em 0 .2em;font-size:18px}
		.nanocat-admin__number{font-size:34px;font-weight:800;line-height:1;margin:.4em 0;color:#2f9e8a}
		.nanocat-admin__card p{color:#50575e;line-height:1.6}
		.nanocat-admin__actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:18px!important}
		.nanocat-admin__panel{display:flex;align-items:center;justify-content:space-between;gap:24px}
		.nanocat-admin__panel h2{margin:0 0 .3em;font-size:18px}
		.nanocat-admin__panel p{margin:0;color:#646970}
		.nanocat-admin__foot{margin-top:20px;color:#646970}
		@media (max-width:1180px){.nanocat-admin__grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
		@media (max-width:960px){.nanocat-admin__grid{grid-template-columns:1fr}}
		@media (max-width:600px){.nanocat-admin__panel{align-items:flex-start;flex-direction:column}}
	</style>
	<?php
}

/**
 * Explain the RSS field on the core link manager screen.
 */
function nanocat_link_manager_notice() {
	$screen = get_current_screen();

	if ( ! $screen || 'link-manager' !== $screen->id ) {
		return;
	}

	echo '<div class="notice notice-info"><p>' . esc_html__( 'NanoCat：为友链填写“RSS 地址”后，友链页会显示对方最近 3 篇文章；健康状态与文章缓存可在“外观 → NanoCat 内容”中刷新。', 'nanocat' ) . '</p></div>';
}
add_action( 'admin_notices', 'nanocat_link_manager_notice' );
