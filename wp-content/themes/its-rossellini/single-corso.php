<?php
/**
 * A course as its own locandina: title, area ink, facts as billing, content as credits.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$area  = its_area( get_the_ID() );
	$ink   = its_area_ink( $area );
	$facts = its_facts();
	$apply = add_query_arg( 'corso', get_post_field( 'post_name' ), its_page_url( 'iscrizioni' ) );
	?>
	<article <?php post_class( 'corso' ); ?> data-ink="<?php echo esc_attr( $ink ); ?>">
		<?php
		$aside = $area ? '<p class="page-head__area" data-ink="' . esc_attr( $ink ) . '">' . esc_html( $area->name ) . '</p>' : '';
		get_template_part(
			'template-parts/page-head',
			null,
			array(
				'title'    => get_the_title(),
				'lead'     => has_excerpt() ? get_the_excerpt() : '',
				'image_id' => (int) get_post_thumbnail_id(),
				'ink'      => $ink,
				'aside'    => $aside,
			)
		);
		?>

		<div class="corso__billing">
			<div class="wrap corso__billing-grid">
				<p class="billing__facts billing__facts--course">
					<span><b><?php echo esc_html( $facts['years'] ); ?></b> <?php echo esc_html( _n( 'anno', 'anni', (int) $facts['years'], 'its-rossellini' ) ); ?></span>
					<span><b><?php echo esc_html( $facts['hours'] ); ?></b> <?php esc_html_e( 'ore in aula e laboratorio', 'its-rossellini' ); ?></span>
					<span><b><?php echo esc_html( $facts['internship'] ); ?></b> <?php esc_html_e( 'ore di stage', 'its-rossellini' ); ?></span>
					<span><b><?php echo esc_html( $facts['places'] ); ?></b> <?php esc_html_e( 'posti', 'its-rossellini' ); ?></span>
				</p>
				<div class="corso__actions">
					<a class="button button--yellow" href="<?php echo esc_url( $apply ); ?>"><?php esc_html_e( 'Preiscriviti a questo corso', 'its-rossellini' ); ?></a>
					<a class="button button--line" href="<?php echo esc_url( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ) ); ?>"><?php esc_html_e( 'Parla con l’orientamento', 'its-rossellini' ); ?></a>
				</div>
			</div>
		</div>

		<div class="wrap corso__body prose prose--credits">
			<?php the_content(); ?>
		</div>
	</article>

	<?php
	$related_args = array(
		'post_type'      => 'corso',
		'posts_per_page' => 3,
		'post__not_in'   => array( get_the_ID() ),
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	);
	$related      = new WP_Query( $related_args );
	if ( $related->have_posts() ) :
		?>
		<section class="section section--paper section--ruled" aria-labelledby="related-title">
			<div class="wrap">
				<div class="section__head section__head--row">
					<h2 id="related-title" class="display display--m"><?php esc_html_e( 'Altri corsi', 'its-rossellini' ); ?></h2>
					<a class="link-arrow" href="<?php echo esc_url( its_page_url( 'offerta-formativa/corsi' ) ); ?>"><?php esc_html_e( 'Tutti i corsi', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
				<div class="wall wall--three">
					<?php
					while ( $related->have_posts() ) :
						$related->the_post();
						echo '<div class="wall__item">';
						get_template_part( 'template-parts/fotobusta' );
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
