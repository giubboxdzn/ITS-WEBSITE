<?php
/**
 * Template Name: Bandi e documenti
 *
 * Operate surface: find the right document in seconds. Filters are a plain GET form.
 *
 * @package its-rossellini
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$docs     = its_documents_query();
	$tipi     = its_filter_terms( 'tipo' );
	$bienni   = its_filter_terms( 'biennio' );
	$sel_tipo = isset( $_GET['tipo'] ) ? sanitize_title( wp_unslash( $_GET['tipo'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$sel_bien = isset( $_GET['biennio'] ) ? sanitize_title( wp_unslash( $_GET['biennio'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$query    = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$filtered = $sel_tipo || $sel_bien || $query;
	get_template_part(
		'template-parts/page-head',
		null,
		array(
			'title' => get_the_title(),
			'lead'  => has_excerpt() ? get_the_excerpt() : __( 'Bandi, graduatorie, convocazioni e albi della Fondazione, per corso e biennio.', 'its-rossellini' ),
		)
	);
	?>
	<div class="wrap page-body page-body--wide">
		<?php if ( '' !== trim( get_the_content() ) ) : ?>
			<div class="prose"><?php the_content(); ?></div>
		<?php endif; ?>
		<?php get_template_part( 'template-parts/child-pages' ); ?>

		<form class="doc-filter" method="get" action="<?php the_permalink(); ?>" role="search" aria-label="<?php esc_attr_e( 'Cerca nei documenti', 'its-rossellini' ); ?>">
			<div class="field field--grow">
				<label for="doc-q"><?php esc_html_e( 'Cerca', 'its-rossellini' ); ?></label>
				<input id="doc-q" type="search" name="q" value="<?php echo esc_attr( $query ); ?>" autocomplete="off" placeholder="<?php esc_attr_e( 'Es. graduatoria Videomaker…', 'its-rossellini' ); ?>">
			</div>
			<?php if ( $tipi ) : ?>
				<div class="field">
					<label for="doc-tipo"><?php esc_html_e( 'Tipo', 'its-rossellini' ); ?></label>
					<select id="doc-tipo" name="tipo">
						<option value=""><?php esc_html_e( 'Tutti i tipi', 'its-rossellini' ); ?></option>
						<?php foreach ( $tipi as $term ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>"<?php selected( $sel_tipo, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>
			<?php if ( $bienni ) : ?>
				<div class="field">
					<label for="doc-biennio"><?php esc_html_e( 'Biennio', 'its-rossellini' ); ?></label>
					<select id="doc-biennio" name="biennio">
						<option value=""><?php esc_html_e( 'Tutti i bienni', 'its-rossellini' ); ?></option>
						<?php foreach ( $bienni as $term ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>"<?php selected( $sel_bien, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>
			<div class="field field--actions">
				<button class="btn btn--blue" type="submit"><?php esc_html_e( 'Filtra', 'its-rossellini' ); ?></button>
				<?php if ( $filtered ) : ?>
					<a class="link-plain" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Azzera filtri', 'its-rossellini' ); ?></a>
				<?php endif; ?>
			</div>
		</form>

		<p class="doc-count" role="status">
			<?php
			/* translators: %s: number of documents */
			echo esc_html( sprintf( _n( '%s documento', '%s documenti', (int) $docs->found_posts, 'its-rossellini' ), number_format_i18n( (int) $docs->found_posts ) ) );
			?>
		</p>

		<?php if ( $docs->have_posts() ) : ?>
			<table class="doc-table">
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Documento', 'its-rossellini' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Tipo', 'its-rossellini' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Biennio', 'its-rossellini' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Pubblicato', 'its-rossellini' ); ?></th>
						<th scope="col"><span class="screen-reader-text"><?php esc_html_e( 'File', 'its-rossellini' ); ?></span></th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ( $docs->have_posts() ) :
						$docs->the_post();
						$t    = get_the_terms( get_the_ID(), 'tipo' );
						$b    = get_the_terms( get_the_ID(), 'biennio' );
						$file = its_document_file( get_the_ID() );
						?>
						<tr>
							<th scope="row"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></th>
							<td data-label="<?php esc_attr_e( 'Tipo', 'its-rossellini' ); ?>"><?php echo esc_html( ( $t && ! is_wp_error( $t ) ) ? $t[0]->name : '—' ); ?></td>
							<td data-label="<?php esc_attr_e( 'Biennio', 'its-rossellini' ); ?>"><?php echo esc_html( ( $b && ! is_wp_error( $b ) ) ? $b[0]->name : '—' ); ?></td>
							<td data-label="<?php esc_attr_e( 'Pubblicato', 'its-rossellini' ); ?>"><time datetime="<?php echo esc_attr( get_the_date( 'Y-m-d' ) ); ?>"><?php echo esc_html( get_the_date( _x( 'd/m/Y', 'document table date format', 'its-rossellini' ) ) ); ?></time></td>
							<td class="doc-table__file">
								<?php if ( $file ) : ?>
									<a href="<?php echo esc_url( $file ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: document title */ __( 'Scarica: %s', 'its-rossellini' ), get_the_title() ) ); ?>"><?php echo its_icon( 'download' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
			<?php
			its_pagination( $docs );
			wp_reset_postdata();
			?>
		<?php else : ?>
			<div class="empty">
				<p><?php esc_html_e( 'Nessun documento corrisponde ai filtri. Prova a togliere il biennio o a cercare per nome del corso.', 'its-rossellini' ); ?></p>
				<a class="link-plain" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Azzera filtri', 'its-rossellini' ); ?></a>
			</div>
		<?php endif; ?>
	</div>
	<?php
endwhile;

get_footer();
