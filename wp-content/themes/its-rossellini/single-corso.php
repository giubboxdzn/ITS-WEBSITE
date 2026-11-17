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
		. '<a class="btn btn--grad" href="' . esc_url( $apply ) . '">' . esc_html__( 'Preiscriviti a questo corso', 'its-rossellini' ) . '</a>'
		. '<a class="btn btn--outline" href="' . esc_url( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ) ) . '">' . esc_html__( 'Parla con l’orientamento', 'its-rossellini' ) . '</a>'
		. '</div>';
	$aside   = ( $area ? '<p class="page-head__area"><span class="chip">' . esc_html( $area->name ) . '</span></p>' : '' ) . $actions;
	$float   = '<div class="glass-card glass-card--facts"><ul>'
		. '<li><strong>' . esc_html( $facts['years'] ) . '</strong> ' . esc_html( _n( 'anno', 'anni', (int) $facts['years'], 'its-rossellini' ) ) . '</li>'
		. '<li><strong>' . esc_html( $facts['hours'] ) . '</strong> ' . esc_html__( 'ore in aula e lab', 'its-rossellini' ) . '</li>'
		. '<li><strong>' . esc_html( $facts['internship'] ) . '</strong> ' . esc_html__( 'ore di stage', 'its-rossellini' ) . '</li>'
		. '<li><strong>' . esc_html( $facts['places'] ) . '</strong> ' . esc_html__( 'posti', 'its-rossellini' ) . '</li>'
		. '</ul></div>';
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
				'float'    => $float,
			)
		);
		?>

		<div class="wrap corso__body prose prose--split">
			<?php
			// Each "heading + list" pair of the course content becomes one card.
			$content = (string) apply_filters( 'the_content', get_the_content() );
			echo preg_replace( '#(<h2\b[^>]*>.*?</h2>)\s*(<(ul|ol)\b[^>]*>.*?</\3>)#s', '<section class="prose-card">$1$2</section>', $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
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
		<section class="panel psec psec--light psec--tint" aria-labelledby="related-title">
			<div class="wrap">
				<div class="panel__head">
					<h2 id="related-title" class="display display--m display--dark"><?php echo wp_kses( __( 'Altri <strong>corsi</strong>', 'its-rossellini' ), array( 'strong' => array() ) ); ?></h2>
					<a class="btn btn--outline" href="<?php echo esc_url( its_page_url( 'offerta-formativa/corsi' ) ); ?>"><?php esc_html_e( 'Tutti i corsi', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
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
	get_template_part( 'template-parts/cta-card' );
endwhile;

get_footer();
