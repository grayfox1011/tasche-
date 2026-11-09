<?php
/**
 * Footer fragment renderers used by the tp/theme-footer module.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Whether the characters of a Footer sito are its Personaggio elements (0.6.8): it has some, or it was made or
 * converted with them (the hidden attribute characterItems, which stays when the last one is removed, so that
 * a footer without characters stays without). The others still show the theme's three places as before
 * (tp_child_footer_render_characters_html()) until tp_child_migrate_footer_characters_068() converts them.
 *
 * @param array $parsed_block The block of Footer sito, as parsed (attributes saved, without Divi's defaults).
 */
function tp_child_footer_characters_as_items( array $parsed_block ): bool {
	if ( 'on' === tp_child_chrome_get_string( is_array( $parsed_block['attrs'] ?? null ) ? $parsed_block['attrs'] : [], 'characterItems', '' ) ) {
		return true;
	}

	foreach ( $parsed_block['innerBlocks'] ?? [] as $inner_block ) {
		if ( 'tp/footer-character' === ( $inner_block['blockName'] ?? '' ) ) {
			return true;
		}
	}

	return false;
}

/**
 * The Personaggi of a Footer sito, printed by Divi (their content: the Voci print nothing), in the box of
 * the characters.
 */
function tp_child_footer_render_character_items_html( string $content ): string {
	return ET\Builder\Framework\Utility\HTMLUtility::render(
		[
			'tag'               => 'div',
			'attributes'        => [ 'class' => 'tp-theme-footer-characters' ],
			'childrenSanitizer' => 'et_core_esc_previously',
			'children'          => trim( $content ),
		]
	);
}

/**
 * Footer characters block of the footers made before 0.6.8 (the theme's three places).
 *
 * @param ModuleElements $elements Module elements instance.
 */
function tp_child_footer_render_characters_html( array $attrs, $elements ): string {
	$asset_base = trailingslashit( TP_CHILD_URL ) . 'assets/';

	$person_one = tp_child_chrome_render_footer_character_slot(
		$elements,
		$attrs,
		'characterOneImage',
		'characterOneUrl',
		$asset_base . 'images/footer/personaggi-1.webp',
		'one',
		[
			'aria-hidden' => 'true',
			'loading'     => 'lazy',
		]
	);

	$person_four = tp_child_chrome_render_footer_character_slot(
		$elements,
		$attrs,
		'characterFourImage',
		'characterFourUrl',
		$asset_base . 'images/footer/personaggi-4.webp',
		'four',
		[
			'aria-hidden' => 'true',
			'loading'     => 'lazy',
		]
	);

	// The third place has no character since 0.5.9 (the Figma has the couple and the pigeons only): it shows the
	// image set in Divi, if any.
	$person_three = tp_child_chrome_render_footer_character_slot(
		$elements,
		$attrs,
		'characterThreeImage',
		'characterThreeUrl',
		'',
		'three',
		[
			'aria-hidden' => 'true',
			'loading'     => 'lazy',
		]
	);

	return ET\Builder\Framework\Utility\HTMLUtility::render(
		[
			'tag'               => 'div',
			'attributes'        => [ 'class' => 'tp-theme-footer-characters' ],
			'childrenSanitizer' => 'et_core_esc_previously',
			'children'          => $person_one . $person_four . $person_three,
		]
	);
}

/**
 * Footer logo block.
 *
 * @param ModuleElements $elements Module elements instance.
 */
function tp_child_footer_render_logo_html( array $attrs, $elements ): string {
	$asset_base = trailingslashit( TP_CHILD_URL ) . 'assets/';
	$logo_link  = esc_url( tp_child_resolve_nav_url( tp_child_chrome_get_string( $attrs, 'logoLink', '#home' ) ) );

	$logo_html = tp_child_chrome_render_image_element(
		$elements,
		$attrs,
		'logoImage',
		'logoUrl',
		$asset_base . 'images/logo-footer.webp',
		[
			'alt' => tp_child_chrome_get_string( $attrs, 'logoAlt', 'Tasche Piene' ),
		]
	);

	return ET\Builder\Framework\Utility\HTMLUtility::render(
		[
			'tag'               => 'div',
			'attributes'        => [ 'class' => 'tp-theme-footer-logo' ],
			'childrenSanitizer' => 'et_core_esc_previously',
			'children'          => ET\Builder\Framework\Utility\HTMLUtility::render(
				[
					'tag'               => 'a',
					'attributes'        => [ 'href' => $logo_link ],
					'childrenSanitizer' => 'et_core_esc_previously',
					'children'          => $logo_html,
				]
			),
		]
	);
}

