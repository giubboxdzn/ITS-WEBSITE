<?php
/**
 * Course page.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$area    = its_area( get_the_ID() );
	$facts   = its_facts();
	$apply   = add_query_arg( 'corso', get_post_field( 'post_name' ), its_page_url( 'iscrizioni' ) );
	$actions = '<div class="page-head__actions">'
		. '<a class="btn btn--cyan" href="' . esc_url( $apply ) . '">' . esc_html__( 'Preiscriviti a questo corso', 'its-rossellini' ) . '</a>'
		. '<a class="btn btn--ghost" href="' . esc_url( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ) ) . '">' . esc_html__( 'Parla con l’orientamento', 'its-rossellini' ) . '</a>'
		. '</div>';
	$aside   = ( $area ? '<p class="page-head__area">' . esc_html( $area->name ) . '</p>' : '' ) . $actions;
	?>
	<article <?php post_class( 'corso' ); ?>>
		<?php
		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'title'    => get_the_title(),
				'lead'     => has_excerpt() ? get_the_excerpt() : '',
				'image_id' => (int) get_post_thumbnail_id(),
				'aside'    => $aside,
			)
		);
		?>

		<div class="facts-strip">
			<ul class="wrap facts-strip__list" aria-label="<?php esc_attr_e( 'Il corso in breve', 'its-rossellini' ); ?>">
				<li><strong><?php echo esc_html( $facts['years'] . ' ' . _n( 'anno', 'anni', (int) $facts['years'], 'its-rossellini' ) ); ?></strong> <?php esc_html_e( 'post-diploma', 'its-rossellini' ); ?></li>
				<li><strong><?php echo esc_html( $facts['hours'] ); ?> <?php esc_html_e( 'ore', 'its-rossellini' ); ?></strong> <?php esc_html_e( 'in aula e laboratorio', 'its-rossellini' ); ?></li>
				<li><strong><?php echo esc_html( $facts['internship'] ); ?> <?php esc_html_e( 'ore', 'its-rossellini' ); ?></strong> <?php esc_html_e( 'di stage in azienda', 'its-rossellini' ); ?></li>
				<li><strong><?php echo esc_html( $facts['places'] ); ?> <?php esc_html_e( 'posti', 'its-rossellini' ); ?></strong> <?php esc_html_e( 'per corso', 'its-rossellini' ); ?></li>
			</ul>
		</div>

		<div class="wrap corso__body prose prose--split">
			<?php the_content(); ?>
		</div>
	</article>

	<?php
	$related = new WP_Query(
		array(
			'post_type'      => 'corso',
			'posts_per_page' => 4,
			'post__not_in'   => array( get_the_ID() ),
			'orderby'        => 'menu_order',
			'order'          => 'ASC',
			'no_found_rows'  => true,
		)
	);
	if ( $related->have_posts() ) :
		?>
		<section class="section section--light" aria-labelledby="related-title">
			<div class="wrap">
				<div class="section__head section__head--row">
					<h2 id="related-title" class="headline headline--m"><?php esc_html_e( 'Altri corsi', 'its-rossellini' ); ?></h2>
					<a class="link-arrow" href="<?php echo esc_url( its_page_url( 'offerta-formativa/corsi' ) ); ?>"><?php esc_html_e( 'Tutti i corsi', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
				<div class="grid">
					<?php
					while ( $related->have_posts() ) :
						$related->the_post();
						echo '<div class="grid__item">';
						get_template_part( 'template-parts/course-card' );
						echo '</div>';
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
