<?php
/**
 * Wiki project archive.
 *
 * @package NanoCat
 */

get_header();

$nanocat_roots       = nanocat_get_wiki_roots();
$nanocat_total_pages = 0;

foreach ( $nanocat_roots as $nanocat_root ) {
	$nanocat_total_pages += nanocat_get_wiki_count( $nanocat_root->ID );
}

$nanocat_topics = get_page_by_path( 'topics' );
$nanocat_links  = get_page_by_path( 'links' );
?>
<main id="primary" class="site-main wiki-view">
	<header class="hub-hero hub-hero--wiki">
		<div class="container hub-hero__inner">
			<div class="hub-hero__copy">
				<nav class="breadcrumbs" aria-label="<?php esc_attr_e( '面包屑导航', 'nanocat' ); ?>">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '首页', 'nanocat' ); ?></a>
					<span class="breadcrumbs__sep" aria-hidden="true">/</span>
					<span aria-current="page"><?php esc_html_e( 'Wiki', 'nanocat' ); ?></span>
				</nav>

				<p class="section-kicker"><?php esc_html_e( '// KNOWLEDGE_BASE', 'nanocat' ); ?></p>
				<h1 class="hub-hero__title"><?php esc_html_e( 'Wiki', 'nanocat' ); ?></h1>
				<p class="hub-hero__deck"><?php esc_html_e( '把踩过的坑、想明白的事和常用命令，整理成可以反复翻阅的文档。', 'nanocat' ); ?></p>

				<div class="hub-hero__meta">
					<span class="status-dot" aria-hidden="true"></span>
					<span>
						<?php
						printf(
							/* translators: 1: number of projects, 2: number of pages. */
							esc_html__( '%1$s 个项目 · %2$s 页文档', 'nanocat' ),
							esc_html( number_format_i18n( count( $nanocat_roots ) ) ),
							esc_html( number_format_i18n( $nanocat_total_pages ) )
						);
						?>
					</span>
				</div>
			</div>

			<div class="hub-hero__terminal" aria-hidden="true">
				<div class="hub-hero__terminal-bar">
					<span class="terminal__dots"><i></i><i></i><i></i></span>
					<span>cat@nanocat: ~/wiki</span>
				</div>
				<div class="hub-hero__terminal-body">
					<p><span class="t-prompt">$</span> find wiki -maxdepth 2</p>
					<?php if ( $nanocat_roots ) : ?>
						<?php foreach ( array_slice( $nanocat_roots, 0, 4 ) as $nanocat_root ) : ?>
							<p class="t-out t-out--branch">wiki/<?php echo esc_html( $nanocat_root->post_name ); ?>/</p>
						<?php endforeach; ?>
					<?php else : ?>
						<p class="t-out">wiki/ <span class="t-comment"># empty, for now</span></p>
					<?php endif; ?>
					<p><span class="t-prompt">$</span> echo "docs are love letters to future me"<span class="terminal__cursor"></span></p>
				</div>
			</div>
		</div>
	</header>

	<div class="container wiki-hub">
		<?php if ( $nanocat_roots ) : ?>
			<div class="wiki-grid">
				<?php foreach ( $nanocat_roots as $nanocat_root ) : ?>
					<?php
					$nanocat_count   = nanocat_get_wiki_count( $nanocat_root->ID );
					$nanocat_updated = get_the_modified_time( 'Y-m-d', $nanocat_root );
					$nanocat_excerpt = nanocat_get_excerpt( 90, $nanocat_root->ID );
					?>
					<article class="wiki-card">
						<a class="wiki-card__media" href="<?php echo esc_url( get_permalink( $nanocat_root ) ); ?>" aria-hidden="true" tabindex="-1">
							<?php if ( has_post_thumbnail( $nanocat_root ) ) : ?>
								<?php echo get_the_post_thumbnail( $nanocat_root, 'nanocat-card', array( 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<span class="wiki-card__glyph"><?php echo esc_html( nanocat_substr( get_the_title( $nanocat_root ), 0, 1 ) ); ?></span>
								<span class="wiki-card__brackets" aria-hidden="true">{ }</span>
							<?php endif; ?>
						</a>

						<div class="wiki-card__body">
							<div class="wiki-card__meta">
								<span><?php esc_html_e( 'PROJECT', 'nanocat' ); ?></span>
								<span class="meta-dot" aria-hidden="true"></span>
								<span><?php echo esc_html( sprintf( _n( '%s 页', '%s 页', $nanocat_count, 'nanocat' ), number_format_i18n( $nanocat_count ) ) ); ?></span>
							</div>
							<h2 class="wiki-card__title">
								<a href="<?php echo esc_url( get_permalink( $nanocat_root ) ); ?>"><?php echo esc_html( get_the_title( $nanocat_root ) ); ?></a>
							</h2>
							<p class="wiki-card__desc"><?php echo esc_html( $nanocat_excerpt ); ?></p>
							<div class="wiki-card__foot">
								<span>
									<?php
									printf(
										/* translators: %s: last updated date. */
										esc_html__( '更新于 %s', 'nanocat' ),
										esc_html( $nanocat_updated )
									);
									?>
								</span>
								<span class="wiki-card__arrow">
									<?php esc_html_e( '打开', 'nanocat' ); ?>
									<?php nanocat_icon( 'arrow-right', 15 ); ?>
								</span>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="content-none">
				<p class="section-kicker"><?php esc_html_e( '// EMPTY_WIKI', 'nanocat' ); ?></p>
				<h2><?php esc_html_e( 'Wiki 还在等第一页。', 'nanocat' ); ?></h2>
				<p><?php esc_html_e( '在后台新建一个顶层 Wiki 页面作为项目，再添加子页面作为文档。', 'nanocat' ); ?></p>
			</div>
		<?php endif; ?>

		<nav class="hub-crosslinks" aria-label="<?php esc_attr_e( '继续探索', 'nanocat' ); ?>">
			<?php if ( $nanocat_topics instanceof WP_Post ) : ?>
				<a class="hub-crosslink" href="<?php echo esc_url( get_permalink( $nanocat_topics ) ); ?>">
					<span class="hub-crosslink__kicker"><?php esc_html_e( '// COLUMNS', 'nanocat' ); ?></span>
					<strong><?php esc_html_e( '去读专栏', 'nanocat' ); ?></strong>
					<?php nanocat_icon( 'arrow-right', 16 ); ?>
				</a>
			<?php endif; ?>
			<?php if ( $nanocat_links instanceof WP_Post ) : ?>
				<a class="hub-crosslink" href="<?php echo esc_url( get_permalink( $nanocat_links ) ); ?>">
					<span class="hub-crosslink__kicker"><?php esc_html_e( '// FRIENDS', 'nanocat' ); ?></span>
					<strong><?php esc_html_e( '看看友链', 'nanocat' ); ?></strong>
					<?php nanocat_icon( 'arrow-right', 16 ); ?>
				</a>
			<?php endif; ?>
		</nav>
	</div>
</main>
<?php
get_footer();
