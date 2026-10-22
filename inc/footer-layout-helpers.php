<?php
/**
 * Shared footer fragment renderers for modular column layout.
 * Output matches the monolithic tp/theme-footer markup.
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Footer shell background block.
 */
function tp_child_footer_render_shell_html( array $attrs ): string {
	$footer_style = tp_child_chrome_style_from_vars(
		[
			'--tp-footer-bg'         => tp_child_chrome_get_string( $attrs, 'footerBackgroundColor', '' ),
			'--tp-footer-link-color' => tp_child_chrome_get_string( $attrs, 'footerLinkColor', '' ),
		]
	);

	return ET\Builder\Framework\Utility\HTMLUtility::render(
		[
			'tag'        => 'footer',
			'attributes' => array_filter(
				[
					'class' => 'site-footer tp-theme-footer',
					'style' => $footer_style ?: null,
				]
			),
			'children'   => '',
		]
	);
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
		[ 'aria-hidden' => 'true' ]
	);

	$person_four = tp_child_chrome_render_footer_character_slot(
		$elements,
		$attrs,
		'characterFourImage',
		'characterFourUrl',
		$asset_base . 'images/footer/personaggi-4.webp',
		'four',
		[ 'aria-hidden' => 'true' ]
	);

	$person_three = tp_child_chrome_render_footer_character_slot(
		$elements,
		$attrs,
		'characterThreeImage',
		'characterThreeUrl',
		$asset_base . 'images/footer/personaggi-3.webp',
		'three',
		[ 'aria-hidden' => 'true' ]
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
 * Footer nav left rail (includes nav wrapper + left column).
 */
function tp_child_footer_render_nav_left_html( array $attrs ): string {
	$nav_aria_label = esc_attr( tp_child_chrome_get_string( $attrs, 'navAriaLabel', 'Footer' ) );
	$left_location  = sanitize_key( tp_child_chrome_get_string( $attrs, 'menuLeftLocation', 'tp_footer_left' ) );
	$left_col       = tp_child_footer_render_nav_column_html(
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

	return ET\Builder\Framework\Utility\HTMLUtility::render(
		[
			'tag'               => 'nav',
			'attributes'        => [
				'class'      => 'tp-theme-footer-nav tp-theme-footer-nav--left-rail',
				'aria-label' => $nav_aria_label,
			],
			'childrenSanitizer' => 'et_core_esc_previously',
			'children'          => $left_col,
		]
	);
}

/**
 * Footer nav right column (positioned inside nav rail via CSS).
 */
function tp_child_footer_render_nav_right_html( array $attrs ): string {
	$right_location = sanitize_key( tp_child_chrome_get_string( $attrs, 'menuRightLocation', 'tp_footer_right' ) );
	$right_col      = tp_child_footer_render_nav_column_html(
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
			'tag'               => 'div',
			'attributes'        => [ 'class' => 'tp-theme-footer-nav-rail-right' ],
			'childrenSanitizer' => 'et_core_esc_previously',
			'children'          => $right_col,
		]
	);
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
 * Extract attrs subset from monolithic footer block attrs.
 */
function tp_child_footer_attrs_from_monolith( array $attrs ): array {
	return $attrs;
}

/**
 * Convert parent-child footer blocks into monolithic tp/theme-footer attrs.
 *
 * @param array<int, array<string, mixed>> $inner_blocks Child blocks.
 * @return array<string, mixed>
 */
function tp_child_footer_child_blocks_to_monolith_attrs( array $inner_blocks ): array {
	$attrs = [];
	$left  = 0;
	$right = 0;
	$copy  = 0;

	foreach ( $inner_blocks as $child ) {
		if ( ! is_array( $child ) ) {
			continue;
		}

		$name = (string) ( $child['blockName'] ?? '' );
		$ca   = is_array( $child['attrs'] ?? null ) ? $child['attrs'] : [];

		switch ( $name ) {
			case 'tp/footer-character':
				$slot    = sanitize_key( tp_child_chrome_get_string( $ca, 'slot', 'one' ) );
				$prefix  = match ( $slot ) {
					'four'  => 'characterFour',
					'three' => 'characterThree',
					default => 'characterOne',
				};
				if ( isset( $ca['image'] ) ) {
					$attrs[ $prefix . 'Image' ] = $ca['image'];
				}
				if ( isset( $ca['imageUrl'] ) ) {
					$attrs[ $prefix . 'Url' ] = $ca['imageUrl'];
				}
				break;

			case 'tp/footer-logo':
				if ( isset( $ca['image'] ) ) {
					$attrs['logoImage'] = $ca['image'];
				}
				if ( isset( $ca['imageUrl'] ) ) {
					$attrs['logoUrl'] = $ca['imageUrl'];
				}
				if ( isset( $ca['alt'] ) ) {
					$attrs['logoAlt'] = $ca['alt'];
				}
				if ( isset( $ca['link'] ) ) {
					$attrs['logoLink'] = $ca['link'];
				}
				break;

			case 'tp/footer-nav-item':
				$column = tp_child_chrome_get_string( $ca, 'column', 'left' );
				if ( 'right' === $column ) {
					$right++;
					if ( isset( $ca['text'] ) ) {
						$attrs[ "footerRight{$right}Text" ] = $ca['text'];
					}
					if ( isset( $ca['link'] ) ) {
						$attrs[ "footerRight{$right}Link" ] = $ca['link'];
					}
				} else {
					$left++;
					if ( isset( $ca['text'] ) ) {
						$attrs[ "footerLeft{$left}Text" ] = $ca['text'];
					}
					if ( isset( $ca['link'] ) ) {
						$attrs[ "footerLeft{$left}Link" ] = $ca['link'];
					}
				}
				break;

			case 'tp/footer-copyright-line':
				$copy++;
				if ( isset( $ca['text'] ) ) {
					$attrs[ "copyrightLine{$copy}" ] = $ca['text'];
				}
				if ( isset( $ca['link'] ) ) {
					$attrs[ "copyrightLink{$copy}" ] = $ca['link'];
				}
				break;
		}
	}

	return $attrs;
}

/**
 * Collect monolithic footer attrs from a parsed layout tree.
 *
 * @param array<int, array<string, mixed>> $blocks Parsed blocks.
 * @return array<string, mixed>
 */
function tp_child_footer_collect_monolith_attrs_from_layout( array $blocks ): array {
	foreach ( $blocks as $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		if ( 'tp/theme-footer' === ( $block['blockName'] ?? '' ) ) {
			$attrs = is_array( $block['attrs'] ?? null ) ? $block['attrs'] : [];

			if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
				$attrs = array_merge( $attrs, tp_child_footer_child_blocks_to_monolith_attrs( $block['innerBlocks'] ) );
			}

			return $attrs;
		}

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$attrs = tp_child_footer_collect_monolith_attrs_from_layout( $block['innerBlocks'] );

			if ( ! empty( $attrs ) ) {
				return $attrs;
			}
		}
	}

	return [];
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

/**
 * Build modular footer blocks markup for column seeding / migration.
 */
function tp_child_footer_modular_blocks_from_attrs( array $attrs ): string {
	$builder_version = $attrs['builderVersion'] ?? ( defined( 'TP_CHILD_SEED_BUILDER_VERSION' ) ? TP_CHILD_SEED_BUILDER_VERSION : '5.5.2' );
	$shell_attrs     = [
		'footerBackgroundColor' => $attrs['footerBackgroundColor'] ?? [ 'innerContent' => [ 'desktop' => [ 'value' => '#054726' ] ] ],
		'footerLinkColor'       => $attrs['footerLinkColor'] ?? [ 'innerContent' => [ 'desktop' => [ 'value' => '#fff5e9' ] ] ],
		'builderVersion'        => $builder_version,
	];

	$blocks = [
		[ 'tp/theme-footer-shell', $shell_attrs ],
		[ 'tp/footer-characters', array_intersect_key( $attrs, array_flip( [ 'characterOneImage', 'characterOneUrl', 'characterFourImage', 'characterFourUrl', 'characterThreeImage', 'characterThreeUrl', 'builderVersion' ] ) ) + [ 'builderVersion' => $builder_version ] ],
		[ 'tp/footer-logo', array_intersect_key( $attrs, array_flip( [ 'logoImage', 'logoUrl', 'logoAlt', 'logoLink', 'builderVersion' ] ) ) + [ 'builderVersion' => $builder_version ] ],
		[ 'tp/footer-nav-left', array_intersect_key( $attrs, array_flip( [ 'footerLeft1Text', 'footerLeft1Link', 'footerLeft2Text', 'footerLeft2Link', 'footerLeft3Text', 'footerLeft3Link', 'navAriaLabel', 'menuLeftLocation', 'builderVersion' ] ) ) + [ 'builderVersion' => $builder_version ] ],
		[ 'tp/footer-nav-right', array_intersect_key( $attrs, array_flip( [ 'footerRight1Text', 'footerRight1Link', 'footerRight2Text', 'footerRight2Link', 'footerRight3Text', 'footerRight3Link', 'menuRightLocation', 'builderVersion' ] ) ) + [ 'builderVersion' => $builder_version ] ],
		[ 'tp/footer-copyright', array_intersect_key( $attrs, array_flip( [ 'copyrightLine1', 'copyrightLine2', 'copyrightLine3', 'copyrightLine4', 'copyrightLine5', 'copyrightLink1', 'copyrightLink2', 'copyrightLink3', 'copyrightLink4', 'copyrightLink5', 'builderVersion' ] ) ) + [ 'builderVersion' => $builder_version ] ],
	];

	$content = '';
	foreach ( $blocks as $block ) {
		$content .= tp_child_seed_block_single( $block[0], $block[1] );
	}

	return $content;
}

/**
 * Replace monolithic footer blocks with modular blocks in parsed content.
 *
 * @param array<int, array<string, mixed>> $blocks Parsed blocks.
 * @return array<int, array<string, mixed>>
 */
function tp_child_footer_migrate_monolith_to_modular( array $blocks ): array {
	foreach ( $blocks as $index => $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$blocks[ $index ]['innerBlocks'] = tp_child_footer_migrate_monolith_to_modular( $block['innerBlocks'] );
		}

		if ( 'tp/theme-footer' !== ( $block['blockName'] ?? '' ) ) {
			continue;
		}

		$attrs = is_array( $block['attrs'] ?? null ) ? $block['attrs'] : [];
		$new_blocks = array_values(
			array_filter(
				parse_blocks( tp_child_footer_modular_blocks_from_attrs( $attrs ) ),
				static function ( $parsed_block ) {
					return is_array( $parsed_block ) && ! empty( $parsed_block['blockName'] );
				}
			)
		);

		if ( empty( $new_blocks ) ) {
			continue;
		}

		array_splice( $blocks, $index, 1, $new_blocks );
	}

	return $blocks;
}

