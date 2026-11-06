<?php
/**
 * 0.6.3: the home is made a step at a time, so the admin shows a progress bar while it is made: the menus, the
 * photos three at a time, then the home, the Theme Builder and the legal pages. Each step is a short request
 * sent by assets/js/tp-seed-progress.js; the bar says what is being done and about how long is left, and a step
 * that fails can be sent again ("Riprova"), since every step can run twice without making anything twice.
 * Without JavaScript the button still opens a page that makes the home in one request (inc/site-seeder.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TP_CHILD_SEED_MEDIA_BATCH', 3 );

/**
 * One step of the seed: "start" (the menus), "media" (the next photos from $offset), "finish" (the pages).
 * Under the lock of the seed, so two pages, or the button and the bar, do not work at the same time.
 */
function tp_child_seed_step( string $step, int $offset, bool $force ): array {
	if ( ! tp_child_lock( TP_CHILD_SEED_LOCK ) ) {
		return [ 'busy' => true ];
	}

	try {
		// The request that held the lock may have just finished.
		wp_cache_delete( 'alloptions', 'options' );
		wp_cache_delete( 'notoptions', 'options' );

		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 120 );
		}

		ignore_user_abort( true );

		$paths = tp_child_seed_media_asset_references();
		$total = count( $paths );

		if ( 'start' === $step ) {
			if ( ! $force && tp_child_seed_site_is_ready() ) {
				return tp_child_seed_step_done();
			}

			tp_child_seed_site_start();

			return [ 'next' => 'media', 'offset' => 0, 'total' => $total ];
		}

		if ( 'media' === $step ) {
			$offset = min( max( 0, $offset ), $total );

			foreach ( array_slice( $paths, $offset, TP_CHILD_SEED_MEDIA_BATCH ) as $path ) {
				tp_child_seed_import_media_asset( $path, true );
			}

			$offset = min( $total, $offset + TP_CHILD_SEED_MEDIA_BATCH );

			return [ 'next' => $offset < $total ? 'media' : 'finish', 'offset' => $offset, 'total' => $total ];
		}

		if ( 'finish' === $step ) {
			// The photos are in the library: this only reads them.
			tp_child_seed_media_assets();
			tp_child_seed_site_finish();

			return tp_child_seed_step_done();
		}

		return [ 'error' => 'step' ];
	} finally {
		delete_option( TP_CHILD_SEED_LOCK );
	}
}

/**
 * The answer of the last step: where the home is.
 */
function tp_child_seed_step_done(): array {
	$home_id = (int) get_option( 'page_on_front' );
	$link    = $home_id ? get_permalink( $home_id ) : '';

	return [
		'done'     => true,
		'home_url' => home_url( '/' ),
		'edit_url' => $link ? add_query_arg( 'et_fb', '1', $link ) : '',
	];
}

/**
 * admin-ajax.php?action=tp_child_seed_step: one step for the bar.
 */
function tp_child_seed_step_ajax(): void {
	check_ajax_referer( 'tp_child_seed_step', 'nonce' );

	if ( ! current_user_can( 'switch_themes' ) ) {
		wp_send_json_error( [ 'message' => 'forbidden' ], 403 );
	}

	$step   = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
	$offset = isset( $_POST['offset'] ) ? absint( wp_unslash( $_POST['offset'] ) ) : 0;
	$force  = ! empty( $_POST['force'] );

	wp_send_json_success( tp_child_seed_step( $step, $offset, $force ) );
}
add_action( 'wp_ajax_tp_child_seed_step', 'tp_child_seed_step_ajax' );

/**
 * The script of the bar, on the admin pages that show the notice.
 */
function tp_child_seed_progress_assets(): void {
	if ( ! current_user_can( 'switch_themes' ) || tp_child_seed_site_is_ready() ) {
		return;
	}

	wp_enqueue_script( 'tp-seed-progress', TP_CHILD_URL . 'assets/js/tp-seed-progress.js', [], tp_child_asset_version( 'assets/js/tp-seed-progress.js' ), true );
}
add_action( 'admin_enqueue_scripts', 'tp_child_seed_progress_assets' );

/**
 * The notice: the button (also without JavaScript), the bar while the home is made, the end, a stop.
 * With $autostart the bar starts at once (a new site): the button is only for a browser without JavaScript.
 */
function tp_child_seed_progress_notice( bool $autostart ): void {
	$bar  = 'max-width:520px;height:12px;margin:12px 0 8px;background:#dcdcde;border-radius:6px;overflow:hidden';
	$fill = 'display:block;width:0;height:100%;background:#2271b1;border-radius:6px;transition:width .4s ease';
	?>
	<div class="notice notice-warning tp-seed" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'tp_child_seed_step' ) ); ?>" data-autostart="<?php echo $autostart ? '1' : '0'; ?>">
		<div class="tp-seed__ask">
			<p><?php esc_html_e( 'Il tema Tasche Piene è attivo, ma la home non è ancora stata creata o aggiornata.', 'tasche-piene-divi-child' ); ?></p>
			<p><a class="button button-primary tp-seed__start" href="<?php echo esc_url( tp_child_seed_site_admin_url() ); ?>"><?php esc_html_e( 'Crea/aggiorna home Tasche Piene', 'tasche-piene-divi-child' ); ?></a></p>
		</div>
		<div class="tp-seed__run" hidden>
			<p><strong><?php esc_html_e( 'Sto creando la home Tasche Piene.', 'tasche-piene-divi-child' ); ?></strong> <?php esc_html_e( 'Ci vuole meno di un minuto: lascia aperta questa pagina.', 'tasche-piene-divi-child' ); ?></p>
			<div class="tp-seed__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-label="<?php esc_attr_e( 'Creazione della home', 'tasche-piene-divi-child' ); ?>" style="<?php echo esc_attr( $bar ); ?>"><span class="tp-seed__fill" style="<?php echo esc_attr( $fill ); ?>"></span></div>
			<p class="tp-seed__status" aria-live="polite"><?php esc_html_e( 'Preparo i menu…', 'tasche-piene-divi-child' ); ?></p>
		</div>
		<div class="tp-seed__done" hidden>
			<p><strong><?php esc_html_e( 'La home Tasche Piene è pronta.', 'tasche-piene-divi-child' ); ?></strong> <?php esc_html_e( 'La trovi in Pagine > Home ed è la pagina iniziale del sito.', 'tasche-piene-divi-child' ); ?></p>
			<p><a class="button button-primary tp-seed__view" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Vedi il sito', 'tasche-piene-divi-child' ); ?></a> <a class="button tp-seed__edit" href="#"><?php esc_html_e( 'Modifica con Divi', 'tasche-piene-divi-child' ); ?></a></p>
		</div>
		<div class="tp-seed__error" hidden>
			<p><strong><?php esc_html_e( 'La creazione della home si è fermata', 'tasche-piene-divi-child' ); ?></strong> <?php esc_html_e( '(la connessione o il server non hanno risposto). Premi Riprova: riparte da dove era arrivata.', 'tasche-piene-divi-child' ); ?></p>
			<p><button type="button" class="button button-primary tp-seed__retry"><?php esc_html_e( 'Riprova', 'tasche-piene-divi-child' ); ?></button></p>
		</div>
	</div>
	<?php
}
