<?php
/**
 * Opt-in bridge between newly authored Divi design values and the original theme.
 *
 * No mask means no markup, stylesheet, migration, or change to the legacy design.
 * Only values recorded by the editor AND still present in the saved block
 * are eligible. Divi remains responsible for CSS generation and breakpoints.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use ET\Builder\FrontEnd\BlockParser\BlockParserStore;

/** Keep the optional editor mask through WordPress block validation. */
function tp_child_design_register_attribute( array $metadata ): array {
	$name = $metadata['name'] ?? '';
	if ( str_starts_with( $name, 'divi/' ) || str_starts_with( $name, 'tp/' ) ) {
		$metadata['attributes']['tpDesignOverrides'] = [ 'type' => 'object' ];
	}
	return $metadata;
}
add_filter( 'block_type_metadata', 'tp_child_design_register_attribute', 20 );

/** Load capture hooks in the builder app before native modules register. */
function tp_child_design_register_builder_asset(): void {
	if ( ! class_exists( '\ET\Builder\VisualBuilder\Assets\PackageBuildManager' ) ) { return; }
	\ET\Builder\VisualBuilder\Assets\PackageBuildManager::register_package_build(
		[
			'name' => 'tp-divi-design-bridge',
			'version' => tp_child_asset_version( 'assets/js/divi-design-bridge.js' ),
			'script' => [
				'src' => TP_CHILD_URL . 'assets/js/divi-design-bridge.js',
				'deps' => [ 'divi-vendor-wp-hooks' ],
				'enqueue_top_window' => false,
				'enqueue_app_window' => true,
			],
		]
	);
}
add_action( 'et_fb_framework_loaded', 'tp_child_design_register_builder_asset', 25 );

/** PackageBuildManager registers WP handles after its before-enqueue action. */
function tp_child_design_order_builder_scripts(): void {
	$scripts = wp_scripts();
	if ( ! isset( $scripts->registered['tp-divi-design-bridge'], $scripts->registered['divi-module-library'] ) ) { return; }
	$library = $scripts->registered['divi-module-library'];
	if ( ! in_array( 'tp-divi-design-bridge', $library->deps, true ) ) {
		$library->deps[] = 'tp-divi-design-bridge';
	}
}
add_action( 'wp_print_scripts', 'tp_child_design_order_builder_scripts', 0 );
add_action( 'wp_print_footer_scripts', 'tp_child_design_order_builder_scripts', 0 );
add_action( 'admin_print_scripts', 'tp_child_design_order_builder_scripts', 0 );

/** Request-local state; deliberately separate from Divi's cached legacy CSS. */
function &tp_child_design_state(): array {
	static $state = [ 'sources' => [], 'buttonSources' => [], 'rules' => [], 'variables' => [] ];
	return $state;
}

function tp_child_design_get_path( array $attrs, string $path, &$exists = null ) {
	$value = $attrs;
	$exists = true;
	foreach ( explode( '.', $path ) as $key ) {
		if ( ! is_array( $value ) || ! array_key_exists( $key, $value ) ) {
			$exists = false;
			return null;
		}
		$value = $value[ $key ];
	}
	return $value;
}

/** Arrays such as font styles and gradient stops are atomic editor values. */
function tp_child_design_value_is_safe( $value, int $depth = 0 ): bool {
	if ( $depth > 12 ) { return false; }
	if ( is_array( $value ) ) {
		if ( count( $value ) > 200 ) { return false; }
		foreach ( $value as $key => $item ) {
			if ( in_array( (string) $key, [ '__proto__', 'prototype', 'constructor' ], true ) || ! tp_child_design_value_is_safe( $item, $depth + 1 ) ) { return false; }
		}
		return true;
	}
	return is_scalar( $value ) && ( ! is_string( $value ) || ( strlen( $value ) <= 8192 && ! preg_match( '/[<>\x00-\x08\x0b\x0c\x0e-\x1f]/', $value ) ) );
}

