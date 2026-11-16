<?php
/**
 * Interior hero in the home page's language: edge to edge on the deep blue, under the header,
 * display title, lead, actions, the page photo and a glass index of the sections.
 *
 * Args: 'title' (plain text), 'lead' (plain text), 'image_id' (int), 'index' (array of [anchor, label]),
 *       'links' (array of [url, label]: the first is the main action).
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$title    = $args['title'] ?? '';
$lead     = $args['lead'] ?? '';
$image_id = (int) ( $args['image_id'] ?? 0 );
$index    = $args['index'] ?? array();
$links    = $args['links'] ?? array();
$long     = mb_strlen( $title ) > 34;
?>
<header class="page-hero<?php echo $image_id ? ' page-hero--photo' : ''; ?>">
	<?php if ( $image_id ) : ?>
		<div class="page-hero__backdrop" aria-hidden="true">
			<?php echo wp_get_attachment_image( $image_id, 'medium_large', false, array( 'alt' => '', 'loading' => 'eager', 'sizes' => '60vw' ) ); ?>
		</div>
	<?php endif; ?>
	<div class="wrap page-hero__grid">
		<div class="page-hero__text">
			<?php its_breadcrumbs(); ?>
			<h1 class="page-hero__title<?php echo $long ? ' page-hero__title--long' : ''; ?>"><?php echo its_display_text( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
			<?php if ( $lead ) : ?>
				<p class="page-hero__lead"><?php echo esc_html( $lead ); ?></p>
			<?php endif; ?>
			<?php if ( $links ) : ?>
				<div class="hero__actions page-hero__actions">
					<?php foreach ( $links as $n => [ $url, $label ] ) : ?>
						<?php $icon = its_link_icon( $url ); ?>
						<a class="btn <?php echo 0 === $n ? 'btn--white' : 'btn--glass'; ?>" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?><?php echo 0 === $n ? its_icon( 'arrow' === $icon ? 'arrow-up' : $icon ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $image_id || count( $index ) > 2 ) : ?>
			<div class="page-hero__aside">
				<?php if ( $image_id ) : ?>
					<figure class="page-hero__photo">
						<?php
						echo wp_get_attachment_image(
							$image_id,
							'its-portrait',
							false,
							array(
								'alt'           => '',
								'loading'       => 'eager',
								'fetchpriority' => 'high',
								'sizes'         => '(max-width: 900px) 92vw, 30rem',
							)
						);
						?>
					</figure>
				<?php endif; ?>
				<?php if ( count( $index ) > 2 ) : ?>
					<nav class="glass-card page-index" aria-labelledby="page-index-title">
						<p id="page-index-title" class="page-index__title"><?php esc_html_e( 'In questa pagina', 'its-rossellini' ); ?></p>
						<ol>
							<?php foreach ( $index as $n => [ $anchor, $label ] ) : ?>
								<li><a href="#<?php echo esc_attr( $anchor ); ?>"><span class="page-index__n" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $n + 1 ) ); ?></span><?php echo esc_html( $label ); ?></a></li>
							<?php endforeach; ?>
						</ol>
					</nav>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</header>
