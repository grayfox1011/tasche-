<?php
/**
 * The photograph of the site: Aspetto > Tasche Piene keeps the home, the header, the footer and the two
 * legal pages as they are at one moment, and can bring them back to that moment.
 *
 * The site is finished: one presses "Salva il sito com'è adesso". If later a page is spoiled, "Ripristina
 * il sito salvato" puts the five of them back as they were (also one thrown in the bin or deleted) and
 * one goes on from there. What the site was just before a restore is kept too, to undo it: before a
 * restore to the photograph and before "Ripristina il sito iniziale" (inc/setup-site.php), which brings
 * the five of them back to the theme's starting content.
 *
 * The photograph lives in the site's database, in one option that is not loaded with every page: an
 * update of the theme does not touch it, a copy of the database carries it along. It keeps what is
 * written in the pages and their Divi settings; the pictures stay in the Media Library, and a picture
 * deleted from there is not brought back.
 *
 * Only an administrator saves and restores (manage_options). An editor does not even see the page.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TP_SNAPSHOT_OPTION = 'tp_site_snapshot';
const TP_SNAPSHOT_UNDO   = 'tp_site_snapshot_undo';

function tp_snapshot_can(): bool {
	return current_user_can( 'manage_options' );
}

/**
 * What Divi keeps beside the content of a page and belongs to how the page is: its settings.
 * Not what Divi works out again by itself (the modules a page uses, the fonts it loads).
 *
 * @return array<string, mixed>
 */
function tp_snapshot_meta( int $id ): array {
	$kept = [];

	foreach ( (array) get_post_meta( $id ) as $name => $values ) {
		$name = (string) $name;
		$own  = 0 === strpos( $name, '_et_pb_' ) || in_array( $name, [ '_divi_off_canvas_data', '_wp_page_template', '_thumbnail_id' ], true );

		if ( $own && isset( $values[0] ) ) {
			$kept[ $name ] = maybe_unserialize( $values[0] );
		}
	}

	return $kept;
}

/**
 * The five pieces as they are now.
 *
 * @return array|WP_Error
 */
function tp_snapshot_take() {
	$state = tp_setup_state();
	$items = [];

	foreach ( tp_setup_items() as $key => $item ) {
		$post = get_post( (int) ( $state['posts'][ $key ] ?? 0 ) );

		if ( ! $post instanceof WP_Post || $post->post_type !== $item['post_type'] || 'trash' === $post->post_status ) {
			continue; // not there now: the photograph has no such piece, and a restore leaves it alone
		}

		$items[ $key ] = [
			'title'   => $post->post_title,
			'slug'    => $post->post_name,
			'status'  => $post->post_status,
			'content' => $post->post_content,
			'meta'    => tp_snapshot_meta( (int) $post->ID ),
		];
	}

	if ( empty( $items['home']['content'] ) ) {
		return new WP_Error( 'no_site', __( 'Il sito non è ancora stato creato, o la home è nel cestino: non c’è niente da salvare.', 'tasche-piene' ) );
	}

	$user = wp_get_current_user();

	return [
		'format' => 1,
		'theme'  => TP_VERSION,
		'time'   => time(),
		'user'   => $user instanceof WP_User && $user->exists() ? $user->display_name : '',
		'items'  => $items,
	];
}

/**
 * A photograph that was saved, or [] when there is none.
 */
function tp_snapshot_get( string $option = TP_SNAPSHOT_OPTION ): array {
	$snapshot = get_option( $option );

	return is_array( $snapshot ) && ! empty( $snapshot['items']['home']['content'] ) ? $snapshot : [];
}

/**
 * Keeps the site as it is now in `$option`.
 *
 * @return array|WP_Error The photograph.
 */
function tp_snapshot_save( string $option = TP_SNAPSHOT_OPTION ) {
	$snapshot = tp_snapshot_take();

	if ( is_wp_error( $snapshot ) ) {
		return $snapshot;
	}

	update_option( $option, $snapshot, false );
	wp_cache_delete( $option, 'options' );
	$kept = tp_snapshot_get( $option );

	// Read again: a database that cannot hold it must not look like a photograph taken.
	if ( ! $kept || (int) $kept['time'] !== (int) $snapshot['time'] || $kept['items']['home']['content'] !== $snapshot['items']['home']['content'] ) {
		return new WP_Error( 'not_kept', __( 'Il database non ha tenuto la fotografia.', 'tasche-piene' ) );
	}

	return $snapshot;
}

