<?php
/**
 * Plugin Name: ITS Rossellini — offline (solo sviluppo locale)
 * Description: La copia locale non parla con internet (WP_HTTP_BLOCK_EXTERNAL in wp-config.php). Le chiamate a WordPress.org (controllo aggiornamenti di core, plugin e temi, directory di temi e plugin) ricevono una risposta «servizio non disponibile» invece di un errore di trasporto: WordPress rinuncia in silenzio invece di stampare il warning «could not establish a secure connection». Non va installato in produzione.
 *
 * @package its-rossellini-dev
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'pre_http_request',
	/**
	 * @param false|array|WP_Error $response Short-circuit value.
	 */
	static function ( $response, array $args, string $url ) {
		if ( false !== $response || 'api.wordpress.org' !== wp_parse_url( $url, PHP_URL_HOST ) || ! _wp_http_get_object()->block_request( $url ) ) {
			return $response;
		}
		return array(
			'headers'  => array(),
			'body'     => '',
			'response' => array(
				'code'    => 503,
				'message' => 'Service Unavailable',
			),
			'cookies'  => array(),
			'filename' => null,
		);
	},
	10,
	3
);
