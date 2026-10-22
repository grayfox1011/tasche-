<?php
/**
 * MarqueeItem::render_callback().
 */

namespace TaschePieneDiviChild\Modules\MarqueeItem\MarqueeItemTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module;
use TaschePieneDiviChild\Modules\MarqueeItem\MarqueeItem;
use WP_Block;

trait RenderCallbackTrait {

	private static function get_string( array $attrs, string $key, string $fallback = '' ): string {
		$value = $attrs[ $key ]['innerContent']['desktop']['value']
			?? $attrs[ $key ]['desktop']['value']
			?? $fallback;

		if ( is_array( $value ) || is_object( $value ) ) {
			return $fallback;
		}

		$value = trim( (string) $value );

		return '' !== $value ? $value : $fallback;
	}

	public static function render_callback(
		array $attrs,
		string $content,
		WP_Block $block,
		ModuleElements $elements,
		array $default_printed_style_attrs
	): string {
		$phrase = sanitize_text_field( self::get_string( $attrs, 'phrase', 'Nuova scritta' ) );

		$item_html = HTMLUtility::render(
			[
				'tag'        => 'span',
				'attributes' => [ 'class' => 'tp-marquee-item__label' ],
				'children'   => esc_html( $phrase ),
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
				'classnamesFunction'       => [ MarqueeItem::class, 'module_classnames' ],
				'stylesComponent'          => [ MarqueeItem::class, 'module_styles' ],
				'scriptDataComponent'      => [ MarqueeItem::class, 'module_script_data' ],
				'id'                       => $block_id,
				'name'                     => $block->block_type->name,
				'moduleCategory'           => $block->block_type->category,
				'childrenIds'              => [],
				'parentAttrs'              => $parent->attrs ?? [],
				'parentId'                 => $parent->id ?? '',
				'parentName'               => $parent->blockName ?? '',
				'children'                 => [
					$elements->style_components( [ 'attrName' => 'module' ] ),
					$item_html,
				],
			]
		);
	}
}
