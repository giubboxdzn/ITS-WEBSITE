<?php
/**
 * Site footer: columns, socials, motion switch and the logo as a watermark.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

$contacts = its_contacts();
$tel      = preg_replace( '/[^0-9+]/', '', $contacts['phone'] );
$funding  = its_funding_logos();
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
			<ul class="colophon__social">
				<?php if ( $contacts['instagram'] ) : ?>
					<li><a href="<?php echo esc_url( $contacts['instagram'] ); ?>" aria-label="Instagram"><?php echo its_icon( 'instagram' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
				<?php endif; ?>
				<?php if ( $contacts['x'] ) : ?>
					<li><a href="<?php echo esc_url( $contacts['x'] ); ?>" aria-label="X"><?php echo its_icon( 'x' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
				<?php endif; ?>
				<li><a href="mailto:<?php echo esc_attr( antispambot( $contacts['email'] ) ); ?>" aria-label="<?php esc_attr_e( 'Email', 'its-rossellini' ); ?>"><?php echo its_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
			</ul>
			<?php echo its_language_switcher( 'footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</div>

		<nav class="colophon__col" aria-labelledby="footer-offerta">
			<h2 id="footer-offerta" class="colophon__title"><?php esc_html_e( 'Offerta e servizi', 'its-rossellini' ); ?></h2>
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

		<div class="colophon__col">
			<h2 class="colophon__title"><?php esc_html_e( 'Contatti', 'its-rossellini' ); ?></h2>
			<address class="colophon__contacts">
				<p><?php echo esc_html( $contacts['address'] ); ?><br><?php echo esc_html( $contacts['transit'] ); ?></p>
				<p><a href="tel:<?php echo esc_attr( $tel ); ?>"><?php echo esc_html( $contacts['phone'] ); ?></a><br><?php echo esc_html( $contacts['hours'] ); ?></p>
				<p><a href="mailto:<?php echo esc_attr( antispambot( $contacts['email'] ) ); ?>"><?php echo esc_html( antispambot( $contacts['email'] ) ); ?></a><br><a href="<?php echo esc_url( its_page_url( 'contatti' ) ); ?>"><?php esc_html_e( 'Tutti gli uffici', 'its-rossellini' ); ?></a></p>
			</address>
		</div>

		<nav class="colophon__col" aria-labelledby="footer-legal">
			<h2 id="footer-legal" class="colophon__title"><?php esc_html_e( 'Trasparenza', 'its-rossellini' ); ?></h2>
			<ul class="colophon__links">
				<li><a href="<?php echo esc_url( its_page_url( 'bandi-albi' ) ); ?>"><?php esc_html_e( 'Bandi e albi', 'its-rossellini' ); ?></a></li>
				<li><a href="<?php echo esc_url( its_page_url( 'bandi-albi/amministrazione-trasparente' ) ); ?>"><?php esc_html_e( 'Amministrazione trasparente', 'its-rossellini' ); ?></a></li>
			</ul>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'legal',
					'container'      => false,
					'menu_class'     => 'colophon__links',
					'depth'          => 1,
					'fallback_cb'    => '__return_false',
				)
			);
			?>
		</nav>
	</div>

	<?php if ( $funding ) : ?>
		<div class="wrap colophon__funding">
			<div class="funding">
				<?php foreach ( $funding as $block ) : ?>
					<ul class="funding__block" aria-label="<?php echo esc_attr( $block['label'] ); ?>">
						<?php foreach ( $block['logos'] as $logo ) : ?>
							<li><img src="<?php echo esc_url( $logo['src'] ); ?>" alt="<?php echo esc_attr( $logo['alt'] ); ?>" width="<?php echo (int) $logo['width']; ?>" height="<?php echo (int) $logo['height']; ?>" style="--logo-h: <?php echo (int) $logo['height']; ?>px" loading="lazy" decoding="async"></li>
						<?php endforeach; ?>
					</ul>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="wrap colophon__base">
		<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php esc_html_e( 'Fondazione ITS Academy Roberto Rossellini', 'its-rossellini' ); ?></p>
		<button class="motion-switch" type="button" role="switch" aria-checked="true" hidden data-motion-toggle>
			<span class="motion-switch__track" aria-hidden="true"><span class="motion-switch__thumb"></span></span>
			<span><?php esc_html_e( 'Animazioni', 'its-rossellini' ); ?></span>
		</button>
		<a class="to-top" href="#top" data-to-top>
			<span><?php esc_html_e( 'Torna su', 'its-rossellini' ); ?></span>
			<span class="to-top__icon" aria-hidden="true"><?php echo its_icon( 'top' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
		</a>
	</div>

	<div class="colophon__mark" aria-hidden="true"><?php echo its_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
