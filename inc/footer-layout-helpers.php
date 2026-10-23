<?php
/**
 * Footer fragment renderers used by the tp/theme-footer module.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Footer characters block.
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

	$person_three = tp_child_chrome_render_footer_character_slot(
		$elements,
		$attrs,
		'characterThreeImage',
		'characterThreeUrl',
		$asset_base . 'images/footer/personaggi-3.webp',
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
 * Footer nav column block.
 */
function tp_child_footer_render_nav_column_html( array $attrs, string $prefix, int $count, string $menu_location, array $defaults ): string {
	$items = tp_child_chrome_resolve_nav_items( $attrs, $prefix, $count, $menu_location, $defaults );

	return tp_child_chrome_render_footer_column( $items );
}

/**
 * Footer copyright block.
 */
function tp_child_footer_render_copyright_html( array $attrs ): string {
	$lines = [
		[ 1, tp_child_chrome_get_string( $attrs, 'copyrightLine1', 'Copyright 2026, Tasche piene tutti i diritti riservati' ), tp_child_chrome_get_string( $attrs, 'copyrightLink1', '' ) ],
		[ 2, tp_child_chrome_get_string( $attrs, 'copyrightLine2', 'P.IVA 03942309300' ), tp_child_chrome_get_string( $attrs, 'copyrightLink2', '' ) ],
		[ 3, tp_child_chrome_get_string( $attrs, 'copyrightLine3', 'Privacy' ), tp_child_chrome_get_string( $attrs, 'copyrightLink3', '' ) ],
		[ 4, tp_child_chrome_get_string( $attrs, 'copyrightLine4', 'Cookie' ), tp_child_chrome_get_string( $attrs, 'copyrightLink4', '' ) ],
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
 * Footer nav block for monolithic layout.
 */
function tp_child_footer_render_nav_monolith_html( array $attrs ): string {
	$nav_aria_label = esc_attr( tp_child_chrome_get_string( $attrs, 'navAriaLabel', 'Footer' ) );
	$left_location  = sanitize_key( tp_child_chrome_get_string( $attrs, 'menuLeftLocation', 'tp_footer_left' ) );
	$right_location = sanitize_key( tp_child_chrome_get_string( $attrs, 'menuRightLocation', 'tp_footer_right' ) );

	$left_col = tp_child_footer_render_nav_column_html(
		$attrs,
		'footerLeft',
		3,
		$left_location,
		[
			[ 'title' => 'MENÙ', 'url' => '#menu', 'target' => '', 'xfn' => '' ],
			[ 'title' => 'DOVE SIAMO', 'url' => '#location', 'target' => '', 'xfn' => '' ],
			[ 'title' => 'SU DI NOI', 'url' => '#chi-siamo', 'target' => '', 'xfn' => '' ],
		]
	);

	$right_col = tp_child_footer_render_nav_column_html(
		$attrs,
		'footerRight',
		3,
		$right_location,
		[
			[ 'title' => 'TORNA SÙ', 'url' => '#home', 'target' => '', 'xfn' => '' ],
			[ 'title' => 'FACEBOOK', 'url' => 'https://facebook.com', 'target' => '_blank', 'xfn' => 'noopener' ],
			[ 'title' => 'INSTAGRAM', 'url' => 'https://instagram.com', 'target' => '_blank', 'xfn' => 'noopener' ],
		]
	);

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