/**
 * Walk parsed blocks and collect attrs from modular footer modules.
 *
 * @param array<int, array<string, mixed>> $blocks Parsed blocks.
 * @return array<string, mixed>
 */
function tp_child_footer_collect_modular_attrs( array $blocks ): array {
	$merged = [];

	foreach ( $blocks as $block ) {
		if ( ! is_array( $block ) ) {
			continue;
		}

		$name = (string) ( $block['blockName'] ?? '' );

		if ( str_starts_with( $name, 'tp/' ) && is_array( $block['attrs'] ?? null ) ) {
			$merged = array_merge( $merged, $block['attrs'] );
		}

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			$merged = array_merge( $merged, tp_child_footer_collect_modular_attrs( $block['innerBlocks'] ) );
		}
	}

	return $merged;
}

/**
 * Build a single tp/theme-footer block from footer attrs.
 */
function tp_child_footer_monolith_block_from_attrs( array $attrs ): string {
	$builder_version = $attrs['builderVersion'] ?? ( defined( 'TP_CHILD_SEED_BUILDER_VERSION' ) ? TP_CHILD_SEED_BUILDER_VERSION : '5.5.2' );

	$block_attrs = array_merge(
		$attrs,
		[
			'module' => [
				'advanced' => [
					'htmlAttributes' => [
						'desktop' => [
							'value' => [
								'class' => 'tp-theme-footer-code',
							],
						],
					],
				],
			],
			'builderVersion' => $builder_version,
		]
	);

	return tp_child_seed_block_single( 'tp/theme-footer', $block_attrs );
}

/**
 * Restore footer TB layout to the monolithic tp/theme-footer module.
 */
function tp_child_footer_restore_monolith_layout( int $post_id ): bool {
	$content = get_post_field( 'post_content', $post_id );

	if ( ! is_string( $content ) || '' === $content ) {
		return false;
	}

	$blocks = parse_blocks( $content );
	$attrs  = tp_child_footer_collect_monolith_attrs_from_layout( $blocks );

	if ( empty( $attrs ) ) {
		$attrs = [];
	}

	$updated = tp_child_seed_footer_layout( tp_child_footer_monolith_block_from_attrs( $attrs ) );

	if ( ! $updated || $updated === $content ) {
		return false;
	}

	$result = wp_update_post(
		[
			'ID'           => $post_id,
			'post_content' => $updated,
		],
		true
	);

	return ! is_wp_error( $result );
}
