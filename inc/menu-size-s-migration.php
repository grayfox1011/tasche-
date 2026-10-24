<?php
/**
 * Menu: the S size of the plate, as in the Figma (frame "statico per copy"). Every main menu row
 * gets an S letter and an S price before the M ones, the M and L prices move to the Figma's where
 * the row still has the previous ones, and the legend reads "S - 150g", "M - 200g", "L - 400g".
 * Rows and prices changed by hand in Divi stay as they are.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prices by dish, matched on the start of the row's name:
 * [ S, M, L, M before, L before ]. An empty S means the dish has no S (as in the Figma).
 */
function tp_child_menu_size_s_prices(): array {
	return [
		'MELANZANA'   => [ '', '10,50€', '14,50€', '6,00€', '9,00€' ],
		'RIPIENI'     => [ '7,00€', '10,50€', '14,50€', '6,00€', '9,00€' ],
		'POLPETTE'    => [ '8,00€', '12,00€', '16,50€', '7,50€', '10,50€' ],
		'POLLO'       => [ '8,00€', '12,00€', '16,50€', '7,50€', '10,50€' ],
		'TRIPPA'      => [ '9,00€', '13,50€', '18,00€', '7,50€', '10,50€' ],
		'STOCCAFISSO' => [ '9,00€', '13,50€', '18,00€', '7,50€', '10,50€' ],
		'CAPRESE'     => [ '7,00€', '10,50€', '14,50€', '6,00€', '9,00€' ],
		'LE ACCIUGHE' => [ '8,00€', '12,00€', '16,50€', '7,50€', '10,50€' ],
	];
}

/**
 * $text as it appears inside serialized block attributes (tags and "--" escaped by WordPress).
 */
function tp_child_menu_size_s_json_text( string $text ): string {
	return substr( serialize_block_attributes( [ $text ] ), 2, -2 );
}

/**
 * A self-closing Divi text block, serialized as WordPress does (the rest of the content is).
 */
function tp_child_menu_size_s_text_block( string $class, string $html, string $admin_label, string $builder_version ): string {
	$module = [
		'advanced' => [
			'htmlAttributes' => [ 'desktop' => [ 'value' => [ 'class' => $class ] ] ],
		],
	];

	if ( '' !== $admin_label ) {
		$module['advanced']['meta'] = [ 'adminLabel' => [ 'desktop' => [ 'value' => $admin_label ] ] ];
	}

	$attrs = [
		'module'         => $module,
		'content'        => [ 'innerContent' => [ 'desktop' => [ 'value' => $html ] ] ],
		'builderVersion' => $builder_version,
	];

	return '<!-- wp:divi/text ' . serialize_block_attributes( $attrs ) . ' /-->';
}

/**
 * The first self-closing Divi text block in $row whose class list has $class.
 */
function tp_child_menu_size_s_find_block( string $row, string $class ): ?array {
	$pattern = '/<!-- wp:divi\/text \{(?:(?!\/-->).)*?"class":"(?:[^"]* )?' . preg_quote( $class, '/' ) . '(?: [^"]*)?"(?:(?!\/-->).)*?\/-->/s';

	if ( ! preg_match( $pattern, $row, $match, PREG_OFFSET_CAPTURE ) ) {
		return null;
	}

	return [ $match[0][0], $match[0][1] ];
}

/**
 * The block with its paragraph text replaced, when the paragraph is exactly $from.
 */
function tp_child_menu_size_s_swap_price( string $block, string $from, string $to ): string {
	$old = '"value":"' . tp_child_menu_size_s_json_text( '<p>' . $from . '</p>' ) . '"';

	if ( 1 !== substr_count( $block, $old ) ) {
		return $block;
	}

	return str_replace( $old, '"value":"' . tp_child_menu_size_s_json_text( '<p>' . $to . '</p>' ) . '"', $block );
}

/**
 * One main menu row: the S letter and price before the M letter, the Figma's M and L prices.
 */
