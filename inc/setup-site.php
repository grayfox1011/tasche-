<?php
/**
 * Creation of the site: the home, the header and the footer (Theme Builder), the two legal pages.
 *
 * The pages are in content/*.html as Divi 5 blocks, made of Divi's own modules only (dev/build.py
 * wrote them). Creating the site copies them into WordPress, puts the pictures and the video of
 * assets/ in the Media Library and writes their addresses in the pages. From then on everything
 * lives in WordPress and is changed from Divi: the theme does not touch the pages again, unless
 * one asks for it in Aspetto > Tasche Piene.
 *
 * A site that had the theme before version 1.0.0 keeps its old home as a draft: its modules were
 * the theme's own and are gone, the new home takes its place.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TP_SETUP_OPTION = 'tp_site';
const TP_SETUP_LOCK   = 'tp_site_lock';
const TP_MEDIA_BATCH  = 3;
// The key the theme has always used for the files it put in the Media Library: a site that
// already has them does not get them twice.
const TP_MEDIA_META = '_tp_child_seed_asset_path';

/**
 * What the theme creates. `content`: the file in content/.
 *
 * @return array<string, array<string, string>>
 */
function tp_setup_items(): array {
	return [
		'privacy' => [ 'content' => 'privacy', 'post_type' => 'page', 'slug' => 'privacy-policy', 'title' => 'Privacy Policy' ],
		'cookie'  => [ 'content' => 'cookie', 'post_type' => 'page', 'slug' => 'cookie-policy', 'title' => 'Cookie Policy' ],
		'home'    => [ 'content' => 'home', 'post_type' => 'page', 'slug' => 'home', 'title' => 'Home' ],
		'header'  => [ 'content' => 'header', 'post_type' => 'et_header_layout', 'slug' => 'tasche-piene-header', 'title' => 'Tasche Piene: testata' ],
		'footer'  => [ 'content' => 'footer', 'post_type' => 'et_footer_layout', 'slug' => 'tasche-piene-footer', 'title' => 'Tasche Piene: piè di pagina' ],
	];
}

function tp_setup_raw_content( string $name ): string {
	// The filter lets a test site take the pages from another folder (dev/build.py with TP_OUT).
	$file = trailingslashit( (string) apply_filters( 'tp_setup_content_dir', TP_PATH . 'content' ) ) . $name . '.html';

	return is_readable( $file ) ? (string) file_get_contents( $file ) : '';
}

/**
 * What was created: [ 'version' => '1.0.0', 'posts' => [ 'home' => 12, ... ] ], or [] for a site not yet created.
 */
function tp_setup_state(): array {
	$state = get_option( TP_SETUP_OPTION );

	return is_array( $state ) ? $state : [];
}

function tp_setup_is_done(): bool {
	$state = tp_setup_state();

	return ! empty( $state['posts']['home'] ) && 'page' === get_post_type( (int) $state['posts']['home'] );
}

/* Media ------------------------------------------------------------------------------------------ */

/**
 * The files of assets/ the pages show ({{media:images/x.webp}}), each once.
 *
 * @return string[] Paths under assets/.
 */
function tp_setup_media_paths(): array {
	static $paths = null;

	if ( null !== $paths ) {
		return $paths;
	}

	$paths = [];

	foreach ( tp_setup_items() as $item ) {
		// {{media:...}} is a file's address in the library, {{id:...}} its number there (a gallery keeps numbers).
		if ( preg_match_all( '/\{\{(?:media|id):([a-z0-9_.\/-]+)\}\}/i', tp_setup_raw_content( $item['content'] ), $matches ) ) {
			$paths = array_merge( $paths, $matches[1] );
		}
	}

	$paths = array_values( array_unique( $paths ) );
	sort( $paths );

	return $paths;
}

function tp_setup_attachment_for( string $path ): int {
	$found = get_posts(
		[
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => TP_MEDIA_META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => 'assets/' . $path, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			'no_found_rows'  => true,
		]
	);

	if ( $found ) {
		return (int) $found[0];
	}

	return tp_setup_same_file_in_library( $path );
}

/**
 * A file somebody already uploaded by hand: same name and same content as the theme's. It is taken as it
 * is, where it is, instead of loading a second copy (the photos added to the site before this version).
 */
