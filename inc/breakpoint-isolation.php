<?php
/**
 * 0.6.5: in the theme's sections a value set in Divi for one device shows on that device only.
 *
 * Divi writes the value of the desktop as a rule without media query (so it reaches tablets and phones too,
 * unless they have their own value) and the tablet's as max-width 980px (so it reaches phones too). In the
 * sections with a tp- class (the home's, the legal pages', the header and footer of the Theme Builder) the
 * rules of their modules get a range of their own instead: desktop from 981px, tablet 768-980px, phone up to
 * 767px as before. What is not set for a device keeps the theme's design for that device.
 *
 * - On the site: while Divi renders the page the theme notes the order classes of the modules inside those
 *   sections; when Divi writes the page's CSS (et_core_page_resource_get_data, module styles at priority 40)
 *   their rules get the ranges.
 * - In the Visual Builder: the same on the <style> Divi keeps in each module (tp_child_vb_bp_isolation_script).
 * - Once (tp_child_bp_freeze_065) and on the pages the theme creates: what a device took from a larger one
 *   (a value set for the desktop only, or for the tablet only) is written on the smaller devices too, so
 *   nothing that shows today changes. Not the background video (Divi prints one per device) nor the column
 *   layout (classes, not rules).
 * - What Divi relies on its cascade for keeps it: its default alignment of the texts ("start", printed for the
 *   desktop only; when the desktop gets an alignment of its own the default stays for the smaller devices) and
 *   the backgrounds set for every device (Divi leaves out a device's background equal to the larger one's).
 * Only with Divi's three devices at their usual widths; with other breakpoints enabled nothing changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The widths of the devices, from Divi's settings: null when breakpoints other than desktop, tablet and
 * phone are enabled (their ranges would need more than this).
 */
function tp_child_bp_ranges(): ?array {
	static $ranges = false;

	if ( false !== $ranges ) {
		return $ranges;
	}

	$ranges = [ 'tablet' => 980, 'phone' => 767 ];

	/**
	 * Filter: false turns the per-device ranges off (Divi's usual cascade: the desktop's value reaches the
	 * smaller devices that have none of their own).
	 */
	if ( ! apply_filters( 'tp_child_bp_isolation_enabled', true ) ) {
		$ranges = null;

		return $ranges;
	}

	if ( class_exists( '\\ET\\Builder\\Framework\\Breakpoint\\Breakpoint' ) ) {
		$enabled = array_values( \ET\Builder\Framework\Breakpoint\Breakpoint::get_enabled_breakpoint_names() );
		sort( $enabled );

		if ( [ 'desktop', 'phone', 'tablet' ] !== $enabled ) {
			$ranges = null;

			return $ranges;
		}

		foreach ( \ET\Builder\Framework\Breakpoint\Breakpoint::get_enabled_breakpoints() as $breakpoint ) {
			$name = $breakpoint['name'] ?? '';

			if ( isset( $ranges[ $name ] ) && ! empty( $breakpoint['maxWidth']['value'] ) ) {
				$ranges[ $name ] = (int) $breakpoint['maxWidth']['value'];
			}
		}
	}

	if ( $ranges['phone'] < 1 || $ranges['tablet'] <= $ranges['phone'] ) {
		$ranges = null;
	}

	return $ranges;
}

/**
 * An order class: et_pb_text_12, et_pb_row_0_tb_header, tp_marquee_item_3, et_pb_button_0_wrapper (a button's
 * box)... (not et_pb_column_1_2, a column type).
 */
function tp_child_bp_order_class_pattern(): string {
	return '(?:et_pb|tp)_[a-z_]+?_\d+(?:_tb_[a-z]+)?(?:_wrapper)?';
}

/**
 * Order classes of the modules inside the theme's sections in this request (et_pb_text_12, et_pb_row_0_tb_header...).
 */
function tp_child_bp_scope( array $add = [] ): array {
	static $scope = [];

	foreach ( $add as $class ) {
		$scope[ $class ] = true;
	}

	return $scope;
}

/**
 * Order classes of the modules whose background is set for desktop, tablet and phone (once more by the freeze, or
 * by hand). Divi does not repeat for a device a background equal to the larger one's (the larger one's rule reaches
 * it): for these modules the background rules keep Divi's cascade.
 */
function tp_child_bp_background_full( array $add = [] ): array {
	static $full = [];

	foreach ( $add as $class ) {
		$full[ $class ] = true;
	}

	return $full;
}

/**
 * Whether a background of the module (of any of its elements) has a value for tablet and for phone.
 */
function tp_child_bp_has_full_background( array $attrs ): bool {
	foreach ( $attrs as $element_attrs ) {
		$background = is_array( $element_attrs ) ? ( $element_attrs['decoration']['background'] ?? null ) : null;

		if ( is_array( $background ) && ! empty( $background['tablet'] ) && ! empty( $background['phone'] ) ) {
			return true;
		}
	}

	return false;
}

/**
 * The module's own order class: the first one in its markup (a button's box et_pb_button_0_wrapper counts as
 * the button's et_pb_button_0).
 */
function tp_child_bp_own_order_class( string $html ): string {
	if ( preg_match_all( '/\sclass="([^"]*)"/', $html, $attributes ) ) {
		foreach ( $attributes[1] as $classes ) {
			if ( preg_match( '/(?<![\w-])(' . tp_child_bp_order_class_pattern() . ')(?![\w-])/', $classes, $match ) ) {
				return preg_replace( '/_wrapper$/', '', $match[1] );
			}
		}
	}

	return '';
}

