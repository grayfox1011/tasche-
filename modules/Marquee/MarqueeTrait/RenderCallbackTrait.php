<?php
/**
 * Marquee::render_callback().
 */

namespace TaschePieneDiviChild\Modules\Marquee\MarqueeTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module;
use TaschePieneDiviChild\Modules\Marquee\Marquee;
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

	private static function get_child_phrase( array $child_attrs ): string {
		return sanitize_text_field( self::get_string( $child_attrs, 'phrase', '' ) );
	}

	private static function get_phrases( array $parsed_block, string $variant = 'green' ): array {
		$phrases = [];

		if ( ! empty( $parsed_block['innerBlocks'] ) && is_array( $parsed_block['innerBlocks'] ) ) {
			foreach ( $parsed_block['innerBlocks'] as $inner_block ) {
				if ( 'tp/marquee-item' !== ( $inner_block['blockName'] ?? '' ) ) {
					continue;
				}

				$phrase = self::get_child_phrase( $inner_block['attrs'] ?? [] );

				if ( '' !== $phrase ) {
					$phrases[] = $phrase;
				}
			}
		}

		if ( empty( $phrases ) ) {
			$phrases = 'materie' === $variant
				? [ 'Le cose che finiscono in tasca' ]
				: [
					'Stocco ogni giovedì',
					'Trippa ogni venerdì',
					'Polpette al sugo',
					'Le acciughe di Luciano',
					'Tasche piene di cucinato',
				];
		}

		return array_values( $phrases );
	}

	private static function render_track_items( array $phrases, string $separator, int $repeat_count ): string {
		$items = '';

		for ( $set = 0; $set < $repeat_count; $set++ ) {
			foreach ( $phrases as $phrase ) {
				$items .= HTMLUtility::render(
					[
						'tag'        => 'span',
						'attributes' => [
							'class'       => 'tp-marquee__phrase',
							'aria-hidden' => 'true',
						],
						'children'   => esc_html( $phrase ),
					]
				);

				if ( '' !== $separator ) {
					$items .= HTMLUtility::render(
						[
							'tag'        => 'span',
							'attributes' => [
								'class'       => 'tp-marquee__separator',
								'aria-hidden' => 'true',
							],
							'children'   => esc_html( $separator ),
						]
					);
				}
			}
		}

		return $items;
	}

	public static function render_callback(
		array $attrs,
		string $content,
		WP_Block $block,
		ModuleElements $elements,
		array $default_printed_style_attrs
	): string {
		$parsed_block = is_array( $block->parsed_block ?? null ) ? $block->parsed_block : [];
		$variant      = self::get_string( $attrs, 'variant', 'green' );
		$direction    = self::get_string( $attrs, 'direction', 'left' );
		$is_materie   = 'materie' === $variant;
		$phrases      = self::get_phrases( $parsed_block, $variant );
		$separator    = sanitize_text_field( self::get_string( $attrs, 'separator', $is_materie ? '•' : '·' ) );
		$repeat_count = max( 2, min( 8, absint( self::get_string( $attrs, 'repeatCount', $is_materie ? '4' : '2' ) ) ) );
		$duration     = max( 8, min( 120, (float) self::get_string( $attrs, 'speed', $is_materie ? '10' : '28' ) ) );
		$pause        = 'on' === self::get_string( $attrs, 'pauseOnHover', $is_materie ? 'off' : 'on' );
		$shift        = -1 * ( 100 / $repeat_count );

		if ( ! in_array( $direction, [ 'left', 'right' ], true ) ) {
			$direction = 'left';
		}

		if ( ! in_array( $variant, [ 'green', 'burgundy', 'cream', 'materie' ], true ) ) {
			$variant = 'green';
		}

		$inner_html = HTMLUtility::render(
			[
				'tag'               => 'div',
				'attributes'        => [
					'class'      => HTMLUtility::classnames(
						[
							'tp-marquee'                  => true,
							'tp-marquee--' . $variant     => true,
							'tp-marquee--' . $direction   => true,
							'tp-marquee--pause-hover'     => $pause,
						]
					),
					'style'      => '--tp-marquee-duration:' . $duration . 's;--tp-marquee-shift:' . $shift . '%',
					'aria-label' => implode( ' ' . $separator . ' ', $phrases ),
				],
				'childrenSanitizer' => 'et_core_esc_previously',
				'children'          => HTMLUtility::render(
					[
						'tag'               => 'div',
						'attributes'        => [ 'class' => 'tp-marquee__track' ],
						'childrenSanitizer' => 'et_core_esc_previously',
						'children'          => self::render_track_items( $phrases, $separator, $repeat_count ),
					]
				),
			]
		);

		$children_ids = ! empty( $parsed_block['innerBlocks'] )
			? array_values(
				array_filter(
					array_map(
						static function ( $inner_block ) {
							return $inner_block['id'] ?? '';
						},
						$parsed_block['innerBlocks']
					)
				)
			)
			: [];

		$block_id       = $parsed_block['id'] ?? '';
		$store_instance = $parsed_block['storeInstance'] ?? '';
		$parent         = $block_id && $store_instance ? BlockParserStore::get_parent( $block_id, $store_instance ) : null;

		return Module::render(
			[
				'orderIndex'               => $parsed_block['orderIndex'] ?? 0,
				'storeInstance'            => $store_instance,
				'attrs'                    => $attrs,
				'elements'                 => $elements,
				'defaultPrintedStyleAttrs' => $default_printed_style_attrs,
				'classnamesFunction'       => [ Marquee::class, 'module_classnames' ],
				'stylesComponent'          => [ Marquee::class, 'module_styles' ],
				'scriptDataComponent'      => [ Marquee::class, 'module_script_data' ],
				'id'                       => $block_id,
				'name'                     => $block->block_type->name,
				'moduleCategory'           => $block->block_type->category,
				'childrenIds'              => $children_ids,
				'parentAttrs'              => $parent->attrs ?? [],
				'parentId'                 => $parent->id ?? '',
				'parentName'               => $parent->blockName ?? '',
				'children'                 => [
					$elements->style_components( [ 'attrName' => 'module' ] ),
					$inner_html,
				],
			]
		);
	}
}
