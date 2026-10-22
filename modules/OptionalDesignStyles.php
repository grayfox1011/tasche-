<?php

namespace TaschePieneDiviChild\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/** Additive design controls: unset fields must not alter the original page. */
final class OptionalDesignStyles {

	public static function has_values( $value ): bool {
		if ( is_array( $value ) ) {
			foreach ( $value as $child ) {
				if ( self::has_values( $child ) ) {
					return true;
				}
			}
			return false;
		}
		return is_scalar( $value ) && null !== $value && '' !== $value;
	}

	/** Return Divi's free-form style record, preserving native responsive rules. */
	public static function styles( $elements, array $attrs, array $names ): array {
		$css = '';
		foreach ( $names as $name ) {
			$decoration = $attrs[ $name ]['decoration'] ?? [];
			if ( ! is_array( $decoration ) || ! self::has_values( $decoration ) ) {
				continue;
			}
			$output = $elements->style(
				[
					'attrName'   => $name,
					'styleProps' => [
						'returnType'  => 'string',
						// Do not inherit module transitions or any absent defaults.
						'attrsFilter' => static function () use ( $decoration ): array {
							return $decoration;
						},
					],
				]
			);
			if ( is_string( $output ) ) {
				$css .= $output;
			}
		}
		if ( '' === $css ) {
			return [];
		}
		// Supplying atRules to ElementStyle would replace breakpoint @media rules.
		// Wrap completed CSS instead. Style::add supports a blank-selector record.
		return [ [ 'selector' => '', 'declaration' => '@layer tp-authored {' . $css . '}', 'atRules' => '' ] ];
	}
}
