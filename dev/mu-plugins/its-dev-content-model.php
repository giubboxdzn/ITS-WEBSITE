<?php
/**
 * Plugin Name: ITS Rossellini — content model (solo sviluppo locale)
 * Description: Registra gli stessi tipi di contenuto e tassonomie che Rossellini Core registra sul sito live, solo se non esistono già. Non va installato in produzione.
 *
 * @package its-rossellini-dev
 */

defined( 'ABSPATH' ) || exit;

// Locally the site answers on localhost and on the PC's LAN IP: Polylang must not cache one home URL.
if ( ! defined( 'PLL_CACHE_HOME_URL' ) ) {
	define( 'PLL_CACHE_HOME_URL', false );
}

add_action(
	'init',
	static function (): void {
		$types = array(
			'corso'          => array( 'Corsi', 'Corso', 'corso', array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes' ) ),
			'evento'         => array( 'Eventi', 'Evento', 'event', array( 'title', 'editor', 'excerpt', 'thumbnail' ) ),
			'edizione_corso' => array( 'Edizioni dei corsi', 'Edizione', 'edizione', array( 'title', 'editor', 'excerpt' ) ),
			'documento'      => array( 'Documenti e bandi', 'Documento', 'documento', array( 'title', 'editor', 'excerpt' ) ),
		);
		foreach ( $types as $type => [ $plural, $singular, $slug, $supports ] ) {
			if ( post_type_exists( $type ) ) {
				continue;
			}
			register_post_type(
				$type,
				array(
					'labels'       => array(
						'name'          => $plural,
						'singular_name' => $singular,
					),
					'public'       => true,
					'show_in_rest' => true,
					'has_archive'  => false,
					'rewrite'      => array( 'slug' => $slug ),
					'supports'     => $supports,
					'menu_icon'    => 'dashicons-media-default',
				)
			);
		}

		$taxonomies = array(
			'area_tecnologica' => array( 'Area tecnologica', array( 'corso' ) ),
			'tipo_evento'      => array( 'Tipo evento', array( 'evento' ) ),
			'famiglia'         => array( 'Famiglia', array( 'edizione_corso' ) ),
			'biennio'          => array( 'Biennio', array( 'documento', 'edizione_corso' ) ),
			'tipo'             => array( 'Tipo', array( 'documento' ) ),
		);
		foreach ( $taxonomies as $tax => [ $label, $objects ] ) {
			if ( taxonomy_exists( $tax ) ) {
				continue;
			}
			register_taxonomy(
				$tax,
				$objects,
				array(
					'label'             => $label,
					'public'            => true,
					'hierarchical'      => true,
					'show_in_rest'      => true,
					'show_admin_column' => true,
				)
			);
		}

		$meta = array(
			'evento'    => array( 'data_evento', 'ora_evento', 'modalita', 'luogo' ),
			'documento' => array( 'file_url', 'formato', 'dimensione' ),
		);
		foreach ( $meta as $type => $keys ) {
			foreach ( $keys as $key ) {
				register_post_meta(
					$type,
					$key,
					array(
						'type'         => 'string',
						'single'       => true,
						'show_in_rest' => true,
					)
				);
			}
		}
	},
	5
);

/*
 * Local installs have no it_IT language pack (wordpress.org is not reachable from the dev
 * sandbox), so translate month and weekday names for date output. Production uses it_IT.
 */
add_filter(
	'gettext',
	static function ( string $translation, string $text, string $domain ): string {
		static $map = array(
			'January'   => 'gennaio',
			'February'  => 'febbraio',
			'March'     => 'marzo',
			'April'     => 'aprile',
			'May'       => 'maggio',
			'June'      => 'giugno',
			'July'      => 'luglio',
			'August'    => 'agosto',
			'September' => 'settembre',
			'October'   => 'ottobre',
			'November'  => 'novembre',
			'December'  => 'dicembre',
			'Monday'    => 'lunedì',
			'Tuesday'   => 'martedì',
			'Wednesday' => 'mercoledì',
			'Thursday'  => 'giovedì',
			'Friday'    => 'venerdì',
			'Saturday'  => 'sabato',
			'Sunday'    => 'domenica',
		);
		return ( 'default' === $domain && isset( $map[ $text ] ) && 'en_US' === get_locale() ) ? $map[ $text ] : $translation;
	},
	10,
	3
);
add_filter(
	'gettext_with_context',
	static function ( string $translation, string $text, string $context, string $domain ): string {
		static $abbr = array(
			'Jan' => 'gen', 'Feb' => 'feb', 'Mar' => 'mar', 'Apr' => 'apr', 'May' => 'mag', 'Jun' => 'giu',
			'Jul' => 'lug', 'Aug' => 'ago', 'Sep' => 'set', 'Oct' => 'ott', 'Nov' => 'nov', 'Dec' => 'dic',
		);
		$is_abbr = str_ends_with( $context, ' abbreviation' ) || 'abbreviated month name' === $context;
		return ( 'default' === $domain && $is_abbr && isset( $abbr[ $text ] ) && 'en_US' === get_locale() ) ? $abbr[ $text ] : $translation;
	},
	10,
	4
);