/** Ignore stale mask entries after reset, undo, presets, or external changes. */
function tp_child_design_valid_values( array $attrs ): array {
	$mask = $attrs['tpDesignOverrides'] ?? [];
	if ( ! is_array( $mask ) || 1 !== (int) ( $mask['version'] ?? 0 ) || ! is_array( $mask['values'] ?? null ) ) {
		return [];
	}
	$values = [];
	foreach ( array_slice( $mask['values'], 0, 1000, true ) as $path => $value ) {
		if ( ! is_string( $path ) || ! preg_match( '/^[A-Za-z][A-Za-z0-9_]*\.decoration\.[A-Za-z][A-Za-z0-9_]*(?:\.[A-Za-z0-9_-]+){2,16}$/D', $path ) || str_starts_with( $path, 'tpDesign' ) ) {
			continue;
		}
		if ( ! tp_child_design_value_is_safe( $value ) || '' === $value || [] === $value ) {
			continue;
		}
		$current = tp_child_design_get_path( $attrs, $path, $exists );
		if ( $exists && $current === $value ) {
			$values[ $path ] = $value;
		}
	}
	return $values;
}

/** Partition the sparse saved leaf values by module.json element attribute. */
function tp_child_design_sparse_decorations( array $values ): array {
	$elements = [];
	foreach ( $values as $path => $value ) {
		$parts = explode( '.', $path );
		$attr = array_shift( $parts );
		array_shift( $parts ); // decoration.
		$node = &$elements[ $attr ];
		foreach ( $parts as $part ) {
			if ( ! is_array( $node ) ) {
				$node = [];
			}
			$node = &$node[ $part ];
		}
		$node = $value;
		unset( $node );
	}
	return $elements;
}

/** Inline locks in the legacy iOS runtime consume these optional variables. */
function tp_child_design_inline_properties(): array {
	return [ 'position', 'left', 'top', 'right', 'bottom', 'inset', 'width', 'min-width', 'max-width', 'height', 'min-height', 'max-height', 'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left', 'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left', 'display', 'visibility', 'opacity', 'transform', '-webkit-transform', 'z-index', 'text-align', 'white-space', 'pointer-events', 'background-position', 'background-size' ];
}

/** Split declarations without breaking semicolons inside strings/URLs. */
function tp_child_design_declarations( string $css ): array {
	$parts = [];
	$part = '';
	$quote = '';
	$depth = 0;
	$escaped = false;
	for ( $i = 0, $length = strlen( $css ); $i < $length; $i++ ) {
		$char = $css[ $i ];
		if ( $escaped ) {
			$part .= $char;
			$escaped = false;
			continue;
		}
		if ( '\\' === $char ) {
			$escaped = true;
		} elseif ( '' !== $quote ) {
			if ( $char === $quote ) { $quote = ''; }
		} elseif ( '"' === $char || "'" === $char ) {
			$quote = $char;
		} elseif ( '(' === $char ) {
			$depth++;
		} elseif ( ')' === $char ) {
			$depth--;
		} elseif ( ';' === $char && 0 === $depth ) {
			$parts[] = $part;
			$part = '';
			continue;
		}
		$part .= $char;
	}
	$parts[] = $part;
	$result = [];
	foreach ( $parts as $declaration ) {
		if ( preg_match( '/^\s*([a-zA-Z-]+)\s*:\s*(.+?)\s*$/sD', $declaration, $match ) ) {
			$result[ strtolower( $match[1] ) ] = preg_replace( '/\s*!important\s*$/i', '', $match[2] );
		}
	}
	return $result;
}

/** Add replacement-DOM selectors; leave the original selector intact. */
function tp_child_design_selector_parts( string $selector ): array {
	$parts = [];
	$part = '';
	$depth = 0;
	$quote = '';
	$escaped = false;
	for ( $i = 0, $length = strlen( $selector ); $i < $length; $i++ ) {
		$char = $selector[ $i ];
		if ( $escaped ) { $escaped = false; }
		elseif ( '\\' === $char ) { $escaped = true; }
		elseif ( '' !== $quote ) { if ( $char === $quote ) { $quote = ''; } }
		elseif ( '"' === $char || "'" === $char ) { $quote = $char; }
		elseif ( '(' === $char || '[' === $char ) { $depth++; }
		elseif ( ')' === $char || ']' === $char ) { $depth--; }
		elseif ( ',' === $char && 0 === $depth ) { $parts[] = trim( $part ); $part = ''; continue; }
		$part .= $char;
	}
	$parts[] = trim( $part );
	return array_values( array_filter( $parts ) );
}

