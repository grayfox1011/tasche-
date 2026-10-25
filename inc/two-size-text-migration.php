<?php
/**
 * Texts with two sizes, each part editable on its own in Divi (since 0.5.3). Two spans in one text
 * module took their sizes from the theme only: Divi had no setting for them. Now the parts are the
 * elements that Divi's Text module sets apart (Design > Heading Text > H4 / H5, Design > Text):
 * - legend of the menu: the big word is a heading 4, the rest the text of the module (the Piatto
 *   sizes one paragraph each);
 * - opening hours: the day is a heading 4, the short day shown on phones a heading 5, the time
 *   the text of the module. "Lunedì-Martedì-Mercoledì" keeps its smaller size on the computer as
 *   the module's own H4 size (20 px), where the theme gave it before;
 * - allergens of the dishes: a Text module of their own after the name (tp-layout.js puts it at the
 *   end of the name, as the superscript of before).
 * The look stays the same: the theme's values are the starting point and the module's win. Modules
 * changed by hand, so that they no longer have the two spans, stay as they are.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A legend box as heading and paragraphs, or null when $html is not the two spans of before.
 */
function tp_child_two_size_legend_html( string $html ): ?string {
	$main = '\s*(?:<p>)?\s*<span class="tp-menu-legend-main">(.*?)</span>\s*';
	$end  = '\s*(?:</p>)?\s*$#s';

	if ( preg_match( '#^' . $main . '<span class="tp-menu-legend-sub">(.*?)</span>' . $end, $html, $match ) ) {
		return '<h4>' . $match[1] . '</h4><p>' . $match[2] . '</p>';
	}

	if ( preg_match( '#^' . $main . '<span class="tp-menu-legend-sizes">((?:\s*<span>(?:(?!</?span).)*</span>)+)\s*</span>' . $end, $html, $match ) ) {
		preg_match_all( '#<span>(.*?)</span>#s', $match[2], $sizes );

		return '<h4>' . $match[1] . '</h4>' . implode( '', array_map( static fn( string $size ): string => '<p>' . $size . '</p>', $sizes[1] ) );
	}

	return null;
}

/**
 * An opening-hours line as [ html, short day ], or null when $html is not the two spans of before.
 */
function tp_child_two_size_hours_html( string $html ): ?array {
	if ( ! preg_match( '#^\s*<p>\s*<span(?: data-mobile="([^"]*)")?>((?:(?!</?span).)*)</span>\s*<span>((?:(?!</?span).)*)</span>\s*</p>\s*$#s', $html, $match ) ) {
		return null;
	}

	$short = $match[1];

	return [ '<h4>' . $match[2] . '</h4>' . ( '' !== $short ? '<h5>' . $short . '</h5>' : '' ) . '<p>' . $match[3] . '</p>', $short ];
}

/**
 * One self-closing Divi text block with its content converted, or as it is.
 */
function tp_child_two_size_block( string $block ): string {
	if ( ! preg_match( '#^<!-- wp:divi/text (\{.*\}) /-->$#s', $block, $json ) ) {
		return $block;
	}

	$attrs = json_decode( $json[1] );
	$class = $attrs->module->advanced->htmlAttributes->desktop->value->class ?? '';
	$texts = $attrs->content->innerContent ?? null;
	$kind  = preg_match( '/(?:^| )tp-menu-legend-box(?: |$)/', $class ) ? 'legend' : ( preg_match( '/(?:^| )tp-hours-item(?: |$)/', $class ) ? 'hours' : '' );

	if ( '' === $kind || ! is_object( $texts ) ) {
		return $block;
	}

	$changed = false;
	$short   = '';

	foreach ( $texts as $breakpoint => $text ) {
		$html = is_object( $text ) && is_string( $text->value ?? null ) ? $text->value : null;
		$new  = null;

		if ( null === $html ) {
			continue;
		}

		if ( 'legend' === $kind ) {
			$new = tp_child_two_size_legend_html( $html );
		} else {
			$hours = tp_child_two_size_hours_html( $html );

			if ( $hours ) {
				list( $new, $day ) = $hours;
				$short             = 'desktop' === $breakpoint ? $day : $short;
			}
		}

		if ( null !== $new ) {
			$texts->{$breakpoint}->value = $new;
			$changed                     = true;
		}
	}

	if ( ! $changed ) {
		return $block;
	}

	// Before, the theme set this day smaller from 981 px up (CSS on its short name): now it is the module's own size.
	if ( 'Lun-Mer' === $short ) {
		$value = tp_child_two_size_node( $attrs, [ 'content', 'decoration', 'headingFont', 'h4', 'font', 'desktop', 'value' ] );

		if ( ! isset( $value->size ) ) {
			$value->size = '20px';
		}
	}

	return '<!-- wp:divi/text ' . serialize_block_attributes( $attrs ) . ' /-->';
}

/**
 * The object at $path under $node, created where it is missing.
 */