/**
 * Puts the pieces of a photograph back in the site. A page in the bin comes out of it, one deleted for
 * good is made again; the Theme Builder shows header and footer again and the home is the front page.
 *
 * @return array<string, int>|WP_Error The pages by name.
 */
function tp_snapshot_apply( array $snapshot ) {
	$state = tp_setup_state();
	$posts = isset( $state['posts'] ) && is_array( $state['posts'] ) ? array_map( 'intval', $state['posts'] ) : [];

	foreach ( tp_setup_items() as $key => $item ) {
		$saved = $snapshot['items'][ $key ] ?? null;

		if ( ! is_array( $saved ) || ! isset( $saved['content'] ) ) {
			continue;
		}

		$id   = (int) ( $posts[ $key ] ?? 0 );
		$post = $id ? get_post( $id ) : null;

		if ( ! $post instanceof WP_Post || $post->post_type !== $item['post_type'] ) {
			$id = 0; // deleted for good: it is made again
		} elseif ( 'trash' === $post->post_status ) {
			wp_untrash_post( $id );
		}

		$id = tp_setup_write_post(
			$id,
			[
				'post_title'   => (string) ( $saved['title'] ?? $item['title'] ),
				'post_name'    => (string) ( $saved['slug'] ?? $item['slug'] ),
				'post_type'    => $item['post_type'],
				'post_status'  => ! empty( $saved['status'] ) ? (string) $saved['status'] : 'publish',
				'post_content' => wp_slash( (string) $saved['content'] ),
			]
		);

		if ( ! $id ) {
			return new WP_Error( 'not_written', sprintf( /* translators: %s: the name of a page. */ __( 'Non sono riuscito a riscrivere «%s».', 'tasche-piene' ), $item['title'] ) );
		}

		$meta = isset( $saved['meta'] ) && is_array( $saved['meta'] ) ? $saved['meta'] : [];

		// The settings of the page as they were: what was added since goes, what was there comes back.
		foreach ( array_keys( tp_snapshot_meta( $id ) ) as $name ) {
			if ( ! array_key_exists( $name, $meta ) ) {
				delete_post_meta( $id, $name );
			}
		}

		foreach ( $meta as $name => $value ) {
			update_post_meta( $id, (string) $name, is_string( $value ) ? wp_slash( $value ) : $value );
		}

		update_post_meta( $id, '_tp_content', '1' );
		tp_setup_forget_divi_cache( $id );
		clean_post_cache( $id );
		$posts[ $key ] = $id;
	}

	tp_setup_theme_builder( (int) ( $posts['header'] ?? 0 ), (int) ( $posts['footer'] ?? 0 ) );

	if ( ! empty( $posts['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $posts['home'] );
	}

	update_option( TP_SETUP_OPTION, [ 'version' => (string) ( $state['version'] ?? TP_VERSION ), 'posts' => array_filter( $posts ) ], false );
	tp_setup_clear_divi_css();

	return $posts;
}

/**
 * Keeps the site as it is now, just before a restore (to the photograph or to the starting site), so
 * that the restore can be undone. A site that cannot be kept (its home is in the bin) leaves nothing to
 * undo: what was kept before an earlier restore goes too, or "Annulla" would bring back that one.
 */
function tp_snapshot_keep_for_undo(): bool {
	if ( is_wp_error( tp_snapshot_save( TP_SNAPSHOT_UNDO ) ) ) {
		delete_option( TP_SNAPSHOT_UNDO );

		return false;
	}

	return true;
}

/**
 * Brings the site back to the photograph. How the site was just before is kept, to undo the restore.
 *
 * @return array<string, int>|WP_Error
 */
function tp_snapshot_restore() {
	$snapshot = tp_snapshot_get();

	if ( ! $snapshot ) {
		return new WP_Error( 'no_photo', __( 'Non c’è nessuna fotografia salvata.', 'tasche-piene' ) );
	}

	tp_snapshot_keep_for_undo();

	return tp_snapshot_apply( $snapshot );
}

/**
 * Undoes the last restore: the site as it was just before it.
 *
 * @return array<string, int>|WP_Error
 */
function tp_snapshot_undo() {
	$before = tp_snapshot_get( TP_SNAPSHOT_UNDO );

	if ( ! $before ) {
		return new WP_Error( 'no_undo', __( 'Non c’è nessun ripristino da annullare.', 'tasche-piene' ) );
	}

	$result = tp_snapshot_apply( $before );

	if ( ! is_wp_error( $result ) ) {
		delete_option( TP_SNAPSHOT_UNDO );
	}

	return $result;
}

/**
 * The three buttons of the card. Only an administrator, only from the card (nonce).
 */
function tp_snapshot_handle(): void {
	if ( ! tp_snapshot_can() ) {
		wp_die( esc_html__( 'Solo un amministratore può salvare o ripristinare il sito.', 'tasche-piene' ), '', [ 'response' => 403 ] );
	}

	check_admin_referer( 'tp_snapshot' );

	$what = isset( $_POST['tp_snapshot'] ) ? sanitize_key( wp_unslash( $_POST['tp_snapshot'] ) ) : '';

	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	if ( 'save' === $what ) {
		$result = tp_snapshot_save();
		$done   = 'saved';
	} elseif ( 'restore' === $what ) {
		$result = tp_snapshot_restore();
		$done   = 'restored';
	} elseif ( 'undo' === $what ) {
		$result = tp_snapshot_undo();
		$done   = 'undone';
	} else {
		$result = new WP_Error( 'unknown', __( 'Richiesta non riconosciuta.', 'tasche-piene' ) );
		$done   = '';
	}

	$args = is_wp_error( $result ) ? [ 'tp_photo' => 'error', 'tp_why' => $result->get_error_code() ] : [ 'tp_photo' => $done ];

	wp_safe_redirect( add_query_arg( $args, tp_setup_page_url() ) );
	exit;
}
add_action( 'admin_post_tp_snapshot', 'tp_snapshot_handle' );

/**
 * "12/11/2026 alle 15:04", in the site's time zone.
 */
function tp_snapshot_when( array $snapshot ): string {
	return wp_date( 'd/m/Y \a\l\l\e H:i', (int) ( $snapshot['time'] ?? 0 ) );
}

/**
 * What the last button did, said at the top of the page.
 */
function tp_snapshot_notice(): void {
	$what = isset( $_GET['tp_photo'] ) ? sanitize_key( wp_unslash( $_GET['tp_photo'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! $what || ! tp_snapshot_can() ) {
		return;
	}

	$why      = isset( $_GET['tp_why'] ) ? sanitize_key( wp_unslash( $_GET['tp_why'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$messages = [
		'saved'    => __( 'Sito salvato: questa è ora la fotografia a cui «Ripristina il sito salvato» riporta.', 'tasche-piene' ),
		'restored' => __( 'Sito ripristinato alla fotografia salvata.', 'tasche-piene' ) . ( tp_snapshot_get( TP_SNAPSHOT_UNDO ) ? ' ' . __( 'Se non era quello che volevi, «Annulla il ripristino» lo riporta a com’era un momento fa.', 'tasche-piene' ) : '' ),
		'undone'   => __( 'Ripristino annullato: il sito è tornato com’era prima del ripristino.', 'tasche-piene' ),
	];
	$errors   = [
		'no_site'     => __( 'Il sito non è ancora stato creato, o la home è nel cestino: non c’è niente da salvare.', 'tasche-piene' ),
		'no_photo'    => __( 'Non c’è nessuna fotografia salvata.', 'tasche-piene' ),
		'no_undo'     => __( 'Non c’è nessun ripristino da annullare.', 'tasche-piene' ),
		'not_kept'    => __( 'Il database non ha tenuto la fotografia: il sito non è stato salvato.', 'tasche-piene' ),
		'not_written' => __( 'Una pagina non si è lasciata riscrivere: il ripristino si è fermato. Riprova.', 'tasche-piene' ),
	];

	if ( isset( $messages[ $what ] ) ) {
		printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $messages[ $what ] ) );
	} elseif ( 'error' === $what ) {
		printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $errors[ $why ] ?? __( 'Non è andata a buon fine.', 'tasche-piene' ) ) );
	}
}

/**
 * The card in Aspetto > Tasche Piene. `$shown`: the site is created, so the card is in sight.
 */
function tp_snapshot_card( bool $shown ): void {
	if ( ! tp_snapshot_can() ) {
		return;
	}

	$snapshot = tp_snapshot_get();
	$before   = tp_snapshot_get( TP_SNAPSHOT_UNDO );
	$names    = [ 'home' => 'home', 'header' => 'testata', 'footer' => 'piè di pagina', 'privacy' => 'Privacy Policy', 'cookie' => 'Cookie Policy' ];
	$inside   = $snapshot ? implode( ', ', array_values( array_intersect_key( $names, $snapshot['items'] ) ) ) : '';
	?>
	<div class="tp-setup__photo card" <?php echo $shown ? '' : 'hidden'; ?>>
		<h2><?php esc_html_e( 'Fotografia del sito', 'tasche-piene' ); ?></h2>
		<p><?php esc_html_e( 'Quando il sito è come deve essere, salvalo: home, testata, piè di pagina, Privacy Policy e Cookie Policy vengono tenute così come sono adesso. Se poi qualcosa viene rovinato, «Ripristina il sito salvato» le riporta a quel momento, anche una pagina finita nel cestino, e da lì si riparte. Lo può fare solo un amministratore.', 'tasche-piene' ); ?></p>
		<?php if ( $snapshot ) : ?>
			<p><strong>
				<?php
				/* translators: 1: date and time, 2: a person's name. */
				echo esc_html( sprintf( __( 'Fotografia salvata il %1$s%2$s.', 'tasche-piene' ), tp_snapshot_when( $snapshot ), ! empty( $snapshot['user'] ) ? ' ' . sprintf( /* translators: %s: a person's name. */ __( 'da %s', 'tasche-piene' ), $snapshot['user'] ) : '' ) );
				?>
			</strong> <?php echo esc_html( sprintf( /* translators: %s: a list of pages. */ __( 'Dentro ci sono: %s.', 'tasche-piene' ), $inside ) ); ?></p>
		<?php else : ?>
			<p><strong><?php esc_html_e( 'Nessuna fotografia salvata.', 'tasche-piene' ); ?></strong></p>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="tp_snapshot">
			<?php wp_nonce_field( 'tp_snapshot' ); ?>
			<p>
				<button type="submit" name="tp_snapshot" value="save" class="button button-primary tp-setup__save"
					<?php if ( $snapshot ) : ?>
						data-confirm="<?php echo esc_attr( sprintf( /* translators: %s: date and time. */ __( 'Salvare il sito com’è adesso? Prende il posto della fotografia del %s.', 'tasche-piene' ), tp_snapshot_when( $snapshot ) ) ); ?>"
					<?php endif; ?>
				><?php esc_html_e( 'Salva il sito com’è adesso', 'tasche-piene' ); ?></button>
				<?php if ( $snapshot ) : ?>
					<button type="submit" name="tp_snapshot" value="restore" class="button tp-setup__restore"
						data-confirm="<?php echo esc_attr( sprintf( /* translators: %s: date and time. */ __( 'Ripristinare il sito alla fotografia del %s? Home, testata, piè di pagina e pagine legali tornano com’erano allora: quello che è stato cambiato dopo va perso.', 'tasche-piene' ), tp_snapshot_when( $snapshot ) ) ); ?>"
					><?php esc_html_e( 'Ripristina il sito salvato', 'tasche-piene' ); ?></button>
				<?php endif; ?>
			</p>
			<?php if ( $before ) : ?>
				<p>
					<button type="submit" name="tp_snapshot" value="undo" class="button-link tp-setup__undo"
						data-confirm="<?php echo esc_attr( sprintf( /* translators: %s: date and time. */ __( 'Annullare l’ultimo ripristino? Il sito torna com’era il %s, subito prima del ripristino.', 'tasche-piene' ), tp_snapshot_when( $before ) ) ); ?>"
					><?php echo esc_html( sprintf( /* translators: %s: date and time. */ __( 'Annulla il ripristino del %s', 'tasche-piene' ), tp_snapshot_when( $before ) ) ); ?></button>
				</p>
			<?php endif; ?>
		</form>
		<p class="description"><?php esc_html_e( 'La fotografia tiene i testi, la disposizione e le impostazioni delle pagine; le foto restano nella libreria Media (una foto eliminata dalla libreria non torna). Sta nel database del sito: un aggiornamento del tema non la tocca.', 'tasche-piene' ); ?></p>
	</div>
	<?php
}
