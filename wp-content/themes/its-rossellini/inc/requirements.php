<?php
/**
 * The plugins the theme relies on. When one is missing or inactive, the admin shows a notice
 * with the button to install or activate it; nothing is installed on its own.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

/**
 * The plugins the theme needs, whether each is active, and where it can be found.
 *
 * - `file`: the plugin's main file when it is installed (it can then be activated);
 * - `slug`: its WordPress.org slug (it can then be installed from the admin); empty when it is not there.
 *
 * @return array<string, array{name: string, active: bool, file: string, slug: string, why: string}>
 */
function its_required_plugins(): array {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$installed = get_plugins();
	$find      = static function ( string $name ) use ( $installed ): string {
		foreach ( $installed as $file => $data ) {
			if ( 0 === stripos( (string) ( $data['Name'] ?? '' ), $name ) ) {
				return (string) $file;
			}
		}
		return '';
	};
	return array(
		'rossellini-core' => array(
			'name'   => 'Rossellini Core',
			'active' => post_type_exists( 'corso' ),
			'file'   => $find( 'Rossellini Core' ),
			'slug'   => '',
			'why'    => __( 'Registra corsi, eventi, edizioni e documenti: senza, quelle pagine restano vuote.', 'its-rossellini' ),
		),
		'polylang'        => array(
			'name'   => 'Polylang',
			'active' => function_exists( 'pll_the_languages' ),
			'file'   => $find( 'Polylang' ),
			'slug'   => 'polylang',
			'why'    => __( 'Gestisce italiano e inglese: senza, il sito resta solo in italiano e il selettore della lingua non compare.', 'its-rossellini' ),
		),
	);
}

/**
 * The action offered for a missing plugin: activate it, install it, or where to get it.
 *
 * @param array{name: string, active: bool, file: string, slug: string, why: string} $plugin One entry of its_required_plugins().
 */
function its_required_plugin_action( array $plugin ): string {
	if ( '' !== $plugin['file'] ) {
		$url = wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . rawurlencode( $plugin['file'] ) ), 'activate-plugin_' . $plugin['file'] );
		return sprintf( '<a class="button button-primary" href="%1$s">%2$s</a>', esc_url( $url ), esc_html__( 'Attiva', 'its-rossellini' ) );
	}
	if ( '' !== $plugin['slug'] ) {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return esc_html__( 'Chiedi a un amministratore di installarlo.', 'its-rossellini' );
		}
		$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . rawurlencode( $plugin['slug'] ) ), 'install-plugin_' . $plugin['slug'] );
		return sprintf( '<a class="button button-primary" href="%1$s">%2$s</a>', esc_url( $url ), esc_html__( 'Installa da WordPress.org', 'its-rossellini' ) );
	}
	return esc_html__( 'Non è su WordPress.org: si copia dal sito attuale, la sua cartella in wp-content/plugins, poi si attiva in Plugin.', 'its-rossellini' );
}

add_action(
	'admin_notices',
	static function (): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$missing = array_filter(
			its_required_plugins(),
			static fn ( array $plugin ): bool => ! $plugin['active']
		);
		if ( ! $missing ) {
			return;
		}
		echo '<div class="notice notice-warning its-requirements"><p><strong>' . esc_html__( 'Tema ITS Rossellini: servono questi plugin.', 'its-rossellini' ) . '</strong></p><ul>';
		foreach ( $missing as $plugin ) {
			printf(
				'<li style="display:flex;flex-wrap:wrap;align-items:center;gap:.5em 1em;margin:.6em 0"><span><strong>%1$s</strong> — %2$s</span> %3$s</li>',
				esc_html( $plugin['name'] ),
				esc_html( $plugin['why'] ),
				its_required_plugin_action( $plugin ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped inside.
			);
		}
		echo '</ul></div>';
	}
);