function tp_child_two_size_node( stdClass $node, array $path ): stdClass {
	foreach ( $path as $key ) {
		if ( ! isset( $node->{$key} ) || ! is_object( $node->{$key} ) ) {
			$node->{$key} = new stdClass();
		}

		$node = $node->{$key};
	}

	return $node;
}

/**
 * The allergens of the dishes whose title has no "[...]", as tp-layout.js shows them (Figma 10:314;
 * the salsiccia's numbers come from the owner).
 */
function tp_child_allergens_map(): array {
	return [
		'/MELANZAN/i'          => '7,8',
		'/RIPIENI LIGURI/i'    => '1,3,7,12',
		'/POLPETTE/i'          => '1,3,7',
		'/POLLO ALLA LIGURE/i' => '8,12',
		'/TRIPPA/i'            => '8,9,12',
		'/STOCCAFISSO/i'       => '4,8,9,12',
		'/CAPRESE/i'           => '7,8',
		'/ACCIUGHE/i'          => '4,7',
		'/SALSICCIA/i'         => '1,7',
	];
}

/**
 * A dish name block with its allergens in a Text module of their own right after it (class
 * tp-menu-allergeni, admin label "Allergeni"): the numbers typed in the title between square
 * brackets, which leave the title, or else the ones of the map. A name that already has the module,
 * or no numbers, stays as it is.
 */
function tp_child_allergens_name_block( string $block, string $after ): string {
	if ( preg_match( '#^\s*<!-- wp:divi/text \{(?:(?!/-->).)*?tp-menu-allergeni#s', $after ) || ! preg_match( '#^<!-- wp:divi/heading (\{.*\}) /-->$#s', $block, $json ) ) {
		return $block;
	}

	$attrs  = json_decode( $json[1] );
	$titles = $attrs->title->innerContent ?? null;
	$title  = is_object( $titles ) && is_string( $titles->desktop->value ?? null ) ? $titles->desktop->value : '';
	$nums   = '';

	if ( preg_match( '/\[([0-9,\s]+)\]/', $title, $match ) ) {
		$nums = preg_replace( '/\s+/', '', $match[1] );

		foreach ( $titles as $breakpoint => $text ) {
			if ( is_object( $text ) && is_string( $text->value ?? null ) ) {
				$titles->{$breakpoint}->value = rtrim( preg_replace( '/\s*\[[0-9,\s]+\]\s*/', ' ', $text->value ) );
			}
		}

		$block = '<!-- wp:divi/heading ' . serialize_block_attributes( $attrs ) . ' /-->';
	} else {
		foreach ( tp_child_allergens_map() as $pattern => $map_nums ) {
			if ( preg_match( $pattern, wp_strip_all_tags( $title ) ) ) {
				$nums = $map_nums;
				break;
			}
		}
	}

	if ( '' === $nums ) {
		return $block;
	}

	$builder_version = is_string( $attrs->builderVersion ?? null ) ? $attrs->builderVersion : '5.2.0';
	$module          = [
		'module'         => [
			'advanced' => [ 'htmlAttributes' => [ 'desktop' => [ 'value' => [ 'class' => 'tp-menu-allergeni' ] ] ] ],
			'meta'     => [ 'adminLabel' => [ 'desktop' => [ 'value' => 'Allergeni' ] ] ],
		],
		'content'        => [ 'innerContent' => [ 'desktop' => [ 'value' => '<p>' . $nums . '</p>' ] ] ],
		'builderVersion' => $builder_version,
	];

	return $block . "\n<!-- wp:divi/text " . serialize_block_attributes( $module ) . ' /-->';
}

/**
 * $content with the legend, the opening hours and the allergens converted; every other block stays
 * byte for byte.
 */
function tp_child_two_size_transform( string $content ): string {
	$content = (string) preg_replace_callback(
		'#<!-- wp:divi/text \{(?:(?!/-->).)*?\} /-->#s',
		static function ( array $match ): string {
			$block = $match[0];

			if ( false === strpos( $block, 'tp-menu-legend-box' ) && false === strpos( $block, 'tp-hours-item' ) ) {
				return $block;
			}

			return tp_child_two_size_block( $block );
		},
		$content
	);

	// The dish names, last first so that the offsets of the earlier ones stay valid.
	preg_match_all( '#<!-- wp:divi/heading \{(?:(?!/-->).)*?"class":"(?:[^"]* )?tp-menu-name(?: [^"]*)?"(?:(?!/-->).)*?\} /-->#s', $content, $names, PREG_OFFSET_CAPTURE );

	foreach ( array_reverse( $names[0] ) as list( $block, $offset ) ) {
		$after   = substr( $content, $offset + strlen( $block ), 400 );
		$content = substr_replace( $content, tp_child_allergens_name_block( $block, $after ), $offset, strlen( $block ) );
	}

	return $content;
}

function tp_child_migrate_two_size_texts(): void {
	tp_child_menu_size_s_migrate_home( 'tp_child_two_size_texts_v1', 'tp_child_two_size_transform' );
}

add_action( 'init', 'tp_child_migrate_two_size_texts', 29 );
