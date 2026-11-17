<?php
/**
 * Closing call on the gradient band, as on the home page.
 *
 * Args: 'title' (plain text, drawn in the display style), 'text' (html), 'links' (array of [url, label]).
 * Without args: the home page's call (visit the labs, pre-enrol).
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$contacts = its_contacts();
$title    = $args['title'] ?? '';
$text     = $args['text'] ?? '';
$links    = $args['links'] ?? array();
if ( '' === $title ) {
	$title = __( 'Vuoi visitare i laboratori?', 'its-rossellini' );
	$text  = esc_html__( 'Prenota un open day o un incontro individuale.', 'its-rossellini' ) . ' ' . esc_html( $contacts['address'] . ', ' . $contacts['transit'] ) . '.';
}
if ( ! $links ) {
	$links = array(
		array( its_page_url( 'incontriamoci', its_page_url( 'contatti' ) ), __( 'Prenota un incontro', 'its-rossellini' ) ),
		array( its_page_url( 'iscrizioni' ), __( 'Preiscriviti al biennio', 'its-rossellini' ) ),
	);
}
// Never a button to the page being read.
$links = array_values( array_filter( $links, static fn( array $link ): bool => ! its_is_current( $link[0] ) ) );
?>
<section class="section" aria-labelledby="cta-title">
	<div class="wrap">
		<div class="cta-card">
			<div>
				<h2 id="cta-title" class="display"><?php echo its_display_text( $title ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				<?php if ( $text ) : ?>
					<p class="lead"><?php echo wp_kses_post( $text ); ?></p>
				<?php endif; ?>
			</div>
			<?php if ( $links ) : ?>
			<div class="cta-card__actions">
				<?php foreach ( $links as $n => [ $url, $label ] ) : ?>
					<?php if ( 0 === $n ) : ?>
						<a class="btn btn--white" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?><?php echo its_icon( 'arrow' === its_link_icon( $url ) ? 'arrow-up' : its_link_icon( $url ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					<?php else : ?>
						<a class="btn btn--outline-light" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>
		</div>
	</div>
</section>
