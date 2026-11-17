<?php
/**
 * Customizer: contacts and published figures, so dated claims stay editable.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'customize_register',
	static function ( WP_Customize_Manager $wp_customize ): void {
		$sections = array(
			'its_contacts' => array(
				__( 'Contatti e sede', 'its-rossellini' ),
				array(
					'its_address'   => array( __( 'Indirizzo', 'its-rossellini' ), 'Via Tiburtina 691, Roma' ),
					'its_transit'   => array( __( 'Come arrivare', 'its-rossellini' ), 'Metro B Pietralata o Monti Tiburtini' ),
					'its_hours'     => array( __( 'Orari', 'its-rossellini' ), 'Lun–Ven 9:30–13:30 / 14:30–18:30' ),
					'its_phone'     => array( __( 'Telefono', 'its-rossellini' ), '06 5576493' ),
					'its_email'     => array( __( 'Email', 'its-rossellini' ), 'info@itsrossellini.it' ),
					'its_instagram' => array( __( 'Instagram (URL)', 'its-rossellini' ), 'https://www.instagram.com/itsrossellini/' ),
					'its_x'         => array( __( 'X (URL)', 'its-rossellini' ), 'https://x.com/Its_Rossellini' ),
				),
			),
			'its_facts'    => array(
				__( 'Dati pubblicati', 'its-rossellini' ),
				array(
					'its_fact_years'             => array( __( 'Durata (anni)', 'its-rossellini' ), '2' ),
					'its_fact_hours'             => array( __( 'Ore in aula e laboratorio', 'its-rossellini' ), '1.000' ),
					'its_fact_internship'        => array( __( 'Ore di tirocinio', 'its-rossellini' ), '800+' ),
					'its_fact_places'            => array( __( 'Posti per corso', 'its-rossellini' ), '28' ),
					'its_fact_employment'        => array( __( 'Diplomati occupati entro 12 mesi', 'its-rossellini' ), '87%' ),
					'its_fact_employment_source' => array( __( 'Fonte del dato occupazionale', 'its-rossellini' ), 'Dato pubblicato da ITS Rossellini' ),
				),
			),
		);
		$wp_customize->add_section( 'its_home', array( 'title' => __( 'Home', 'its-rossellini' ) ) );
		$wp_customize->add_setting(
			'its_hero_video',
			array(
				'default'           => '',
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				'its_hero_video',
				array(
					'label'       => __( 'Video della home (MP4, senza audio)', 'its-rossellini' ),
					'description' => __( 'Come copertina si usa l’immagine in evidenza della pagina iniziale.', 'its-rossellini' ),
					'section'     => 'its_home',
					'mime_type'   => 'video',
				)
			)
		);

		// Legge 4/2004, art. 3-quater: the accessibility statement and the feedback channel, linked from every page.
		$wp_customize->add_section(
			'its_accessibility',
			array(
				'title'       => __( 'Accessibilità', 'its-rossellini' ),
				'description' => __( 'Link nel footer di ogni pagina, come chiede la Legge 4/2004 (art. 3-quater).', 'its-rossellini' ),
			)
		);
		$wp_customize->add_setting(
			'its_a11y_statement',
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			'its_a11y_statement',
			array(
				'label'       => __( 'Dichiarazione di accessibilità (URL)', 'its-rossellini' ),
				'description' => __( 'Il link generato da form.agid.gov.it, oppure una pagina del sito. Vuoto: la pagina con slug «dichiarazione-di-accessibilita», se esiste.', 'its-rossellini' ),
				'section'     => 'its_accessibility',
				'type'        => 'url',
			)
		);
		$wp_customize->add_setting(
			'its_a11y_email',
			array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_email',
			)
		);
		$wp_customize->add_control(
			'its_a11y_email',
			array(
				'label'       => __( 'Email per le segnalazioni di accessibilità', 'its-rossellini' ),
				'description' => __( 'Il meccanismo di feedback indicato nella dichiarazione. Vuoto: l’email dei contatti.', 'its-rossellini' ),
				'section'     => 'its_accessibility',
				'type'        => 'email',
			)
		);

		foreach ( $sections as $section => [ $title, $fields ] ) {
			$wp_customize->add_section( $section, array( 'title' => $title ) );
			foreach ( $fields as $id => [ $label, $default ] ) {
				$wp_customize->add_setting(
					$id,
					array(
						'default'           => $default,
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				$wp_customize->add_control(
					$id,
					array(
						'label'   => $label,
						'section' => $section,
						'type'    => 'text',
					)
				);
			}
		}
	}
);

/**
 * Customizer texts visitors read, offered for translation in Polylang (Languages → Translations).
 */
add_action(
	'init',
	static function (): void {
		if ( ! is_admin() || ! function_exists( 'pll_register_string' ) ) {
			return;
		}
		$texts = array(
			'its_address'                => 'Via Tiburtina 691, Roma',
			'its_transit'                => 'Metro B Pietralata o Monti Tiburtini',
			'its_hours'                  => 'Lun–Ven 9:30–13:30 / 14:30–18:30',
			'its_fact_employment_source' => 'Dato pubblicato da ITS Rossellini',
		);
		foreach ( $texts as $mod => $default ) {
			pll_register_string( $mod, (string) get_theme_mod( $mod, $default ), 'ITS Rossellini' );
		}
	},
	20
);

/**
 * The default Customizer texts, listed for the theme's translation files (never called).
 *
 * @return string[]
 */
function its_customizer_default_texts(): array {
	return array(
		__( 'Via Tiburtina 691, Roma', 'its-rossellini' ),
		__( 'Metro B Pietralata o Monti Tiburtini', 'its-rossellini' ),
		__( 'Lun–Ven 9:30–13:30 / 14:30–18:30', 'its-rossellini' ),
		__( 'Dato pubblicato da ITS Rossellini', 'its-rossellini' ),
	);
}