/**
 * render_block: every module with a full background notes its order class.
 */
function tp_child_bp_collect_background( $html, $block ) {
	if ( ! is_string( $html ) || '' === $html || empty( $block['attrs'] ) || ! is_array( $block['attrs'] ) ) {
		return $html;
	}

	if ( tp_child_bp_has_full_background( $block['attrs'] ) ) {
		$class = tp_child_bp_own_order_class( $html );

		if ( '' !== $class ) {
			tp_child_bp_background_full( [ $class ] );
		}
	}

	return $html;
}
add_filter( 'render_block', 'tp_child_bp_collect_background', 999, 2 );

/**
 * The classes of a section, wherever Divi keeps them: in the theme's content in the CSS Class field, in a section
 * made in the Visual Builder in Advanced > Attributes (tp_child_block_class_names()). Until 0.6.6 only the first
 * place was read, so on the site the sections made by hand kept Divi's cascade (the builder already ranged them).
 */
function tp_child_bp_section_class( array $block ): string {
	if ( function_exists( 'tp_child_block_class_names' ) ) {
		return tp_child_block_class_names( (array) ( $block['attrs'] ?? [] ) );
	}

	return (string) ( $block['attrs']['module']['advanced']['htmlAttributes']['desktop']['value']['class'] ?? '' );
}

/**
 * 0.6.6: a section with its tp- class only in Advanced > Attributes, the ones the 0.6.5 freeze did not see.
 */
function tp_child_bp_section_attributes_only( array $block ): bool {
	$field = (string) ( $block['attrs']['module']['advanced']['htmlAttributes']['desktop']['value']['class'] ?? '' );

	return ! preg_match( '/(^|\s)tp-/', $field ) && (bool) preg_match( '/(^|\s)tp-/', tp_child_bp_section_class( $block ) );
}

/**
 * render_block: a section with a tp- class, rendered with everything inside it.
 */
function tp_child_bp_collect_scope( $html, $block ) {
	if ( 'divi/section' !== ( $block['blockName'] ?? '' ) || ! is_string( $html ) || '' === $html ) {
		return $html;
	}

	if ( ! preg_match( '/(^|\s)tp-/', tp_child_bp_section_class( $block ) ) ) {
		return $html;
	}

	// The order classes of the section and of every module in it (et_pb_text_12, et_pb_row_0_tb_header...).
	if ( preg_match_all( '/\sclass="([^"]*)"/', $html, $attributes ) ) {
		$found = [];

		foreach ( $attributes[1] as $classes ) {
			if ( preg_match_all( '/(?<![\w-])(' . tp_child_bp_order_class_pattern() . ')(?![\w-])/', $classes, $matches ) ) {
				foreach ( $matches[1] as $class ) {
					$found[] = $class;
				}
			}
		}

		tp_child_bp_scope( $found );
	}

	return $html;
}
add_filter( 'render_block', 'tp_child_bp_collect_scope', 999, 2 );

/**
 * Top-level statements of a stylesheet: [ 'type' => 'rule'|'media'|'other', 'prelude' => ..., 'body' => ..., 'text' => ... ].
 */
function tp_child_bp_split_css( string $css ): array {
	$out   = [];
	$len   = strlen( $css );
	$i     = 0;
	$start = 0;

	while ( $i < $len ) {
		$ch = $css[ $i ];

		if ( '/' === $ch && '*' === ( $css[ $i + 1 ] ?? '' ) ) {
			$end = strpos( $css, '*/', $i + 2 );
			$i   = false === $end ? $len : $end + 2;
			continue;
		}

		if ( '{' === $ch ) {
			$prelude = trim( substr( $css, $start, $i - $start ) );
			$depth   = 1;
			$j       = $i + 1;

			while ( $j < $len && $depth > 0 ) {
				$c = $css[ $j ];

				if ( '"' === $c || "'" === $c ) {
					$k = $j + 1;
					while ( $k < $len && $css[ $k ] !== $c ) {
						$k += ( '\\' === $css[ $k ] ) ? 2 : 1;
					}
					$j = $k + 1;
					continue;
				}

				if ( '/' === $c && '*' === ( $css[ $j + 1 ] ?? '' ) ) {
					$end = strpos( $css, '*/', $j + 2 );
					$j   = false === $end ? $len : $end + 2;
					continue;
				}

				if ( '{' === $c ) {
					++$depth;
				} elseif ( '}' === $c ) {
					--$depth;
				}

				++$j;
			}

			$body = substr( $css, $i + 1, max( 0, $j - $i - 2 ) );
			$type = '@' === ( $prelude[0] ?? '' ) ? ( 0 === stripos( $prelude, '@media' ) ? 'media' : 'other' ) : 'rule';

			$out[] = [
				'type'    => $type,
				'prelude' => $prelude,
				'body'    => $body,
				'text'    => substr( $css, $start, $j - $start ),
			];

			$i     = $j;
			$start = $j;
			continue;
		}

		if ( ';' === $ch ) {
			$chunk = ltrim( substr( $css, $start, $i - $start ) );

			// @charset, @import...
			if ( '' !== $chunk && '@' === $chunk[0] ) {
				$out[] = [ 'type' => 'other', 'prelude' => '', 'body' => '', 'text' => substr( $css, $start, $i - $start + 1 ) ];
				$start = $i + 1;
			}
		}

		++$i;
	}

	$rest = substr( $css, $start );

	if ( '' !== trim( $rest ) ) {
		$out[] = [ 'type' => 'other', 'prelude' => '', 'body' => '', 'text' => $rest ];
	}

	return $out;
}

