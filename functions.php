<?php
/**
 * Clean bootstrap for the Tasche Piene Divi child theme.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( "TP_CHILD_VERSION", "0.4.3" );
define( 'TP_CHILD_PATH', trailingslashit( get_stylesheet_directory() ) );
define( 'TP_CHILD_URL', trailingslashit( get_stylesheet_directory_uri() ) );
define( 'TP_CHILD_MODULES_JSON_PATH', TP_CHILD_PATH . 'modules-json/' );

/**
 * Invalidate Divi's static CSS files after a migration rewrote saved layouts, so the
 * pages are not served with CSS built from their old content. Divi 5 has no
 * et_core_clear_cache(), and ET_Core_PageResource is ready only from wp_loaded; the
 * public do_remove_static_resources() also works for visitors, cron and WP-CLI.
 */
function tp_child_clear_divi_static_css(): void {
	static $scheduled = false;

	if ( $scheduled ) {
		return;
	}

	$scheduled = true;
	$clear     = static function (): void {
		if ( class_exists( 'ET_Core_PageResource' ) && ET_Core_PageResource::can_write_to_filesystem() ) {
			ET_Core_PageResource::do_remove_static_resources( 'all', 'all' );
		}
	};

	if ( did_action( 'wp_loaded' ) ) {
		$clear();
	} else {
		add_action( 'wp_loaded', $clear );
	}
}

require_once TP_CHILD_PATH . 'inc/media-upload-fallback.php';
require_once TP_CHILD_PATH . 'inc/svg-upload-support.php';
require_once TP_CHILD_PATH . 'inc/theme-chrome-helpers.php';
require_once TP_CHILD_PATH . 'inc/chrome-menu-config.php';
require_once TP_CHILD_PATH . 'inc/chrome-image-migration.php';
require_once TP_CHILD_PATH . 'inc/chrome-nav-migration.php';
require_once TP_CHILD_PATH . 'inc/chrome-menu-cleanup.php';
require_once TP_CHILD_PATH . 'inc/nav-url-migration.php';
require_once TP_CHILD_PATH . 'inc/hero-anchor-migration.php';
require_once TP_CHILD_PATH . 'inc/design-cleanup-migration.php';

/**
 * The one-time migrations check their flag on every request. Until 0.4.0 the
 * flags were saved without autoload, so each check cost a database query: load
 * them with the other options from now on.
 */
function tp_child_autoload_migration_flags(): void {
	if ( get_option( 'tp_child_migration_flags_autoload_v1' ) ) {
		return;
	}

	if ( function_exists( 'wp_set_option_autoload_values' ) ) {
		wp_set_option_autoload_values(
			array_fill_keys(
				[
					'tp_child_chrome_images_v4',
					'tp_child_chrome_images_v5',
					'tp_child_chrome_nav_wp_v1',
					'tp_child_chrome_menus_v1',
					'tp_child_nav_urls_v1',
					'tp_child_nav_menu_titles_v2',
					'tp_child_legal_pages_v2',
					'tp_child_hero_home_id_v1',
					'tp_child_nav_hash_home_v3',
					'tp_child_materie_marquee_migrated_v1',
					'tp_child_design_cleanup_v1',
				],
				true
			)
		);
	}

	update_option( 'tp_child_migration_flags_autoload_v1', '1', true );
}
add_action( 'after_setup_theme', 'tp_child_autoload_migration_flags', 1 );

/**
 * Allow JPEG extension aliases that WordPress does not include by default.
 * Without these aliases Plupload rejects the file in the browser before PHP sees it.
 */
function tp_child_allow_jpeg_alias_mimes( array $mimes ): array {
	$mimes['jfif|pjpeg|pjp'] = 'image/jpeg';

	return $mimes;
}
add_filter( 'upload_mimes', 'tp_child_allow_jpeg_alias_mimes' );

/**
 * Register editable navigation locations used by the Theme Builder chrome.
 */
function tp_child_register_nav_menus(): void {
	$menus = [];

	foreach ( tp_child_nav_menu_config() as $location => $config ) {
		$menus[ $location ] = $config['location'];
	}

	register_nav_menus( $menus );
}
add_action( 'after_setup_theme', 'tp_child_register_nav_menus' );

/**
 * Default nav menu items. They are used as fallback and for local seeding.
 */
function tp_child_nav_menu_defaults( string $location ): array {
	$menus = [
		'tp_main_nav'     => [
			[ 'title' => 'Home', 'url' => '#home' ],
			[ 'title' => 'I tramezzini', 'url' => '#menu' ],
			[ 'title' => 'La nostra idea', 'url' => '#chi-siamo' ],
			[ 'title' => 'Zena', 'url' => '#materie' ],
			[ 'title' => 'Dove siamo', 'url' => '#location' ],
		],
		'tp_footer_left'  => [
			[ 'title' => 'MENÙ', 'url' => '#menu' ],
			[ 'title' => 'DOVE SIAMO', 'url' => '#location' ],
			[ 'title' => 'SU DI NOI', 'url' => '#chi-siamo' ],
		],
		'tp_footer_right' => [
			[ 'title' => 'TORNA SÙ', 'url' => '#home' ],
			[ 'title' => 'FACEBOOK', 'url' => 'https://facebook.com', 'target' => '_blank' ],
			[ 'title' => 'INSTAGRAM', 'url' => 'https://instagram.com', 'target' => '_blank' ],
		],
	];

	return $menus[ $location ] ?? [];
}

/**
 * Create default editable nav menus if they do not exist yet.
 */
