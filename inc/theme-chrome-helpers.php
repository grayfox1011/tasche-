<?php
/**
 * Shared helpers for Theme Builder header/footer custom modules.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read a scalar module attribute from Divi 5 attrs.
 */
function tp_child_chrome_get_string( array $attrs, string $key, string $fallback = '' ): string {
	$value = $attrs[ $key ]['innerContent']['desktop']['value']
		?? $attrs[ $key ]['desktop']['value']
		?? $fallback;

	if ( is_array( $value ) || is_object( $value ) ) {
		return $fallback;
	}

	$value = trim( (string) $value );

	return '' !== $value ? $value : $fallback;
}

/**
 * Resolve an uploaded image URL with legacy text URL and theme default fallbacks.
 */
function tp_child_chrome_get_image_url( array $attrs, string $upload_key, string $legacy_key, string $default_url ): string {
	$raw = $attrs[ $upload_key ]['innerContent']['desktop']['value']
		?? $attrs[ $upload_key ]['desktop']['value']
		?? '';

	$uploaded = '';

	if ( is_string( $raw ) ) {
		$uploaded = trim( $raw );
	} elseif ( is_array( $raw ) ) {
		$uploaded = trim( (string) ( $raw['src'] ?? $raw['url'] ?? '' ) );
	}

	$legacy = tp_child_chrome_get_string( $attrs, $legacy_key, '' );
	$url    = $uploaded ?: ( $legacy ?: $default_url );

	return esc_url( $url );
}

/**
 * Build an inline style string from CSS custom properties.
 */
function tp_child_chrome_style_from_vars( array $vars ): string {
	$parts = [];

	foreach ( $vars as $property => $value ) {
		$value = trim( (string) $value );

		if ( '' === $value || ! is_string( $property ) || ! preg_match( '/^--[a-z0-9-]+$/', $property ) ) {
			continue;
		}

		$parts[] = $property . ':' . $value;
	}

	$parts = array_filter( $parts );

	return $parts ? implode( ';', $parts ) : '';
}

/**
 * Collect labeled links stored as paired text/link fields on the module.
 */
function tp_child_chrome_collect_link_fields( array $attrs, string $prefix, int $count ): array {
	$items = [];

	for ( $index = 1; $index <= $count; $index++ ) {
		$text = tp_child_chrome_get_string( $attrs, "{$prefix}{$index}Text", '' );
		$link = tp_child_chrome_get_string( $attrs, "{$prefix}{$index}Link", '' );

		if ( '' === $text ) {
			continue;
		}

		$items[] = [
			'title'  => $text,
			'url'    => '' !== $link ? $link : '#',
			'target' => '',
			'xfn'    => '',
		];
	}

	return $items;
}

/**
 * Render nav items from a WordPress menu location.
 */
function tp_child_chrome_menu_location_items( string $location ): array {
	$locations = get_nav_menu_locations();

	if ( empty( $locations[ $location ] ) ) {
		return [];
	}

	$menu_items = wp_get_nav_menu_items( (int) $locations[ $location ] );

	if ( ! is_array( $menu_items ) ) {
		return [];
	}

	return array_values(
		array_filter(
			$menu_items,
			static function ( $item ): bool {
				return $item instanceof WP_Post && 0 === (int) $item->menu_item_parent;
			}
		)
	);
}

/**
 * Build header/footer nav items: WordPress menu first, legacy module fields as fallback.
 */
function tp_child_chrome_resolve_nav_items( array $attrs, string $prefix, int $count, string $fallback_location, array $default_items ): array {
	$wp_items = tp_child_chrome_menu_location_items( $fallback_location );

	if ( ! empty( $wp_items ) ) {
		return $wp_items;
	}

	$custom_items = tp_child_chrome_collect_link_fields( $attrs, $prefix, $count );

	if ( ! empty( $custom_items ) ) {
		return array_map(
			static function ( array $item ): object {
				return (object) $item;
			},
			$custom_items
		);
	}

	return array_map(
		static function ( array $item ): object {
			return (object) $item;
		},
		$default_items
	);
}

/**
 * Serialize nav items for the Visual Builder preview.
 */
function tp_child_chrome_nav_items_as_arrays( string $location ): array {
	$items = tp_child_chrome_menu_location_items( $location );

	if ( empty( $items ) && function_exists( 'tp_child_nav_menu_defaults' ) ) {
		$items = array_map(
			static function ( array $item ): object {
				return (object) $item;
			},
			tp_child_nav_menu_defaults( $location )
		);
	}

	$serialized = [];

	foreach ( $items as $item ) {
		$serialized[] = [
			'title'  => isset( $item->title ) ? (string) $item->title : '',
			'url'    => tp_child_resolve_nav_url( isset( $item->url ) ? (string) $item->url : '#' ),
			'target' => isset( $item->target ) ? (string) $item->target : '',
		];
	}

	return $serialized;
}

/**
 * All theme menu locations exposed to the Visual Builder bundle.
 */
