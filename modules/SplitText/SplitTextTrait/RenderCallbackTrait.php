<?php
/**
 * SplitText::render_callback().
 */

namespace TaschePieneDiviChild\Modules\SplitText\SplitTextTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module;
use TaschePieneDiviChild\Modules\SplitText\SplitText;
use WP_Block;

trait RenderCallbackTrait {

	public static function render_callback(
		array $attrs,
		string $content,
		WP_Block $block,
		ModuleElements $elements,
		array $default_printed_style_attrs
	): string {
		$get = function ( string $key, $fallback = '' ) use ( $attrs ) {
			return $attrs[ $key ]['innerContent']['desktop']['value']
				?? $attrs[ $key ]['desktop']['value']
				?? $fallback;
		};

		$get_string = function ( string $key, string $fallback = '' ) use ( $get ): string {
			$value = $get( $key, $fallback );

			return is_scalar( $value ) ? trim( (string) $value ) : $fallback;
		};

		$lines = array_values(
			array_filter(
				[
					sanitize_text_field( $get_string( 'lineOne', 'Titolo animato' ) ),
					sanitize_text_field( $get_string( 'lineTwo', '' ) ),
					sanitize_text_field( $get_string( 'lineThree', '' ) ),
					sanitize_text_field( $get_string( 'lineFour', '' ) ),
					sanitize_text_field( $get_string( 'lineFive', '' ) ),
					sanitize_text_field( $get_string( 'lineSix', '' ) ),
				],
				static function ( string $line ): bool {
					return '' !== $line;
				}
			)
		);

		if ( empty( $lines ) ) {
			$lines = [ 'Titolo animato' ];
		}

		$allowed_tags = [ 'h1', 'h2', 'h3', 'p', 'div' ];
		$tag_name     = strtolower( $get_string( 'tagName', 'h1' ) );
		$tag_name     = in_array( $tag_name, $allowed_tags, true ) ? $tag_name : 'h1';

		$variant         = sanitize_html_class( $get_string( 'variant', 'hero' ) );
		$split_type      = sanitize_key( $get_string( 'splitType', 'chars' ) );
		$trigger         = sanitize_key( $get_string( 'trigger', 'load' ) );
		$responsive_mode = sanitize_key( $get_string( 'responsiveMode', 'desktop' ) );
		$duration        = max( 0.1, min( 3, (float) $get_string( 'duration', '0.52' ) ) );
		$stagger         = max( 0, min( 1, (float) $get_string( 'stagger', '0.018' ) ) );
		$delay           = max( 0, min( 5, (float) $get_string( 'delay', '0.14' ) ) );
		$y_percent       = max( 0, min( 200, (int) $get_string( 'yPercent', '110' ) ) );
		$ease            = sanitize_text_field( $get_string( 'ease', 'power2.out' ) );
		$label           = implode( ' ', $lines );

		$children = [];

		foreach ( $lines as $line ) {
			$children[] = HTMLUtility::render(
				[
					'tag'               => 'span',
					'attributes'        => [
						'class'       => 'tp-split-text__line',
						'aria-hidden' => 'true',
					],
					'childrenSanitizer' => 'et_core_esc_previously',
					'children'          => HTMLUtility::render(
						[
							'tag'        => 'span',
							'attributes' => [ 'class' => 'tp-split-text__line-inner' ],
							'children'   => esc_html( $line ),
						]
					),
				]
			);
		}

		$inner_html = HTMLUtility::render(
			[
				'tag'               => $tag_name,
				'attributes'        => [
					'class'                   => 'tp-split-text tp-split-text--' . $variant,
					'aria-label'              => $label,
					'data-tp-split-text'      => '1',
					'data-tp-split-type'      => $split_type,
					'data-tp-split-trigger'   => $trigger,
					'data-tp-split-responsive'=> $responsive_mode,
					'data-tp-split-duration'  => (string) $duration,
					'data-tp-split-stagger'   => (string) $stagger,
					'data-tp-split-delay'     => (string) $delay,
					'data-tp-split-y'         => (string) $y_percent,
					'data-tp-split-ease'      => $ease,
				],
				'childrenSanitizer' => 'et_core_esc_previously',
				'children'          => $children,
			]
		);

		$parsed_block = is_array( $block->parsed_block ?? null ) ? $block->parsed_block : [];
		$block_id     = $parsed_block['id'] ?? '';
		$store        = $parsed_block['storeInstance'] ?? '';
		$parent       = $block_id && $store ? BlockParserStore::get_parent( $block_id, $store ) : null;

		return Module::render(
			[
				'orderIndex'               => $parsed_block['orderIndex'] ?? 0,
				'storeInstance'            => $store,
				'attrs'                    => $attrs,
				'elements'                 => $elements,
				'defaultPrintedStyleAttrs' => $default_printed_style_attrs,
				'classnamesFunction'       => [ SplitText::class, 'module_classnames' ],
				'stylesComponent'          => [ SplitText::class, 'module_styles' ],
				'scriptDataComponent'      => [ SplitText::class, 'module_script_data' ],
				'id'                       => $block_id,
				'name'                     => $block->block_type->name,
				'moduleCategory'           => $block->block_type->category,
				'childrenIds'              => [],
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
