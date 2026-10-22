<?php
/**
 * First-run site seed for fresh WordPress installs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TP_CHILD_SEED_VERSION', '2026-06-19.1' );
define( 'TP_CHILD_SEED_BUILDER_VERSION', '5.5.2' );

function tp_child_seed_raw_home_content(): string {
	$file = TP_CHILD_PATH . 'inc/seed/home-content.php';

	if ( ! file_exists( $file ) ) {
		return '';
	}

	$content = require $file;

	return is_string( $content ) ? $content : '';
}

function tp_child_seed_media_asset_references(): array {
	$content = tp_child_seed_raw_home_content();
	$assets  = [
		'assets/images/logo-footer.webp',
		'assets/images/footer/personaggi-1.webp',
		'assets/images/footer/personaggi-3.webp',
		'assets/images/footer/personaggi-4.webp',
	];

	if ( preg_match_all( '~\{\{TP_CHILD_URL\}\}(assets/(?:images|video)/[^"\'\s<)]+)~', $content, $matches ) ) {
		$assets = array_merge( $assets, $matches[1] );
	}

	$assets = array_values( array_unique( $assets ) );
	sort( $assets );

	return $assets;
}

function tp_child_seed_media_attachment_id_for_asset( string $relative_path ): int {
	$attachments = get_posts(
		[
			'post_type'      => 'attachment',
			'post_status'    => 'inherit',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_tp_child_seed_asset_path',
			'meta_value'     => $relative_path,
			'no_found_rows'  => true,
		]
	);

	return ! empty( $attachments[0] ) ? (int) $attachments[0] : 0;
}

function tp_child_seed_media_title_from_filename( string $filename ): string {
	$title = pathinfo( $filename, PATHINFO_FILENAME );
	$title = str_replace( [ '-', '_' ], ' ', $title );
	$title = trim( preg_replace( '~\s+~', ' ', $title ) ?: $title );

	return ucwords( $title );
}

function tp_child_seed_import_media_asset( string $relative_path ): array {
	if ( ! preg_match( '~^assets/(?:images|video)/~', $relative_path ) ) {
		return [];
	}

	$theme_root  = realpath( TP_CHILD_PATH );
	$source      = realpath( TP_CHILD_PATH . $relative_path );
	$source_hash = $source && is_readable( $source ) ? sha1_file( $source ) : "";

	if ( ! $theme_root || ! $source || 0 !== strpos( $source, $theme_root ) || ! is_readable( $source ) ) {
		return [];
	}

	$existing_id = tp_child_seed_media_attachment_id_for_asset( $relative_path );

	if ( $existing_id ) {
		$url = wp_get_attachment_url( $existing_id );

		if ( $url ) {
			$existing_hash = (string) get_post_meta( $existing_id, "_tp_child_seed_asset_hash", true );
			$attached_file = get_attached_file( $existing_id );

			if (
				$source_hash
				&& $source_hash !== $existing_hash
				&& is_string( $attached_file )
				&& ( is_writable( $attached_file ) || ( ! file_exists( $attached_file ) && is_writable( dirname( $attached_file ) ) ) )
				&& copy( $source, $attached_file )
			) {
				update_post_meta( $existing_id, "_tp_child_seed_asset_hash", $source_hash );

				require_once ABSPATH . "wp-admin/includes/image.php";

				$metadata = wp_generate_attachment_metadata( $existing_id, $attached_file );

				if ( is_array( $metadata ) ) {
					wp_update_attachment_metadata( $existing_id, $metadata );
				}
			}

			return [
				'id'  => $existing_id,
				'url' => esc_url_raw( $url ),
			];
		}
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$uploads = wp_upload_dir();

	if ( ! empty( $uploads['error'] ) || empty( $uploads['path'] ) || empty( $uploads['url'] ) ) {
		return [];
	}

	if ( ! wp_mkdir_p( $uploads['path'] ) ) {
		return [];
	}

	$filename = wp_unique_filename( $uploads['path'], basename( $source ) );
	$dest     = trailingslashit( $uploads['path'] ) . $filename;

	if ( ! copy( $source, $dest ) ) {
		return [];
	}

	$filetype = wp_check_filetype( $filename, null );

	if ( empty( $filetype['type'] ) ) {
		@unlink( $dest );
		return [];
	}

	$attachment_id = wp_insert_attachment(
		[
			'post_mime_type' => $filetype['type'],
			'post_title'     => tp_child_seed_media_title_from_filename( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		],
		$dest
	);

	if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		@unlink( $dest );
		return [];
	}

	$metadata = wp_generate_attachment_metadata( (int) $attachment_id, $dest );

	if ( is_array( $metadata ) ) {
		wp_update_attachment_metadata( (int) $attachment_id, $metadata );
	}

	update_post_meta( (int) $attachment_id, '_tp_child_seed_asset_path', $relative_path );
	update_post_meta( (int) $attachment_id, '_tp_child_seed_asset_hash', $source_hash ?: '' );

	$url = wp_get_attachment_url( (int) $attachment_id );

	return $url
		? [
			'id'  => (int) $attachment_id,
			'url' => esc_url_raw( $url ),
		]
		: [];
}

function tp_child_seed_media_assets(): array {
	static $assets = null;

	if ( is_array( $assets ) ) {
		return $assets;
	}

	$assets = [];

	foreach ( tp_child_seed_media_asset_references() as $relative_path ) {
		$asset = tp_child_seed_import_media_asset( $relative_path );

		if ( ! empty( $asset['id'] ) && ! empty( $asset['url'] ) ) {
			$assets[ $relative_path ] = $asset;
		}
	}

	return $assets;
}

function tp_child_seed_media_asset_url( string $relative_path ): string {
	$assets = tp_child_seed_media_assets();

	return $assets[ $relative_path ]['url'] ?? TP_CHILD_URL . $relative_path;
}

function tp_child_seed_attach_media_ids_to_content( string $content, array $assets ): string {
	foreach ( $assets as $asset ) {
		if ( empty( $asset['id'] ) || empty( $asset['url'] ) ) {
			continue;
		}

		$url = esc_url_raw( $asset['url'] );
		$id  = (string) (int) $asset['id'];

		$content = preg_replace_callback(
			'~("src"\s*:\s*"' . preg_quote( $url, '~' ) . '"\s*,\s*"id"\s*:\s*")([^"]*)(")~',
			static function ( array $matches ) use ( $id ): string {
				return $matches[1] . $id . $matches[3];
			},
			$content
		) ?? $content;

		$content = preg_replace_callback(
			'~("src"\s*:\s*"' . preg_quote( $url, '~' ) . '")(?!\s*,\s*"id"\s*:)~',
			static function ( array $matches ) use ( $id ): string {
				return $matches[1] . ',"id":"' . $id . '"';
			},
			$content
		) ?? $content;
	}

	return $content;
}

function tp_child_seed_home_content(): string {
	$content = tp_child_seed_raw_home_content();

	if ( '' === $content ) {
		return '';
	}

	$assets = tp_child_seed_media_assets();

	foreach ( $assets as $relative_path => $asset ) {
		if ( ! empty( $asset['url'] ) ) {
			$content = str_replace( '{{TP_CHILD_URL}}' . $relative_path, esc_url_raw( $asset['url'] ), $content );
		}
	}

	$content = tp_child_seed_attach_media_ids_to_content( $content, $assets );

	return str_replace( '{{TP_CHILD_URL}}', esc_url_raw( TP_CHILD_URL ), $content );
}

function tp_child_seed_json( array $value ): string {
	$json = wp_json_encode(
		$value,
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
	);

	return is_string( $json ) ? $json : '{}';
}

function tp_child_seed_block_open( string $name, array $attrs = [] ): string {
	$attrs_part = empty( $attrs ) ? '' : ' ' . tp_child_seed_json( $attrs );

	return "<!-- wp:{$name}{$attrs_part} -->\n";
}

function tp_child_seed_block_close( string $name ): string {
	return "<!-- /wp:{$name} -->\n";
}

function tp_child_seed_block_single( string $name, array $attrs = [] ): string {
	$attrs_part = empty( $attrs ) ? '' : ' ' . tp_child_seed_json( $attrs );

	return "<!-- wp:{$name}{$attrs_part} /-->\n";
}

function tp_child_seed_section_attrs( string $class_name, string $label ): array {
	return [
		'module'         => [
			'advanced' => [
				'htmlAttributes' => [
					'desktop' => [
						'value' => [
							'class' => $class_name,
						],
					],
				],
			],
			'meta'     => [
				'adminLabel' => [
					'desktop' => [
						'value' => $label,
					],
				],
			],
		],
		'builderVersion' => TP_CHILD_SEED_BUILDER_VERSION,
	];
}

function tp_child_seed_row_attrs(): array {
	return [
		'module'         => [
			'advanced'   => [
				'columnStructure'     => [
					'desktop' => [ 'value' => '4_4' ],
				],
				'flexColumnStructure' => [
					'desktop' => [ 'value' => 'equal-columns_1' ],
				],
			],
			'decoration' => [
				'sizing' => [
					'desktop' => [
						'value' => [
							'width'    => '100%',
							'maxWidth' => 'none',
						],
					],
				],
			],
		],
		'builderVersion' => TP_CHILD_SEED_BUILDER_VERSION,
	];
}

function tp_child_seed_column_attrs(): array {
	return [
		'module'         => [
			'advanced' => [
				'type' => [
					'desktop' => [ 'value' => '4_4' ],
				],
			],
		],
		'builderVersion' => TP_CHILD_SEED_BUILDER_VERSION,
	];
}

function tp_child_seed_footer_column_attrs(): array {
	return [
		'module'         => [
			'advanced' => [
				'type' => [
					'desktop' => [ 'value' => '4_4' ],
				],
				'htmlAttributes' => [
					'desktop' => [
						'value' => [
							'class' => 'tp-theme-footer-container',
						],
					],
				],
			],
		],
		'builderVersion' => TP_CHILD_SEED_BUILDER_VERSION,
	];
}

function tp_child_seed_code_block( string $class_name, string $html ): string {
	return tp_child_seed_block_single(
		'divi/code',
		[
			'module'         => [
				'advanced' => [
					'htmlAttributes' => [
						'desktop' => [
							'value' => [
								'class' => $class_name,
							],
						],
					],
				],
			],
			'content'        => [
				'innerContent' => [
					'desktop' => [
						'value' => trim( $html ),
					],
				],
			],
			'builderVersion' => TP_CHILD_SEED_BUILDER_VERSION,
		]
	);
}

function tp_child_seed_module_layout( string $section_class, string $label, string $module_markup ): string {
	$content  = tp_child_seed_block_open( 'divi/placeholder' );
	$content .= tp_child_seed_block_open( 'divi/section', tp_child_seed_section_attrs( $section_class, $label ) );
	$content .= tp_child_seed_block_open( 'divi/row', tp_child_seed_row_attrs() );
	$content .= tp_child_seed_block_open( 'divi/column', tp_child_seed_column_attrs() );
	$content .= $module_markup;
	$content .= tp_child_seed_block_close( 'divi/column' );
	$content .= tp_child_seed_block_close( 'divi/row' );
	$content .= tp_child_seed_block_close( 'divi/section' );
	$content .= tp_child_seed_block_close( 'divi/placeholder' );

	return $content;
}

function tp_child_seed_footer_layout( string $module_markup ): string {
	$content  = tp_child_seed_block_open( 'divi/placeholder' );
	$content .= tp_child_seed_block_open( 'divi/section', tp_child_seed_section_attrs( 'tp-theme-builder-footer-shell', 'Tasche Piene Footer' ) );
	$content .= tp_child_seed_block_open( 'divi/row', tp_child_seed_row_attrs() );
	$content .= tp_child_seed_block_open( 'divi/column', tp_child_seed_column_attrs() );
	$content .= $module_markup;
	$content .= tp_child_seed_block_close( 'divi/column' );
	$content .= tp_child_seed_block_close( 'divi/row' );
	$content .= tp_child_seed_block_close( 'divi/section' );
	$content .= tp_child_seed_block_close( 'divi/placeholder' );

	return $content;
}

function tp_child_seed_layout( string $section_class, string $label, string $code_class, string $html ): string {
	return tp_child_seed_module_layout( $section_class, $label, tp_child_seed_code_block( $code_class, $html ) );
}

function tp_child_seed_header_html(): string {
	$logo = esc_url( TP_CHILD_URL . 'assets/svg/logo-tasche-piene-nav.svg' );

	return <<<HTML
<header class="site-header tp-theme-header" id="site-header">
	<nav class="tp-theme-nav" aria-label="Principale">
		<a href="#home" class="tp-theme-nav-logo" aria-label="Tasche Piene">
			<img src="{$logo}" alt="Tasche Piene">
		</a>
		<button class="tp-theme-nav-toggle" type="button" aria-label="Apri menu" aria-expanded="false" aria-controls="primary-navigation">
			<span></span>
			<span></span>
			<span></span>
		</button>
		<ul id="primary-navigation" class="tp-theme-nav-links">
			<li><a href="#menu"><span class="tp-nav-link-text">I tramezzini</span></a></li>
			<li><a href="#chi-siamo"><span class="tp-nav-link-text">La nostra idea</span></a></li>
			<li><a href="#materie"><span class="tp-nav-link-text">Zena</span></a></li>
			<li><a href="#location"><span class="tp-nav-link-text">Dove siamo</span></a></li>
			<li><a href="tel:+393513738630"><span class="tp-nav-link-text">Chiamaci</span></a></li>
		</ul>
	</nav>
</header>
HTML;
}

function tp_child_seed_footer_html(): string {
	$logo     = esc_url( tp_child_seed_media_asset_url( 'assets/images/logo-footer.webp' ) );
	$person_1 = esc_url( tp_child_seed_media_asset_url( 'assets/images/footer/personaggi-1.webp' ) );
	$person_3 = esc_url( tp_child_seed_media_asset_url( 'assets/images/footer/personaggi-3.webp' ) );
	$person_4 = esc_url( tp_child_seed_media_asset_url( 'assets/images/footer/personaggi-4.webp' ) );

	return <<<HTML
<footer class="site-footer tp-theme-footer">
	<div class="tp-theme-footer-container">
		<div class="tp-theme-footer-characters">
			<img class="tp-theme-footer-character tp-theme-footer-character--one" src="{$person_1}" alt="" aria-hidden="true">
			<img class="tp-theme-footer-character tp-theme-footer-character--four" src="{$person_4}" alt="" aria-hidden="true">
			<img class="tp-theme-footer-character tp-theme-footer-character--three" src="{$person_3}" alt="" aria-hidden="true">
		</div>
		<div class="tp-theme-footer-logo">
			<img src="{$logo}" alt="Tasche Piene">
		</div>
		<nav class="tp-theme-footer-nav" aria-label="Footer">
			<!--TP_FOOTER_NAV_LEFT-->
			<!--TP_FOOTER_NAV_RIGHT-->
		</nav>
		<div class="tp-theme-footer-copyright">
			<span>Copyright 2026, Tasche piene tutti i diritti riservati</span>
			<span>P.IVA 03942309300</span>
			<span>Privacy-Cookie</span>
			<span>Designed By Mochi</span>
		</div>
	</div>
</footer>
HTML;
}

function tp_child_seed_find_post_id( string $post_type, string $slug ): int {
	$post = get_page_by_path( $slug, OBJECT, $post_type );

	return $post instanceof WP_Post ? (int) $post->ID : 0;
}

function tp_child_seed_upsert_post( string $post_type, string $slug, string $title, string $content ): int {
	$post_id = tp_child_seed_find_post_id( $post_type, $slug );
	$args    = [
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_type'    => $post_type,
		'post_status'  => 'publish',
		'post_content' => wp_slash( $content ),
	];

	if ( $post_id ) {
		$args['ID'] = $post_id;
		$result     = wp_update_post( $args, true );
	} else {
		$args['post_author'] = get_current_user_id() ?: 1;
		$result              = wp_insert_post( $args, true );
	}

	if ( is_wp_error( $result ) ) {
		error_log( 'Tasche Piene seed error: ' . $result->get_error_message() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		return 0;
	}

	$post_id = (int) $result;
	update_post_meta( $post_id, '_tp_child_seed_managed', '1' );

	return $post_id;
}

function tp_child_seed_divi_canvases_used(): string {
	return serialize(
		[
			'canvas_portal_ids'   => [],
			'appended_above'      => [],
			'appended_below'      => [],
			'interaction_targets' => [],
			'all_canvas_metadata' => [],
		]
	);
}

function tp_child_seed_off_canvas_data(): string {
	return serialize(
		[
			'activeCanvasId' => wp_generate_uuid4(),
			'mainCanvasName' => 'Main Canvas',
		]
	);
}

function tp_child_seed_clear_divi_cache_meta( int $post_id ): void {
	foreach (
		[
			'_divi_dynamic_assets_cached_modules',
			'_divi_dynamic_assets_cached_feature_used',
			'_et_builder_post_features_cache',
			'_et_builder_dynamic_assets_loading_attr_threshold',
		] as $meta_key
	) {
		delete_post_meta( $post_id, $meta_key );
	}
}

function tp_child_seed_apply_divi_meta( int $post_id, string $settings_title, string $built_for = '', string $show_page_creation = 'off', bool $off_canvas = false ): void {
	$meta = [
		'_et_pb_use_builder'                   => 'on',
		'_et_pb_show_page_creation'            => $show_page_creation,
		'_et_pb_content_area_background_color' => '',
		'_et_pb_custom_css'                    => '',
		'_et_pb_enable_shortcode_tracking'     => 'off',
		'_et_pb_gutter_width'                  => '3',
		'_et_pb_page_z_index'                  => 'auto',
		'_et_pb_section_background_color'      => '',
		'_et_pb_first_image'                   => '',
		'_et_pb_truncate_post'                 => '',
		'_et_pb_truncate_post_date'            => '',
		'_et_pb_use_divi_5'                    => 'on',
		'_et_pb_ab_current_shortcode'          => '[et_pb_split_track id="' . $post_id . '" /]',
		'_et_pb_post_settings_title'           => $settings_title,
		'_et_pb_post_settings_excerpt'         => '',
		'_divi_dynamic_assets_canvases_used'   => tp_child_seed_divi_canvases_used(),
	];

	if ( '' !== $built_for ) {
		$meta['_et_pb_built_for_post_type'] = $built_for;
	}

	if ( $off_canvas ) {
		$meta['_divi_off_canvas_data'] = tp_child_seed_off_canvas_data();
	}

	foreach ( $meta as $key => $value ) {
		update_post_meta( $post_id, $key, $value );
	}

	tp_child_seed_clear_divi_cache_meta( $post_id );
	clean_post_cache( $post_id );
}

function tp_child_seed_home_page(): int {
	$content = tp_child_seed_home_content();
	$post_id = tp_child_seed_upsert_post( 'page', 'tasche-piene-hero', 'Tasche Piene Hero', $content );

	if ( ! $post_id ) {
		return 0;
	}

	tp_child_seed_apply_divi_meta( $post_id, 'Tasche Piene Hero', 'page', 'off', true );

	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $post_id );
	update_option( 'page_for_posts', 0 );

	return $post_id;
}

function tp_child_seed_header_block(): string {
	$attrs = [
		'module' => [
			'advanced' => [
				'htmlAttributes' => [
					'desktop' => [
						'value' => [
							'class' => 'tp-theme-header-code',
						],
					],
				],
			],
		],
		'navBackgroundColor' => [
			'innerContent' => [ 'desktop' => [ 'value' => '#fef5e9' ] ],
		],
		'navLinkColor' => [
			'innerContent' => [ 'desktop' => [ 'value' => '#054726' ] ],
		],
		'logoUrl' => [
			'innerContent' => [ 'desktop' => [ 'value' => TP_CHILD_URL . 'assets/svg/logo-tasche-piene-nav.svg' ] ],
		],
		'logoImage' => tp_child_chrome_seed_image_value( TP_CHILD_URL . 'assets/svg/logo-tasche-piene-nav.svg' ),
		'logoAlt' => [
			'innerContent' => [ 'desktop' => [ 'value' => 'Tasche Piene' ] ],
		],
		'logoLink' => [
			'innerContent' => [ 'desktop' => [ 'value' => '#home' ] ],
		],
		'menuLocation' => [
			'innerContent' => [ 'desktop' => [ 'value' => 'tp_main_nav' ] ],
		],
		'burgerOpenLabel' => [
			'innerContent' => [ 'desktop' => [ 'value' => 'Apri menu' ] ],
		],
		'burgerCloseLabel' => [
			'innerContent' => [ 'desktop' => [ 'value' => 'Chiudi menu' ] ],
		],
		'builderVersion' => TP_CHILD_SEED_BUILDER_VERSION,
	];

	return tp_child_seed_block_single( 'tp/theme-header', $attrs );
}

function tp_child_seed_footer_block(): string {
	$text_attr = static function ( string $value ): array {
		return [ 'innerContent' => [ 'desktop' => [ 'value' => $value ] ] ];
	};

	$attrs = [
		'module' => [
			'advanced' => [
				'htmlAttributes' => [
					'desktop' => [ 'value' => [ 'class' => 'tp-theme-footer-code' ] ],
				],
			],
		],
		'footerBackgroundColor' => $text_attr( '#054726' ),
		'footerLinkColor'       => $text_attr( '#fff5e9' ),
		'navAriaLabel'          => $text_attr( 'Footer' ),
		'logoImage'             => tp_child_chrome_seed_image_value( tp_child_seed_media_asset_url( 'assets/images/logo-footer.webp' ) ),
		'logoAlt'               => $text_attr( 'Tasche Piene' ),
		'logoLink'              => $text_attr( '#home' ),
		'menuLeftLocation'      => $text_attr( 'tp_footer_left' ),
		'menuRightLocation'     => $text_attr( 'tp_footer_right' ),
		'copyrightLine1'        => $text_attr( 'Copyright 2026, Tasche piene tutti i diritti riservati' ),
		'copyrightLine2'        => $text_attr( 'P.IVA 03942309300' ),
		'copyrightLine3'        => $text_attr( 'Privacy' ),
		'copyrightLine4'        => $text_attr( 'Cookie' ),
		'copyrightLine5'        => $text_attr( 'Designed By Mochi' ),
		'builderVersion'        => TP_CHILD_SEED_BUILDER_VERSION,
	];

	return tp_child_seed_block_single( 'tp/theme-footer', $attrs );
}


function tp_child_seed_theme_builder(): array {
	$header_id = tp_child_seed_upsert_post(
		'et_header_layout',
		'tasche-piene-header',
		'Tasche Piene Default Template Header Layout',
		tp_child_seed_module_layout( 'tp-theme-builder-header-shell', 'Tasche Piene Header', tp_child_seed_header_block() )
	);

	$footer_id = tp_child_seed_upsert_post(
		'et_footer_layout',
		'tasche-piene-footer',
		'Tasche Piene Default Template Footer Layout',
		tp_child_seed_footer_layout( tp_child_seed_footer_block() )
	);

	$template_id = tp_child_seed_upsert_post(
		'et_template',
		'tasche-piene-default-template',
		'Tasche Piene Default Template',
		''
	);

	$builder_id = tp_child_seed_upsert_post(
		'et_theme_builder',
		'theme-builder',
		'Theme Builder',
		''
	);

	if ( $header_id ) {
		tp_child_seed_apply_divi_meta( $header_id, 'Tasche Piene Default Template Header Layout', '', 'on', false );
	}

	if ( $footer_id ) {
		tp_child_seed_apply_divi_meta( $footer_id, 'Tasche Piene Default Template Footer Layout', '', 'off', true );
	}

	if ( $template_id ) {
		update_post_meta( $template_id, '_et_autogenerated_title', '0' );
		update_post_meta( $template_id, '_et_default', '1' );
		update_post_meta( $template_id, '_et_enabled', '1' );
		update_post_meta( $template_id, '_et_header_layout_id', (string) $header_id );
		update_post_meta( $template_id, '_et_header_layout_enabled', '1' );
		update_post_meta( $template_id, '_et_header_layout_global', '1' );
		update_post_meta( $template_id, '_et_body_layout_id', '0' );
		update_post_meta( $template_id, '_et_body_layout_enabled', '1' );
		update_post_meta( $template_id, '_et_footer_layout_id', (string) $footer_id );
		update_post_meta( $template_id, '_et_footer_layout_enabled', '1' );
		update_post_meta( $template_id, '_et_footer_layout_global', '1' );
		clean_post_cache( $template_id );
	}

	if ( $builder_id && $template_id ) {
		update_post_meta( $builder_id, '_et_template', (string) $template_id );
		clean_post_cache( $builder_id );
	}

	return [
		'header_id'   => $header_id,
		'footer_id'   => $footer_id,
		'template_id' => $template_id,
		'builder_id'  => $builder_id,
	];
}

function tp_child_seed_site( bool $force = false ): array {
	if ( ! $force && TP_CHILD_SEED_VERSION === get_option( 'tp_child_seed_site_version' ) ) {
		return [ 'skipped' => true ];
	}

	if ( function_exists( 'tp_child_seed_nav_menus' ) ) {
		tp_child_seed_nav_menus();
	}

	tp_child_seed_media_assets();

	$home_id       = tp_child_seed_home_page();
	$theme_builder = tp_child_seed_theme_builder();
	$legal_pages   = function_exists( 'tp_child_seed_legal_pages' ) ? tp_child_seed_legal_pages() : [];

	update_option( 'tp_child_seed_site_version', TP_CHILD_SEED_VERSION );
	flush_rewrite_rules( false );

	return array_merge(
		[
			'skipped'      => false,
			'home_id'      => $home_id,
			'privacy_id'   => (int) ( $legal_pages['privacy'] ?? 0 ),
			'cookie_id'    => (int) ( $legal_pages['cookie'] ?? 0 ),
		],
		$theme_builder
	);
}

function tp_child_seed_site_auto_enabled(): bool {
	return defined( "TP_CHILD_ENABLE_AUTO_SEED" ) && true === TP_CHILD_ENABLE_AUTO_SEED;
}

function tp_child_seed_site_admin_url(): string {
	return wp_nonce_url( admin_url( "themes.php?tp_child_seed_site=1" ), "tp_child_seed_site" );
}

function tp_child_seed_site_manual_requested(): bool {
	if ( ! is_admin() || ! current_user_can( "switch_themes" ) ) {
		return false;
	}

	if ( empty( $_GET["tp_child_seed_site"] ) ) {
		return false;
	}

	$requested = sanitize_text_field( wp_unslash( $_GET["tp_child_seed_site"] ) );

	if ( "1" !== $requested ) {
		return false;
	}

	$nonce = isset( $_GET["_wpnonce"] ) ? sanitize_text_field( wp_unslash( $_GET["_wpnonce"] ) ) : "";

	return (bool) wp_verify_nonce( $nonce, "tp_child_seed_site" );
}

function tp_child_seed_site_after_switch(): void {
	update_option( "tp_child_seed_site_pending", "1", false );

	if ( function_exists( 'tp_child_seed_legal_pages' ) ) {
		tp_child_seed_legal_pages();
	}

	if ( ! tp_child_seed_site_auto_enabled() ) {
		return;
	}

	tp_child_seed_site( true );
	delete_option( "tp_child_seed_site_pending" );
}
add_action( 'after_switch_theme', 'tp_child_seed_site_after_switch', 30 );

function tp_child_seed_site_should_skip_admin_fallback(): bool {
	if ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) {
		return true;
	}

	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return true;
	}

	global $pagenow;

	return in_array( $pagenow, [ 'admin-ajax.php', 'async-upload.php', 'upload.php', 'media-new.php' ], true );
}

function tp_child_seed_site_admin_fallback(): void {
	$manual_request = tp_child_seed_site_manual_requested();

	if ( ! $manual_request && ! tp_child_seed_site_auto_enabled() ) {
		return;
	}

	if ( tp_child_seed_site_should_skip_admin_fallback() ) {
		return;
	}

	if ( TP_CHILD_SEED_VERSION === get_option( 'tp_child_seed_site_version' ) ) {
		return;
	}

	if ( ! current_user_can( 'switch_themes' ) ) {
		return;
	}

	tp_child_seed_site( $manual_request );
	delete_option( "tp_child_seed_site_pending" );

	if ( $manual_request && ! headers_sent() ) {
		wp_safe_redirect( add_query_arg( "tp_child_seeded", "1", admin_url( "themes.php" ) ) );
		exit;
	}
}
add_action( "admin_init", "tp_child_seed_site_admin_fallback", 5 );

function tp_child_seed_site_admin_notice(): void {
	if ( ! current_user_can( "switch_themes" ) ) {
		return;
	}

	if ( ! empty( $_GET["tp_child_seeded"] ) ) {
		echo "<div class=\"notice notice-success is-dismissible\"><p>" . esc_html__( "Seed Tasche Piene completato.", "tasche-piene-divi-child" ) . "</p></div>";
	}

	if ( TP_CHILD_SEED_VERSION === get_option( "tp_child_seed_site_version" ) ) {
		return;
	}

	$url = tp_child_seed_site_admin_url();

	echo "<div class=\"notice notice-warning\"><p>" . esc_html__( "Il tema Tasche Piene e attivo, ma la home non e ancora stata creata o aggiornata.", "tasche-piene-divi-child" ) . "</p><p><a class=\"button button-primary\" href=\"" . esc_url( $url ) . "\">" . esc_html__( "Crea/aggiorna home Tasche Piene", "tasche-piene-divi-child" ) . "</a></p></div>";
}
add_action( "admin_notices", "tp_child_seed_site_admin_notice" );

function tp_child_seed_media_library_fallback(): void {
	if ( ! current_user_can( "upload_files" ) ) {
		return;
	}

	if ( TP_CHILD_SEED_VERSION !== get_option( "tp_child_seed_site_version" ) ) {
		return;
	}

	tp_child_seed_media_assets();
}
add_action( "load-upload.php", "tp_child_seed_media_library_fallback", 1 );

if ( defined( "WP_CLI" ) && WP_CLI && class_exists( "WP_CLI" ) ) {
	WP_CLI::add_command(
		"tp seed",
		static function (): void {
			$result = tp_child_seed_site( true );
			delete_option( "tp_child_seed_site_pending" );

			WP_CLI::success( wp_json_encode( $result ) );
		}
	);
}
