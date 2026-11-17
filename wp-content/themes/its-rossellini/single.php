<?php
/**
 * Single news post (Read surface: restrained, the article leads).
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$cats = get_the_category();
	?>
	<article <?php post_class( 'article' ); ?>>
		<header class="page-head page-head--article">
			<div class="wrap page-head__grid">
				<div class="page-head__text">
					<?php its_breadcrumbs(); ?>
					<h1 class="page-head__title page-head__title--article"><?php the_title(); ?></h1>
					<p class="article__meta">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<?php if ( $cats ) : ?>
							&middot; <a href="<?php echo esc_url( get_category_link( $cats[0] ) ); ?>"><?php echo esc_html( $cats[0]->name ); ?></a>
						<?php endif; ?>
					</p>
				</div>
			</div>
		</header>
		<?php $prose = its_prose( (string) apply_filters( 'the_content', get_the_content() ), true ); ?>
		<div class="wrap page-body reading">
			<div class="reading__main">
				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="article__photo">
						<?php the_post_thumbnail( 'large', array( 'sizes' => '(max-width: 900px) 92vw, 48rem' ) ); ?>
					</figure>
				<?php endif; ?>
				<div class="prose prose--reading">
					<?php echo $prose['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
			<aside class="reading__aside" aria-label="<?php esc_attr_e( 'Informazioni sulla news', 'its-rossellini' ); ?>">
				<div class="aside-card">
					<dl class="aside-card__facts">
						<div><dt><?php esc_html_e( 'Pubblicato', 'its-rossellini' ); ?></dt><dd><time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></dd></div>
						<?php if ( $cats ) : ?>
							<div><dt><?php esc_html_e( 'Categoria', 'its-rossellini' ); ?></dt><dd><a class="chip" href="<?php echo esc_url( get_category_link( $cats[0] ) ); ?>"><?php echo esc_html( $cats[0]->name ); ?></a></dd></div>
						<?php endif; ?>
						<div><dt><?php esc_html_e( 'Lettura', 'its-rossellini' ); ?></dt><dd>
							<?php
							/* translators: %d: minutes */
							echo esc_html( sprintf( _n( '%d minuto', '%d minuti', $prose['minutes'], 'its-rossellini' ), $prose['minutes'] ) );
							?>
						</dd></div>
					</dl>
					<?php echo its_toc( $prose['toc'], __( 'In questo articolo', 'its-rossellini' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<a class="btn btn--outline btn--small" href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ?: its_page_url( 'news' ) ); ?>"><?php esc_html_e( 'Tutte le news', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
			</aside>
		</div>
		<nav class="wrap article__nav" aria-label="<?php esc_attr_e( 'Altre news', 'its-rossellini' ); ?>">
			<?php
			the_post_navigation(
				array(
					'prev_text' => '<span class="article__nav-label">' . esc_html__( 'Precedente', 'its-rossellini' ) . '</span> %title',
					'next_text' => '<span class="article__nav-label">' . esc_html__( 'Successiva', 'its-rossellini' ) . '</span> %title',
				)
			);
			?>
		</nav>
	</article>
	<?php
	$more_news = new WP_Query(
		array(
			'post_type'           => 'post',
			'posts_per_page'      => 3,
			'post__not_in'        => array( get_the_ID() ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	if ( $more_news->have_posts() ) :
		?>
		<section class="panel psec psec--light psec--tint" aria-labelledby="more-news-title">
			<div class="wrap">
				<div class="panel__head">
					<h2 id="more-news-title" class="display display--m display--dark"><?php echo wp_kses( __( 'Altre <strong>news</strong>', 'its-rossellini' ), array( 'strong' => array() ) ); ?></h2>
					<a class="btn btn--outline" href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ?: its_page_url( 'news' ) ); ?>"><?php esc_html_e( 'Tutte le news', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
				<div class="news-grid">
					<?php
					while ( $more_news->have_posts() ) :
						$more_news->the_post();
						get_template_part( 'template-parts/news-card' );
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
	get_template_part( 'template-parts/cta-card' );
endwhile;

get_footer();
