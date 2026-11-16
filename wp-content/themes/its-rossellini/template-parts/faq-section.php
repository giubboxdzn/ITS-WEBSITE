<?php
/**
 * The first FAQ items as an accordion, with links to the FAQ page and to contacts.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$em = array(
	'em'     => array(),
	'strong' => array(),
);
?>
<?php $faq = its_faq_items( 6 ); ?>
<?php if ( $faq ) : ?>
	<section class="section faq-section" aria-labelledby="faq-title">
		<div class="wrap wrap--narrow">
			<div class="panel__head panel__head--center">
				<h2 id="faq-title" class="display"><?php echo wp_kses( __( 'Tutto quello che <strong>devi sapere.</strong>', 'its-rossellini' ), $em ); ?></h2>
			</div>
			<?php get_template_part( 'template-parts/faq-list', null, array( 'items' => $faq ) ); ?>
			<p class="faq-section__more">
				<?php esc_html_e( 'Non trovi la tua domanda?', 'its-rossellini' ); ?>
				<a href="<?php echo esc_url( its_page_url( 'faq' ) ); ?>"><?php esc_html_e( 'Tutte le FAQ', 'its-rossellini' ); ?></a>
				<?php esc_html_e( 'oppure', 'its-rossellini' ); ?>
				<a href="<?php echo esc_url( its_page_url( 'contatti' ) ); ?>"><?php esc_html_e( 'scrivici', 'its-rossellini' ); ?></a>.
			</p>
		</div>
	</section>
<?php endif; ?>