/**
 * Selectors of a rule split at the top-level commas.
 */
function tp_child_bp_split_selectors( string $prelude ): array {
	$parts = [];
	$depth = 0;
	$cur   = '';
	$len   = strlen( $prelude );

	for ( $i = 0; $i < $len; $i++ ) {
		$c = $prelude[ $i ];

		if ( '(' === $c || '[' === $c ) {
			++$depth;
		} elseif ( ')' === $c || ']' === $c ) {
			--$depth;
		}

		if ( ',' === $c && 0 === $depth ) {
			$parts[] = $cur;
			$cur     = '';
			continue;
		}

		$cur .= $c;
	}

	$parts[] = $cur;

	return array_values( array_filter( array_map( 'trim', $parts ), 'strlen' ) );
}

/**
 * The order classes a selector names, in order (the last one is the module the rule styles).
 */
function tp_child_bp_selector_classes( string $selector ): array {
	if ( ! preg_match_all( '/\.(' . tp_child_bp_order_class_pattern() . ')(?![\w-])/', $selector, $matches ) ) {
		return [];
	}

	return $matches[1];
}

/**
 * Whether a selector names one of the order classes in scope (null: every selector).
 */
function tp_child_bp_selector_in_scope( string $selector, ?array $scope ): bool {
	if ( null === $scope ) {
		return true;
	}

	foreach ( tp_child_bp_selector_classes( $selector ) as $class ) {
		if ( isset( $scope[ $class ] ) ) {
			return true;
		}
	}

	return false;
}

/**
 * The declarations of a rule: [ [ 'prop' => 'text-align', 'value' => 'start', 'text' => 'text-align:start' ], ... ].
 */
function tp_child_bp_split_declarations( string $body ): array {
	$parts = [];
	$depth = 0;
	$cur   = '';
	$len   = strlen( $body );

	for ( $i = 0; $i < $len; $i++ ) {
		$c = $body[ $i ];

		if ( '"' === $c || "'" === $c ) {
			$k = $i + 1;

			while ( $k < $len && $body[ $k ] !== $c ) {
				$k += ( '\\' === $body[ $k ] ) ? 2 : 1;
			}

			$cur .= substr( $body, $i, $k - $i + 1 );
			$i    = $k;
			continue;
		}

		if ( '(' === $c ) {
			++$depth;
		} elseif ( ')' === $c ) {
			--$depth;
		}

		if ( ';' === $c && $depth <= 0 ) {
			$parts[] = $cur;
			$cur     = '';
			continue;
		}

		$cur .= $c;
	}

	$parts[] = $cur;
	$out     = [];

	foreach ( $parts as $text ) {
		$text = trim( $text );

		if ( '' === $text ) {
			continue;
		}

		$colon = strpos( $text, ':' );
		$out[] = [
			'prop'  => false === $colon ? '' : strtolower( trim( substr( $text, 0, $colon ) ) ),
			'value' => false === $colon ? '' : trim( substr( $text, $colon + 1 ) ),
			'text'  => $text,
		];
	}

	return $out;
}

/**
 * Whether a declaration keeps the place Divi gave it (no device range of its own):
 * - "text-align: start", Divi's default alignment of the text modules (no setting writes "start"), that Divi
 *   prints for the desktop only and that every device uses;
 * - the background of a module that has one set for every device: Divi leaves out a device's background that
 *   equals the larger device's (the larger one's rule reaches it).
 */
function tp_child_bp_keeps_cascade( array $declaration, bool $background_full ): bool {
	if ( 'text-align' === $declaration['prop'] && 'start' === strtolower( $declaration['value'] ) ) {
		return true;
	}

	return $background_full && 0 === strpos( $declaration['prop'], 'background' );
}

/**
 * A text module's own selector (.et_pb_text_12, .et_pb_text_0_tb_footer), where Divi writes its alignment.
 */
function tp_child_bp_is_text_selector( string $selector ): bool {
	return (bool) preg_match( '/^\.et_pb_text_(?=[A-Za-z0-9_-]*\d)[A-Za-z0-9_-]+$/', trim( $selector ) );
}

/**
 * What the tablet and the phone set themselves: [ 'tablet' => [ selector => [ property => true ] ], 'phone' => ... ].
 */
function tp_child_bp_own_properties( string $css, array $ranges ): array {
	$own       = [ 'tablet' => [], 'phone' => [] ];
	$tablet_re = '/^@media\s+only\s+screen\s+and\s*\(\s*max-width\s*:\s*' . $ranges['tablet'] . 'px\s*\)$/i';
	$phone_re  = '/^@media\s+only\s+screen\s+and\s*\(\s*max-width\s*:\s*' . $ranges['phone'] . 'px\s*\)$/i';

	foreach ( tp_child_bp_split_css( $css ) as $statement ) {
		if ( 'media' !== $statement['type'] ) {
			continue;
		}

		$prelude = preg_replace( '/\s+/', ' ', $statement['prelude'] );
		$level   = preg_match( $tablet_re, $prelude ) ? 'tablet' : ( preg_match( $phone_re, $prelude ) ? 'phone' : '' );

		if ( '' === $level ) {
			continue;
		}

		foreach ( tp_child_bp_split_css( $statement['body'] ) as $rule ) {
			if ( 'rule' !== $rule['type'] ) {
				continue;
			}

			$props = array_column( tp_child_bp_split_declarations( $rule['body'] ), 'prop' );

			foreach ( tp_child_bp_split_selectors( $rule['prelude'] ) as $selector ) {
				foreach ( $props as $prop ) {
					$own[ $level ][ $selector ][ $prop ] = true;
				}
			}
		}
	}

	return $own;
}