/**
 * Link of the Privacy or Cookie line of a Footer sito made anew in the Theme Builder: the module has no link
 * saved yet (only Divi's empty default), so it points to the theme's legal page, as the footer the theme
 * creates does. A link the owner set (or emptied) in Divi is kept as it is; without the page there is no link.
 * $saved are the attributes saved in the layout, without Divi's defaults.
 */
function tp_child_footer_legal_link( array $attrs, string $key, string $type ): string {
	if ( isset( $attrs[ $key ] ) || ! function_exists( 'tp_child_legal_page_slugs' ) ) {
		return '';
	}

	$slug = tp_child_legal_page_slugs()[ $type ] ?? '';
	$page = '' !== $slug ? get_page_by_path( $slug, OBJECT, 'page' ) : null;

	return $page instanceof WP_Post && 'publish' === $page->post_status ? (string) get_permalink( $page ) : '';
}

/**
 * Footer copyright block.
 */
function tp_child_footer_render_copyright_html( array $attrs, ?array $saved = null ): string {
	$saved = $saved ?? $attrs;
	$lines = [
		[ 1, tp_child_chrome_get_string( $attrs, 'copyrightLine1', 'Copyright 2026, Tasche piene tutti i diritti riservati' ), tp_child_chrome_get_string( $attrs, 'copyrightLink1', '' ) ],
		[ 2, tp_child_chrome_get_string( $attrs, 'copyrightLine2', 'P.IVA 03942309300' ), tp_child_chrome_get_string( $attrs, 'copyrightLink2', '' ) ],
		[ 3, tp_child_chrome_get_string( $attrs, 'copyrightLine3', 'Privacy' ), tp_child_chrome_get_string( $attrs, 'copyrightLink3', tp_child_footer_legal_link( $saved, 'copyrightLink3', 'privacy' ) ) ],
		[ 4, tp_child_chrome_get_string( $attrs, 'copyrightLine4', 'Cookie' ), tp_child_chrome_get_string( $attrs, 'copyrightLink4', tp_child_footer_legal_link( $saved, 'copyrightLink4', 'cookie' ) ) ],
		[ 5, tp_child_chrome_get_string( $attrs, 'copyrightLine5', 'Designed By Mochi' ), tp_child_chrome_get_string( $attrs, 'copyrightLink5', '' ) ],
	];

	$children = '';
	foreach ( $lines as $line ) {
		$children .= ET\Builder\Framework\Utility\HTMLUtility::render(
			[
				'tag'               => 'span',
				'childrenSanitizer' => 'et_core_esc_previously',
				'children'          => tp_child_chrome_render_copyright_line( esc_html( $line[1] ), esc_url( $line[2] ) ),
			]
		);
	}

	return ET\Builder\Framework\Utility\HTMLUtility::render(
		[
			'tag'               => 'div',
			'attributes'        => [ 'class' => 'tp-theme-footer-copyright' ],
			'childrenSanitizer' => 'et_core_esc_previously',
			'children'          => $children,
		]
	);
}

/**
 * Footer nav block for monolithic layout: the Voci of Footer sito by column (0.5.8), or without Voci the
 * WordPress menu of each column, as before (tp_child_footer_nav_columns_without_voci()).
 */
function tp_child_footer_render_nav_monolith_html( array $attrs, ?array $columns = null ): string {
	$nav_aria_label = esc_attr( tp_child_chrome_get_string( $attrs, 'navAriaLabel', 'Footer' ) );
	$columns        = $columns ?? tp_child_footer_nav_columns_without_voci( $attrs );
	$left_col       = tp_child_chrome_render_footer_column( $columns['left'] ?? [] );
	$right_col      = tp_child_chrome_render_footer_column( $columns['right'] ?? [] );

	return ET\Builder\Framework\Utility\HTMLUtility::render(
		[
			'tag'               => 'nav',
			'attributes'        => [
				'class'      => 'tp-theme-footer-nav',
				'aria-label' => $nav_aria_label,
			],
			'childrenSanitizer' => 'et_core_esc_previously',
			'children'          => $left_col . $right_col,
		]
	);
}
