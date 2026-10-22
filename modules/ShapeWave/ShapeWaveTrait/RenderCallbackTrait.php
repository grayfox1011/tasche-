<?php
/**
 * ShapeWave::render_callback().
 */

namespace TaschePieneDiviChild\Modules\ShapeWave\ShapeWaveTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module;
use TaschePieneDiviChild\Modules\ShapeWave\ShapeWave;
use WP_Block;

trait RenderCallbackTrait {

	private static function get_attr_value( array $attrs, string $key, $fallback = '' ) {
		return $attrs[ $key ]['innerContent']['desktop']['value']
			?? $attrs[ $key ]['desktop']['value']
			?? $fallback;
	}

	private static function get_string( array $attrs, string $key, string $fallback = '' ): string {
		$value = self::get_attr_value( $attrs, $key, $fallback );

		if ( is_array( $value ) || is_object( $value ) ) {
			return $fallback;
		}

		$value = trim( (string) $value );

		return '' !== $value ? $value : $fallback;
	}

	private static function get_number( array $attrs, string $key, float $fallback, float $min, float $max ): float {
		$value = (float) self::get_string( $attrs, $key, (string) $fallback );

		if ( ! is_finite( $value ) ) {
			$value = $fallback;
		}

		return max( $min, min( $max, $value ) );
	}

	private static function get_shapes(): array {
		return [
			'choice-layered-desktop' => [
				'label'      => 'Choice transizione desktop',
				'view_box'   => '0 0 1811.7627 428.3477',
				'paths'      => [
					[
						'class' => 'tp-shape-wave__path tp-shape-wave__path--green',
						'fill'  => 'green',
						'd'     => 'M999.447 217.19C1300.56 148.011 1608.33 129.375 1724.58 128.705L1811.76 178.981L1711.82 428.348L0 428.348L0 281.543L0 0C207.686 101.221 698.337 286.369 999.447 217.19Z',
					],
					[
						'class' => 'tp-shape-wave__path tp-shape-wave__path--burgundy',
						'fill'  => 'burgundy',
						'd'     => 'M846.006 243.417C479.085 140.469 222.372 125.73 139.881 131.229L149.78 414.999L1664.32 414.999L1664.32 332.508C1544.43 345.707 1212.93 346.366 846.006 243.417Z',
					],
				],
				'fill'       => 'burgundy',
				'background' => 'transparent',
				'width'      => 1811.7627,
				'height'     => 428.3477,
			],
			'choice-green-overlap' => [
				'label'      => 'Choice verde su bordeaux',
				'view_box'   => '0 0 1811.7627 428.3477',
				'path'       => 'M999.447 217.19C1300.56 148.011 1608.33 129.375 1724.58 128.705L1811.76 178.981L1711.82 428.348L0 428.348L0 281.543L0 0C207.686 101.221 698.337 286.369 999.447 217.19Z',
				'fill'       => 'green',
				'background' => 'transparent',
				'width'      => 1811.7627,
				'height'     => 428.3477,
			],
			'choice-cream-cover'   => [
				'label'      => 'Choice copertura crema',
				'view_box'   => '0 0 1524.4385 240.1285',
				'path'       => 'M0 0H1524.4385V226.27C1404.55 239.469 1073.05 240.128 706.125 137.179C339.204 34.2306 82.4911 19.4918 0 24.991Z',
				'fill'       => 'cream',
				'background' => 'transparent',
				'width'      => 1524.44,
				'height'     => 240.13,
			],
			'choice-burgundy-top'  => [
				'label'      => 'Choice bordeaux mobile',
				'view_box'   => '0 0 472 114',
				'path'       => 'M218.646 60.4652C105.115 28.6112 25.6842 24.0507 0.160177 25.7523L3.22312 113.555H471.846V88.0312C434.75 92.115 332.178 92.3192 218.646 60.4652Z',
				'fill'       => 'burgundy',
				'background' => 'cream',
				'width'      => 471.49,
				'height'     => 113.54,
			],
			'choice-cream-bottom'  => [
				'label'      => 'Choice crema sotto',
				'view_box'   => '0 0 525 99',
				'path'       => 'M243.182 38.9778C116.818 3.5233 28.409 -1.55258 0 0.341285L3.40914 98.0687H525V69.6596C483.712 74.205 369.546 74.4322 243.182 38.9778Z',
				'fill'       => 'cream',
				'background' => 'burgundy',
				'width'      => 524.8,
				'height'     => 98.03,
			],
		];
	}

	private static function get_color_value( string $color, string $fallback_token ): string {
		$colors = [
			'auto'        => 'auto',
			'transparent' => 'transparent',
			'green'       => '#054726',
			'burgundy'    => '#560b22',
			'cream'       => '#fff5e9',
			'sand'        => '#f8ebdb',
		];

		$key = array_key_exists( $color, $colors ) ? $color : $fallback_token;

		if ( 'auto' === $key ) {
			$key = $fallback_token;
		}

		return $colors[ $key ] ?? $colors[ $fallback_token ] ?? '#054726';
	}