/**
 * Append CSS under a media query ('' = none), merged with the previous piece when the query is the same.
 */
function tp_child_bp_emit( array &$chunks, string $media, string $css ): void {
	if ( '' === $css ) {
		return;
	}

	$last = count( $chunks ) - 1;

	if ( $last >= 0 && $chunks[ $last ][0] === $media ) {
		$chunks[ $last ][1] .= $css;

		return;
	}

	$chunks[] = [ $media, $css ];
}

/**
 * One rule of the desktop (no media query) or of the tablet (max-width 980px): what is outside the scope and what
 * keeps Divi's cascade stays where it is, the rest gets the device's range. A text module's alignment set for the
 * desktop (or the tablet) leaves Divi's default ("start") to the devices that have none of their own.
 */
function tp_child_bp_transform_rule( array &$chunks, array $rule, string $media, string $range, ?array $scope, array $background_full, array $own, array $queries ): void {
	$groups = [];

	foreach ( tp_child_bp_split_selectors( $rule['prelude'] ) as $selector ) {
		if ( ! tp_child_bp_selector_in_scope( $selector, $scope ) ) {
			$key = 'out';
		} else {
			$classes = tp_child_bp_selector_classes( $selector );
			$subject = $classes ? preg_replace( '/_wrapper$/', '', (string) end( $classes ) ) : '';
			$key     = '' !== $subject && isset( $background_full[ $subject ] ) ? 'in-bg' : 'in';
		}

		$groups[ $key ][] = $selector;
	}

	if ( ! empty( $groups['out'] ) ) {
		tp_child_bp_emit( $chunks, $media, implode( ',', $groups['out'] ) . '{' . $rule['body'] . '}' );
	}

	$declarations = null;

	foreach ( [ 'in', 'in-bg' ] as $key ) {
		if ( empty( $groups[ $key ] ) ) {
			continue;
		}

		if ( null === $declarations ) {
			$declarations = tp_child_bp_split_declarations( $rule['body'] );
		}

		$keep = [];
		$wrap = [];

		foreach ( $declarations as $declaration ) {
			if ( tp_child_bp_keeps_cascade( $declaration, 'in-bg' === $key ) ) {
				$keep[] = $declaration['text'];
			} else {
				$wrap[] = $declaration;
			}
		}

		$selectors = implode( ',', $groups[ $key ] );

		if ( $keep ) {
			tp_child_bp_emit( $chunks, $media, $selectors . '{' . implode( ';', $keep ) . '}' );
		}

		if ( $wrap ) {
			tp_child_bp_emit( $chunks, $range, $selectors . '{' . implode( ';', array_column( $wrap, 'text' ) ) . '}' );
		}

		// The desktop's alignment of a text module: Divi's "start" stays for the smaller devices without their own.
		if ( '' === $media && in_array( 'text-align', array_column( $wrap, 'prop' ), true ) ) {
			foreach ( $groups[ $key ] as $selector ) {
				if ( ! tp_child_bp_is_text_selector( $selector ) ) {
					continue;
				}

				$tablet = isset( $own['tablet'][ $selector ]['text-align'] );
				$phone  = isset( $own['phone'][ $selector ]['text-align'] );
				$query  = ! $tablet && ! $phone ? $queries['below'] : ( ! $tablet ? $queries['tablet'] : ( ! $phone ? $queries['phone'] : '' ) );

				if ( '' !== $query ) {
					tp_child_bp_emit( $chunks, $query, $selector . '{text-align:start}' );
				}
			}
		}
	}
}

/**
 * The CSS with the device ranges: the rules without media query become desktop-only (from 981px), the tablet's
 * max-width query becomes the tablet's range (768-980px), the phone's stays. $scope = order classes in scope
 * (null: every rule). $background_full = order classes of the modules with a background for every device.
 * $own = tp_child_bp_own_properties() of the whole stylesheet (null: of $css).
 */
function tp_child_bp_transform_css( string $css, ?array $scope, array $ranges, array $background_full = [], ?array $own = null ): string {
	$queries   = [
		'desktop' => '@media only screen and (min-width: ' . ( $ranges['tablet'] + 1 ) . 'px)',
		'tablet'  => '@media only screen and (min-width: ' . ( $ranges['phone'] + 1 ) . 'px) and (max-width: ' . $ranges['tablet'] . 'px)',
		'below'   => '@media only screen and (max-width: ' . $ranges['tablet'] . 'px)',
		'phone'   => '@media only screen and (max-width: ' . $ranges['phone'] . 'px)',
	];
	$tablet_re = '/^@media\s+only\s+screen\s+and\s*\(\s*max-width\s*:\s*' . $ranges['tablet'] . 'px\s*\)$/i';
	$own       = null === $own ? tp_child_bp_own_properties( $css, $ranges ) : $own;
	$chunks    = [];

	foreach ( tp_child_bp_split_css( $css ) as $statement ) {
		if ( 'rule' === $statement['type'] ) {
			tp_child_bp_transform_rule( $chunks, $statement, '', $queries['desktop'], $scope, $background_full, $own, $queries );
			continue;
		}

		if ( 'media' === $statement['type'] && preg_match( $tablet_re, preg_replace( '/\s+/', ' ', $statement['prelude'] ) ) ) {
			foreach ( tp_child_bp_split_css( $statement['body'] ) as $inner ) {
				if ( 'rule' === $inner['type'] ) {
					tp_child_bp_transform_rule( $chunks, $inner, $statement['prelude'], $queries['tablet'], $scope, $background_full, $own, $queries );
				} else {
					tp_child_bp_emit( $chunks, $statement['prelude'], $inner['text'] );
				}
			}

			continue;
		}

		tp_child_bp_emit( $chunks, '', $statement['text'] );
	}

	$result = '';

	foreach ( $chunks as $chunk ) {
		$result .= '' === $chunk[0] ? $chunk[1] : $chunk[0] . '{' . $chunk[1] . '}';
	}

	return $result;
}

