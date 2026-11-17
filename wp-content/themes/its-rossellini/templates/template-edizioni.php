<?php
/**
 * Template Name: Edizioni dei corsi
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$editions = get_posts(
		array(
			'post_type'      => 'edizione_corso',
			'posts_per_page' => 300,
			'orderby'        => 'title',
			'order'          => 'ASC',
		)
	);
	$groups   = array();
	foreach ( $editions as $edition ) {
		$family = get_the_terms( $edition, 'famiglia' );
		$key    = ( $family && ! is_wp_error( $family ) ) ? $family[0]->name : __( 'Altre edizioni', 'its-rossellini' );
		$cycle  = get_the_terms( $edition, 'biennio' );

		$groups[ $key ][] = array(
			'post'  => $edition,
			'cycle' => ( $cycle && ! is_wp_error( $cycle ) ) ? $cycle[0]->name : '',
		);
	}
	ksort( $groups );
	get_template_part(
		'template-parts/page-head',
		null,
		array(
			'title' => get_the_title(),
			'lead'  => has_excerpt() ? get_the_excerpt() : __( 'Tutte le edizioni, in corso e concluse, per corso e biennio.', 'its-rossellini' ),
		)
	);
	?>
	<div class="wrap page-body page-body--wide">
		<?php if ( '' !== trim( get_the_content() ) ) : ?>
			<div class="prose"><?php the_content(); ?></div>
		<?php endif; ?>
		<div class="editions">
			<?php foreach ( $groups as $family => $items ) : ?>
				<?php usort( $items, static fn( $a, $b ) => strcmp( $b['cycle'], $a['cycle'] ) ); ?>
				<section class="editions__group">
					<h2 class="editions__family"><?php echo esc_html( $family ); ?></h2>
					<ul>
						<?php foreach ( $items as $item ) : ?>
							<li>
								<a href="<?php echo esc_url( get_permalink( $item['post'] ) ); ?>"><?php echo esc_html( get_the_title( $item['post'] ) ); ?></a>
								<?php if ( $item['cycle'] ) : ?>
									<span><?php echo esc_html( $item['cycle'] ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
	get_template_part( 'template-parts/cta-card' );
endwhile;

get_footer();
