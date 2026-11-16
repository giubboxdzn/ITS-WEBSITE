<?php
/**
 * Interior hero: the home hero's markup and classes, with the page's values.
 * Full-bleed footage (the page photo when it is large enough, else the home video), title and actions
 * bottom left, glass cards bottom right, and a white bar over the lower edge with the page's sections.
 *
 * Args: 'title' (plain text), 'lead' (plain text), 'image_id' (int), 'index' (array of [anchor, label]),
 *       'links' (array of [url, label]: the first is the main action), 'stat' ([figure, label] or empty).
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$title    = $args['title'] ?? '';
$lead     = $args['lead'] ?? '';
$index    = $args['index'] ?? array();
$links    = $args['links'] ?? array();
$stat     = $args['stat'] ?? array();
$facts    = its_facts();
$contacts = its_contacts();
$meet_url = its_page_url( 'incontriamoci', its_page_url( 'contatti' ) );
$next     = its_next_event();
$media    = its_page_hero_media( (int) ( $args['image_id'] ?? 0 ) );
$video    = $media['video'];
$image_id = $media['image'];
$poster   = $image_id ? (string) wp_get_attachment_image_url( $image_id, 'full' ) : '';
if ( ! $stat ) {
	$stat = array( $facts['employment'], __( 'dei diplomati lavora entro 12 mesi', 'its-rossellini' ) );
}
?>
<section class="hero hero--page" aria-labelledby="hero-title">
	<div class="hero__card">
		<div class="hero__media">
			<?php if ( $video ) : ?>
				<video class="hero__video" muted loop playsinline preload="metadata" poster="<?php echo esc_url( $poster ); ?>" aria-hidden="true" data-hero-video>
					<source src="<?php echo esc_url( $video ); ?>" type="video/mp4">
				</video>
			<?php elseif ( $image_id ) : ?>
				<?php
				echo wp_get_attachment_image(
					$image_id,
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
			<?php its_breadcrumbs(); ?>
			<h1 id="hero-title" class="hero__title<?php echo mb_strlen( $title ) > 26 ? ' hero__title--long' : ''; ?>"><?php echo its_display_text( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
			<?php if ( $lead ) : ?>
				<p class="hero__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
			<?php if ( $links ) : ?>
				<div class="hero__actions">
					<?php foreach ( array_slice( $links, 0, 2 ) as $n => [ $url, $label ] ) : ?>
						<?php $icon = its_link_icon( $url ); ?>
						<?php if ( 0 === $n ) : ?>
							<a class="btn btn--white" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?><?php echo its_icon( 'arrow' === $icon ? 'arrow-up' : $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						<?php else : ?>
							<a class="btn btn--glass" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<div class="hero__float">
			<div class="glass-card">
				<p class="glass-card__num"><?php echo esc_html( $stat[0] ); ?></p>
				<p class="glass-card__text"><?php echo esc_html( $stat[1] ); ?></p>
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

	<?php if ( count( $index ) > 1 ) : ?>
		<nav class="logos-bar hero-index" aria-label="<?php esc_attr_e( 'In questa pagina', 'its-rossellini' ); ?>">
			<ol>
				<?php foreach ( $index as $n => [ $anchor, $label ] ) : ?>
					<li><a href="#<?php echo esc_attr( $anchor ); ?>"><span class="hero-index__n" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $n + 1 ) ); ?></span><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
			</ol>
		</nav>
	<?php endif; ?>
</section>