/**
 * et_core_page_resource_get_data: the module styles of the page (priority 40) with the ranges.
 */
function tp_child_bp_resource_data( $data, $context, $resource ) {
	$ranges = tp_child_bp_ranges();
	$scope  = tp_child_bp_scope();

	if ( ! $ranges || ! $scope || ! is_array( $data ) || empty( $data[40] ) || ! is_array( $data[40] ) ) {
		return $data;
	}

	$slug = is_object( $resource ) && isset( $resource->slug ) ? (string) $resource->slug : '';

	if ( 0 !== strpos( $slug, 'et-core-unified' ) ) {
		return $data;
	}

	$own = tp_child_bp_own_properties( implode( "\n", array_filter( $data[40], 'is_string' ) ), $ranges );

	foreach ( $data[40] as $key => $css ) {
		if ( is_string( $css ) && '' !== $css ) {
			$data[40][ $key ] = tp_child_bp_transform_css( $css, $scope, $ranges, tp_child_bp_background_full(), $own );
		}
	}

	return $data;
}
add_filter( 'et_core_page_resource_get_data', 'tp_child_bp_resource_data', 10, 3 );

/**
 * A list (0, 1, 2... keys), as array_is_list() of PHP 8.1.
 */
function tp_child_bp_is_list( array $value ): bool {
	return [] === $value || array_keys( $value ) === range( 0, count( $value ) - 1 );
}

/**
 * Values that a device takes from a larger one today, written on it: the desktop's on tablet and phone, the
 * tablet's on phone, key by key, also for the hover/sticky states. The background video and the column layout
 * stay as they are.
 */
function tp_child_bp_freeze_responsive( array $node, string $group ): array {
	$skip = [];

	if ( 'background' === $group ) {
		$skip = [ 'video', 'parallax' ];
	} elseif ( 'sizing' === $group ) {
		$skip = [ 'flexType' ];
	} elseif ( in_array( $group, [ 'font', 'headingFont', 'bodyFont' ], true ) ) {
		$skip = [ 'headingLevel' ];
	}

	$merge = static function ( $base, $over ) use ( &$merge ) {
		if ( ! is_array( $base ) || ! is_array( $over ) || tp_child_bp_is_list( $base ) || tp_child_bp_is_list( $over ) ) {
			return null === $over ? $base : $over;
		}

		foreach ( $over as $k => $v ) {
			$base[ $k ] = array_key_exists( $k, $base ) ? $merge( $base[ $k ], $v ) : $v;
		}

		return $base;
	};
	$strip = static function ( $states ) use ( $skip, $group ) {
		foreach ( (array) $states as $state => $value ) {
			if ( is_array( $value ) && ! tp_child_bp_is_list( $value ) ) {
				foreach ( $skip as $key ) {
					unset( $states[ $state ][ $key ] );
				}

				// The parallax of a background image is markup Divi prints per device, as the video.
				if ( 'background' === $group && isset( $states[ $state ]['image'] ) && is_array( $states[ $state ]['image'] ) ) {
					unset( $states[ $state ]['image']['parallax'] );
				}

				if ( [] === $states[ $state ] ) {
					unset( $states[ $state ] );
				}
			}
		}

		return $states;
	};

	$above = null;

	foreach ( [ 'desktop', 'tablet', 'phone' ] as $bp ) {
		$own = isset( $node[ $bp ] ) && is_array( $node[ $bp ] ) ? $node[ $bp ] : null;

		if ( null !== $above ) {
			$inherited = $strip( $above );
			$merged    = null === $own ? $inherited : $merge( $inherited, $own );

			if ( is_array( $merged ) && [] !== $merged && $merged !== $own ) {
				$node[ $bp ] = $merged;
			}
		}

		$current = isset( $node[ $bp ] ) && is_array( $node[ $bp ] ) ? $node[ $bp ] : null;
		$above   = null === $above ? $current : ( null === $current ? $above : $merge( $above, $current ) );
	}

	return $node;
}

/**
 * The decoration values of one module.
 */
