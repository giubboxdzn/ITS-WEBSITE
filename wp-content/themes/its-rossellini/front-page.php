<?php
/**
 * Front page: the locandina.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

$facts    = its_facts();
$contacts = its_contacts();
$next     = its_next_event();
$meet_url = its_page_url( 'incontriamoci', its_page_url( 'contatti' ) );

// Real photography only: the front page's featured image, else course photos.
$photos = array();
if ( has_post_thumbnail( get_queried_object_id() ) ) {
	$photos[] = get_post_thumbnail_id( get_queried_object_id() );
}
foreach ( its_courses( 12 )->posts as $course ) {
	if ( has_post_thumbnail( $course ) ) {
		$photos[] = get_post_thumbnail_id( $course );
	}
}
$photos = array_values( array_unique( array_filter( $photos ) ) );
// The first two courses print large right below the hero: start the hero from a different photo.
$hero_photo = has_post_thumbnail( get_queried_object_id() ) ? $photos[0] : ( $photos[2] ?? $photos[0] ?? 0 );
$academy    = get_page_by_path( 'academy' );
$set_photo  = ( $academy && has_post_thumbnail( $academy ) ) ? get_post_thumbnail_id( $academy ) : ( $photos[4] ?? end( $photos ) ?: 0 );
?>

<section class="locandina" aria-labelledby="locandina-title">
	<div class="wrap locandina__grid">
		<div class="locandina__copy">
			<h1 id="locandina-title" class="locandina__title" data-register>
				<span class="locandina__plate" aria-hidden="true"><?php esc_html_e( 'Il futuro si gira adesso.', 'its-rossellini' ); ?></span>
				<span class="locandina__ink"><?php esc_html_e( 'Il futuro si gira adesso.', 'its-rossellini' ); ?></span>
			</h1>
			<p class="locandina__lead"><?php esc_html_e( 'Due anni per trasformare il tuo talento in una professione tra cinema, TV, digital, live e tecnologia.', 'its-rossellini' ); ?></p>
			<div class="locandina__actions">
				<a class="button button--yellow" href="<?php echo esc_url( $meet_url ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?></a>
				<?php $course_count = (int) wp_count_posts( 'corso' )->publish; ?>
				<a class="button button--line" href="#corsi">
					<?php
					echo esc_html(
						$course_count > 1
							/* translators: 1: Italian plural article (i / gli), 2: number of courses */
							? sprintf( __( 'Scopri %1$s %2$d corsi', 'its-rossellini' ), ( str_starts_with( (string) $course_count, '8' ) || 11 === $course_count ) ? 'gli' : 'i', $course_count )
							: __( 'Scopri i corsi', 'its-rossellini' )
					);
					?>
				</a>
			</div>
		</div>

		<?php if ( $hero_photo ) : ?>
			<figure class="locandina__photo">
				<div class="duotone" data-ink="blu">
					<?php
					echo wp_get_attachment_image(
						$hero_photo,
						'its-locandina',
						false,
						array(
							'loading'       => 'eager',
							'fetchpriority' => 'high',
							'sizes'         => '(max-width: 900px) 92vw, 38vw',
							'alt'           => '',
						)
					);
					?>
				</div>
				<?php if ( $next ) : ?>
					<figcaption class="locandina__next">
						<?php echo its_stamp( its_stamp_state( $next->its_date ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a href="<?php echo esc_url( get_permalink( $next ) ); ?>">
							<strong><?php echo esc_html( get_the_title( $next ) ); ?></strong>
							<span><?php echo esc_html( wp_date( 'j F', $next->its_date->getTimestamp() ) ); ?></span>
						</a>
					</figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>
	</div>

	<div class="wrap">
		<div class="billing">
			<p class="billing__facts">
				<span><b><?php echo esc_html( $facts['years'] ); ?></b> <?php esc_html_e( 'anni', 'its-rossellini' ); ?></span>
				<span><b><?php echo esc_html( $facts['hours'] ); ?></b> <?php esc_html_e( 'ore in aula e laboratorio', 'its-rossellini' ); ?></span>
				<span><b><?php echo esc_html( $facts['internship'] ); ?></b> <?php esc_html_e( 'ore di stage in azienda', 'its-rossellini' ); ?></span>
				<span><b><?php echo esc_html( $facts['places'] ); ?></b> <?php esc_html_e( 'posti per corso', 'its-rossellini' ); ?></span>
			</p>
			<p class="billing__credits">
				<?php esc_html_e( 'Corsi biennali post-diploma della Fondazione ITS Academy Roberto Rossellini', 'its-rossellini' ); ?>
				&middot; <?php echo esc_html( $contacts['address'] ); ?>
				&middot; <a href="<?php echo esc_url( its_page_url( 'offerta-formativa/corsi-finanziati' ) ); ?>"><?php esc_html_e( 'Formazione finanziata da FSE+ Lazio, PNRR e fondi nazionali ITS', 'its-rossellini' ); ?></a>
			</p>
		</div>
	</div>
</section>

<section class="section section--paper" id="corsi" aria-labelledby="corsi-title">
	<div class="wrap">
		<div class="section__head">
			<h2 id="corsi-title" class="display"><?php esc_html_e( 'Trova il tuo campo d’azione.', 'its-rossellini' ); ?></h2>
			<p class="section__lead"><?php esc_html_e( 'Percorsi biennali, presenza quotidiana nei laboratori e docenti che lavorano davvero nell’industria creativa e tecnologica.', 'its-rossellini' ); ?></p>
		</div>
		<?php get_template_part( 'template-parts/course-wall' ); ?>
	</div>
</section>

<section class="section section--yellow" aria-labelledby="set-title">
	<div class="wrap set">
		<?php if ( $set_photo ) : ?>
			<figure class="set__photo">
				<div class="duotone" data-ink="blu">
					<?php
					echo wp_get_attachment_image(
						$set_photo,
						'its-fotobusta',
						false,
						array(
							'sizes' => '(max-width: 900px) 92vw, 55vw',
							'alt'   => '',
						)
					);
					?>
				</div>
				<?php $caption = wp_get_attachment_caption( $set_photo ); ?>
				<?php if ( $caption ) : ?>
					<figcaption><?php echo esc_html( $caption ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>
		<div class="set__text">
			<h2 id="set-title" class="display"><?php esc_html_e( 'Non una classe. Un set aperto.', 'its-rossellini' ); ?></h2>
			<p class="section__lead"><?php esc_html_e( 'Qui teoria e pratica entrano nella stessa inquadratura. Produci, sperimenti, sbagli, riprovi e costruisci un portfolio mentre impari.', 'its-rossellini' ); ?></p>
			<dl class="credits">
				<div><dt><?php esc_html_e( 'Laboratori veri', 'its-rossellini' ); ?></dt><dd><?php esc_html_e( 'Spazi e tecnologie per lavorare come in produzione.', 'its-rossellini' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Docenti dall’industria', 'its-rossellini' ); ?></dt><dd><?php esc_html_e( 'Professionisti che insegnano facendo.', 'its-rossellini' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Imprese nel percorso', 'its-rossellini' ); ?></dt><dd><?php esc_html_e( 'Stage, brief e relazioni che iniziano prima del diploma.', 'its-rossellini' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Teatro di posa', 'its-rossellini' ); ?></dt><dd><?php esc_html_e( '360 m² nella sede di Via Tiburtina, con aule e laboratori.', 'its-rossellini' ); ?></dd></div>
			</dl>
			<a class="link-arrow" href="<?php echo esc_url( its_page_url( 'academy' ) ); ?>"><?php esc_html_e( 'Conosci l’Academy', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
	</div>
</section>

<section class="section section--blue" aria-labelledby="percorso-title">
	<div class="wrap">
		<h2 id="percorso-title" class="claim">
			<?php
			printf(
				/* translators: 1: article (Il / L’), 2: share of graduates employed, e.g. 87% */
				esc_html__( '%1$s%2$s dei diplomati lavora entro 12 mesi.', 'its-rossellini' ),
				esc_html( its_article_for_number( $facts['employment'] ) ),
				'<em>' . esc_html( $facts['employment'] ) . '</em>'
			);
			?>
		</h2>
		<p class="claim__source"><?php echo esc_html( $facts['source'] ); ?>.</p>

		<ol class="programma" aria-label="<?php esc_attr_e( 'Il percorso, dal primo ciak al primo contratto', 'its-rossellini' ); ?>">
			<li>
				<h3><?php esc_html_e( 'Orientati', 'its-rossellini' ); ?></h3>
				<p><?php esc_html_e( 'Conosci persone, corsi e laboratori prima di scegliere.', 'its-rossellini' ); ?></p>
				<p class="programma__detail"><?php esc_html_e( 'Open day, colloquio individuale, FAQ', 'its-rossellini' ); ?></p>
				<a class="link-arrow" href="<?php echo esc_url( $meet_url ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</li>
			<li>
				<h3><?php esc_html_e( 'Fai esperienza', 'its-rossellini' ); ?></h3>
				<p><?php esc_html_e( 'Lavora su brief, set e progetti reali, in squadra.', 'its-rossellini' ); ?></p>
				<p class="programma__detail">
					<?php
					/* translators: %s: hours of classes and labs */
					printf( esc_html__( '%s ore in aula e laboratorio, portfolio', 'its-rossellini' ), esc_html( $facts['hours'] ) );
					?>
				</p>
				<a class="link-arrow" href="<?php echo esc_url( its_page_url( 'laboratori' ) ); ?>"><?php esc_html_e( 'I laboratori', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</li>
			<li>
				<h3><?php esc_html_e( 'Entra nel settore', 'its-rossellini' ); ?></h3>
				<p><?php esc_html_e( 'Porta le competenze dentro un’azienda, prima del diploma.', 'its-rossellini' ); ?></p>
				<p class="programma__detail">
					<?php
					/* translators: %s: internship hours */
					printf( esc_html__( '%s ore di stage, placement', 'its-rossellini' ), esc_html( $facts['internship'] ) );
					?>
				</p>
				<a class="link-arrow" href="<?php echo esc_url( its_page_url( 'servizi/placement' ) ); ?>"><?php esc_html_e( 'Stage e placement', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</li>
		</ol>
	</div>
</section>

<?php
$events = its_events_by_date( 0 );
$news   = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => 4,
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);
?>
<section class="section section--paper" aria-labelledby="ora-title">
	<div class="wrap">
		<div class="section__head section__head--row">
			<h2 id="ora-title" class="display"><?php esc_html_e( 'News, eventi, occasioni.', 'its-rossellini' ); ?></h2>
			<a class="link-arrow" href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ?: its_page_url( 'news' ) ); ?>"><?php esc_html_e( 'Tutte le news', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
		<div class="ora">
			<div class="affissioni">
				<?php
				while ( $news->have_posts() ) :
					$news->the_post();
					get_template_part( 'template-parts/dated-item', null, array( 'show_excerpt' => false ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
			<aside class="in-programma" aria-labelledby="programma-title">
				<h3 id="programma-title" class="in-programma__title"><?php esc_html_e( 'In programma', 'its-rossellini' ); ?></h3>
				<?php if ( $events['upcoming'] ) : ?>
					<?php
					foreach ( array_slice( $events['upcoming'], 0, 3 ) as $post ) :
						setup_postdata( $GLOBALS['post'] = $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						get_template_part( 'template-parts/dated-item', null, array( 'heading' => 'h4' ) );
					endforeach;
					wp_reset_postdata();
					?>
				<?php else : ?>
					<p class="in-programma__empty"><?php esc_html_e( 'Nessun open day in calendario in questo momento. Puoi visitare la sede con un incontro individuale, su appuntamento.', 'its-rossellini' ); ?></p>
					<a class="button button--blue" href="<?php echo esc_url( $meet_url ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?></a>
				<?php endif; ?>
				<a class="link-arrow" href="<?php echo esc_url( its_page_url( 'eventi' ) ); ?>"><?php esc_html_e( 'Tutti gli eventi', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</aside>
		</div>
	</div>
</section>

<?php
$partners = trim( (string) apply_filters( 'the_content', (string) get_post_field( 'post_content', get_queried_object_id() ) ) );
if ( $partners ) :
	?>
	<section class="section section--paper section--ruled" aria-labelledby="partner-title">
		<div class="wrap partners">
			<h2 id="partner-title" class="partners__title"><?php esc_html_e( 'In collaborazione con', 'its-rossellini' ); ?></h2>
			<div class="partners__logos"><?php echo $partners; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</div>
	</section>
<?php endif; ?>

<section class="section section--yellow close" aria-labelledby="close-title">
	<div class="wrap close__grid">
		<h2 id="close-title" class="display close__title"><?php esc_html_e( 'Vieni a vedere il set.', 'its-rossellini' ); ?></h2>
		<div class="close__text">
			<p><?php echo esc_html( $contacts['address'] ); ?> &middot; <?php echo esc_html( $contacts['transit'] ); ?><br><?php echo esc_html( $contacts['hours'] ); ?></p>
			<div class="close__actions">
				<a class="button button--blue" href="<?php echo esc_url( $meet_url ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?></a>
				<a class="button button--line-blue" href="<?php echo esc_url( its_page_url( 'iscrizioni' ) ); ?>"><?php esc_html_e( 'Preiscriviti al biennio', 'its-rossellini' ); ?></a>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