function tp_child_design_alias_selectors( string $selector, string $source ): string {
	$selectors = [ $selector ];
	$pattern = '/\.' . preg_quote( $source, '/' ) . '(?![a-zA-Z0-9_-])/';
	$alias = '[data-tp-design-source~="' . $source . '"]';
	if ( ! preg_match( $pattern, $selector ) ) {
		return $selector;
	}
	$selectors[] = preg_replace( $pattern, $alias, $selector );
	foreach ( tp_child_design_selector_parts( $selector ) as $single ) {
		if ( ! preg_match( $pattern, $single, $match, PREG_OFFSET_CAPTURE ) ) {
			continue;
		}
		$offset = $match[0][1];
		$prefix = substr( $single, 0, $offset );
		$tail = substr( $single, $offset + strlen( $match[0][0] ) );
		// The mobile Alberto title preserves a wrapper but omits .et_pb_text_inner.
		$module_tail = preg_replace( '/\s+\.et_pb_text_inner(?![\w-])/', '', $tail );
		$selectors[] = $prefix . $alias . '[data-tp-design-element="module"]' . $module_tail;
		// Choice text modules collapse wrapper/inner/title into one semantic node.
		$text_tail = preg_replace( '/\s+(?:\.et_pb_text_inner|\.et_pb_module_header)(?![\w-])/', '', $tail );
		$text_tail = preg_replace( '/\s+(h[1-6]|p)(?![\w-])/i', ':is($1)', $text_tail );
		$selectors[] = $prefix . $alias . '[data-tp-design-element="content"]' . $text_tail;
		// Choice image module loses the extra .et_pb_image_wrap span.
		$image_tail = preg_replace( '/\s+\.et_pb_image_wrap(?![\w-])/', '', $tail );
		$selectors[] = $prefix . $alias . '[data-tp-design-element="image"]' . $image_tail;
	}
	return implode( ',', array_unique( array_filter( array_map( 'trim', $selectors ) ) ) );
}

/** Consume native style output; CSS generation itself remains owned by Divi. */
function tp_child_design_collect_rules( array $native, string $source ): void {
	$state = &tp_child_design_state();
	foreach ( $native as $rule ) {
		if ( ! is_array( $rule ) ) { continue; }
		if ( ! isset( $rule['selector'], $rule['declaration'] ) ) {
			tp_child_design_collect_rules( $rule, $source );
			continue;
		}
		if ( ! is_string( $rule['selector'] ) || ! is_string( $rule['declaration'] ) || '' === $rule['selector'] ) { continue; }
		$declarations = tp_child_design_declarations( $rule['declaration'] );
		$css = '';
		$typography = '';
		$button_surface = '';
		foreach ( $declarations as $property => $value ) {
			if ( '' === $value ) { continue; }
			$css .= $property . ':' . $value . '!important;';
			if ( in_array( $property, tp_child_design_inline_properties(), true ) ) {
				$variable = '--tp-design-' . $property;
				$css .= $variable . ':' . $value . '!important;';
				$state['variables'][ $variable ] = true;
			}
			if ( preg_match( '/^(?:font-(?:size|family|weight|style|variant)|line-height|letter-spacing|word-spacing|text-(?:transform|align|shadow|decoration(?:-line|-style|-color|-thickness)?)|color)$/D', $property ) ) {
				$typography .= $property . ':' . $value . '!important;';
			}
			if ( isset( $state['buttonSources'][ $source ] ) && preg_match( '/^(?:background(?:-color|-image)?|border-radius)$/D', $property ) ) {
				$button_surface .= $property . ':' . $value . '!important;';
			}
		}
		if ( '' === $css ) { continue; }
		$selector = tp_child_design_alias_selectors( $rule['selector'], $source );
		$at_rules = is_string( $rule['atRules'] ?? null ) ? $rule['atRules'] : '';
		$key = $at_rules . '|' . $selector;
		$existing = $state['rules'][ $key ]['declaration'] ?? '';
		$state['rules'][ $key ] = [ 'selector' => $selector, 'declaration' => $existing . $css, 'atRules' => $at_rules ];
		if ( '' !== $typography ) {
			$inner = implode( ',', array_map( static fn( $part ) => $part . ' :is(p,.tp-split-text__line,.tp-split-text__line-inner,.tp-split-line,.tp-intro-button__label)', tp_child_design_selector_parts( $selector ) ) );
			$inner_key = $at_rules . '|' . $inner;
			$existing = $state['rules'][ $inner_key ]['declaration'] ?? '';
			$state['rules'][ $inner_key ] = [ 'selector' => $inner, 'declaration' => $existing . $typography, 'atRules' => $at_rules ];
		}
		if ( '' !== $button_surface ) {
			$inner = implode( ',', array_map( static fn( $part ) => $part . ' .tp-intro-button__label', tp_child_design_selector_parts( $selector ) ) );
			$inner_key = $at_rules . '|' . $inner;
			$existing = $state['rules'][ $inner_key ]['declaration'] ?? '';
			$state['rules'][ $inner_key ] = [ 'selector' => $inner, 'declaration' => $existing . $button_surface, 'atRules' => $at_rules ];
		}
	}
}