function tp_child_bp_freeze_attrs( array $attrs ): array {
	// Decorations that are not CSS of the module (or that Divi prints per device in other ways).
	$skip_groups = [ 'attributes', 'animation', 'disabledOn', 'conditions', 'interactions', 'scroll', 'sticky', 'link' ];
	// Advanced settings that Divi prints as CSS per device: a button's alignment, a text's alignment.
	$advanced_groups = [ 'alignment', 'text' ];

	foreach ( $attrs as $element => $element_attrs ) {
		if ( ! is_array( $element_attrs ) ) {
			continue;
		}

		if ( ! empty( $element_attrs['decoration'] ) && is_array( $element_attrs['decoration'] ) ) {
			foreach ( $element_attrs['decoration'] as $group => $value ) {
				if ( in_array( $group, $skip_groups, true ) || ! is_array( $value ) ) {
					continue;
				}

				$attrs[ $element ]['decoration'][ $group ] = tp_child_bp_freeze_group( $value, (string) $group );
			}
		}

		foreach ( $advanced_groups as $group ) {
			if ( isset( $element_attrs['advanced'][ $group ] ) && is_array( $element_attrs['advanced'][ $group ] ) ) {
				$attrs[ $element ]['advanced'][ $group ] = tp_child_bp_freeze_group( $element_attrs['advanced'][ $group ], $group );
			}
		}
	}

	return $attrs;
}

/**
 * A decoration group: responsive values may sit one or two levels down (headingFont > h2 > font).
 */
function tp_child_bp_freeze_group( array $value, string $group ): array {
	if ( isset( $value['desktop'] ) || isset( $value['tablet'] ) ) {
		return tp_child_bp_freeze_responsive( $value, $group );
	}

	foreach ( $value as $key => $sub ) {
		if ( is_array( $sub ) ) {
			$value[ $key ] = tp_child_bp_freeze_group( $sub, $group );
		}
	}

	return $value;
}

/**
 * Blocks: the modules inside the sections with a tp- class (with $attributes_only, only the sections that have it
 * in Advanced > Attributes alone).
 */
function tp_child_bp_freeze_blocks( array $blocks, bool $in_scope = false, bool $attributes_only = false ): array {
	foreach ( $blocks as $i => $block ) {
		if ( empty( $block['blockName'] ) ) {
			continue;
		}

		$section = 'divi/section' === $block['blockName'] && (
			$attributes_only
				? tp_child_bp_section_attributes_only( $block )
				: preg_match( '/(^|\s)tp-/', tp_child_bp_section_class( $block ) )
		);
		$here    = $in_scope || $section;

		if ( $here && ! empty( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
			$blocks[ $i ]['attrs'] = tp_child_bp_freeze_attrs( $block['attrs'] );
		}

		if ( ! empty( $block['innerBlocks'] ) ) {
			$blocks[ $i ]['innerBlocks'] = tp_child_bp_freeze_blocks( $block['innerBlocks'], $here, $attributes_only );
		}
	}

	return $blocks;
}

/**
 * One post: written only when something changes (one revision more).
 */
function tp_child_bp_freeze_post( int $post_id, bool $attributes_only = false ): bool {
	$content = (string) get_post_field( 'post_content', $post_id );

	if ( '' === $content || false === strpos( $content, 'tp-' ) || false === strpos( $content, 'wp:divi/' ) ) {
		return false;
	}

	$frozen = serialize_blocks( tp_child_bp_freeze_blocks( parse_blocks( $content ), false, $attributes_only ) );

	if ( $frozen === $content || serialize_blocks( parse_blocks( $content ) ) === $frozen ) {
		return false;
	}

	wp_update_post(
		[
			'ID'           => $post_id,
			'post_content' => wp_slash( $frozen ),
		]
	);

	return true;
}

/**
 * The posts with the theme's sections: pages, posts, Theme Builder layouts, Divi Library.
 */
function tp_child_bp_freeze_posts( array $ids = [], bool $attributes_only = false ): int {
	if ( ! $ids ) {
		$ids = get_posts(
			[
				'post_type'      => [ 'page', 'post', 'et_header_layout', 'et_footer_layout', 'et_body_layout', 'et_pb_layout' ],
				'post_status'    => [ 'publish', 'draft', 'private', 'pending', 'future' ],
				'posts_per_page' => -1,
				'fields'         => 'ids',
			]
		);
	}

	$changed = 0;

	foreach ( array_unique( array_map( 'intval', $ids ) ) as $id ) {
		if ( $id > 0 && tp_child_bp_freeze_post( $id, $attributes_only ) ) {
			++$changed;
		}
	}

	return $changed;
}

/**
 * Once after the update to 0.6.5 (and on a site where the theme already created its pages): what shows today
 * on each device stays, then Divi rebuilds the CSS of the pages with the ranges.
 */
function tp_child_bp_freeze_065(): void {
	$flag = 'tp_child_bp_freeze_065_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	if ( tp_child_bp_ranges() ) {
		tp_child_bp_freeze_posts();
	}

	tp_child_clear_divi_static_css();
	update_option( $flag, TP_CHILD_VERSION, true );
}
add_action( 'init', 'tp_child_bp_freeze_065', 47 );

/**
 * Once after the update to 0.6.6: the sections made by hand in the Visual Builder (their tp- class in Advanced >
 * Attributes) get on the site the device ranges that the builder already showed. What shows today on each device
 * stays, as the 0.6.5 did for the theme's sections; from now on a change made for one device stays on it.
 */
function tp_child_bp_freeze_066(): void {
	$flag = 'tp_child_bp_freeze_066_v1';

	if ( get_option( $flag ) ) {
		return;
	}

	if ( tp_child_bp_ranges() ) {
		tp_child_bp_freeze_posts( [], true );
	}

	// The CSS Divi saved for those pages has no ranges yet.
	tp_child_clear_divi_static_css();
	update_option( $flag, TP_CHILD_VERSION, true );
}
add_action( 'init', 'tp_child_bp_freeze_066', 48 );

/**
 * Visual Builder: the same ranges on the <style> of the modules inside the theme's sections.
 */
