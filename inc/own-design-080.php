<?php
/**
 * 0.8.0: Divi's design values on the pieces the theme draws itself.
 *
 * - The buttons with the arrow ("Guarda il menù", "Le materie prime", "Mappe": classes tp-hero-button,
 *   tp-intro-button, tp-location-button). On the site tasche-piene.js moves the text of the link into a label
 *   and adds the arrow: the button one sees is those two pieces, not the link, so the colour, the font, the
 *   background, the border, the corners and the shadow chosen in Divi reached the link and nothing changed
 *   (or a coloured box, or a shadow wider than the button, came out beside it). The link now says which
 *   values it has, with a class per value and device (tp-own-color-d, tp-own-bg-t, tp-own-border-p ...), and
 *   the stylesheet lets the pieces take those values from the link (assets/css/tasche-piene-figma.css,
 *   "0.8.0"). A button without values of its own has no such class and is drawn as before.
 * - The Striscia scorrevole: the strip painted its green over the module's background (Design > Sfondo). A
 *   module with a background of its own says so (tp-own-bg-*) and the strip lets it through.
 *
 * The devices follow Divi: with the theme's device isolation (inc/breakpoint-isolation.php) a value is of its
 * device only; without it the value of the computer is also of tablet and phone, and the tablet's of the phone.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether a saved value says something (a colour, a size, a list of styles).
 *
 * @param mixed $value Value of a field as Divi saves it.
 */
function tp_child_own_design_has( $value ): bool {
	if ( is_array( $value ) ) {
		foreach ( $value as $item ) {
			if ( tp_child_own_design_has( $item ) ) {
				return true;
			}
		}

		return false;
	}

	return is_scalar( $value ) && '' !== trim( (string) $value );
}

/**
 * The suffixes of the devices where a responsive attribute has a value for which $test says yes, in the given
 * state ("value" or "hover").
 *
 * @param mixed    $node  Responsive attribute: [ 'desktop' => [ 'value' => ..., 'hover' => ... ], 'tablet' => ... ].
 * @param string   $state "value" or "hover".
 * @param callable $test  Receives the value of a device, returns bool.
 * @return string[] Among "d", "t", "p".
 */
function tp_child_own_design_devices( $node, string $state, callable $test ): array {
	if ( ! is_array( $node ) ) {
		return [];
	}

	$isolated = function_exists( 'tp_child_bp_ranges' ) && null !== tp_child_bp_ranges();
	$found    = [];
	$above    = false;

	foreach ( [ 'desktop' => 'd', 'tablet' => 't', 'phone' => 'p' ] as $device => $suffix ) {
		$value = $node[ $device ][ $state ] ?? null;
		$own   = null !== $value && (bool) $test( $value );

		if ( $own || ( ! $isolated && $above ) ) {
			$found[] = $suffix;
		}

		$above = $above || $own;
	}

	return $found;
}

/**
 * The classes that say which values of Pulsante > Design (and the shadow of Design > Ombra riquadro) a button
 * has, per device.
 *
 * @param array $attrs Attributes of a divi/button block.
 * @return string[]
 */