/** Optional responsive media settings for the existing native mobile player. */
function tp_child_design_video_values( array $values, array $attrs ): array {
	$result = [];
	foreach ( [ 'desktop', 'tablet', 'phone' ] as $breakpoint ) {
		$base = 'module.decoration.background.' . $breakpoint . '.value.';
		$format_changed = array_key_exists( $base . 'video.mp4', $values ) || array_key_exists( $base . 'video.webm', $values );
		if ( $format_changed ) {
			foreach ( [ 'mp4', 'webm' ] as $format ) {
				$value = tp_child_design_get_path( $attrs, $base . 'video.' . $format );
				$result[ $breakpoint ][ $format ] = is_string( $value ) ? esc_url_raw( $value ) : '';
			}
		}
		foreach ( [ 'image.url', 'video.image', 'video.poster' ] as $poster_path ) {
			if ( array_key_exists( $base . $poster_path, $values ) ) {
				$result[ $breakpoint ]['poster'] = esc_url_raw( (string) $values[ $base . $poster_path ] );
			}
		}
	}
	return $result;
}

/** Register CSS/identity only for modules that have valid authored overrides. */
function tp_child_design_render_bridge( string $html, array $args ): string {
	$attrs = $args['attrs'] ?? [];
	if ( ! is_array( $attrs ) || empty( $attrs['tpDesignOverrides'] ) ) { return $html; }
	if ( ! empty( $args['id'] ) && class_exists( BlockParserStore::class ) ) {
		$block = BlockParserStore::get( $args['id'], $args['storeInstance'] ?? null );
		if ( $block && is_array( $block->attrs ) ) { $attrs = $block->attrs; }
	}
	$values = tp_child_design_valid_values( $attrs );
	$elements = $args['elements'] ?? null;
	if ( ! $values || ! is_object( $elements ) || ! is_callable( [ $elements, 'style' ] ) ) { return $html; }
	$order_class = $elements->order_class ?? '';
	if ( ! preg_match( '/\.((?:et_pb_|tp_)[a-zA-Z0-9_]+_\d+(?:_tb_\w+)?)(?![\w-])/', $order_class, $match ) ) {
		if ( ! preg_match( '/\b((?:et_pb_|tp_)[a-zA-Z0-9_]+_\d+(?:_tb_\w+)?)\b/', $args['moduleClassname'] ?? '', $match ) ) { return $html; }
	}
	$source = $match[1];
	$state = &tp_child_design_state();
	$state['sources'][ $source ] = true;
	if ( preg_match( '/\btp-(?:intro|location)-button\b/', $args['moduleClassname'] ?? '' ) ) { $state['buttonSources'][ $source ] = true; }
	$groups = [ 'background', 'font', 'bodyFont', 'headingFont', 'icon', 'spacing', 'sizing', 'border', 'boxShadow', 'filters', 'transform', 'overflow', 'position', 'zIndex', 'order', 'button', 'layout', 'transition' ];
	foreach ( tp_child_design_sparse_decorations( $values ) as $attr_name => $decoration ) {
		$style_props = [
			'attrsFilter' => static fn( $unused ) => $decoration,
			'defaultPrintedStyleAttrs' => [],
			'advancedStyles' => [],
		];
		foreach ( $groups as $group ) { $style_props[ $group ] = [ 'important' => true ]; }
		$native = $elements->style( [ 'attrName' => $attr_name, 'styleProps' => $style_props, 'isMergeRecursiveProps' => true ] );
		if ( is_array( $native ) ) { tp_child_design_collect_rules( $native, $source ); }
	}
	$stamped = false;
	// Append attributes rather than inserting before class: the legacy Choice
	// replacement deliberately matches its original <div class="..."> prefix.
	$updated = preg_replace_callback(
		'/<[a-zA-Z][a-zA-Z0-9:-]*\b(?:"[^"]*"|\'[^\']*\'|[^\'">])*>/s',
		static function ( $match ) use ( $source, $values, $attrs, &$stamped ) {
			$tag = $match[0];
			if ( $stamped || ! preg_match( '/\sclass\s*=\s*(["\'])(.*?)\1/s', $tag, $class_match ) ) { return $tag; }
			$classes = preg_split( '/\s+/', html_entity_decode( $class_match[2], ENT_QUOTES, 'UTF-8' ) );
			if ( ! in_array( $source, $classes, true ) ) { return $tag; }
			$extra = ' data-tp-design-source="' . esc_attr( $source ) . '" data-tp-design-element="module"';
			if ( in_array( 'tp-hero-section', $classes, true ) ) {
				$video = tp_child_design_video_values( $values, $attrs );
				if ( $video ) { $extra .= ' data-tp-design-video="' . esc_attr( wp_json_encode( $video ) ) . '"'; }
			}
			$stamped = true;
			return str_ends_with( $tag, '/>' ) ? substr( $tag, 0, -2 ) . $extra . '/>' : substr( $tag, 0, -1 ) . $extra . '>';
		},
		$html
	);
	return is_string( $updated ) ? $updated : $html;
}
add_filter( 'divi_module_wrapper_render', 'tp_child_design_render_bridge', 30, 2 );