function tp_child_seed_nav_menus(): void {
	$locations = get_theme_mod( 'nav_menu_locations', [] );

	foreach ( tp_child_nav_menu_config() as $location => $menu_config ) {
		$menu = tp_child_get_nav_menu_by_config( $location );

		if ( ! $menu ) {
			$menu_id = wp_create_nav_menu( $menu_config['name'] );
			$menu    = wp_get_nav_menu_object( $menu_id );
		}

		if ( ! $menu || is_wp_error( $menu ) ) {
			continue;
		}

		$items = wp_get_nav_menu_items( $menu->term_id );

		if ( empty( $items ) ) {
			foreach ( tp_child_nav_menu_defaults( $location ) as $item ) {
				wp_update_nav_menu_item(
					$menu->term_id,
					0,
					[
						'menu-item-title'  => $item['title'],
						'menu-item-url'    => $item['url'],
						'menu-item-target' => $item['target'] ?? '',
						'menu-item-status' => 'publish',
						'menu-item-type'   => 'custom',
					]
				);
			}
		}

		$locations[ $location ] = (int) $menu->term_id;
	}

	set_theme_mod( 'nav_menu_locations', $locations );
}
add_action( 'after_switch_theme', 'tp_child_seed_nav_menus' );

/**
 * Simple admin hub for the three site menus.
 */
function tp_child_register_menus_admin_page(): void {
	add_theme_page(
		__( 'Menu del sito', 'tasche-piene-divi-child' ),
		__( 'Menu sito', 'tasche-piene-divi-child' ),
		'edit_theme_options',
		'tp-child-menus',
		'tp_child_render_menus_admin_page'
	);
}
add_action( 'admin_menu', 'tp_child_register_menus_admin_page' );

/**
 * Render the menu hub page.
 */
function tp_child_render_menus_admin_page(): void {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Menu del sito Tasche Piene', 'tasche-piene-divi-child' ); ?></h1>
		<p><?php esc_html_e( 'Ogni menu corrisponde a una zona precisa del sito. Modifica qui testi e link — non serve toccare il Theme Builder per le voci di navigazione.', 'tasche-piene-divi-child' ); ?></p>
		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;max-width:960px;margin-top:24px;">
			<?php foreach ( tp_child_nav_menu_config() as $location => $config ) : ?>
				<div class="card" style="padding:16px;">
					<h2 style="margin-top:0;"><?php echo esc_html( $config['admin_label'] ); ?></h2>
					<p style="color:#50575e;"><?php echo esc_html( $config['help'] ); ?></p>
					<p style="color:#787c82;font-size:12px;"><?php echo esc_html( $config['name'] ); ?></p>
					<p>
						<a class="button button-primary" href="<?php echo esc_url( tp_child_nav_menu_edit_url( $location ) ); ?>">
							<?php esc_html_e( 'Modifica voci', 'tasche-piene-divi-child' ); ?>
						</a>
					</p>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}

/**
 * Short hint on the native Menus screen.
 */
function tp_child_nav_menus_admin_notice(): void {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || 'nav-menus' !== $screen->id ) {
		return;
	}

	$url = admin_url( 'themes.php?page=tp-child-menus' );

	echo '<div class="notice notice-info"><p>';
	echo wp_kses_post(
		sprintf(
			/* translators: %s: link to the Tasche Piene menu hub */
			__( 'Per Tasche Piene usa i tre menu dedicati: Header, Footer sinistra e Footer destra. Guida rapida: <a href="%s">Menu sito</a>.', 'tasche-piene-divi-child' ),
			esc_url( $url )
		)
	);
	echo '</p></div>';
}
add_action( 'admin_notices', 'tp_child_nav_menus_admin_notice' );

require_once TP_CHILD_PATH . 'inc/site-seeder.php';
require_once TP_CHILD_PATH . 'inc/menu-label-text-modules.php';
require_once TP_CHILD_PATH . 'inc/legal-pages.php';
require_once TP_CHILD_PATH . 'inc/footer-layout-helpers.php';
require_once TP_CHILD_PATH . 'inc/materie-marquee-migration.php';

/**
 * Render a single footer menu column from a WordPress menu location.
 */
function tp_child_render_footer_menu_column( string $location ): string {
	$locations = get_nav_menu_locations();
	$items     = [];

	if ( ! empty( $locations[ $location ] ) ) {
		$menu_items = wp_get_nav_menu_items( (int) $locations[ $location ] );

		if ( is_array( $menu_items ) ) {
			$items = array_values(
				array_filter(
					$menu_items,
					static function ( WP_Post $item ): bool {
						return 0 === (int) $item->menu_item_parent;
					}
				)
			);
		}
	}

	if ( empty( $items ) ) {
		$items = array_map(
			static function ( array $item ): object {
				return (object) [
					'title'  => $item['title'],
					'url'    => $item['url'],
					'target' => $item['target'] ?? '',
					'xfn'    => '',
				];
			},
			tp_child_nav_menu_defaults( $location )
		);
	}

	$output = '<div class="tp-theme-footer-nav-col">';

	foreach ( $items as $item ) {
		$title  = isset( $item->title ) ? (string) $item->title : '';
		$url    = tp_child_resolve_nav_url( isset( $item->url ) ? (string) $item->url : '#' );
		$target = isset( $item->target ) ? (string) $item->target : '';
		$rel    = isset( $item->xfn ) ? trim( (string) $item->xfn ) : '';

		if ( '_blank' === $target && '' === $rel ) {
			$rel = 'noopener';
		}

		$output .= sprintf(
			'<a href="%1$s"%2$s%3$s>%4$s</a>',
			esc_url( $url ),
			$target ? ' target="' . esc_attr( $target ) . '"' : '',
			$rel ? ' rel="' . esc_attr( $rel ) . '"' : '',
			esc_html( $title )
		);
	}

	$output .= '</div>';

	return $output;
}

