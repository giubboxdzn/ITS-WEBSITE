<?php
/**
 * Front page: rounded panels on a deep-navy canvas.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

$facts    = its_facts();
$contacts = its_contacts();
$meet_url = its_page_url( 'incontriamoci', its_page_url( 'contatti' ) );
$video    = its_hero_video();
$next     = its_next_event();
$em       = array(
	'em'     => array(),
	'strong' => array(),
);

// Real photography only: the front page's featured image, then course photos sorted by size.
$hero_photo = has_post_thumbnail( get_queried_object_id() ) ? (int) get_post_thumbnail_id( get_queried_object_id() ) : 0;
$photos     = array();
foreach ( its_courses( 12 )->posts as $course ) {
	if ( has_post_thumbnail( $course ) ) {
		$id            = (int) get_post_thumbnail_id( $course );
		$meta          = wp_get_attachment_metadata( $id );
		$photos[ $id ] = (int) ( $meta['width'] ?? 0 );
	}
}
unset( $photos[ $hero_photo ] );
arsort( $photos );
$photos     = array_keys( $photos );
$hero_photo = $hero_photo ?: ( $photos[0] ?? 0 );
$poster     = $hero_photo ? (string) wp_get_attachment_image_url( $hero_photo, 'full' ) : '';
$wide_photo = $photos[0] ?? 0;
$tile_a     = $photos[1] ?? 0;
$tile_b     = $photos[2] ?? 0;
// With the video playing, its poster photo is free for the path section (the sharpest after the wide one).
$path_photo = $video ? $hero_photo : ( $photos[3] ?? $wide_photo );

$partners = trim( (string) apply_filters( 'the_content', (string) get_post_field( 'post_content', get_queried_object_id() ) ) );
?>

<section class="hero" aria-labelledby="hero-title">
	<div class="hero__card">
		<div class="hero__media">
			<?php if ( $video ) : ?>
				<video class="hero__video" muted loop playsinline preload="metadata" poster="<?php echo esc_url( $poster ); ?>" aria-hidden="true" data-hero-video>
					<source src="<?php echo esc_url( $video ); ?>" type="video/mp4">
				</video>
			<?php elseif ( $hero_photo ) : ?>
				<?php
				echo wp_get_attachment_image(
					$hero_photo,
					'full',
					false,
					array(
						'class'         => 'hero__image',
						'loading'       => 'eager',
						'fetchpriority' => 'high',
						'sizes'         => '100vw',
						'alt'           => '',
					)
				);
				?>
			<?php endif; ?>
		</div>

		<div class="hero__content">
			<h1 id="hero-title" class="hero__title" data-split="server"><?php echo its_split_words( wp_kses( __( 'Il futuro <strong>si gira</strong> adesso.', 'its-rossellini' ), $em ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped inside. ?></h1>
			<p class="hero__lead"><?php esc_html_e( 'Due anni per trasformare il tuo talento in una professione tra cinema, TV, digital, live e tecnologia.', 'its-rossellini' ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--white" href="<?php echo esc_url( $meet_url ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?><?php echo its_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<a class="btn btn--glass" href="#corsi"><?php esc_html_e( 'Scopri i corsi', 'its-rossellini' ); ?></a>
			</div>
		</div>

		<div class="hero__float">
			<div class="glass-card">
				<p class="glass-card__num"><?php echo esc_html( $facts['employment'] ); ?></p>
				<p class="glass-card__text"><?php esc_html_e( 'dei diplomati lavora entro 12 mesi', 'its-rossellini' ); ?></p>
			</div>
			<a class="glass-card glass-card--link" href="<?php echo esc_url( $next ? get_permalink( $next ) : $meet_url ); ?>">
				<span class="glass-card__icon"><?php echo its_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span>
					<strong><?php echo esc_html( $next ? get_the_title( $next ) : __( 'Visita la sede', 'its-rossellini' ) ); ?></strong>
					<span><?php echo esc_html( $next ? wp_date( 'j F Y', $next->its_date->getTimestamp() ) : $contacts['address'] ); ?></span>
				</span>
			</a>
			<?php if ( $video ) : ?>
				<button class="hero__toggle" type="button" hidden data-video-toggle data-label-pause="<?php esc_attr_e( 'Metti in pausa il video', 'its-rossellini' ); ?>" data-label-play="<?php esc_attr_e( 'Riproduci il video', 'its-rossellini' ); ?>" aria-label="<?php esc_attr_e( 'Metti in pausa il video', 'its-rossellini' ); ?>">
					<?php echo its_icon( 'pause', 'when-playing' ) . its_icon( 'play', 'when-paused' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $partners ) : ?>
		<div class="logos-bar">
			<h2 class="screen-reader-text"><?php esc_html_e( 'I nostri partner', 'its-rossellini' ); ?></h2>
			<?php echo $partners; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>
	<?php endif; ?>
</section>

<section class="section" aria-labelledby="set-title">
	<div class="wrap bento">
		<article class="tile tile--cta">
			<h3><?php esc_html_e( 'Vieni a vedere i laboratori', 'its-rossellini' ); ?></h3>
			<p><?php esc_html_e( 'Prenota un open day o un incontro individuale. Ti mostriamo aule, laboratori e il teatro di posa.', 'its-rossellini' ); ?></p>
			<div class="tile__actions">
				<a class="btn btn--outline-light" href="<?php echo esc_url( its_page_url( 'academy' ) ); ?>"><?php esc_html_e( 'L’Academy', 'its-rossellini' ); ?></a>
				<a class="btn btn--white" href="<?php echo esc_url( $meet_url ); ?>"><?php esc_html_e( 'Prenota', 'its-rossellini' ); ?><?php echo its_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</div>
		</article>
		<div class="tile tile--stat bento__s1">
			<p class="tile__label"><?php esc_html_e( 'Corsi biennali', 'its-rossellini' ); ?></p>
			<p class="tile__num"><?php echo esc_html( (string) its_course_count() ); ?></p>
		</div>
		<?php if ( $tile_a ) : ?>
			<figure class="tile tile--img bento__i1"><?php echo wp_get_attachment_image( $tile_a, 'its-card', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 760px) 46vw, 220px' ) ); ?></figure>
		<?php endif; ?>
		<div class="tile tile--stat bento__s2">
			<p class="tile__label"><?php esc_html_e( 'Aree tecnologiche', 'its-rossellini' ); ?></p>
			<p class="tile__num"><?php echo esc_html( (string) count( its_course_areas() ) ); ?></p>
		</div>
		<?php if ( $tile_b ) : ?>
			<figure class="tile tile--img bento__i2"><?php echo wp_get_attachment_image( $tile_b, 'its-card', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 760px) 46vw, 220px' ) ); ?></figure>
		<?php endif; ?>
		<div class="bento__head">
			<h2 id="set-title" class="display"><?php echo wp_kses( __( 'Non una classe. <strong>Un set aperto.</strong>', 'its-rossellini' ), $em ); ?></h2>
			<p class="lead"><?php esc_html_e( 'Qui teoria e pratica entrano nella stessa inquadratura. Produci, sperimenti, sbagli, riprovi e costruisci un portfolio mentre impari.', 'its-rossellini' ); ?></p>
		</div>
		<?php if ( $wide_photo ) : ?>
			<figure class="tile tile--img bento__wide">
				<?php echo wp_get_attachment_image( $wide_photo, 'full', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 900px) 92vw, 760px' ) ); ?>
				<span class="glass-chip" aria-hidden="true"><?php esc_html_e( 'Imparare facendo', 'its-rossellini' ); ?></span>
			</figure>
		<?php endif; ?>
		<ul class="tile tile--list bento__list">
			<li><strong><?php esc_html_e( 'Laboratori veri', 'its-rossellini' ); ?></strong><span><?php esc_html_e( 'Spazi e tecnologie per lavorare come in produzione.', 'its-rossellini' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Docenti dall’industria', 'its-rossellini' ); ?></strong><span><?php esc_html_e( 'Professionisti che insegnano facendo.', 'its-rossellini' ); ?></span></li>
			<li><strong><?php esc_html_e( 'Imprese nel percorso', 'its-rossellini' ); ?></strong><span><?php esc_html_e( 'Stage, brief e relazioni che iniziano prima del diploma.', 'its-rossellini' ); ?></span></li>
		</ul>
		<div class="tile tile--stat tile--accent bento__s3">
			<p class="tile__label"><?php esc_html_e( 'Teatro di posa', 'its-rossellini' ); ?></p>
			<p class="tile__num">360 m²</p>
			<p class="tile__note"><?php esc_html_e( 'Aule, laboratori e set nella sede di Via Tiburtina.', 'its-rossellini' ); ?></p>
		</div>
	</div>
</section>

<section class="panel" id="corsi" aria-labelledby="corsi-title">
	<div class="wrap">
		<div class="panel__head panel__head--center">
			<h2 id="corsi-title" class="display display--dark"><?php echo wp_kses( __( 'Trova il tuo <strong>campo d’azione.</strong>', 'its-rossellini' ), $em ); ?></h2>
			<p class="lead">
				<?php
				$count = its_course_count();
				printf(
					/* translators: %d: number of courses */
					esc_html( _n( '%d percorso biennale, con presenza quotidiana nei laboratori e docenti che lavorano davvero nell’industria creativa e tecnologica.', '%d percorsi biennali, con presenza quotidiana nei laboratori e docenti che lavorano davvero nell’industria creativa e tecnologica.', $count, 'its-rossellini' ) ),
					(int) $count
				);
				?>
			</p>
		</div>
		<?php get_template_part( 'template-parts/course-wall', null, array( 'eager' => 0 ) ); ?>
		<p class="panel__more"><a class="btn btn--outline" href="<?php echo esc_url( its_page_url( 'offerta-formativa/corsi' ) ); ?>"><?php esc_html_e( 'Vedi tutti i corsi', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></p>
	</div>
</section>

<?php
$compare = array(
	array( 'calendar', __( 'Durata', 'its-rossellini' ), sprintf( /* translators: %s: years */ __( '%s anni, dopo il diploma', 'its-rossellini' ), $facts['years'] ) ),
	array( 'cap', __( 'Aula e laboratorio', 'its-rossellini' ), sprintf( /* translators: %s: hours */ __( 'circa %s ore', 'its-rossellini' ), $facts['hours'] ) ),
	array( 'users', __( 'Tirocinio in azienda', 'its-rossellini' ), sprintf( /* translators: %s: hours */ __( '%s ore', 'its-rossellini' ), $facts['internship'] ) ),
	array( 'euro', __( 'Costo', 'its-rossellini' ), __( 'Percorsi finanziati con risorse pubbliche', 'its-rossellini' ) ),
	array( 'users', __( 'Posti', 'its-rossellini' ), sprintf( /* translators: %s: places */ __( 'fino a %s per corso', 'its-rossellini' ), $facts['places'] ) ),
	array( 'check', __( 'Accesso', 'its-rossellini' ), __( 'Diploma di scuola superiore o titolo estero idoneo', 'its-rossellini' ) ),
	array( 'globe', __( 'Esperienza all’estero', 'its-rossellini' ), __( 'Tirocini con Erasmus+, secondo bandi e requisiti', 'its-rossellini' ) ),
	array( 'arrow-up', __( 'Dopo il diploma', 'its-rossellini' ), sprintf( /* translators: %s: share employed */ __( '%s lavora entro 12 mesi', 'its-rossellini' ), $facts['employment'] ) ),
);
?>
<section class="section compare-section" aria-labelledby="compare-title">
	<div class="wrap">
		<div class="split-head">
			<h2 id="compare-title" class="display"><?php echo wp_kses( __( 'Tutto in chiaro. <strong>Poi decidi.</strong>', 'its-rossellini' ), $em ); ?></h2>
			<p class="lead"><?php esc_html_e( 'Le risposte che contano prima di candidarti, prese dai bandi e dalle FAQ dell’Academy.', 'its-rossellini' ); ?></p>
		</div>
		<div class="compare">
			<div class="compare__brand"><?php echo its_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'ITS Academy Roberto Rossellini', 'its-rossellini' ); ?></span></div>
			<dl class="compare__rows">
				<?php foreach ( $compare as [ $icon, $label, $value ] ) : ?>
					<div class="compare__row">
						<dt><?php echo its_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $label ); ?></dt>
						<dd><span class="compare__check"><?php echo its_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><?php echo esc_html( $value ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
			<div class="compare__actions">
				<a class="btn btn--white" href="<?php echo esc_url( its_page_url( 'iscrizioni' ) ); ?>"><?php esc_html_e( 'Preiscriviti', 'its-rossellini' ); ?><?php echo its_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<a class="btn btn--outline-light" href="<?php echo esc_url( its_page_url( 'bandi-albi' ) ); ?>"><?php esc_html_e( 'Bandi e documenti', 'its-rossellini' ); ?></a>
			</div>
		</div>
	</div>
</section>

<section class="section" aria-labelledby="percorso-title">
	<div class="wrap overlap">
		<?php if ( $path_photo ) : ?>
			<figure class="overlap__media"><?php echo wp_get_attachment_image( $path_photo, 'full', false, array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 900px) 92vw, 60vw' ) ); ?></figure>
		<?php endif; ?>
		<div class="overlap__card">
			<h2 id="percorso-title" class="display display--dark display--m"><?php echo wp_kses( __( 'Dal primo ciak <strong>al primo contratto.</strong>', 'its-rossellini' ), $em ); ?></h2>
			<ol class="steps">
				<li>
					<h3><?php esc_html_e( 'Orientati', 'its-rossellini' ); ?></h3>
					<p><?php esc_html_e( 'Conosci persone, corsi e laboratori: open day, colloquio individuale, FAQ.', 'its-rossellini' ); ?></p>
				</li>
				<li>
					<h3><?php esc_html_e( 'Fai esperienza', 'its-rossellini' ); ?></h3>
					<p>
						<?php
						/* translators: %s: hours of classes and labs */
						printf( esc_html__( 'Lavora su brief, set e progetti reali: %s ore, portfolio, lavoro in team.', 'its-rossellini' ), esc_html( $facts['hours'] ) );
						?>
					</p>
				</li>
				<li>
					<h3><?php esc_html_e( 'Entra nel settore', 'its-rossellini' ); ?></h3>
					<p>
						<?php
						/* translators: %s: internship hours */
						printf( esc_html__( 'Porta le competenze dentro un’azienda: %s ore di stage e placement.', 'its-rossellini' ), esc_html( $facts['internship'] ) );
						?>
					</p>
				</li>
			</ol>
			<a class="btn btn--grad" href="<?php echo esc_url( its_page_url( 'servizi/placement', its_page_url( 'servizi' ) ) ); ?>"><?php esc_html_e( 'Stage e placement', 'its-rossellini' ); ?><?php echo its_icon( 'arrow-up' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
	</div>
</section>

<section class="section" aria-labelledby="place-title">
	<div class="wrap place">
		<div class="place__text">
			<h2 id="place-title" class="display"><?php echo wp_kses( __( 'Parti da Roma. <strong>Arriva ovunque.</strong>', 'its-rossellini' ), $em ); ?></h2>
			<p class="lead"><?php esc_html_e( 'Lezioni, laboratori e teatro di posa sono in Via Tiburtina, a pochi passi dalla Metro B. E con Erasmus+ il tirocinio può portarti all’estero.', 'its-rossellini' ); ?></p>
			<ul class="place__chips">
				<li><?php echo its_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php echo esc_html( $contacts['address'] ); ?></strong><?php esc_html_e( 'Sede legale e operativa', 'its-rossellini' ); ?></span></li>
				<li><?php echo its_icon( 'train' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Metro B', 'its-rossellini' ); ?></strong><?php esc_html_e( 'Pietralata · Monti Tiburtini', 'its-rossellini' ); ?></span></li>
				<li><?php echo its_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Orari', 'its-rossellini' ); ?></strong><?php echo esc_html( $contacts['hours'] ); ?></span></li>
				<li><a href="<?php echo esc_url( its_page_url( 'servizi/erasmus', its_page_url( 'servizi' ) ) ); ?>"><?php echo its_icon( 'globe' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Erasmus+', 'its-rossellini' ); ?></strong><?php esc_html_e( 'Tirocinio all’estero', 'its-rossellini' ); ?></span></a></li>
			</ul>
		</div>
		<div class="place__globe" aria-hidden="true">
			<div class="globe">
				<span class="globe__pin"><span><?php esc_html_e( 'Roma', 'its-rossellini' ); ?></span></span>
			</div>
		</div>
	</div>
</section>

<?php get_template_part( 'template-parts/news-panel' ); ?>

<?php get_template_part( 'template-parts/faq-section' ); ?>

<?php get_template_part( 'template-parts/cta-card' ); ?>

<?php
get_footer();