function tp_child_own_design_button_classes( array $attrs ): array {
	$decoration = is_array( $attrs['button']['decoration'] ?? null ) ? $attrs['button']['decoration'] : [];
	$classes    = [];
	$add        = static function ( string $name, array $devices ) use ( &$classes ): void {
		foreach ( $devices as $suffix ) {
			$classes[] = 'tp-own-' . $name . '-' . $suffix;
		}
	};
	$key        = static function ( string $key ): callable {
		return static function ( $value ) use ( $key ): bool {
			return is_array( $value ) && tp_child_own_design_has( $value[ $key ] ?? null );
		};
	};

	$font = $decoration['font']['font'] ?? null;

	foreach ( [ 'color' => 'color', 'size' => 'size', 'family' => 'family', 'weight' => 'weight', 'style' => 'style', 'letterSpacing' => 'ls', 'lineHeight' => 'lh' ] as $field => $name ) {
		$add( $name, tp_child_own_design_devices( $font, 'value', $key( $field ) ) );
	}

	$add( 'hcolor', tp_child_own_design_devices( $font, 'hover', $key( 'color' ) ) );

	$background = $decoration['background'] ?? null;
	$painted    = static function ( $value ): bool {
		return is_array( $value ) && tp_child_own_design_has( $value['color'] ?? null );
	};
	$pictured   = static function ( $value ): bool {
		return is_array( $value ) && ( 'on' === ( $value['gradient']['enabled'] ?? '' ) || tp_child_own_design_has( $value['image']['url'] ?? null ) );
	};

	$add( 'bg', tp_child_own_design_devices( $background, 'value', $painted ) );
	$add( 'bgimg', tp_child_own_design_devices( $background, 'value', $pictured ) );
	$add( 'hbg', tp_child_own_design_devices( $background, 'hover', $painted ) );

	$border = $decoration['border'] ?? null;

	$add( 'border', tp_child_own_design_devices( $border, 'value', $key( 'styles' ) ) );
	$add( 'radius', tp_child_own_design_devices( $border, 'value', $key( 'radius' ) ) );

	// The shadow is the module's (Design > Ombra riquadro): Divi writes it on the link.
	$add(
		'shadow',
		tp_child_own_design_devices(
			$attrs['module']['decoration']['boxShadow'] ?? null,
			'value',
			static function ( $value ): bool {
				return is_array( $value ) && tp_child_own_design_has( $value['style'] ?? null ) && 'none' !== $value['style'];
			}
		)
	);

	return array_values( array_unique( $classes ) );
}

/**
 * The classes that say on which devices a module has a background of its own (Design > Sfondo).
 *
 * @param array $attrs Attributes of the block.
 * @return string[]
 */
function tp_child_own_design_background_classes( array $attrs ): array {
	$devices = tp_child_own_design_devices(
		$attrs['module']['decoration']['background'] ?? null,
		'value',
		static function ( $value ): bool {
			return is_array( $value ) && ( tp_child_own_design_has( $value['color'] ?? null ) || 'on' === ( $value['gradient']['enabled'] ?? '' ) || tp_child_own_design_has( $value['image']['url'] ?? null ) );
		}
	);

	return array_map(
		static function ( string $suffix ): string {
			return 'tp-own-bg-' . $suffix;
		},
		$devices
	);
}

/**
 * Add classes to the first element of the given tag that has $needle among its classes.
 */
function tp_child_own_design_add_classes( string $html, string $tag, string $needle, array $classes ): string {
	if ( ! $classes ) {
		return $html;
	}

	return (string) preg_replace_callback(
		'/<' . $tag . '\b[^>]*\bclass="([^"]*)"/',
		static function ( array $match ) use ( $needle, $classes ): string {
			if ( ! preg_match( '/(?<![\w-])' . preg_quote( $needle, '/' ) . '(?![\w-])/', $match[1] ) ) {
				return $match[0];
			}

			return str_replace( 'class="' . $match[1] . '"', 'class="' . esc_attr( trim( $match[1] . ' ' . implode( ' ', $classes ) ) ) . '"', $match[0] );
		},
		$html,
		1
	);
}

/**
 * render_block: the classes of the values of their own on the theme's buttons and on the Striscia scorrevole.
 */
function tp_child_own_design_classes( $block_content, $block ) {
	if ( ! is_string( $block_content ) || '' === $block_content || ! is_array( $block ) ) {
		return $block_content;
	}

	$name  = $block['blockName'] ?? '';
	$attrs = is_array( $block['attrs'] ?? null ) ? $block['attrs'] : [];

	if ( 'divi/button' === $name ) {
		if ( ! preg_match( '/<a\b[^>]*\bclass="[^"]*(?<![\w-])tp-(?:intro|hero|location)-button(?![\w-])/', $block_content ) ) {
			return $block_content;
		}

		return tp_child_own_design_add_classes( $block_content, 'a', 'et_pb_button', tp_child_own_design_button_classes( $attrs ) );
	}

	if ( 'tp/marquee' === $name ) {
		return tp_child_own_design_add_classes( $block_content, 'div', 'tp_marquee', tp_child_own_design_background_classes( $attrs ) );
	}

	return $block_content;
}
add_filter( 'render_block', 'tp_child_own_design_classes', 12, 2 );
