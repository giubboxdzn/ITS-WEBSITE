<?php
/**
 * Site header: the poster's top band.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;
?><!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script>(function(r){r.classList.replace('no-js','js');if('scrollRestoration' in history){history.scrollRestoration='manual';}var m=null;try{m=localStorage.getItem('its-motion');}catch(e){}if(m==='off'||(m!=='on'&&window.matchMedia('(prefers-reduced-motion: reduce)').matches)){r.classList.add('motion-off');}})(document.documentElement);</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main"><?php esc_html_e( 'Vai al contenuto', 'its-rossellini' ); ?></a>

<header class="masthead<?php echo is_front_page() ? ' masthead--over' : ' masthead--dark'; ?>" data-masthead>
	<div class="masthead__bar wrap">
		<a class="masthead__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<?php echo its_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="screen-reader-text"><?php bloginfo( 'name' ); ?></span>
		</a>

		<button class="masthead__toggle" type="button" aria-expanded="false" aria-controls="site-nav">
			<?php echo its_icon( 'menu', 'when-closed' ) . its_icon( 'close', 'when-open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span><?php esc_html_e( 'Menu', 'its-rossellini' ); ?></span>
		</button>

		<div class="masthead__nav" id="site-nav">
			<nav aria-label="<?php esc_attr_e( 'Principale', 'its-rossellini' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'nav',
						'depth'          => 1,
						'fallback_cb'    => static function (): void {
							echo '<ul class="nav">';
							wp_list_pages(
								array(
									'title_li' => '',
									'depth'    => 1,
								)
							);
							echo '</ul>';
						},
					)
				);
				?>
			</nav>
			<div class="masthead__aside">
				<?php echo its_language_switcher( 'header' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<a class="btn btn--cta btn--small" href="<?php echo esc_url( its_page_url( 'iscrizioni' ) ); ?>"><?php esc_html_e( 'Preiscriviti', 'its-rossellini' ); ?></a>
			</div>
		</div>
	</div>
</header>

<main id="main" class="site-main" tabindex="-1">
