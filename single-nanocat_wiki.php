<?php
/**
 * Single wiki page.
 *
 * @package NanoCat
 */

get_header();

while ( have_posts() ) :
	the_post();

	$nanocat_root = nanocat_get_wiki_root();

	if ( ! $nanocat_root ) {
		continue;
	}

	$nanocat_root_id    = (int) $nanocat_root->ID;
	$nanocat_count      = nanocat_get_wiki_count( $nanocat_root_id );
	$nanocat_siblings   = nanocat_get_wiki_siblings( $nanocat_root_id, get_the_ID() );
	$nanocat_ancestors  = array_reverse( get_post_ancestors( get_the_ID() ) );
	$nanocat_is_root    = (int) get_the_ID() === $nanocat_root_id;
	$nanocat_children   = get_pages(
		array(
			'post_type'   => 'nanocat_wiki',
			'post_status' => 'publish',
			'parent'      => get_the_ID(),
			'sort_column' => 'menu_order,post_title',
			'sort_order'  => 'ASC',
		)
	);
	$nanocat_projects   = nanocat_get_wiki_roots( 6 );
	$nanocat_archive    = get_post_type_archive_link( 'nanocat_wiki' );
	$nanocat_updated    = get_the_modified_time( 'Y-m-d' );
	?>
	<main id="primary" class="site-main wiki-single-view">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'wiki-page' ); ?>>
			<header class="wiki-head">
				<div class="container wiki-head__inner">
					<nav class="breadcrumbs" aria-label="<?php esc_attr_e( '面包屑导航', 'nanocat' ); ?>">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( '首页', 'nanocat' ); ?></a>
						<span class="breadcrumbs__sep" aria-hidden="true">/</span>
						<a href="<?php echo esc_url( $nanocat_archive ); ?>"><?php esc_html_e( 'Wiki', 'nanocat' ); ?></a>
						<span class="breadcrumbs__sep" aria-hidden="true">/</span>

						<?php if ( $nanocat_is_root ) : ?>
							<span aria-current="page"><?php echo esc_html( get_the_title( $nanocat_root ) ); ?></span>
						<?php else : ?>
							<a href="<?php echo esc_url( get_permalink( $nanocat_root ) ); ?>"><?php echo esc_html( get_the_title( $nanocat_root ) ); ?></a>
							<?php
							foreach ( $nanocat_ancestors as $nanocat_ancestor_id ) :
								if ( (int) $nanocat_ancestor_id === $nanocat_root_id ) {
									continue;
								}
								?>
								<span class="breadcrumbs__sep" aria-hidden="true">/</span>
								<a href="<?php echo esc_url( get_permalink( $nanocat_ancestor_id ) ); ?>"><?php echo esc_html( get_the_title( $nanocat_ancestor_id ) ); ?></a>
							<?php endforeach; ?>
							<span class="breadcrumbs__sep" aria-hidden="true">/</span>
							<span aria-current="page"><?php the_title(); ?></span>
						<?php endif; ?>
					</nav>

					<p class="section-kicker"><?php esc_html_e( '// WIKI_PAGE', 'nanocat' ); ?></p>
					<h1 class="wiki-title"><?php the_title(); ?></h1>

					<?php if ( has_excerpt() ) : ?>
						<p class="wiki-deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php endif; ?>

					<div class="wiki-meta">
						<span class="status-dot" aria-hidden="true"></span>
						<span><?php printf( esc_html__( '更新于 %s', 'nanocat' ), esc_html( $nanocat_updated ) ); ?></span>
						<span class="meta-dot" aria-hidden="true"></span>
						<span><?php echo esc_html( sprintf( _n( '%s 页文档', '%s 页文档', $nanocat_count, 'nanocat' ), number_format_i18n( $nanocat_count ) ) ); ?></span>
						<?php
						edit_post_link(
							__( '编辑页面', 'nanocat' ),
							'<span class="entry-edit">',
							'</span>'
						);
						?>
					</div>
				</div>
			</header>

			<div class="container wiki-shell">
				<div class="wiki-main">
					<details class="wiki-tree-mobile">
						<summary>
							<span><?php esc_html_e( '项目目录', 'nanocat' ); ?></span>
							<span class="wiki-tree-mobile__count"><?php echo esc_html( sprintf( _n( '%s 页', '%s 页', $nanocat_count, 'nanocat' ), number_format_i18n( $nanocat_count ) ) ); ?></span>
						</summary>
						<nav class="wiki-tree" aria-label="<?php esc_attr_e( 'Wiki 目录', 'nanocat' ); ?>">
							<a class="wiki-tree__root<?php echo $nanocat_is_root ? ' is-current' : ''; ?>" href="<?php echo esc_url( get_permalink( $nanocat_root ) ); ?>">
								<?php nanocat_icon( 'home', 15 ); ?>
								<span><?php echo esc_html( get_the_title( $nanocat_root ) ); ?></span>
							</a>
							<?php nanocat_wiki_tree( $nanocat_root_id, get_the_ID() ); ?>
						</nav>
					</details>

					<div class="entry-content wiki-content" data-entry-content>
						<?php
						the_content();
						wp_link_pages(
							array(
								'before' => '<nav class="page-links" aria-label="' . esc_attr__( '分页', 'nanocat' ) . '"><span class="page-links__label">' . esc_html__( '页面', 'nanocat' ) . '</span>',
								'after'  => '</nav>',
							)
						);
						?>
					</div>

					<?php if ( $nanocat_children ) : ?>
						<section class="wiki-index" aria-labelledby="wiki-index-title">
							<div class="wiki-index__head">
								<p class="section-kicker"><?php esc_html_e( '// IN THIS PROJECT', 'nanocat' ); ?></p>
								<h2 id="wiki-index-title"><?php esc_html_e( '继续阅读', 'nanocat' ); ?></h2>
							</div>
							<div class="wiki-index__grid">
								<?php
								$nanocat_child_index = 0;
								foreach ( $nanocat_children as $nanocat_child ) :
									$nanocat_child_index++;
									?>
									<a class="wiki-index__card" href="<?php echo esc_url( get_permalink( $nanocat_child ) ); ?>">
										<span class="wiki-index__number" aria-hidden="true"><?php echo esc_html( str_pad( (string) $nanocat_child_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
										<span class="wiki-index__copy">
											<strong><?php echo esc_html( get_the_title( $nanocat_child ) ); ?></strong>
											<span><?php echo esc_html( nanocat_get_excerpt( 62, $nanocat_child->ID ) ); ?></span>
										</span>
										<?php nanocat_icon( 'arrow-right', 16 ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endif; ?>

					<nav class="wiki-pager" aria-label="<?php esc_attr_e( 'Wiki 翻页', 'nanocat' ); ?>">
						<?php if ( $nanocat_siblings['previous'] ) : ?>
							<a class="wiki-pager__link wiki-pager__link--prev" href="<?php echo esc_url( get_permalink( $nanocat_siblings['previous'] ) ); ?>">
								<span class="wiki-pager__label"><?php nanocat_icon( 'arrow-left', 14 ); ?><?php esc_html_e( '上一页', 'nanocat' ); ?></span>
								<strong><?php echo esc_html( get_the_title( $nanocat_siblings['previous'] ) ); ?></strong>
							</a>
						<?php else : ?>
							<span></span>
						<?php endif; ?>

						<?php if ( $nanocat_siblings['next'] ) : ?>
							<a class="wiki-pager__link wiki-pager__link--next" href="<?php echo esc_url( get_permalink( $nanocat_siblings['next'] ) ); ?>">
								<span class="wiki-pager__label"><?php esc_html_e( '下一页', 'nanocat' ); ?><?php nanocat_icon( 'arrow-right', 14 ); ?></span>
								<strong><?php echo esc_html( get_the_title( $nanocat_siblings['next'] ) ); ?></strong>
							</a>
						<?php else : ?>
							<a class="wiki-pager__link wiki-pager__link--next" href="<?php echo esc_url( $nanocat_archive ); ?>">
								<span class="wiki-pager__label"><?php esc_html_e( '返回', 'nanocat' ); ?><?php nanocat_icon( 'arrow-right', 14 ); ?></span>
								<strong><?php esc_html_e( '全部 Wiki 项目', 'nanocat' ); ?></strong>
							</a>
						<?php endif; ?>
					</nav>

					<a class="back-link" href="<?php echo esc_url( $nanocat_archive ); ?>">
						<?php nanocat_icon( 'arrow-left', 16 ); ?>
						<?php esc_html_e( '返回 Wiki 首页', 'nanocat' ); ?>
					</a>
				</div>

				<aside class="wiki-sidebar" aria-label="<?php esc_attr_e( 'Wiki 项目目录', 'nanocat' ); ?>">
					<div class="wiki-sidebar__sticky">
						<div class="wiki-sidebar__card">
							<p class="section-kicker"><?php esc_html_e( '// PROJECT', 'nanocat' ); ?></p>
							<a class="wiki-sidebar__project" href="<?php echo esc_url( get_permalink( $nanocat_root ) ); ?>">
								<?php echo esc_html( get_the_title( $nanocat_root ) ); ?>
							</a>
							<p class="wiki-sidebar__count"><?php echo esc_html( sprintf( _n( '%s 页文档', '%s 页文档', $nanocat_count, 'nanocat' ), number_format_i18n( $nanocat_count ) ) ); ?></p>

							<nav class="wiki-tree" aria-label="<?php esc_attr_e( 'Wiki 目录', 'nanocat' ); ?>">
								<a class="wiki-tree__root<?php echo $nanocat_is_root ? ' is-current' : ''; ?>" href="<?php echo esc_url( get_permalink( $nanocat_root ) ); ?>">
									<?php nanocat_icon( 'home', 15 ); ?>
									<span><?php esc_html_e( '项目首页', 'nanocat' ); ?></span>
								</a>
								<?php nanocat_wiki_tree( $nanocat_root_id, get_the_ID() ); ?>
							</nav>
						</div>

						<?php if ( count( $nanocat_projects ) > 1 ) : ?>
							<div class="wiki-sidebar__other">
								<p class="section-kicker"><?php esc_html_e( '// OTHER_PROJECTS', 'nanocat' ); ?></p>
								<ul>
									<?php foreach ( $nanocat_projects as $nanocat_project ) : ?>
										<?php if ( (int) $nanocat_project->ID === $nanocat_root_id ) : ?>
											<?php continue; ?>
										<?php endif; ?>
										<li>
											<a href="<?php echo esc_url( get_permalink( $nanocat_project ) ); ?>">
												<?php echo esc_html( get_the_title( $nanocat_project ) ); ?>
												<span><?php echo esc_html( number_format_i18n( nanocat_get_wiki_count( $nanocat_project->ID ) ) ); ?></span>
											</a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php endif; ?>
					</div>
				</aside>
			</div>
		</article>
	</main>
	<?php
endwhile;

get_footer();