	private static function render_svg( array $shape ): string {
		if ( isset( $shape['paths'] ) && is_array( $shape['paths'] ) ) {
			$paths = '';

			foreach ( $shape['paths'] as $path ) {
				if ( ! is_array( $path ) || empty( $path['d'] ) ) {
					continue;
				}

				$paths .= sprintf(
					'<path class="%1$s" d="%2$s" fill="%3$s"></path>',
					esc_attr( $path['class'] ?? 'tp-shape-wave__path' ),
					esc_attr( $path['d'] ),
					esc_attr( self::get_color_value( (string) ( $path['fill'] ?? 'auto' ), (string) ( $path['fill'] ?? 'green' ) ) )
				);
			}

			return sprintf(
				'<svg class="tp-shape-wave__svg" viewBox="%1$s" preserveAspectRatio="none" aria-hidden="true" focusable="false">%2$s</svg>',
				esc_attr( $shape['view_box'] ),
				$paths
			);
		}

		if ( ! empty( $shape['clip_path'] ) ) {
			$clip_id = wp_unique_id( 'tp-shape-wave-clip-' );

			return sprintf(
				'<svg class="tp-shape-wave__svg" viewBox="%1$s" preserveAspectRatio="none" aria-hidden="true" focusable="false"><defs><clipPath id="%2$s"><path d="%3$s"></path></clipPath></defs><path class="tp-shape-wave__path" d="%4$s" fill="currentColor" clip-path="url(#%2$s)"></path></svg>',
				esc_attr( $shape['view_box'] ),
				esc_attr( $clip_id ),
				esc_attr( $shape['clip_path'] ),
				esc_attr( $shape['path'] )
			);
		}

		return sprintf(
			'<svg class="tp-shape-wave__svg" viewBox="%1$s" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path class="tp-shape-wave__path" d="%2$s" fill="currentColor"></path></svg>',
			esc_attr( $shape['view_box'] ),
			esc_attr( $shape['path'] )
		);
	}

	public static function render_callback(
		array $attrs,
		string $content,
		WP_Block $block,
		ModuleElements $elements,
		array $default_printed_style_attrs
	): string {
		$shapes       = self::get_shapes();
		$variant      = self::get_string( $attrs, 'variant', 'choice-green-overlap' );
		$shape        = $shapes[ $variant ] ?? $shapes['choice-green-overlap'];
		$fill         = self::get_color_value( self::get_string( $attrs, 'fillColor', 'auto' ), $shape['fill'] );
		$background   = self::get_color_value( self::get_string( $attrs, 'backgroundColor', 'auto' ), $shape['background'] );
		$width        = self::get_number( $attrs, 'shapeWidth', (float) $shape['width'], 120, 2400 );
		$height       = self::get_number( $attrs, 'shapeHeight', (float) $shape['height'], 20, 700 );
		$offset_y     = self::get_number( $attrs, 'offsetY', 0, -400, 400 );
		$align        = self::get_string( $attrs, 'align', 'center' );
		$flip_x       = 'on' === self::get_string( $attrs, 'flipHorizontal', 'off' );
		$flip_y       = 'on' === self::get_string( $attrs, 'flipVertical', 'off' );
		$parsed_block = is_array( $block->parsed_block ?? null ) ? $block->parsed_block : [];

		if ( ! in_array( $align, [ 'left', 'center', 'right' ], true ) ) {
			$align = 'center';
		}

		$style = sprintf(
			'--tp-shape-wave-width:%1$.2fpx;--tp-shape-wave-height:%2$.2fpx;--tp-shape-wave-fill:%3$s;--tp-shape-wave-bg:%4$s;--tp-shape-wave-offset-y:%5$.2fpx;',
			$width,
			$height,
			$fill,
			$background,
			$offset_y
		);

		$inner_html = HTMLUtility::render(
			[
				'tag'               => 'div',
				'attributes'        => [
					'class' => HTMLUtility::classnames(
						[
							'tp-shape-wave'                   => true,
							'tp-shape-wave--' . $variant      => true,
							'tp-shape-wave--align-' . $align  => true,
							'tp-shape-wave--flip-x'           => $flip_x,
							'tp-shape-wave--flip-y'           => $flip_y,
						]
					),
					'style' => $style,
				],
				'childrenSanitizer' => 'et_core_esc_previously',
				'children'          => self::render_svg( $shape ),
			]
		);

		return Module::render(
			[
				'orderIndex'               => $parsed_block['orderIndex'] ?? 0,
				'storeInstance'            => $parsed_block['storeInstance'] ?? '',
				'attrs'                    => $attrs,
				'elements'                 => $elements,
				'defaultPrintedStyleAttrs' => $default_printed_style_attrs,
				'classnamesFunction'       => [ ShapeWave::class, 'module_classnames' ],
				'stylesComponent'          => [ ShapeWave::class, 'module_styles' ],
				'scriptDataComponent'      => [ ShapeWave::class, 'module_script_data' ],
				'id'                       => $parsed_block['id'] ?? '',
				'name'                     => $block->block_type->name,
				'moduleCategory'           => $block->block_type->category,
				'children'                 => [
					$elements->style_components( [ 'attrName' => 'module' ] ),
					$inner_html,
				],
			]
		);
	}
}
