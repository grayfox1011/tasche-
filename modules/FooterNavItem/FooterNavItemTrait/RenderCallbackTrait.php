<?php
/**
 * FooterNavItem::render_callback().
 *
 * The links of the two menus are printed by Footer sito, from the attributes of its Voci (see
 * tp_child_footer_nav_items_from_block()): a Voce prints only its module wrapper, which Footer sito
 * does not output.
 */

namespace TaschePieneDiviChild\Modules\FooterNavItem\FooterNavItemTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module;
use TaschePieneDiviChild\Modules\FooterNavItem\FooterNavItem;
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

		return Module::render(
			[
				'orderIndex'               => $parsed_block['orderIndex'] ?? 0,
				'storeInstance'            => $store,
				'attrs'                    => $attrs,
				'elements'                 => $elements,
				'defaultPrintedStyleAttrs' => $default_printed_style_attrs,
				'classnamesFunction'       => [ FooterNavItem::class, 'module_classnames' ],
				'stylesComponent'          => [ FooterNavItem::class, 'module_styles' ],
				'scriptDataComponent'      => [ FooterNavItem::class, 'module_script_data' ],
				'id'                       => $block_id,
				'name'                     => $block->block_type->name,
				'moduleCategory'           => $block->block_type->category,
				'childrenIds'              => [],
				'parentAttrs'              => $parent->attrs ?? [],
				'parentId'                 => $parent->id ?? '',
				'parentName'               => $parent->blockName ?? '',
				'children'                 => [
					$elements->style_components( [ 'attrName' => 'module' ] ),
				],
			]
		);
	}
}
