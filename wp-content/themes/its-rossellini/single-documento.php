<?php
/**
 * Single document: what it is, which cycle, and the file. Nothing else competes.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$id      = get_the_ID();
	$file    = its_document_file( $id );
	$tipo    = get_the_terms( $id, 'tipo' );
	$biennio = get_the_terms( $id, 'biennio' );
	$format  = (string) its_meta( $id, its_meta_keys( 'doc_format' ) );
	$size    = (string) its_meta( $id, its_meta_keys( 'doc_size' ) );
	$facts   = array_filter(
		array(
			__( 'Tipo', 'its-rossellini' )       => ( $tipo && ! is_wp_error( $tipo ) ) ? $tipo[0]->name : '',
			__( 'Biennio', 'its-rossellini' )    => ( $biennio && ! is_wp_error( $biennio ) ) ? $biennio[0]->name : '',
			__( 'Pubblicato', 'its-rossellini' ) => get_the_date(),
			__( 'Formato', 'its-rossellini' )    => trim( $format . ( $size ? ' · ' . $size : '' ), ' ·' ),
		)
	);
	get_template_part( 'template-parts/page-head', null, array( 'title' => get_the_title() ) );
	?>
	<div class="wrap page-body page-body--split">
		<dl class="facts">
			<?php foreach ( $facts as $label => $value ) : ?>
				<div><dt><?php echo esc_html( $label ); ?></dt><dd><?php echo esc_html( $value ); ?></dd></div>
			<?php endforeach; ?>
		</dl>
		<div class="prose">
			<?php the_content(); ?>
			<p class="prose__actions">
				<?php if ( $file ) : ?>
					<a class="btn btn--blue" href="<?php echo esc_url( $file ); ?>">
						<?php echo its_icon( 'download' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php
						/* translators: %s: file format, e.g. PDF */
						echo esc_html( $format ? sprintf( __( 'Apri il documento (%s)', 'its-rossellini' ), $format ) : __( 'Apri il documento', 'its-rossellini' ) );
						?>
					</a>
				<?php else : ?>
					<span class="notice"><?php esc_html_e( 'Il file di questo documento non è ancora disponibile online. Per riceverlo scrivi alla segreteria.', 'its-rossellini' ); ?></span>
				<?php endif; ?>
				<a class="link-arrow" href="<?php echo esc_url( its_page_url( 'bandi-albi' ) ); ?>"><?php esc_html_e( 'Tutti i documenti', 'its-rossellini' ); ?><?php echo its_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</p>
		</div>
	</div>
	<?php
endwhile;

get_footer();
