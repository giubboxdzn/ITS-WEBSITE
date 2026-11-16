<?php
/**
 * Not found.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part(
	'template-parts/page-head',
	null,
	array(
		'title' => __( 'Questa scena è stata tagliata.', 'its-rossellini' ),
		'lead'  => __( 'La pagina che cerchi non esiste più o ha cambiato indirizzo. Cerca qui sotto o riparti da una di queste pagine.', 'its-rossellini' ),
		'aside' => get_search_form( array( 'echo' => false ) ),
	)
);
?>
<div class="wrap page-body">
	<nav class="index-list" aria-label="<?php esc_attr_e( 'Pagine principali', 'its-rossellini' ); ?>">
		<ul>
			<?php
			$links = array(
				array( its_page_url( 'offerta-formativa/corsi' ), __( 'I corsi 2026–2028', 'its-rossellini' ) ),
				array( its_page_url( 'iscrizioni' ), __( 'Preiscrizione', 'its-rossellini' ) ),
				array( its_page_url( 'bandi-albi' ), __( 'Bandi e documenti', 'its-rossellini' ) ),
				array( its_page_url( 'contatti' ), __( 'Contatti e sede', 'its-rossellini' ) ),
			);
			foreach ( $links as [ $url, $label ] ) :
				?>
				<li><a href="<?php echo esc_url( $url ); ?>"><span><?php echo esc_html( $label ); ?></span><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
</div>
<?php
get_footer();