/** Called by legacy Choice rendering: absent masks return an exact empty string. */
function tp_child_design_source_attributes( ?DOMNode $node, string $element = 'module' ): string {
	if ( ! $node instanceof DOMElement ) { return ''; }
	$state = &tp_child_design_state();
	$tokens = preg_split( '/\s+/', $node->getAttribute( 'class' ) . ' ' . $node->getAttribute( 'data-tp-design-source' ) );
	$sources = array_values( array_filter( array_unique( $tokens ), static fn( $token ) => isset( $state['sources'][ $token ] ) ) );
	if ( ! $sources ) { return ''; }
	return ' data-tp-design-source="' . esc_attr( implode( ' ', $sources ) ) . '" data-tp-design-element="' . esc_attr( $element ) . '"';
}

/** Wrap complete native media rules; atRules cannot be used for the outer layer. */
function tp_child_design_print_styles(): void {
	$state = &tp_child_design_state();
	if ( ! $state['rules'] ) { return; }
	$rules = array_values( $state['rules'] );
	usort( $rules, static function ( $a, $b ) {
		$rank = static function ( $query ) {
			if ( '' === $query ) { return -1000000; }
			if ( preg_match( '/max-width:\s*([\d.]+)px/', $query, $match ) ) { return -(float) $match[1]; }
			if ( preg_match( '/min-width:\s*([\d.]+)px/', $query, $match ) ) { return (float) $match[1]; }
			return 1000000;
		};
		return $rank( $a['atRules'] ) <=> $rank( $b['atRules'] );
	} );
	$css = '';
	foreach ( array_keys( $state['variables'] ) as $variable ) {
		$css .= '@property ' . $variable . '{syntax:"*";inherits:false;}';
	}
	if ( $state['variables'] ) {
		$css .= ':where(body.tp-child-theme *){';
		foreach ( array_keys( $state['variables'] ) as $variable ) { $css .= $variable . ':initial;'; }
		$css .= '}';
	}
	$css .= '@layer tp-authored{';
	foreach ( $rules as $rule ) {
		$block = $rule['selector'] . '{' . $rule['declaration'] . '}';
		$css .= $rule['atRules'] ? $rule['atRules'] . '{' . $block . '}' : $block;
	}
	$css .= '}';
	// Prevent an untrusted saved value from terminating the style element.
	$css = str_replace( '<', '\\3c ', $css );
	echo '<style id="tp-authored-design">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Native CSS, escaped style delimiter.
}
add_action( 'wp_footer', 'tp_child_design_print_styles', 99 );
