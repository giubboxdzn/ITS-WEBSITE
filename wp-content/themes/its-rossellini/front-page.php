<?php
/**
 * Front page.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

$facts    = its_facts();
$contacts = its_contacts();
$meet_url = its_page_url( 'incontriamoci', its_page_url( 'contatti' ) );
$video    = its_hero_video();

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
$photos     = array_values( array_unique( array_filter( $photos ) ) );
$hero_photo = $photos[0] ?? 0;
$academy    = get_page_by_path( 'academy' );
$set_photo  = ( $academy && has_post_thumbnail( $academy ) ) ? get_post_thumbnail_id( $academy ) : ( $photos[5] ?? end( $photos ) ?: 0 );
$poster     = $hero_photo ? (string) wp_get_attachment_image_url( $hero_photo, 'full' ) : '';
?>

<section class="hero" aria-labelledby="hero-title">
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
	<div class="wrap hero__content">
		<h1 id="hero-title" class="hero__title">
			<?php
			echo wp_kses(
				__( 'Il futuro <em>si gira</em> adesso.', 'its-rossellini' ),
				array( 'em' => array() )
			);
			?>
		</h1>
		<div class="hero__bottom">
			<p class="hero__lead"><?php esc_html_e( 'Due anni per trasformare il tuo talento in una professione tra cinema, TV, digital, live e tecnologia.', 'its-rossellini' ); ?></p>
			<div class="hero__actions">
				<a class="btn btn--cyan" href="<?php echo esc_url( $meet_url ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?></a>
				<a class="btn btn--ghost" href="#corsi"><?php esc_html_e( 'Scopri i corsi', 'its-rossellini' ); ?></a>
			</div>
			<?php if ( $video ) : ?>
				<button class="hero__toggle" type="button" hidden data-video-toggle data-label-pause="<?php esc_attr_e( 'Metti in pausa il video', 'its-rossellini' ); ?>" data-label-play="<?php esc_attr_e( 'Riproduci il video', 'its-rossellini' ); ?>" aria-label="<?php esc_attr_e( 'Metti in pausa il video', 'its-rossellini' ); ?>">
					<?php echo its_icon( 'pause', 'when-playing' ) . its_icon( 'play', 'when-paused' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			<?php endif; ?>
		</div>
	</div>
</section>

<section class="section section--light" id="corsi" aria-labelledby="corsi-title">
	<div class="wrap">
		<div class="section__head">
			<h2 id="corsi-title" class="headline">
				<?php echo wp_kses( __( 'Trova il tuo <em>campo d’azione.</em>', 'its-rossellini' ), array( 'em' => array() ) ); ?>
			</h2>
			<p class="section__lead">
				<?php
				$count = (int) wp_count_posts( 'corso' )->publish;
				printf(
					/* translators: %d: number of courses */
					esc_html( _n( '%d percorso biennale, con presenza quotidiana nei laboratori e docenti che lavorano davvero nell’industria creativa e tecnologica.', '%d percorsi biennali, con presenza quotidiana nei laboratori e docenti che lavorano davvero nell’industria creativa e tecnologica.', $count, 'its-rossellini' ) ),
					(int) $count
				);
				?>
			</p>
		</div>
		<?php get_template_part( 'template-parts/course-wall' ); ?>
	</div>
</section>

