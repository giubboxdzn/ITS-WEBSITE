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
		<?php if ( has_post_thumbnail() ) : ?>
			<figure class="wrap article__photo">
				<?php the_post_thumbnail( 'large', array( 'sizes' => '(max-width: 1100px) 92vw, 1000px' ) ); ?>
			</figure>
		<?php endif; ?>
		<div class="wrap page-body">
			<div class="prose">
				<?php the_content(); ?>
			</div>
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
endwhile;

get_footer();