function tp_child_chrome_collect_vb_menus(): array {
	$locations = [ 'tp_main_nav', 'tp_footer_left', 'tp_footer_right' ];
	$menus     = [];

	foreach ( $locations as $location ) {
		$menus[ $location ] = tp_child_chrome_nav_items_as_arrays( $location );
	}

	return $menus;
}

/**
 * Canonical front page URL.
 */
function tp_child_home_url(): string {
	return user_trailingslashit( home_url( '/' ) );
}

/**
 * Canonical hero anchor used by Home / Torna su.
 */
function tp_child_hero_anchor(): string {
	return '#home';
}

/**
 * Full URL that lands on the homepage hero from any page.
 */
function tp_child_hero_url(): string {
	return tp_child_home_url() . '#home';
}

/**
 * Whether a URL points at the homepage root (not a section anchor on the home).
 */
function tp_child_is_home_url( string $url ): bool {
	$url  = trim( $url );
	$home = untrailingslashit( tp_child_home_url() );

	if ( str_contains( $url, '#' ) ) {
		$parts    = explode( '#', $url, 2 );
		$base     = untrailingslashit( $parts[0] );
		$fragment = isset( $parts[1] ) ? trim( $parts[1] ) : '';

		if ( $base !== $home ) {
			return false;
		}

		return '' === $fragment || 'home' === $fragment;
	}

	return untrailingslashit( $url ) === $home;
}

/**
 * Extract a section anchor from a URL, if present.
 */
function tp_child_nav_url_anchor( string $url ): string {
	$url = trim( $url );

	if ( str_starts_with( $url, '#' ) ) {
		return $url;
	}

	$home = tp_child_home_url();

	if ( str_starts_with( $url, $home . '#' ) ) {
		return substr( $url, strlen( $home ) );
	}

	return '';
}

/**
 * Compare nav URLs regardless of absolute vs hash form.
 */
function tp_child_nav_urls_equivalent( string $a, string $b ): bool {
	$a = trim( $a );
	$b = trim( $b );

	if ( $a === $b ) {
		return true;
	}

	$normalize = static function ( string $url ): string {
		$url = trim( $url );

		$anchor = tp_child_nav_url_anchor( $url );

		if ( '' !== $anchor && '#home' !== $anchor ) {
			return $anchor;
		}

		if ( '' === $url || '#' === $url || '#home' === $url || tp_child_is_home_url( $url ) ) {
			return '#home';
		}

		return $url;
	};

	return $normalize( $a ) === $normalize( $b );
}

/**
 * Turn in-page anchors into absolute home URLs so links work from any page.
 */
function tp_child_resolve_nav_url( string $url ): string {
	$url = trim( $url );

	if ( '' === $url || '#' === $url || '#home' === $url || tp_child_is_home_url( $url ) ) {
		return is_front_page() ? tp_child_hero_anchor() : tp_child_hero_url();
	}

	$anchor = tp_child_nav_url_anchor( $url );

	if ( '' !== $anchor ) {
		return is_front_page() ? $anchor : tp_child_home_url() . $anchor;
	}

	return $url;
}

/**
 * Match a menu item URL to the default label for its location.
 */
function tp_child_nav_menu_title_from_defaults( string $location, string $url ): string {
	$url = trim( $url );

	foreach ( tp_child_nav_menu_defaults( $location ) as $item ) {
		if ( tp_child_nav_urls_equivalent( $url, $item['url'] ) ) {
			return $item['title'];
		}
	}

	return '';
}

/**
 * Render a list of nav links for the header.
 */
function tp_child_chrome_render_header_links( array $items ): string {
	$links = '';

	foreach ( $items as $item ) {
		$title  = isset( $item->title ) ? (string) $item->title : '';
		$url    = tp_child_resolve_nav_url( isset( $item->url ) ? (string) $item->url : '#' );
		$target = isset( $item->target ) ? (string) $item->target : '';
		$rel    = isset( $item->xfn ) ? trim( (string) $item->xfn ) : '';

		if ( '_blank' === $target && '' === $rel ) {
			$rel = 'noopener';
		}

		$links .= sprintf(
			'<li><a href="%1$s"%2$s%3$s><span class="tp-nav-link-text">%4$s</span></a></li>',
			esc_url( $url ),
			$target ? ' target="' . esc_attr( $target ) . '"' : '',
			$rel ? ' rel="' . esc_attr( $rel ) . '"' : '',
			esc_html( $title )
		);
	}

	return $links;
}

/**
 * Render a footer nav column.
 */