<section class="section" aria-labelledby="set-title">
	<div class="wrap split">
		<?php if ( $set_photo ) : ?>
			<figure class="split__media">
				<?php
				echo wp_get_attachment_image(
					$set_photo,
					'large',
					false,
					array(
						'sizes' => '(max-width: 900px) 92vw, 50vw',
						'alt'   => '',
					)
				);
				$caption = wp_get_attachment_caption( $set_photo );
				if ( $caption ) {
					echo '<figcaption>' . esc_html( $caption ) . '</figcaption>';
				}
				?>
			</figure>
		<?php endif; ?>
		<div class="split__text">
			<h2 id="set-title" class="headline"><?php echo wp_kses( __( 'Non una classe. <em>Un set aperto.</em>', 'its-rossellini' ), array( 'em' => array() ) ); ?></h2>
			<p class="section__lead"><?php esc_html_e( 'Qui teoria e pratica entrano nella stessa inquadratura. Produci, sperimenti, sbagli, riprovi e costruisci un portfolio mentre impari.', 'its-rossellini' ); ?></p>
			<dl class="features">
				<div><dt><?php esc_html_e( 'Laboratori veri', 'its-rossellini' ); ?></dt><dd><?php esc_html_e( 'Spazi e tecnologie per lavorare come in produzione.', 'its-rossellini' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Teatro di posa', 'its-rossellini' ); ?></dt><dd><?php esc_html_e( '360 m² nella sede di Via Tiburtina, con aule e laboratori.', 'its-rossellini' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Docenti dall’industria', 'its-rossellini' ); ?></dt><dd><?php esc_html_e( 'Professionisti che insegnano facendo.', 'its-rossellini' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Imprese nel percorso', 'its-rossellini' ); ?></dt><dd><?php esc_html_e( 'Stage, brief e relazioni che iniziano prima del diploma.', 'its-rossellini' ); ?></dd></div>
			</dl>
			<a class="link-arrow" href="<?php echo esc_url( its_page_url( 'academy' ) ); ?>"><?php esc_html_e( 'Conosci l’Academy', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
	</div>
</section>

<section class="section section--dark" aria-labelledby="fuori-title">
	<div class="wrap">
		<h2 id="fuori-title" class="headline"><?php echo wp_kses( __( 'La formazione <em>succede fuori.</em>', 'its-rossellini' ), array( 'em' => array() ) ); ?></h2>

		<div class="outcome">
			<p class="outcome__claim">
				<?php
				printf(
					/* translators: 1: article (Il / L’), 2: share of graduates employed, e.g. 87% */
					esc_html__( '%1$s%2$s dei diplomati lavora entro 12 mesi.', 'its-rossellini' ),
					esc_html( its_article_for_number( $facts['employment'] ) ),
					'<strong>' . esc_html( $facts['employment'] ) . '</strong>'
				);
				?>
				<span class="outcome__source"><?php echo esc_html( $facts['source'] ); ?>.</span>
			</p>
			<ul class="outcome__facts">
				<li><strong><?php echo esc_html( $facts['hours'] ); ?> ore</strong> <?php esc_html_e( 'in aula e laboratorio', 'its-rossellini' ); ?></li>
				<li><strong><?php echo esc_html( $facts['internship'] ); ?> ore</strong> <?php esc_html_e( 'di tirocinio in azienda', 'its-rossellini' ); ?></li>
				<li><strong><?php echo esc_html( $facts['years'] ); ?> anni</strong> <?php esc_html_e( 'per entrare nel settore', 'its-rossellini' ); ?></li>
			</ul>
		</div>

		<div class="path">
			<h2 class="headline headline--m path__title"><?php echo wp_kses( __( 'Dal primo ciak <em>al primo contratto.</em>', 'its-rossellini' ), array( 'em' => array() ) ); ?></h2>
			<ol class="path__steps">
				<li>
					<h3><?php esc_html_e( 'Orientati', 'its-rossellini' ); ?></h3>
					<p class="path__line"><?php esc_html_e( 'Conosci persone, corsi e laboratori.', 'its-rossellini' ); ?></p>
					<p class="path__detail"><?php esc_html_e( 'Open day, colloquio individuale, FAQ', 'its-rossellini' ); ?></p>
				</li>
				<li>
					<h3><?php esc_html_e( 'Fai esperienza', 'its-rossellini' ); ?></h3>
					<p class="path__line"><?php esc_html_e( 'Lavora su brief, set e progetti reali.', 'its-rossellini' ); ?></p>
					<p class="path__detail">
						<?php
						/* translators: %s: hours of classes and labs */
						printf( esc_html__( '%s ore, portfolio, lavoro in team', 'its-rossellini' ), esc_html( $facts['hours'] ) );
						?>
					</p>
				</li>
				<li>
					<h3><?php esc_html_e( 'Entra nel settore', 'its-rossellini' ); ?></h3>
					<p class="path__line"><?php esc_html_e( 'Porta le competenze dentro un’azienda.', 'its-rossellini' ); ?></p>
					<p class="path__detail">
						<?php
						/* translators: %s: internship hours */
						printf( esc_html__( '%s ore di stage, placement', 'its-rossellini' ), esc_html( $facts['internship'] ) );
						?>
					</p>
				</li>
			</ol>
		</div>
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
<section class="section" aria-labelledby="news-title">
	<div class="wrap">
		<div class="section__head section__head--row">
			<h2 id="news-title" class="headline"><?php echo wp_kses( __( 'News, eventi, <em>occasioni.</em>', 'its-rossellini' ), array( 'em' => array() ) ); ?></h2>
			<a class="link-arrow" href="<?php echo esc_url( get_permalink( (int) get_option( 'page_for_posts' ) ) ?: its_page_url( 'news' ) ); ?>"><?php esc_html_e( 'Tutte le news', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
		<div class="rows">
			<?php
			foreach ( array_slice( $events['upcoming'], 0, 2 ) as $post ) :
				setup_postdata( $GLOBALS['post'] = $post ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				get_template_part( 'template-parts/dated-item' );
			endforeach;
			wp_reset_postdata();
			while ( $news->have_posts() ) :
				$news->the_post();
				get_template_part( 'template-parts/dated-item' );
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>

<?php
$partners = trim( (string) apply_filters( 'the_content', (string) get_post_field( 'post_content', get_queried_object_id() ) ) );
if ( $partners ) :
	?>
	<section class="section section--light" aria-labelledby="partner-title">
		<div class="wrap partners">
			<h2 id="partner-title" class="headline headline--m"><?php esc_html_e( 'I nostri partner', 'its-rossellini' ); ?></h2>
			<div class="partners__logos"><?php echo $partners; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		</div>
	</section>
<?php endif; ?>

<section class="cta" aria-labelledby="cta-title">
	<div class="wrap cta__grid">
		<h2 id="cta-title" class="headline"><?php echo wp_kses( __( 'Vuoi visitare <em>i laboratori?</em>', 'its-rossellini' ), array( 'em' => array() ) ); ?></h2>
		<div class="cta__text">
			<p><?php esc_html_e( 'Prenota un open day o un incontro individuale.', 'its-rossellini' ); ?> <?php echo esc_html( $contacts['address'] ); ?>, <?php echo esc_html( $contacts['transit'] ); ?>.</p>
			<div class="cta__actions">
				<a class="btn btn--navy" href="<?php echo esc_url( $meet_url ); ?>"><?php esc_html_e( 'Prenota un incontro', 'its-rossellini' ); ?></a>
				<a class="btn btn--outline-navy" href="<?php echo esc_url( its_page_url( 'iscrizioni' ) ); ?>"><?php esc_html_e( 'Preiscriviti al biennio', 'its-rossellini' ); ?></a>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