function tp_child_menu_size_s_row( string $row ): string {
	if ( ! preg_match( '/"class":"(?:[^"]* )?tp-menu-name(?: [^"]*)?".*?"title":\{"innerContent":\{"desktop":\{"value":"([^"]*)"/s', $row, $name ) ) {
		return $row;
	}

	$name   = strtoupper( trim( wp_strip_all_tags( json_decode( '"' . $name[1] . '"' ) ?? '' ) ) );
	$prices = null;

	foreach ( tp_child_menu_size_s_prices() as $prefix => $values ) {
		if ( 0 === strpos( $name, $prefix ) ) {
			$prices = $values;
			break;
		}
	}

	if ( null === $prices || false !== strpos( $row, 'tp-menu-price-value-s' ) ) {
		return $row;
	}

	list( $price_s, $price_m, $price_l, $before_m, $before_l ) = $prices;

	foreach ( [ 'tp-menu-price-value-m' => [ $before_m, $price_m ], 'tp-menu-price-value-l' => [ $before_l, $price_l ] ] as $class => $swap ) {
		$found = tp_child_menu_size_s_find_block( $row, $class );

		if ( $found ) {
			$row = substr_replace( $row, tp_child_menu_size_s_swap_price( $found[0], $swap[0], $swap[1] ), $found[1], strlen( $found[0] ) );
		}
	}

	$letter_m = tp_child_menu_size_s_find_block( $row, 'tp-menu-label-m' );

	if ( '' === $price_s || ! $letter_m ) {
		return $row;
	}

	$builder_version = preg_match( '/"builderVersion":"([^"]+)"/', $letter_m[0], $version ) ? $version[1] : '5.2.0';
	$blocks          = tp_child_menu_size_s_text_block( 'tp-menu-label tp-menu-label-s', '<p>S</p>', 'Etichetta S', $builder_version ) . "\n"
		. tp_child_menu_size_s_text_block( 'tp-menu-price tp-menu-price-value-s', '<p>' . $price_s . '</p>', '', $builder_version ) . "\n\n";

	return substr_replace( $row, $blocks, $letter_m[1], 0 );
}

/**
 * The home content with the S size in the main menu and the S, M, L legend.
 */
function tp_child_menu_size_s_transform( string $content ): string {
	$table = strpos( $content, tp_child_menu_size_s_json_text( 'tp-menu-table--main' ) );

	if ( false === $table ) {
		return $content;
	}

	$end = strpos( $content, tp_child_menu_size_s_json_text( 'tp-menu-table--sides' ), $table );
	$end = false === $end ? strlen( $content ) : $end;

	// Main menu rows: from each row's opening comment to the next one.
	preg_match_all( '/<!-- wp:divi\/group \{[^>]*?"class":"tp-menu-row(?: [^"]*)?"[^>]*?-->/', $content, $starts, PREG_OFFSET_CAPTURE, $table );
	$bounds = [];

	foreach ( $starts[0] as $start ) {
		if ( $start[1] < $end ) {
			$bounds[] = [ $start[1], false !== strpos( $start[0], 'proposal' ) ];
		}
	}

	for ( $i = count( $bounds ) - 1; $i >= 0; $i-- ) {
		if ( $bounds[ $i ][1] ) {
			continue;
		}

		$from = $bounds[ $i ][0];
		$to   = isset( $bounds[ $i + 1 ] ) ? $bounds[ $i + 1 ][0] : $end;
		$row  = substr( $content, $from, $to - $from );

		$content = substr_replace( $content, tp_child_menu_size_s_row( $row ), $from, $to - $from );
	}

	return str_replace(
		tp_child_menu_size_s_json_text( '<span>Regular - 200g</span><span>Large - 400g</span>' ),
		tp_child_menu_size_s_json_text( '<span>S - 150g</span><span>M - 200g</span><span>L - 400g</span>' ),
		$content
	);
}

/**
 * Once: the S size on the home page. WordPress keeps the previous content as a revision.
 */
function tp_child_migrate_menu_size_s(): void {
	$flag = 'tp_child_menu_size_s_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	$home_id = (int) get_option( 'page_on_front' );
	$content = $home_id > 0 ? (string) get_post_field( 'post_content', $home_id ) : '';
	$updated = '' !== $content ? tp_child_menu_size_s_transform( $content ) : $content;

	if ( $updated !== $content ) {
		// The block JSON must be saved verbatim, whoever triggers the request.
		kses_remove_filters();
		wp_update_post(
			[
				'ID'           => $home_id,
				'post_content' => wp_slash( $updated ),
			]
		);
		kses_init();
		tp_child_seed_clear_divi_cache_meta( $home_id );
		tp_child_clear_divi_static_css();
	}

	update_option( $flag, TP_CHILD_VERSION, true );
}

add_action( 'init', 'tp_child_migrate_menu_size_s', 26 );