function tp_setup_same_file_in_library( string $path ): int {
	$file = TP_PATH . 'assets/' . $path;
	$name = wp_basename( $path );

	if ( ! is_readable( $file ) ) {
		return 0;
	}

	$candidates = get_posts(
		[
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 20,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				[
					'key'     => '_wp_attached_file',
					'value'   => $name,
					'compare' => 'LIKE',
				],
			],
		]
	);

	foreach ( $candidates as $id ) {
		$attached = get_attached_file( (int) $id );

		if ( $attached && wp_basename( $attached ) === $name && is_readable( $attached )
			&& filesize( $attached ) === filesize( $file ) && md5_file( $attached ) === md5_file( $file ) ) {
			update_post_meta( (int) $id, TP_MEDIA_META, 'assets/' . $path );

			return (int) $id;
		}
	}

	return 0;
}

/**
 * A file of assets/ in the Media Library: its address there, or the theme's own file when the
 * library cannot take it (no room, folder not writable).
 */
function tp_setup_media_url( string $path, bool $create = true ): string {
	static $urls = [];

	if ( isset( $urls[ $path ] ) ) {
		return $urls[ $path ];
	}

	$fallback = TP_URL . 'assets/' . $path;
	$root     = realpath( TP_PATH . 'assets' );
	$source   = realpath( TP_PATH . 'assets/' . $path );

	if ( ! $root || ! $source || 0 !== strpos( $source, $root . DIRECTORY_SEPARATOR ) || ! is_readable( $source ) ) {
		return $fallback;
	}

	$existing = tp_setup_attachment_for( $path );
	if ( $existing ) {
		$url = wp_get_attachment_url( $existing );
		if ( $url ) {
			return $urls[ $path ] = esc_url_raw( $url );
		}
	}

	if ( ! $create ) {
		return $fallback;
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$uploads = wp_upload_dir();
	if ( ! empty( $uploads['error'] ) || empty( $uploads['path'] ) || ! wp_mkdir_p( $uploads['path'] ) ) {
		return $fallback;
	}

	$filename = wp_unique_filename( $uploads['path'], basename( $source ) );
	$target   = trailingslashit( $uploads['path'] ) . $filename;
	$types    = [
		'webp' => 'image/webp',
		'png'  => 'image/png',
		'jpg'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'svg'  => 'image/svg+xml',
		'mp4'  => 'video/mp4',
		'webm' => 'video/webm',
	];
	$type     = $types[ strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ] ?? '';

	if ( '' === $type || ! copy( $source, $target ) ) {
		return $fallback;
	}

	$title = ucfirst( trim( str_replace( [ '-', '_' ], ' ', pathinfo( $filename, PATHINFO_FILENAME ) ) ) );
	$id    = wp_insert_attachment(
		[
			'post_mime_type' => $type,
			'post_title'     => $title,
			'post_content'   => '',
			'post_status'    => 'inherit',
		],
		$target
	);

	if ( is_wp_error( $id ) || ! $id ) {
		wp_delete_file( $target );

		return $fallback;
	}

	update_post_meta( (int) $id, TP_MEDIA_META, 'assets/' . $path );

	if ( 'image/svg+xml' !== $type ) {
		$metadata = wp_generate_attachment_metadata( (int) $id, $target );
		if ( is_array( $metadata ) && $metadata ) {
			wp_update_attachment_metadata( (int) $id, $metadata );
		}
	}

	$url = wp_get_attachment_url( (int) $id );

	return $urls[ $path ] = $url ? esc_url_raw( $url ) : $fallback;
}

/* Pages ------------------------------------------------------------------------------------------ */

/**
 * A text as it stands inside the JSON of a block (what WordPress does in serialize_block_attributes).
 */
function tp_setup_json_text( string $text ): string {
	$json = substr( (string) wp_json_encode( $text, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), 1, -1 );

	return str_replace( [ '--', '<', '>', '&', '\\"' ], [ '\\u002d\\u002d', '\\u003c', '\\u003e', '\\u0026', '\\u0022' ], $json );
}

/**
 * A page of content/ with its addresses written in: {{media:x}}, {{url:home}}, {{url:privacy}}, {{url:cookie}}, {{date}}.
 *
 * @param array<string, int> $posts The pages created so far.
 */
function tp_setup_content( string $name, array $posts ): string {
	$content = tp_setup_raw_content( $name );
	$urls    = [
		'{{url:home}}'    => home_url( '/' ),
		'{{url:privacy}}' => ! empty( $posts['privacy'] ) ? (string) get_permalink( $posts['privacy'] ) : home_url( '/privacy-policy/' ),
		'{{url:cookie}}'  => ! empty( $posts['cookie'] ) ? (string) get_permalink( $posts['cookie'] ) : home_url( '/cookie-policy/' ),
		'{{date}}'        => date_i18n( 'd/m/Y' ),
	];

	foreach ( $urls as $mark => $value ) {
		$content = str_replace( $mark, tp_setup_json_text( $value ), $content );
	}

	$content = (string) preg_replace_callback(
		'/\{\{media:([a-z0-9_.\/-]+)\}\}/i',
		static fn( array $match ): string => tp_setup_json_text( tp_setup_media_url( $match[1] ) ),
		$content
	);

	// The number of the file in the library (0 when the library could not take it).
	return (string) preg_replace_callback(
		'/\{\{id:([a-z0-9_.\/-]+)\}\}/i',
		static function ( array $match ): string {
			tp_setup_media_url( $match[1] );

			return (string) tp_setup_attachment_for( $match[1] );
		},
		$content
	);
}

/**
 * The page the theme created for `$key`, or the one that has its address and may be taken over:
 * an old page of the theme (before 1.0.0) or WordPress's own sample page, never one written by hand.
 */
function tp_setup_find_post( string $key, array $item ): int {
	$state = tp_setup_state();
	$id    = (int) ( $state['posts'][ $key ] ?? 0 );

	if ( $id && get_post_type( $id ) === $item['post_type'] && 'trash' !== get_post_status( $id ) ) {
		return $id;
	}

	$post = get_page_by_path( $item['slug'], OBJECT, $item['post_type'] );
	if ( ! $post instanceof WP_Post ) {
		return 0;
	}

	if ( 'page' !== $item['post_type'] ) {
		return (int) $post->ID; // header and footer of the Theme Builder are the theme's own
	}

	if ( '1' === get_post_meta( $post->ID, '_tp_content', true ) ) {
		return (int) $post->ID; // created by this theme, the note of it was lost
	}

	if ( 'home' === $key ) {
		return 0; // the old home is put aside, not written over (tp_setup_put_aside_old_home)
	}

	$old_theme = '1' === get_post_meta( $post->ID, '_tp_child_seed_managed', true ) || false !== strpos( $post->post_content, 'tp-legal' );
	$sample    = 'draft' === $post->post_status;

	return $old_theme || $sample ? (int) $post->ID : -1; // -1: someone's own page, left as it is
}

/**
 * The home of the theme before 1.0.0 (its modules were the theme's own, and are gone), and any other
 * page at /home/: kept as drafts, so the new home takes the address and the front of the site.
 */
function tp_setup_put_aside_old_home(): void {
	$old = [];

	$at_home = get_page_by_path( 'home', OBJECT, 'page' );
	if ( $at_home instanceof WP_Post ) {
		$old[] = $at_home;
	}

	$front = get_post( (int) get_option( 'page_on_front' ) );
	if ( $front instanceof WP_Post && 'page' === $front->post_type && '1' === get_post_meta( $front->ID, '_tp_child_seed_managed', true ) ) {
		$old[] = $front;
	}

	foreach ( $old as $post ) {
		if ( '1' === get_post_meta( $post->ID, '_tp_content', true ) || 'draft' === get_post_status( $post->ID ) ) {
			continue;
		}

		wp_update_post(
			[
				'ID'          => $post->ID,
				'post_name'   => 'home-versione-precedente',
				'post_title'  => $post->post_title . ' (versione precedente)',
				'post_status' => 'draft',
			]
		);
	}
}

function tp_setup_save_post( string $key, array $item, array $posts ): int {
	$found = tp_setup_find_post( $key, $item );

	if ( -1 === $found ) {
		$post = get_page_by_path( $item['slug'], OBJECT, $item['post_type'] );

		return $post instanceof WP_Post ? (int) $post->ID : 0;
	}

	if ( ! $found && 'home' === $key ) {
		tp_setup_put_aside_old_home();
	}

	$id = tp_setup_write_post(
		$found,
		[
			'post_title'   => $item['title'],
			'post_name'    => $item['slug'],
			'post_type'    => $item['post_type'],
			'post_status'  => 'publish',
			'post_content' => wp_slash( tp_setup_content( $item['content'], $posts ) ),
		]
	);

	if ( ! $id ) {
		return 0;
	}

	$meta = [
		'_tp_content'                => '1',
		'_et_pb_use_builder'         => 'on',
		'_et_pb_use_divi_5'          => 'on',
		'_et_pb_show_page_creation'  => 'off',
		'_et_pb_post_settings_title' => $item['title'],
	];

	if ( 'page' === $item['post_type'] ) {
		$meta['_et_pb_built_for_post_type'] = 'page';
		$meta['_et_pb_page_layout']         = 'et_no_sidebar';
	}

	foreach ( $meta as $name => $value ) {
		update_post_meta( $id, $name, $value );
	}

	tp_setup_forget_divi_cache( $id );
	clean_post_cache( $id );

	return $id;
}

/**
 * Writes a page as it is given: over the page `$found`, or as a new one when `$found` is 0.
 *
 * The content is the theme's own (its files, or a photograph of the site taken by the theme). WordPress
 * takes out of a page what not every user may write (an <iframe>, as the map is) when who saves has not
 * the right to write any HTML: on a network of sites, or where the host has switched that right off.
 * Here the content is saved whole.
 *
 * @param array<string, string> $args What wp_insert_post takes; post_content already slashed.
 * @return int The page, or 0.
 */
function tp_setup_write_post( int $found, array $args ): int {
	$filtered = false !== has_filter( 'content_save_pre', 'wp_filter_post_kses' );
	if ( $filtered ) {
		kses_remove_filters();
	}

	if ( $found ) {
		$args['ID'] = $found;
		$result     = wp_update_post( $args, true );
	} else {
		$args['post_author'] = get_current_user_id() ?: 1;
		$result              = wp_insert_post( $args, true );
	}

	if ( $filtered ) {
		kses_init_filters();
	}

	return is_wp_error( $result ) || ! $result ? 0 : (int) $result;
}

/**
 * What Divi worked out for a page as it was before it was written again.
 */
function tp_setup_forget_divi_cache( int $id ): void {
	foreach ( [ '_divi_dynamic_assets_cached_modules', '_divi_dynamic_assets_cached_feature_used', '_divi_dynamic_assets_canvases_used', '_et_builder_post_features_cache', '_et_builder_dynamic_assets_loading_attr_threshold', '_tp_child_seed_managed' ] as $name ) {
		delete_post_meta( $id, $name );
	}
}

/**
 * The default template of the Theme Builder shows the theme's header and footer on every page.
 * Other templates one has made stay as they are.
 */
function tp_setup_theme_builder( int $header_id, int $footer_id ): void {
	if ( ! $header_id || ! $footer_id ) {
		return;
	}

	$find = static function ( string $post_type, string $slug, string $title ): int {
		$post = get_page_by_path( $slug, OBJECT, $post_type );
		if ( $post instanceof WP_Post ) {
			return (int) $post->ID;
		}

		$id = wp_insert_post(
			[
				'post_type'   => $post_type,
				'post_name'   => $slug,
				'post_title'  => $title,
				'post_status' => 'publish',
				'post_author' => get_current_user_id() ?: 1,
			],
			true
		);

		return is_wp_error( $id ) ? 0 : (int) $id;
	};

	$builder_id  = 0;
	$builders    = get_posts( [ 'post_type' => 'et_theme_builder', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'no_found_rows' => true ] );
	$builder_id  = $builders ? (int) $builders[0] : $find( 'et_theme_builder', 'theme-builder', 'Theme Builder' );
	$template_id = $find( 'et_template', 'tasche-piene-default-template', 'Tasche Piene: tutto il sito' );

	if ( ! $builder_id || ! $template_id ) {
		return;
	}

	$meta = [
		'_et_autogenerated_title'   => '0',
		'_et_default'               => '1',
		'_et_enabled'               => '1',
		'_et_header_layout_id'      => (string) $header_id,
		'_et_header_layout_enabled' => '1',
		'_et_header_layout_global'  => '1',
		'_et_body_layout_id'        => '0',
		'_et_body_layout_enabled'   => '1',
		'_et_footer_layout_id'      => (string) $footer_id,
		'_et_footer_layout_enabled' => '1',
		'_et_footer_layout_global'  => '1',
	];

	foreach ( $meta as $name => $value ) {
		update_post_meta( $template_id, $name, $value );
	}

	// Divi keeps one _et_template row per template; only another default template gives way.
	$linked = array_map( 'intval', (array) get_post_meta( $builder_id, '_et_template', false ) );

	foreach ( array_unique( $linked ) as $linked_id ) {
		if ( $linked_id !== $template_id && '1' === get_post_meta( $linked_id, '_et_default', true ) ) {
			delete_post_meta( $builder_id, '_et_template', (string) $linked_id );
		}
	}

	if ( ! in_array( $template_id, $linked, true ) ) {
		add_post_meta( $builder_id, '_et_template', (string) $template_id );
	}

	clean_post_cache( $template_id );
	clean_post_cache( $builder_id );
}

/**
 * Divi's own starting choices (Theme Customizer), so a module added later looks at home: the two
 * fonts, the colours of text, titles and links, the palette of the colour pickers. Only where
 * nobody chose yet.
 */
function tp_setup_divi_options(): void {
	if ( ! function_exists( 'et_get_option' ) || ! function_exists( 'et_update_option' ) ) {
		return;
	}

	$choices = [
		'body_font'          => 'DM Sans',
		'heading_font'       => 'Bowlby One',
		'body_font_size'     => '18',
		'font_color'         => '#3e372e',
		'header_color'       => '#560b22',
		'link_color'         => '#560b22',
		'accent_color'       => '#054726',
		'divi_color_palette' => '#fff5e9|#f8ebdb|#054726|#560b22|#3e372e|#c5b197|#ffffff|#000000',
	];

	// In the Visual Builder a module switched off for a device (Advanced > Visibility) is not drawn in
	// that device's view, as on the site: the pieces made only for phones do not crowd the computer
	// view. Builder settings > "Nascondi moduli disabilitati" turns it back.
	$choices['et_fb_pref_hide_disabled_modules'] = 'true';

	// With the mouse over a piece Divi draws its tools on top of it (a bar with settings, duplicate and
	// delete, and a "+"), and those of every box around it. On the small pieces of this site, a price,
	// a day, a voice of the menu, the tools cover the text: one cannot click it to write, and a click
	// meant for the text can land on "delete". So the tools on hover start switched off: a double click
	// writes in a text, a click opens the settings, the right mouse button duplicates and deletes.
	// They come back from the builder's side bar: "Icone azione al passaggio del mouse".
	$choices['et_fb_pref_app.interaction_layers'] = [
		'actionOnHover'       => false,
		'parentActionOnHover' => false,
		'xRay'                => false,
	];

	foreach ( $choices as $name => $value ) {
		$current = et_get_option( $name, '' );
		if ( '' === $current || false === $current || null === $current ) {
			et_update_option( $name, $value );
		}
	}
}

/**
 * Divi keeps the CSS of each page in files: the pages have just changed, the files are made again.
 */
function tp_setup_clear_divi_css(): void {
	if ( class_exists( 'ET_Core_PageResource' ) && ET_Core_PageResource::can_write_to_filesystem() ) {
		ET_Core_PageResource::do_remove_static_resources( 'all', 'all' );
	}
}

/**
 * The pages, the Theme Builder and the front page. The pictures are already in the library
 * (tp_setup_step) or are put there now, one after the other.
 *
 * @return array<string, int> The pages by name.
 */
function tp_setup_finish(): array {
	$posts = [];

	foreach ( tp_setup_items() as $key => $item ) {
		$posts[ $key ] = tp_setup_save_post( $key, $item, $posts );
	}

	tp_setup_theme_builder( $posts['header'] ?? 0, $posts['footer'] ?? 0 );

	if ( ! empty( $posts['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $posts['home'] );
	}

	if ( ! get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		flush_rewrite_rules( false );
	}

	tp_setup_divi_options();

	// The colour behind the pages (Customizer > Background), where a page leaves it in sight.
	if ( ! get_theme_mod( 'background_color' ) ) {
		set_theme_mod( 'background_color', 'fff5e9' );
	}

	update_option( TP_SETUP_OPTION, [ 'version' => TP_VERSION, 'posts' => array_filter( $posts ) ], false );
	delete_option( 'tp_setup_pending' );
	tp_setup_clear_divi_css();

	return $posts;
}

/* Steps: the browser asks for one after the other and shows how far the work is ----------------- */

function tp_setup_lock(): bool {
	$now = time();

	if ( add_option( TP_SETUP_LOCK, (string) $now, '', false ) ) {
		return true;
	}

	// A request that died keeps the lock: after two minutes it is free again.
	if ( $now - (int) get_option( TP_SETUP_LOCK ) > 120 ) {
		update_option( TP_SETUP_LOCK, (string) $now, false );

		return true;
	}

	return false;
}

/**
 * One step: 'start', 'media' (a few files from `$offset`), 'finish'.
 *
 * @return array<string, mixed> What to ask next, or that the site is done.
 */
function tp_setup_step( string $step, int $offset ): array {
	if ( ! tp_setup_lock() ) {
		return [ 'busy' => true ];
	}

	try {
		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 180 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}

		ignore_user_abort( true );

		$paths = tp_setup_media_paths();
		$total = count( $paths );

		if ( 'start' === $step ) {
			// The site is there and is about to go back to its starting content: how it is now is kept,
			// so that "Annulla il ripristino" (inc/site-snapshot.php) can bring it back.
			if ( tp_setup_is_done() && tp_snapshot_can() ) {
				tp_snapshot_keep_for_undo();
			}

			return [ 'next' => 'media', 'offset' => 0, 'total' => $total ];
		}

		if ( 'media' === $step ) {
			$offset = min( max( 0, $offset ), $total );

			foreach ( array_slice( $paths, $offset, TP_MEDIA_BATCH ) as $path ) {
				tp_setup_media_url( $path );
			}

			$offset = min( $total, $offset + TP_MEDIA_BATCH );

			return [ 'next' => $offset < $total ? 'media' : 'finish', 'offset' => $offset, 'total' => $total ];
		}

		if ( 'finish' === $step ) {
			$posts = tp_setup_finish();

			return [ 'done' => ! empty( $posts['home'] ) ];
		}

		return [ 'error' => 'step' ];
	} finally {
		delete_option( TP_SETUP_LOCK );
	}
}

function tp_setup_step_ajax(): void {
	check_ajax_referer( 'tp_setup', 'nonce' );

	if ( ! current_user_can( 'switch_themes' ) ) {
		wp_send_json_error( [ 'message' => 'forbidden' ], 403 );
	}

	$step   = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
	$offset = isset( $_POST['offset'] ) ? absint( wp_unslash( $_POST['offset'] ) ) : 0;

	wp_send_json_success( tp_setup_step( $step, $offset ) );
}
add_action( 'wp_ajax_tp_setup', 'tp_setup_step_ajax' );

/* Admin: Aspetto > Tasche Piene ------------------------------------------------------------------ */

function tp_setup_page_url(): string {
	return admin_url( 'themes.php?page=tasche-piene' );
}

function tp_setup_menu(): void {
	add_theme_page( 'Tasche Piene', 'Tasche Piene', 'switch_themes', 'tasche-piene', 'tp_setup_page' );
}
add_action( 'admin_menu', 'tp_setup_menu' );

/**
 * The theme has just been switched on, or has just been updated from a version before 1.0.0: the
 * site is to be created. The next admin page opens Aspetto > Tasche Piene, which starts by itself.
 */
function tp_setup_mark_pending(): void {
	if ( ! tp_setup_is_done() ) {
		update_option( 'tp_setup_pending', '1', false );
	}
}
add_action( 'after_switch_theme', 'tp_setup_mark_pending' );

function tp_setup_redirect(): void {
	if ( wp_doing_ajax() || ! current_user_can( 'switch_themes' ) || tp_setup_is_done() ) {
		return;
	}

	// An update from a zip does not switch the theme on again: the site is still to be created.
	if ( ! get_option( 'tp_setup_pending' ) && ! get_option( 'tp_setup_seen' ) ) {
		update_option( 'tp_setup_pending', '1', false );
	}

	if ( ! get_option( 'tp_setup_pending' ) ) {
		return;
	}

	$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'tasche-piene' === $page || isset( $_GET['activate-multi'] ) || ! empty( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}

	// Once: whoever leaves the page without creating the site is not brought back to it again.
	delete_option( 'tp_setup_pending' );
	update_option( 'tp_setup_seen', '1', false );
	wp_safe_redirect( add_query_arg( 'tp_start', '1', tp_setup_page_url() ) );
	exit;
}
add_action( 'admin_init', 'tp_setup_redirect' );

function tp_setup_notice(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! current_user_can( 'switch_themes' ) || tp_setup_is_done() || ( $screen && 'appearance_page_tasche-piene' === $screen->id ) ) {
		return;
	}

	printf(
		'<div class="notice notice-warning"><p>%s</p><p><a class="button button-primary" href="%s">%s</a></p></div>',
		esc_html__( 'Il tema Tasche Piene è attivo, ma il sito (home, testata, piè di pagina) non è ancora stato creato.', 'tasche-piene' ),
		esc_url( tp_setup_page_url() ),
		esc_html__( 'Crea il sito', 'tasche-piene' )
	);
}
add_action( 'admin_notices', 'tp_setup_notice' );

function tp_setup_assets( string $hook ): void {
	if ( 'appearance_page_tasche-piene' !== $hook ) {
		return;
	}

	wp_enqueue_script( 'tp-setup', TP_URL . 'assets/js/tp-setup.js', [], tp_asset_version( 'assets/js/tp-setup.js' ), true );
}
add_action( 'admin_enqueue_scripts', 'tp_setup_assets' );

/**
 * Without JavaScript the button sends the whole work in one request.
 */
function tp_setup_run_from_form(): void {
	if ( empty( $_POST['tp_setup_run'] ) ) {
		return;
	}

	check_admin_referer( 'tp_setup_form' );

	if ( ! current_user_can( 'switch_themes' ) ) {
		wp_die( esc_html__( 'Non hai i permessi per creare il sito.', 'tasche-piene' ) );
	}

	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 600 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	foreach ( tp_setup_media_paths() as $path ) {
		tp_setup_media_url( $path );
	}

	tp_setup_finish();
	wp_safe_redirect( add_query_arg( 'tp_done', '1', tp_setup_page_url() ) );
	exit;
}
add_action( 'admin_init', 'tp_setup_run_from_form', 5 );

/**
 * What the creation of the site did, said at the top of the page it comes back to. With `again` the
 * site was already there and has been brought back to the theme's starting content.
 */
function tp_setup_done_notice(): void {
	$what = isset( $_GET['tp_done'] ) ? sanitize_key( wp_unslash( $_GET['tp_done'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( ! $what || ! tp_setup_is_done() ) {
		return;
	}

	$message = 'again' === $what
		? __( 'Sito iniziale ripristinato: home, testata, piè di pagina, Privacy Policy e Cookie Policy sono tornate ai contenuti di partenza del tema.', 'tasche-piene' )
		: __( 'Sito iniziale creato: home, testata e piè di pagina (Theme Builder), Privacy Policy e Cookie Policy.', 'tasche-piene' );

	if ( 'again' === $what && tp_snapshot_can() && tp_snapshot_get( TP_SNAPSHOT_UNDO ) ) {
		$message .= ' ' . __( 'Se non era quello che volevi, «Annulla il ripristino» lo riporta a com’era un momento fa.', 'tasche-piene' );
	}

	printf( '<div class="notice notice-success is-dismissible tp-setup__done"><p>%s</p></div>', esc_html( $message ) );
}

/**
 * The notices of the page answer to these: WordPress takes them out of the address once the page is
 * open, so that loading it again does not say twice what was done once.
 *
 * @param string[] $args
 * @return string[]
 */
function tp_setup_removable_args( $args ): array {
	return array_merge( (array) $args, [ 'tp_done', 'tp_photo', 'tp_why' ] );
}
add_filter( 'removable_query_args', 'tp_setup_removable_args' );

/**
 * Aspetto > Tasche Piene, two cards: the starting site (created the first time, brought back later)
 * and the photograph of the site (inc/site-snapshot.php).
 */
function tp_setup_page(): void {
	$done      = tp_setup_is_done();
	$autostart = ! $done && ! empty( $_GET['tp_start'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$old_homes = get_posts( [ 'post_type' => 'page', 'post_status' => 'draft', 'name' => 'home-versione-precedente', 'posts_per_page' => 1, 'no_found_rows' => true ] );
	$old_home  = $old_homes ? $old_homes[0] : null;
	$bar       = 'max-width:520px;height:12px;margin:12px 0 8px;background:#dcdcde;border-radius:6px;overflow:hidden';
	$fill      = 'display:block;width:0;height:100%;background:#2271b1;border-radius:6px;transition:width .4s ease';
	?>
	<div class="wrap">
		<h1>Tasche Piene</h1>
		<?php tp_setup_done_notice(); ?>
		<?php tp_snapshot_notice(); ?>
		<p style="max-width:720px"><?php esc_html_e( 'Il sito è fatto solo di moduli Divi: testi, immagini, colori, misure e spazi si cambiano da Divi, sulla pagina o nel Theme Builder. Il tema aggiunge i caratteri e pochi effetti.', 'tasche-piene' ); ?></p>

		<div class="tp-setup" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'tp_setup' ) ); ?>" data-page="<?php echo esc_url( tp_setup_page_url() ); ?>" data-autostart="<?php echo $autostart ? '1' : '0'; ?>" data-done="<?php echo $done ? '1' : '0'; ?>">
			<div class="tp-setup__ask card" <?php echo $done ? 'hidden' : ''; ?>>
				<h2><?php esc_html_e( 'Sito iniziale', 'tasche-piene' ); ?></h2>
				<p><?php esc_html_e( 'Il sito non è ancora stato creato. «Crea il sito» mette la home, la testata e il piè di pagina (Theme Builder), Privacy Policy e Cookie Policy con i contenuti di partenza del tema, e porta foto e video nella libreria Media. Ci vuole circa un minuto.', 'tasche-piene' ); ?></p>
				<form method="post">
					<?php wp_nonce_field( 'tp_setup_form' ); ?>
					<p><button type="submit" name="tp_setup_run" value="1" class="button button-primary tp-setup__start"><?php esc_html_e( 'Crea il sito', 'tasche-piene' ); ?></button></p>
				</form>
			</div>

			<div class="tp-setup__run card" hidden>
				<h2><?php esc_html_e( 'Sto preparando il sito iniziale', 'tasche-piene' ); ?></h2>
				<p><?php esc_html_e( 'Lascia aperta questa pagina.', 'tasche-piene' ); ?></p>
				<div class="tp-setup__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" aria-label="<?php esc_attr_e( 'Preparazione del sito iniziale', 'tasche-piene' ); ?>" style="<?php echo esc_attr( $bar ); ?>"><span class="tp-setup__fill" style="<?php echo esc_attr( $fill ); ?>"></span></div>
				<p class="tp-setup__status" aria-live="polite"></p>
			</div>

			<div class="tp-setup__error card" hidden>
				<h2><?php esc_html_e( 'La preparazione si è fermata', 'tasche-piene' ); ?></h2>
				<p><?php esc_html_e( 'La connessione o il server non hanno risposto. Premi Riprova: riparte dal punto in cui si era fermata.', 'tasche-piene' ); ?></p>
				<p><button type="button" class="button button-primary tp-setup__retry"><?php esc_html_e( 'Riprova', 'tasche-piene' ); ?></button></p>
			</div>

			<div class="tp-setup__again card" <?php echo $done ? '' : 'hidden'; ?>>
				<h2><?php esc_html_e( 'Sito iniziale', 'tasche-piene' ); ?></h2>
				<p><?php esc_html_e( 'È il sito come il tema lo crea la prima volta. «Ripristina il sito iniziale» riporta home, testata, piè di pagina, Privacy Policy e Cookie Policy ai contenuti di partenza del tema: le modifiche fatte da Divi a queste pagine vanno perse. Serve dopo un aggiornamento del tema che cambia le pagine, o quando non c’è una fotografia a cui tornare.', 'tasche-piene' ); ?></p>
				<p><button type="button" class="button tp-setup__redo" data-confirm="<?php esc_attr_e( 'Ripristinare il sito iniziale? Home, testata, piè di pagina e pagine legali tornano ai contenuti di partenza del tema: le modifiche fatte da Divi a queste pagine vanno perse.', 'tasche-piene' ); ?>"><?php esc_html_e( 'Ripristina il sito iniziale', 'tasche-piene' ); ?></button></p>
				<?php if ( $old_home instanceof WP_Post ) : ?>
					<p class="description"><?php esc_html_e( 'La home della versione precedente del tema è rimasta tra le bozze (Pagine > Home (versione precedente)): era fatta con moduli che il tema non ha più. Quando non serve, si può cestinare.', 'tasche-piene' ); ?></p>
				<?php endif; ?>
			</div>

			<?php tp_snapshot_card( $done ); ?>
		</div>
	</div>
	<?php
}
