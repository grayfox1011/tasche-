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

	public static function render_callback(
		array $attrs,
		string $content,
		WP_Block $block,
		ModuleElements $elements,
		array $default_printed_style_attrs
	): string {
		$parsed_block = is_array( $block->parsed_block ?? null ) ? $block->parsed_block : [];
		$block_id     = $parsed_block['id'] ?? '';
		$store        = $parsed_block['storeInstance'] ?? '';
		$parent       = $block_id && $store ? BlockParserStore::get_parent( $block_id, $store ) : null;
		$saved        = $attrs['phrase']['innerContent']['desktop']['value'] ?? '';
		$phrase       = is_string( $saved ) ? sanitize_text_field( $saved ) : '';

		// The strip shows its phrases itself: it reads them here, with dynamic content already resolved
		// (the saved text only: the "Nuova scritta" default of module.json is for the builder).
		MarqueeItem::remember_phrase( $store . ':' . $block_id, $phrase );

		$item_html = HTMLUtility::render(
			[
				'tag'        => 'span',
				'attributes' => [ 'class' => 'tp-marquee-item__label' ],
				'children'   => esc_html( '' !== $phrase ? $phrase : 'Nuova scritta' ),
			]
		);

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