/**
 * Render the global Theme Builder footer. Navigation labels/URLs come from WP menus.
 */
function tp_child_theme_footer_shortcode(): string {
	$logo     = esc_url( TP_CHILD_URL . 'assets/images/logo-footer.webp' );
	$person_1 = esc_url( TP_CHILD_URL . 'assets/images/footer/personaggi-1.webp' );
	$person_3 = esc_url( TP_CHILD_URL . 'assets/images/footer/personaggi-3.webp' );
	$person_4 = esc_url( TP_CHILD_URL . 'assets/images/footer/personaggi-4.webp' );

	ob_start();
	?>
	<footer class="site-footer tp-theme-footer">
		<div class="tp-theme-footer-container">
			<div class="tp-theme-footer-characters">
				<img class="tp-theme-footer-character tp-theme-footer-character--one" src="<?php echo $person_1; ?>" alt="" aria-hidden="true">
				<img class="tp-theme-footer-character tp-theme-footer-character--four" src="<?php echo $person_4; ?>" alt="" aria-hidden="true">
				<img class="tp-theme-footer-character tp-theme-footer-character--three" src="<?php echo $person_3; ?>" alt="" aria-hidden="true">
			</div>
			<div class="tp-theme-footer-logo">
				<img src="<?php echo $logo; ?>" alt="Tasche Piene">
			</div>
			<nav class="tp-theme-footer-nav" aria-label="Footer">
				<?php
				echo tp_child_render_footer_menu_column( 'tp_footer_left' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo tp_child_render_footer_menu_column( 'tp_footer_right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</nav>
			<div class="tp-theme-footer-copyright">
				<span>Copyright 2026, Tasche piene tutti i diritti riservati</span>
				<span>P.IVA 03942309300</span>
				<span>Privacy-Cookie</span>
				<span>Designed By Mochi</span>
			</div>
		</div>
	</footer>
	<?php

	return trim( ob_get_clean() );
}
add_shortcode( 'tp_theme_footer', 'tp_child_theme_footer_shortcode' );

/**
 * Replace Theme Builder Code Module placeholders with editable WordPress menus.
 */
function tp_child_replace_footer_nav_placeholders( string $block_content, array $block ): string {
	if ( false === strpos( $block_content, 'TP_FOOTER_NAV_' ) ) {
		return $block_content;
	}

	return strtr(
		$block_content,
		[
			'<!--TP_FOOTER_NAV_LEFT-->'  => tp_child_render_footer_menu_column( 'tp_footer_left' ),
			'<!--TP_FOOTER_NAV_RIGHT-->' => tp_child_render_footer_menu_column( 'tp_footer_right' ),
		]
	);
}
add_filter( 'render_block', 'tp_child_replace_footer_nav_placeholders', 20, 2 );

/**
 * Autoload the small set of custom Divi modules owned by this child theme.
 */
spl_autoload_register(
	function ( string $class ): void {
		$prefix = 'TaschePieneDiviChild\\Modules\\';

		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$relative_class = substr( $class, strlen( $prefix ) );
		$file           = TP_CHILD_PATH . 'modules/' . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);

require_once TP_CHILD_PATH . 'modules/Modules.php';

/**
 * Cache-bust assets during local development.
 */
function tp_child_asset_version( string $relative_path ): string {
	$path = TP_CHILD_PATH . ltrim( $relative_path, '/' );

	return file_exists( $path ) ? (string) filemtime( $path ) : TP_CHILD_VERSION;
}

/**
 * Detect Divi Visual Builder so JS runtimes do not fight the editor.
 */
function tp_child_is_visual_builder(): bool {
	if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
		return true;
	}

	// Divi opens the builder only for users who can edit: a visitor adding ?et_fb=1 to
	// a URL must still get the normal page with its scripts.
	if ( ! isset( $_GET['et_fb'] ) || ! is_user_logged_in() || ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	$et_fb = wp_unslash( $_GET['et_fb'] );

	return ! is_array( $et_fb ) && '1' === sanitize_text_field( $et_fb );
}

/**
 * Check current singular content for a block name.
 */
function tp_child_has_block( string $block_name ): bool {
	if ( has_block( $block_name ) ) {
		return true;
	}

	if ( ! is_singular() ) {
		return false;
	}

	$content = get_post_field( 'post_content', get_queried_object_id() );

	return is_string( $content ) && false !== strpos( $content, '<!-- wp:' . $block_name );
}

/**
 * Check current singular content for a class or text marker.
 * The answer is kept for the request: the same markers are asked several times.
 */
function tp_child_content_has_marker( string $marker ): bool {
	static $found = [];

	if ( ! is_singular() ) {
		return false;
	}

	$post_id = get_queried_object_id();

	if ( ! isset( $found[ $post_id ][ $marker ] ) ) {
		$content                      = get_post_field( 'post_content', $post_id );
		$found[ $post_id ][ $marker ] = is_string( $content ) && false !== strpos( $content, $marker );
	}

	return $found[ $post_id ][ $marker ];
}

/**
 * Detect the Tasche Piene landing page, including duplicated local slugs.
 */
function tp_child_is_landing_context(): bool {
	return is_front_page()
		|| is_page( 'tasche-piene' )
		|| is_page( 'tasche-piene-hero' )
		|| tp_child_content_has_marker( 'tp-hero-section' );
}

/**
 * The prototype preloader is a frontend-only landing-page treatment.
 */
function tp_child_should_show_preloader(): bool {
	if ( isset( $_GET["nopreloader"] ) ) {
		return false;
	}

	return ! tp_child_is_visual_builder() && tp_child_is_landing_context();
}

/**
 * Enable preloader styles as early as possible, but only when JS is available.
 */
function tp_child_preloader_head_script(): void {
	if ( ! tp_child_should_show_preloader() ) {
		return;
	}

	echo "<script>document.documentElement.classList.add('tp-preloader-enabled');</script>\n";
}
add_action( 'wp_head', 'tp_child_preloader_head_script', 1 );

/**
 * Render the prototype preload overlay from the child theme.
 */
function tp_child_render_preloader(): void {
	if ( ! tp_child_should_show_preloader() ) {
		return;
	}
	?>
	<div class="tp-preloader" aria-hidden="true">
		<div class="tp-preloader-panel"></div>
		<div class="tp-preloader-logo-wrap">
			<div class="tp-preloader-logo-line tp-preloader-logo-line--top">
				<div class="tp-preloader-logo-piece tp-preloader-logo-piece--top"></div>
			</div>
			<div class="tp-preloader-logo-line tp-preloader-logo-line--bottom">
				<div class="tp-preloader-logo-piece tp-preloader-logo-piece--bottom"></div>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_body_open', 'tp_child_render_preloader', 1 );

/**
 * Enqueue the project stylesheet and runtime. Divi already enqueues its own
 * stylesheet and the child style.css (divi-style-child) at the default priority,
 * so the project stylesheet still prints after them.
 */
function tp_child_enqueue_assets(): void {
	wp_enqueue_style(
		'tp-site',
		TP_CHILD_URL . 'assets/css/tasche-piene.css',
		[],
		tp_child_asset_version( 'assets/css/tasche-piene.css' )
	);

	if ( tp_child_is_visual_builder() ) {
		return;
	}

	wp_enqueue_script(
		'tp-site',
		TP_CHILD_URL . 'assets/js/tasche-piene.js',
		[],
		tp_child_asset_version( 'assets/js/tasche-piene.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tp_child_enqueue_assets', 20 );

/**
 * Render the Figma-locked Tasca/Piatto section from editable Divi source modules.
 * The hidden source stays editable in Divi; frontend output keeps forced structure/styles.
 */
function tp_child_choice_default_data(): array {
	return [
		'title_lines' => [ 'Tasca o piatto.', 'Mangi qui o a casa.', 'Sta a te.' ],
		'cards'       => [
			'tasca'  => [
				'image' => TP_CHILD_URL . 'assets/images/dishes/flow-choice-tasca.webp',
				'alt'   => 'La tasca ripiena',
				'title' => 'La tasca ripiena',
				'chips' => [ 'Il format originale', '60g di pane', '100 g di ripieno' ],
				'desc'  => 'Un panino piccolo, si mangia in mano e si porta ovunque. Tante persone ne prendono due: lo diciamo subito così non ci sono sorprese.',
			],
			'piatto' => [
				'image' => TP_CHILD_URL . 'assets/images/dishes/flow-choice-piatto.webp',
				'alt'   => 'Il piatto',
				'title' => 'Il piatto',
				'chips' => [ '3 dimensioni S-M-L', 'Pane a parte', 'Include un contorno' ],
				'desc'  => 'Stesso cibo della tasca, ma al piatto. Ripieno in vaschetta, pane a parte in un sacchettino, così resta croccante. Include un contorno a scelta.',
			],
		],
	];
}

function tp_child_choice_class_predicate( string $class_name ): string {
	return "contains(concat(' ', normalize-space(@class), ' '), ' " . $class_name . " ')";
}

function tp_child_choice_xpath( string $html ): ?DOMXPath {
	if ( ! class_exists( 'DOMDocument' ) || '' === trim( $html ) ) {
		return null;
	}

	$previous = libxml_use_internal_errors( true );
	$document = new DOMDocument( '1.0', 'UTF-8' );
	$loaded   = $document->loadHTML( '<?xml encoding="utf-8" ?><div id="tp-choice-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
	libxml_clear_errors();
	libxml_use_internal_errors( $previous );

	return $loaded ? new DOMXPath( $document ) : null;
}

function tp_child_choice_first_node( DOMXPath $xpath, string $query, ?DOMNode $context = null ): ?DOMNode {
	$nodes = $xpath->query( $query, $context );

	if ( ! $nodes instanceof DOMNodeList || 0 === $nodes->length ) {
		return null;
	}

	return $nodes->item( 0 );
}

function tp_child_choice_text_with_breaks( DOMNode $node ): string {
	$text = '';

	foreach ( $node->childNodes as $child ) {
		if ( $child instanceof DOMText || $child instanceof DOMCdataSection ) {
			$text .= $child->nodeValue;
			continue;
		}

		if ( ! $child instanceof DOMElement ) {
			continue;
		}

		$tag = strtolower( $child->tagName );

		if ( 'br' === $tag ) {
			$text .= "\n";
			continue;
		}

		$is_block = in_array( $tag, [ 'p', 'div', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'li' ], true );

		if ( $is_block && '' !== trim( $text ) ) {
			$text .= "\n";
		}

		$text .= tp_child_choice_text_with_breaks( $child );

		if ( $is_block ) {
			$text .= "\n";
		}
	}

	return $text;
}

function tp_child_choice_lines_from_node( ?DOMNode $node ): array {
	if ( ! $node ) {
		return [];
	}

	$text  = str_replace( "\xc2\xa0", ' ', tp_child_choice_text_with_breaks( $node ) );
	$lines = preg_split( '/\R+/u', $text ) ?: [];
	$lines = array_map(
		static function ( string $line ): string {
			return trim( preg_replace( '/[\t ]+/u', ' ', $line ) ?? $line );
		},
		$lines
	);

	return array_values( array_filter( $lines, static fn( string $line ): bool => '' !== $line ) );
}

function tp_child_choice_text_from_node( ?DOMNode $node ): string {
	$lines = tp_child_choice_lines_from_node( $node );

	return trim( implode( ' ', $lines ) );
}

function tp_child_choice_extract_chips( DOMXPath $xpath, DOMNode $card, array $fallback ): array {
	$chips = [];
	$nodes = $xpath->query( './/*[' . tp_child_choice_class_predicate( 'tp-choice-card-chip' ) . ']', $card );

	if ( $nodes instanceof DOMNodeList && $nodes->length > 0 ) {
		foreach ( $nodes as $node ) {
			$text = tp_child_choice_text_from_node( $node );
			if ( '' !== $text ) {
				$chips[] = $text;
			}
		}

		return $chips;
	}

	$chips_node = tp_child_choice_first_node( $xpath, './/*[' . tp_child_choice_class_predicate( 'tp-choice-card-chips' ) . ']', $card );

	if ( $chips_node ) {
		$chips = tp_child_choice_lines_from_node( $chips_node );

		if ( 1 === count( $chips ) ) {
			$split = preg_split( '/\s*[|•;]+\s*/u', $chips[0] ) ?: [];
			$split = array_values( array_filter( array_map( 'trim', $split ), static fn( string $item ): bool => '' !== $item ) );
			$chips = count( $split ) > 1 ? $split : $chips;
		}

		return $chips;
	}

	return $fallback;
}

function tp_child_choice_data_from_divi_section( string $source_html ): array {
	$data  = tp_child_choice_default_data();
	$data['_extracted'] = false;
	$xpath = tp_child_choice_xpath( $source_html );

	if ( ! $xpath ) {
		return $data;
	}

	$title_node = tp_child_choice_first_node( $xpath, '//*[' . tp_child_choice_class_predicate( 'tp-choice-title' ) . ']' );

	if ( $title_node ) {
		$data['title_lines'] = tp_child_choice_lines_from_node( $title_node );
		$data['_extracted']  = true;
	}

	foreach ( [ 'tasca', 'piatto' ] as $card_key ) {
		$card = tp_child_choice_first_node( $xpath, '//*[' . tp_child_choice_class_predicate( 'tp-choice-card--' . $card_key ) . ']' );

		if ( ! $card ) {
			continue;
		}

		$data['_extracted'] = true;

		$image = tp_child_choice_first_node( $xpath, './/*[' . tp_child_choice_class_predicate( 'tp-choice-card-image' ) . ']//img', $card );
		if ( $image instanceof DOMElement ) {
			$src = $image->getAttribute( 'src' );
			$alt = $image->getAttribute( 'alt' );

			if ( '' !== $src ) {
				$data['cards'][ $card_key ]['image'] = $src;
			}

			if ( '' !== $alt ) {
				$data['cards'][ $card_key ]['alt'] = $alt;
			}
		}

		$title_node = tp_child_choice_first_node( $xpath, './/*[' . tp_child_choice_class_predicate( 'tp-choice-card-title' ) . ']', $card );
		if ( $title_node ) {
			$data['cards'][ $card_key ]['title'] = tp_child_choice_text_from_node( $title_node );
		}

		$data['cards'][ $card_key ]['chips'] = tp_child_choice_extract_chips( $xpath, $card, $data['cards'][ $card_key ]['chips'] );

		$description_node = tp_child_choice_first_node( $xpath, './/*[' . tp_child_choice_class_predicate( 'tp-choice-card-description' ) . ']', $card );
		if ( $description_node ) {
			$data['cards'][ $card_key ]['desc'] = tp_child_choice_text_from_node( $description_node );
		}
	}

	return $data;
}

/**
 * Attributi responsive (srcset + sizes) per un'immagine da URL upload.
 * Usa le varianti generate da WordPress (esistono anche sul sito utente) cosi'
 * il browser carica la misura giusta invece dell'originale 1600px: su iPhone 11
 * ~768px (~1.5MB decodificati) invece di 6.8MB -> niente piu' immagini che non
 * caricano per pressione memoria. Se l'URL non e' un attachment -> stringa vuota
 * (fallback all'img semplice). */
function tp_child_responsive_img_attrs( string $url, string $sizes ): string {
	if ( '' === $url || ! function_exists( 'attachment_url_to_postid' ) ) {
		return '';
	}
	$id = attachment_url_to_postid( $url );
	if ( ! $id ) {
		return '';
	}
	$srcset = wp_get_attachment_image_srcset( $id, 'large' );
	if ( ! $srcset ) {
		return '';
	}
	return sprintf( ' srcset="%s" sizes="%s"', esc_attr( $srcset ), esc_attr( $sizes ) );
}

function tp_child_choice_card_markup( string $modifier, array $card ): string {
	$chips = '';

	foreach ( $card['chips'] as $chip ) {
		$chips .= sprintf( '<li><span class="chip__icon" aria-hidden="true">◆</span>%s</li>', esc_html( $chip ) );
	}

	$img_extra = tp_child_responsive_img_attrs( $card['image'], '(max-width: 767px) 90vw, 640px' );

	return sprintf(
		'<article class="choice-card choice-card--%1$s" tabindex="0"><div class="choice-card__image"><img src="%2$s" alt="%3$s" loading="lazy" decoding="async"%7$s /></div><div class="choice-card__content"><h3 class="choice-card__title">%4$s</h3><ul class="choice-card__chips">%5$s</ul><p class="choice-card__desc">%6$s</p></div></article>',
		esc_attr( $modifier ),
		esc_url( $card['image'] ),
		esc_attr( $card['alt'] ),
		esc_html( $card['title'] ),
		$chips,
		esc_html( $card['desc'] ),
		$img_extra
	);
}

function tp_child_choice_normalize_data( array $data ): array {
	$normalized = tp_child_choice_default_data();

	if ( array_key_exists( 'title_lines', $data ) && is_array( $data['title_lines'] ) ) {
		$normalized['title_lines'] = array_values( array_map( 'strval', $data['title_lines'] ) );
	}

	if ( ! isset( $data['cards'] ) || ! is_array( $data['cards'] ) ) {
		return $normalized;
	}

	foreach ( [ 'tasca', 'piatto' ] as $card_key ) {
		if ( ! isset( $data['cards'][ $card_key ] ) || ! is_array( $data['cards'][ $card_key ] ) ) {
			continue;
		}
		foreach ( [ 'image', 'alt', 'title', 'desc' ] as $field ) {
			if ( array_key_exists( $field, $data['cards'][ $card_key ] ) ) {
				$normalized['cards'][ $card_key ][ $field ] = (string) $data['cards'][ $card_key ][ $field ];
			}
		}

		if ( array_key_exists( 'chips', $data['cards'][ $card_key ] ) && is_array( $data['cards'][ $card_key ]['chips'] ) ) {
			$normalized['cards'][ $card_key ]['chips'] = array_values( array_map( 'strval', $data['cards'][ $card_key ]['chips'] ) );
		}
	}

	return $normalized;
}

function tp_child_choice_standalone_markup( array $data = [] ): string {
	$data = tp_child_choice_normalize_data( $data );

	$title_html = implode(
		"\n\t\t",
		array_map(
			static fn( string $line ): string => '<span>' . esc_html( $line ) . '</span>',
			$data['title_lines']
		)
	);
	$tasca_card = tp_child_choice_card_markup( 'tasca', $data['cards']['tasca'] );
	$piatto_card = tp_child_choice_card_markup( 'piatto', $data['cards']['piatto'] );
	$mobile_green_wave_url = esc_url( get_stylesheet_directory_uri() . '/assets/svg/choice-mobile-green-wave.svg' );
	$mobile_bordeaux_wave_url = esc_url( get_stylesheet_directory_uri() . '/assets/svg/choice-mobile-bordeaux-wave.svg' );

	return <<<HTML
<section id="choice" class="choice tp-choice-standalone" data-section="choice" data-y="2607" data-h="1263" aria-label="Tasca o piatto">
	<div class="choice__stage">
	<svg class="choice__wave choice__wave--green" viewBox="0 0 1811.7627 428.3477" preserveAspectRatio="xMinYMin meet" aria-hidden="true" focusable="false">
		<path d="M999.447 217.19C1300.56 148.011 1608.33 129.375 1724.58 128.705L1811.76 178.981L1711.82 428.348L0 428.348L0 281.543L0 0C207.686 101.221 698.337 286.369 999.447 217.19Z" fill="#054726" />
	</svg>
	<svg class="choice__wave choice__wave--bordeaux" viewBox="0 0 1524.4385 284.7612" preserveAspectRatio="xMinYMin meet" aria-hidden="true" focusable="false">
		<path d="M706.125 113.179C339.204 10.2306 82.4911 -4.50822 0 0.990986L9.89911 284.761L1524.44 284.761L1524.44 202.27C1404.55 215.469 1073.05 216.128 706.125 113.179Z" fill="#560B22" />
	</svg>
	<img class="choice__mobile-wave choice__mobile-wave--green-top" src="{$mobile_green_wave_url}" alt="" aria-hidden="true" decoding="async" loading="eager" />
	<img class="choice__mobile-wave choice__mobile-wave--bordeaux-top" src="{$mobile_bordeaux_wave_url}" alt="" aria-hidden="true" decoding="async" loading="eager" />
	<img class="choice__mobile-wave choice__mobile-wave--green-bottom" src="{$mobile_green_wave_url}" alt="" aria-hidden="true" decoding="async" loading="eager" />
	<img class="choice__mobile-wave choice__mobile-wave--bordeaux-bottom" src="{$mobile_bordeaux_wave_url}" alt="" aria-hidden="true" decoding="async" loading="eager" />
	<h2 class="choice__title">
		{$title_html}
	</h2>
	<div class="choice__cards">
		{$tasca_card}
		{$piatto_card}
	</div>
	</div>
</section>
HTML;
}

function tp_child_replace_choice_section( string $content ): string {
	if ( tp_child_is_visual_builder() || false === strpos( $content, "tp-choice-section" ) || false !== strpos( $content, "tp-choice-standalone" ) ) {
		return $content;
	}

	$pattern       = "/<div class=\"[^\"]*\btp-choice-section\b[^\"]*\"[^>]*>.*?(?=<div class=\"[^\"]*\btp-menu-section\b)/s";
	$choice_source = '';

	if ( preg_match( $pattern, $content, $matches ) ) {
		$choice_source = $matches[0];
	}

	$data      = tp_child_choice_data_from_divi_section( $choice_source );
	$extracted = ! empty( $data['_extracted'] );
	unset( $data['_extracted'] );

	// Hardening (2026-06-08): only swap in the pixel-perfect standalone when we
	// actually matched the choice section AND extracted its editable content from
	// the Divi modules. Otherwise leave the editable Divi .tp-choice-section on
	// the frontend instead of emitting a defaults-based standalone that would
	// silently ignore client edits made in the Visual Builder.
	if ( '' === $choice_source || ! $extracted ) {
		return $content;
	}

	// A callback, so "$1" or "\1" written in the editorial text is kept as text.
	$replacement = tp_child_choice_standalone_markup( $data ) . "\n";
	$updated     = preg_replace_callback(
		$pattern,
		static function () use ( $replacement ): string {
			return $replacement;
		},
		$content,
		1,
		$replaced
	);

	if ( is_string( $updated ) && $replaced > 0 ) {
		return $updated;
	}

	// Replacement failed unexpectedly: keep the editable Divi source visible
	// rather than hiding it behind an unverified standalone.
	return $content;
}
add_filter( "the_content", "tp_child_replace_choice_section", 99 );

/**
 * Load the GSAP preloader runtime on landing pages.
 */
function tp_child_enqueue_preloader_assets(): void {
	if ( ! tp_child_should_show_preloader() ) {
		return;
	}

	wp_enqueue_script( 'gsap-core', TP_CHILD_URL . 'assets/vendor/gsap.min.js', [], '3.12.7', true );

	wp_enqueue_script(
		'tp-preloader',
		TP_CHILD_URL . 'assets/js/tp-preloader.js',
		[ 'gsap-core' ],
		tp_child_asset_version( 'assets/js/tp-preloader.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tp_child_enqueue_preloader_assets', 32 );

/**
 * Register the Visual Builder bundle for child-theme modules. The stylesheet
 * reaches the builder preview through tp_child_enqueue_assets().
 */
function tp_child_register_vb_package_builds(): void {
	if ( ! function_exists( 'et_builder_d5_enabled' ) || ! et_builder_d5_enabled() ) {
		return;
	}

	if ( ! class_exists( '\\ET\\Builder\\VisualBuilder\\Assets\\PackageBuildManager' ) ) {
		return;
	}

	\ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
		[
			'name'    => 'tp-child-builder-bundle-script',
			'version' => tp_child_asset_version( 'scripts/bundle.js' ),
			'script'  => [
				'src'                => TP_CHILD_URL . 'scripts/bundle.js',
				'deps'               => [ 'divi-module-library', 'divi-vendor-wp-hooks' ],
				'enqueue_top_window' => false,
				'enqueue_app_window' => true,
			],
		]
	);
}

function tp_child_enqueue_vb_assets(): void {
	if ( ! function_exists( 'et_core_is_fb_enabled' ) || ! et_core_is_fb_enabled() ) {
		return;
	}

	tp_child_register_vb_package_builds();
}

/**
 * Expose WordPress menu items to the Visual Builder chrome preview.
 */
function tp_child_localize_vb_chrome_menus(): void {
	if ( ! function_exists( 'et_core_is_fb_enabled' ) || ! et_core_is_fb_enabled() ) {
		return;
	}

	wp_register_script( 'tp-chrome-menus-vb', false, [], TP_CHILD_VERSION, true );
	wp_enqueue_script( 'tp-chrome-menus-vb' );
	wp_add_inline_script(
		'tp-chrome-menus-vb',
		'window.tpChromeMenus = ' . wp_json_encode( tp_child_chrome_collect_vb_menus() ) . ';',
		'before'
	);
}
add_action( 'et_fb_framework_loaded', 'tp_child_register_vb_package_builds', 20 );
add_action( 'divi_visual_builder_assets_before_enqueue_scripts', 'tp_child_enqueue_vb_assets' );
add_action( 'divi_visual_builder_assets_before_enqueue_scripts', 'tp_child_localize_vb_chrome_menus', 5 );

/**
 * Load SplitText for the landing title reveals. The custom module is optional;
 * native Divi modules are animated by stable `.tp-*` classes.
 */
function tp_child_enqueue_split_text_assets(): void {
	$has_split_module = tp_child_has_block( 'tp/split-text' );
	$has_split_targets = $has_split_module
		|| tp_child_content_has_marker( 'tp-hero-section' )
		|| tp_child_content_has_marker( 'tp-intro-title' )
		|| tp_child_content_has_marker( 'tp-alberto-title' )
		|| tp_child_content_has_marker( 'tp-choice-title' )
		|| tp_child_content_has_marker( 'tp-menu-title' )
		|| tp_child_content_has_marker( 'tp-materie-title' )
		|| tp_child_content_has_marker( 'tp-location-title' );

	if ( tp_child_is_visual_builder() || ! $has_split_targets ) {
		return;
	}

	wp_enqueue_script( 'gsap-core', TP_CHILD_URL . 'assets/vendor/gsap.min.js', [], '3.12.7', true );
	wp_enqueue_script( 'gsap-splittext', TP_CHILD_URL . 'assets/vendor/SplitText.min.js', [ 'gsap-core' ], '3.12.7', true );

	wp_enqueue_script(
		'tp-split-text-runtime',
		TP_CHILD_URL . 'assets/js/tp-split-text.js',
		[ 'gsap-core', 'gsap-splittext' ],
		tp_child_asset_version( 'assets/js/tp-split-text.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tp_child_enqueue_split_text_assets', 30 );

/**
 * Load keyboard focus support for the Tasca/Piatto choice cards.
 */
function tp_child_enqueue_choice_card_assets(): void {
	if ( tp_child_is_visual_builder() || ! tp_child_content_has_marker( 'tp-choice-section' ) ) {
		return;
	}

	wp_enqueue_script(
		'tp-choice-cards',
		TP_CHILD_URL . 'assets/js/tp-choice-cards.js',
		[ 'tp-site' ],
		tp_child_asset_version( 'assets/js/tp-choice-cards.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tp_child_enqueue_choice_card_assets', 35 );

/**
 * Load GSAP hover runtime only for the editable menu rows.
 */
function tp_child_enqueue_menu_assets(): void {
	if ( tp_child_is_visual_builder() || ! tp_child_content_has_marker( 'tp-menu-section' ) ) {
		return;
	}

	wp_enqueue_script( 'gsap-core', TP_CHILD_URL . 'assets/vendor/gsap.min.js', [], '3.12.7', true );

	wp_enqueue_script(
		'tp-menu',
		TP_CHILD_URL . 'assets/js/tp-menu.js',
		[ 'gsap-core' ],
		tp_child_asset_version( 'assets/js/tp-menu.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tp_child_enqueue_menu_assets', 36 );

/**
 * Load GSAP reveal/hover runtime only for the Materie Prime cards.
 */
function tp_child_enqueue_materie_assets(): void {
	if ( tp_child_is_visual_builder() || ! tp_child_content_has_marker( 'tp-materie-section' ) ) {
		return;
	}

	wp_enqueue_script( 'gsap-core', TP_CHILD_URL . 'assets/vendor/gsap.min.js', [], '3.12.7', true );

	wp_enqueue_script(
		'tp-materie',
		TP_CHILD_URL . 'assets/js/tp-materie.js',
		[ 'gsap-core', 'tp-site' ],
		tp_child_asset_version( 'assets/js/tp-materie.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'tp_child_enqueue_materie_assets', 37 );

/**
 * Small body markers for scoped CSS/debugging.
 */
function tp_child_body_classes( array $classes ): array {
	$classes[] = 'tp-child-theme';

	if ( tp_child_is_landing_context() ) {
		$classes[] = 'tp-landing-page';
	}

	if ( tp_child_should_show_preloader() ) {
		$classes[] = 'tp-preloader-page';
	}

	if ( tp_child_is_visual_builder() && ! in_array( 'et-fb', $classes, true ) ) {
		$classes[] = 'et-fb';
	}

	return $classes;
}
add_filter( 'body_class', 'tp_child_body_classes' );

/**
 * Responsive a11y fix (2026-06-07): allow pinch-zoom on iOS / all devices.
 *
 * Divi core (web/wp-content/themes/Divi/functions.php) prints a viewport meta
 * with `maximum-scale=1.0, user-scalable=0`, which disables pinch-zoom and
 * fails WCAG 1.4.4. Override it from the child theme without editing the
 * parent: remove Divi's wp_head callback and print our own at priority 1.
 */
function tp_child_viewport_meta() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1.0" />' . "\n";
}
add_action( 'init', function () {
	remove_action( 'wp_head', 'et_add_viewport_meta' );
} );
add_action( 'wp_head', 'tp_child_viewport_meta', 1 );

/**
 * Front-end layout helpers in assets/js/tp-layout.js: scales of the 1512px stages
 * and of the mobile menu card, mobile hero video, Alberto mobile flow, menu
 * allergens. Printed where they used to be inline, before jQuery and Divi's
 * scripts, so each part still runs on DOMContentLoaded ahead of them.
 */
function tp_child_print_layout_script(): void {
	if ( is_admin() || tp_child_is_visual_builder() ) {
		return;
	}

	wp_register_script( 'tp-layout', TP_CHILD_URL . 'assets/js/tp-layout.js', [], tp_child_asset_version( 'assets/js/tp-layout.js' ), true );
	wp_add_inline_script(
		'tp-layout',
		'window.tpHeroVideo = ' . wp_json_encode(
			[
				'mp4'    => esc_url( TP_CHILD_URL . 'assets/video/videodefinitivo.mp4' ),
				'webm'   => esc_url( TP_CHILD_URL . 'assets/video/videodefinitivo.webm' ),
				'poster' => esc_url( TP_CHILD_URL . 'assets/images/hero-poster.webp' ),
			]
		) . ';',
		'before'
	);
	wp_print_scripts( 'tp-layout' );
}
add_action( 'wp_footer', 'tp_child_print_layout_script' );

/**
 * Visual Builder: scale the modular 1512x982 footer canvas to the real preview width.
 * 100vw is wrong in VB because the canvas is narrower than the browser window.
 * The builder renders (and re-renders) the footer after load: a light observer
 * tracks which footer node is on the page, a ResizeObserver reads its width only
 * when it changes.
 */
function tp_child_vb_footer_scale_script() {
	if ( ! tp_child_is_visual_builder() ) {
		return;
	}
	?>
<script id="tp-vb-footer-scale-js">
(function(){
	var section = null;
	var resizer = typeof ResizeObserver !== 'undefined' ? new ResizeObserver(upd) : null;
	function upd(){
		var root = document.documentElement;
		if (!section) {
			root.style.removeProperty('--tp-vb-footer-scale');
			return;
		}
		var w = section.clientWidth || 0;
		var scale = w > 0 ? Math.min(1, w / 1512) : 1;
		root.style.setProperty('--tp-vb-footer-scale', scale.toFixed(6));
	}
	function track(){
		var current = document.querySelector('.tp-theme-builder-footer-shell');
		if (current === section) {
			return;
		}
		if (section && resizer) {
			resizer.unobserve(section);
		}
		section = current;
		if (section && resizer) {
			resizer.observe(section, { box: 'border-box' });
		}
		upd();
	}
	function boot(){
		track();
		if (!resizer) {
			window.addEventListener('resize', upd, { passive: true });
		}
		if (typeof MutationObserver !== 'undefined') {
			new MutationObserver(track).observe(document.documentElement, { childList: true, subtree: true, attributes: true, attributeFilter: ['class'] });
		}
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
</script>
	<?php
}
add_action( 'wp_footer', 'tp_child_vb_footer_scale_script', 5 );