function tp_child_chrome_render_footer_column( array $items ): string {
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
 * Render a copyright line with optional link.
 */
function tp_child_chrome_render_copyright_line( string $text, string $link ): string {
	$text = esc_html( $text );

	if ( '' !== $link ) {
		return '<a href="' . esc_url( $link ) . '">' . $text . '</a>';
	}

	return $text;
}

/**
 * Seed helper for paired text/link attrs.
 */
function tp_child_chrome_seed_link_pair( string $text, string $link ): array {
	return [
		'innerContent' => [
			'desktop' => [
				'value' => $text,
			],
		],
	];
}

function tp_child_chrome_seed_link_url( string $url ): array {
	return [
		'innerContent' => [
			'desktop' => [
				'value' => $url,
			],
		],
	];
}

/**
 * Read the uploaded image src from a Divi image attribute.
 */
function tp_child_chrome_get_image_src_from_attr( array $attrs, string $upload_key ): string {
	$raw = $attrs[ $upload_key ]['innerContent']['desktop']['value']
		?? $attrs[ $upload_key ]['desktop']['value']
		?? '';

	if ( is_string( $raw ) ) {
		return trim( $raw );
	}

	if ( is_array( $raw ) ) {
		return trim( (string) ( $raw['src'] ?? $raw['url'] ?? '' ) );
	}

	return '';
}

/**
 * Build a Divi image attribute value for seeding or migration.
 */
function tp_child_chrome_seed_image_value( string $url ): array {
	return [
		'innerContent' => [
			'desktop' => [
				'value' => [
					'src' => $url,
					'id'  => 0,
				],
			],
		],
	];
}

/**
 * Copy legacy URL fields into upload src fields when src is still empty.
 */
function tp_child_chrome_migrate_image_attrs( array $attrs, array $definitions ): array {
	foreach ( $definitions as $definition ) {
		$upload_key = $definition[0];
		$legacy_key = $definition[1];
		$default    = $definition[2] ?? '';

		if ( '' !== tp_child_chrome_get_image_src_from_attr( $attrs, $upload_key ) ) {
			continue;
		}

		$legacy = tp_child_chrome_get_string( $attrs, $legacy_key, '' );
		$src    = $legacy ?: $default;

		if ( '' === $src ) {
			continue;
		}

		$attrs[ $upload_key ]['innerContent']['desktop']['value'] = [
			'src' => $src,
			'id'  => 0,
		];
		$attrs[ $legacy_key ]['innerContent']['desktop']['value'] = '';
	}

	return $attrs;
}

/**
 * Whether rendered image markup includes a non-empty src attribute.
 */
function tp_child_chrome_image_html_has_src( string $html ): bool {
	if ( '' === trim( $html ) ) {
		return false;
	}

	if ( preg_match( '/\ssrc=(["\'])([^"\']*)\1/i', $html, $matches ) ) {
		return '' !== trim( $matches[2] );
	}

	return false;
}

/**
 * Build a plain image tag for chrome fallbacks.
 */
function tp_child_chrome_build_image_tag( string $url, array $attributes = [] ): string {
	if ( '' === trim( $url ) ) {
		return '';
	}

	$render_attributes = array_merge(
		[
			'src' => $url,
			'alt' => '',
		],
		$attributes
	);

	$attribute_string = '';

	foreach ( $render_attributes as $name => $value ) {
		if ( '' === (string) $value && 'alt' !== $name ) {
			continue;
		}

		$attribute_string .= sprintf( ' %s="%s"', esc_attr( (string) $name ), esc_attr( (string) $value ) );
	}

	return '<img' . $attribute_string . '>';
}

/**
 * Render a chrome image via Divi elements with theme fallbacks on the frontend.
 *
 * @param ModuleElements $elements Module elements instance.
 */
function tp_child_chrome_render_image_element( $elements, array $attrs, string $attr_name, string $legacy_key, string $default_url, array $attributes = [] ): string {
	$html = $elements->render(
		[
			'attrName'   => $attr_name,
			'attributes' => $attributes,
		]
	);

	if ( tp_child_chrome_image_html_has_src( $html ) ) {
		return $html;
	}

	$uploaded = tp_child_chrome_get_image_src_from_attr( $attrs, $attr_name );
	$legacy   = tp_child_chrome_get_string( $attrs, $legacy_key, '' );
	$url      = $uploaded ?: $legacy ?: $default_url;

	return tp_child_chrome_build_image_tag( $url, $attributes );
}

/**
 * Render a footer character inside a positioned slot wrapper.
 *
 * @param ModuleElements $elements Module elements instance.
 */
function tp_child_chrome_render_footer_character_slot(
	$elements,
	array $attrs,
	string $attr_name,
	string $legacy_key,
	string $default_url,
	string $slot_modifier,
	array $image_attributes = []
): string {
	$url = tp_child_chrome_get_image_url( $attrs, $attr_name, $legacy_key, $default_url );

	if ( '' === $url ) {
		return '';
	}

	$image_html = tp_child_chrome_build_image_tag(
		$url,
		array_merge(
			[
				'class' => 'tp-theme-footer-character',
			],
			$image_attributes
		)
	);

	return sprintf(
		'<div class="tp-theme-footer-character-slot tp-theme-footer-character-slot--%1$s">%2$s</div>',
		esc_attr( $slot_modifier ),
		$image_html
	);
}