function tp_child_vb_bp_isolation_script(): void {
	if ( ! tp_child_is_visual_builder() ) {
		return;
	}

	$ranges = tp_child_bp_ranges();

	if ( ! $ranges ) {
		return;
	}
	?>
<script id="tp-vb-bp-isolation-js">
(function(){
	var TABLET = <?php echo (int) $ranges['tablet']; ?>, PHONE = <?php echo (int) $ranges['phone']; ?>;
	var Q = {
		desktop: '@media only screen and (min-width: ' + (TABLET + 1) + 'px)',
		tablet: '@media only screen and (min-width: ' + (PHONE + 1) + 'px) and (max-width: ' + TABLET + 'px)',
		below: '@media only screen and (max-width: ' + TABLET + 'px)',
		phone: '@media only screen and (max-width: ' + PHONE + 'px)'
	};
	var TABLET_RE = new RegExp('^@media\\s+only\\s+screen\\s+and\\s*\\(\\s*max-width\\s*:\\s*' + TABLET + 'px\\s*\\)$', 'i');
	var PHONE_RE = new RegExp('^@media\\s+only\\s+screen\\s+and\\s*\\(\\s*max-width\\s*:\\s*' + PHONE + 'px\\s*\\)$', 'i');
	var TEXT_SEL = /^\.et_pb_text_(?=[A-Za-z0-9_-]*\d)[A-Za-z0-9_-]+$/;

	// Top-level statements: {type: rule|media|other, prelude, body, text}.
	function split(css){
		var out = [], i = 0, start = 0, n = css.length;
		while (i < n) {
			var ch = css.charAt(i);
			if (ch === '/' && css.charAt(i + 1) === '*') { var e = css.indexOf('*/', i + 2); i = e < 0 ? n : e + 2; continue; }
			if (ch === '{') {
				var prelude = css.slice(start, i).trim(), depth = 1, j = i + 1;
				while (j < n && depth > 0) {
					var c = css.charAt(j);
					if (c === '"' || c === "'") { var k = j + 1; while (k < n && css.charAt(k) !== c) { k += css.charAt(k) === '\\' ? 2 : 1; } j = k + 1; continue; }
					if (c === '/' && css.charAt(j + 1) === '*') { var e2 = css.indexOf('*/', j + 2); j = e2 < 0 ? n : e2 + 2; continue; }
					if (c === '{') { depth++; } else if (c === '}') { depth--; }
					j++;
				}
				var type = prelude.charAt(0) === '@' ? (/^@media/i.test(prelude) ? 'media' : 'other') : 'rule';
				out.push({ type: type, prelude: prelude, body: css.slice(i + 1, Math.max(i + 1, j - 1)), text: css.slice(start, j) });
				i = j; start = j; continue;
			}
			i++;
		}
		if (css.slice(start).trim()) { out.push({ type: 'other', prelude: '', body: '', text: css.slice(start) }); }
		return out;
	}
	// Selectors split at the top-level commas.
	function selectors(prelude){
		var parts = [], depth = 0, cur = '';
		for (var i = 0; i < prelude.length; i++) {
			var c = prelude.charAt(i);
			if (c === '(' || c === '[') { depth++; } else if (c === ')' || c === ']') { depth--; }
			if (c === ',' && depth === 0) { parts.push(cur.trim()); cur = ''; continue; }
			cur += c;
		}
		parts.push(cur.trim());
		return parts.filter(function(p){ return p.length > 0; });
	}
	// Declarations: [{prop, value, text}].
	function declarations(body){
		var parts = [], depth = 0, cur = '', n = body.length;
		for (var i = 0; i < n; i++) {
			var c = body.charAt(i);
			if (c === '"' || c === "'") { var k = i + 1; while (k < n && body.charAt(k) !== c) { k += body.charAt(k) === '\\' ? 2 : 1; } cur += body.slice(i, k + 1); i = k; continue; }
			if (c === '(') { depth++; } else if (c === ')') { depth--; }
			if (c === ';' && depth <= 0) { parts.push(cur); cur = ''; continue; }
			cur += c;
		}
		parts.push(cur);
		var out = [];
		parts.forEach(function(t){
			t = t.trim();
			if (!t) { return; }
			var colon = t.indexOf(':');
			out.push({ prop: colon < 0 ? '' : t.slice(0, colon).trim().toLowerCase(), value: colon < 0 ? '' : t.slice(colon + 1).trim(), text: t });
		});
		return out;
	}
	// Divi's default alignment of the texts and a background set for every device keep Divi's cascade.
	function keeps(d, bgFull){
		if (d.prop === 'text-align' && d.value.toLowerCase() === 'start') { return true; }
		return bgFull && d.prop.indexOf('background') === 0;
	}
	function ownProperties(stmts){
		var own = { tablet: {}, phone: {} };
		stmts.forEach(function(s){
			if (s.type !== 'media') { return; }
			var p = s.prelude.replace(/\s+/g, ' '), level = TABLET_RE.test(p) ? 'tablet' : (PHONE_RE.test(p) ? 'phone' : '');
			if (!level) { return; }
			split(s.body).forEach(function(r){
				if (r.type !== 'rule') { return; }
				var props = declarations(r.body).map(function(d){ return d.prop; });
				selectors(r.prelude).forEach(function(sel){
					var o = own[level][sel] || (own[level][sel] = {});
					props.forEach(function(prop){ o[prop] = true; });
				});
			});
		});
		return own;
	}
	function transform(css, bgFull){
		var stmts = split(css), own = ownProperties(stmts), chunks = [];
		function emit(media, text){
			if (!text) { return; }
			var last = chunks[chunks.length - 1];
			if (last && last[0] === media) { last[1] += text; } else { chunks.push([media, text]); }
		}
		function rule(r, media, range){
			var sels = selectors(r.prelude), list = declarations(r.body), keep = [], wrap = [];
			list.forEach(function(d){ if (keeps(d, bgFull)) { keep.push(d.text); } else { wrap.push(d); } });
			if (keep.length) { emit(media, sels.join(',') + '{' + keep.join(';') + '}'); }
			if (wrap.length) { emit(range, sels.join(',') + '{' + wrap.map(function(d){ return d.text; }).join(';') + '}'); }
			// The desktop's alignment of a text module: Divi's "start" stays for the smaller devices without their own.
			if (media === '' && wrap.some(function(d){ return d.prop === 'text-align'; })) {
				sels.forEach(function(sel){
					if (!TEXT_SEL.test(sel)) { return; }
					var t = !!(own.tablet[sel] && own.tablet[sel]['text-align']), p = !!(own.phone[sel] && own.phone[sel]['text-align']);
					var q = !t && !p ? Q.below : (!t ? Q.tablet : (!p ? Q.phone : ''));
					if (q) { emit(q, sel + '{text-align:start}'); }
				});
			}
		}
		stmts.forEach(function(s){
			if (s.type === 'rule') { rule(s, '', Q.desktop); return; }
			if (s.type === 'media' && TABLET_RE.test(s.prelude.replace(/\s+/g, ' '))) {
				split(s.body).forEach(function(r){ if (r.type === 'rule') { rule(r, s.prelude, Q.tablet); } else { emit(s.prelude, r.text); } });
				return;
			}
			emit('', s.text);
		});
		return chunks.map(function(c){ return c[0] ? c[0] + '{' + c[1] + '}' : c[1]; }).join('');
	}
	// Whether one of the module's backgrounds has a value for tablet and for phone (Divi's store).
	function backgroundFull(module){
		try {
			var id = module && module.getAttribute('data-id');
			var store = window.divi && window.divi.data && window.divi.data.select('divi/edit-post');
			var attrs = id && store && store.getModuleAttrs ? store.getModuleAttrs(id) : null;
			attrs = attrs && typeof attrs.toJS === 'function' ? attrs.toJS() : attrs;
			for (var key in attrs) {
				var bg = attrs[key] && attrs[key].decoration && attrs[key].decoration.background;
				if (bg && bg.tablet && bg.phone) { return true; }
			}
		} catch (e) {}
		return false;
	}
	function inScope(style){
		var module = style.parentElement;
		var section = module && module.closest ? module.closest('.et_pb_section') : null;
		return !!section && /(^|\s)tp-/.test(section.className);
	}
	// Divi's <style> (kept by React) is left as it is, only switched off; the ranges go in a copy right after it,
	// redone each time Divi rewrites the original.
	var copies = new WeakMap();
	function release(style){
		var copy = copies.get(style);
		if (copy && copy.parentNode) { copy.parentNode.removeChild(copy); }
		copies.delete(style);
		if (style.getAttribute('media') === 'not all') { style.removeAttribute('media'); }
	}
	function fix(style){
		if (!style || !style.parentNode) { return; }
		if (!inScope(style)) { release(style); return; }
		var next = transform(style.textContent || '', backgroundFull(style.parentElement));
		var copy = copies.get(style);
		if (!copy || copy.parentNode !== style.parentNode || copy.previousSibling !== style) {
			if (copy && copy.parentNode) { copy.parentNode.removeChild(copy); }
			copy = document.createElement('style');
			copy.className = 'tp-vb-bp-styles';
			style.parentNode.insertBefore(copy, style.nextSibling);
			copies.set(style, copy);
		}
		if (copy.textContent !== next) { copy.textContent = next; }
		if (style.getAttribute('media') !== 'not all') { style.setAttribute('media', 'not all'); }
	}
	function scan(root){
		var list = (root.querySelectorAll ? root.querySelectorAll('style.et-vb-module-styles') : []);
		for (var i = 0; i < list.length; i++) { fix(list[i]); }
		if (root.matches && root.matches('style.et-vb-module-styles')) { fix(root); }
	}
	function dropped(node){
		if (node.nodeType !== 1) { return; }
		if (node.matches && node.matches('style.et-vb-module-styles')) { release(node); return; }
		var list = node.querySelectorAll ? node.querySelectorAll('style.et-vb-module-styles') : [];
		for (var i = 0; i < list.length; i++) { release(list[i]); }
	}
	function boot(){
		scan(document);
		new MutationObserver(function(mutations){
			mutations.forEach(function(m){
				if (m.type === 'characterData') { var p = m.target.parentNode; if (p && p.matches && p.matches('style.et-vb-module-styles')) { fix(p); } return; }
				for (var j = 0; j < m.removedNodes.length; j++) { dropped(m.removedNodes[j]); }
				if (m.target && m.target.matches && m.target.matches('style.et-vb-module-styles')) { fix(m.target); }
				for (var i = 0; i < m.addedNodes.length; i++) { if (m.addedNodes[i].nodeType === 1) { scan(m.addedNodes[i]); } }
			});
		}).observe(document.documentElement, { childList: true, subtree: true, characterData: true });
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', boot); } else { boot(); }
})();
</script>
	<?php
}
add_action( 'wp_footer', 'tp_child_vb_bp_isolation_script', 5 );
