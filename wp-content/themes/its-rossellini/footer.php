<?php
/**
 * Site footer: the poster's foot, with the real contacts.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$contacts = its_contacts();
$tel      = preg_replace( '/[^0-9+]/', '', $contacts['phone'] );
?>
</main>

<footer class="colophon">
	<div class="wrap colophon__grid">
		<div class="colophon__id">
			<a class="colophon__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php echo its_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="screen-reader-text"><?php bloginfo( 'name' ); ?></span>
			</a>
			<p class="colophon__claim"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></p>
		</div>

		<address class="colophon__contacts">
			<p><?php echo its_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $contacts['address'] ); ?><br><?php echo esc_html( $contacts['transit'] ); ?></span></p>
			<p><?php echo its_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $contacts['phone'] ); ?></a><br><?php echo esc_html( $contacts['hours'] ); ?></span></p>
			<p><?php echo its_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><a href="mailto:<?php echo esc_attr( antispambot( $contacts['email'] ) ); ?>"><?php echo esc_html( antispambot( $contacts['email'] ) ); ?></a><br><a href="<?php echo esc_url( its_page_url( 'contatti' ) ); ?>"><?php esc_html_e( 'Tutti gli uffici', 'its-rossellini' ); ?></a></span></p>
		</address>

		<nav class="colophon__nav" aria-label="<?php esc_attr_e( 'Link utili', 'its-rossellini' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'colophon__links',
					'depth'          => 1,
					'fallback_cb'    => '__return_false',
				)
			);
			?>
		</nav>
	</div>

	<div class="wrap colophon__wordmark" aria-hidden="true"><?php echo its_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>

	<div class="wrap colophon__base">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php esc_html_e( 'Fondazione ITS Academy Roberto Rossellini', 'its-rossellini' ); ?></p>
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'legal',
				'container'      => 'nav',
				'container_aria_label' => __( 'Note legali', 'its-rossellini' ),
				'menu_class'     => 'colophon__legal',
				'depth'          => 1,
				'fallback_cb'    => '__return_false',
			)
		);
		?>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
