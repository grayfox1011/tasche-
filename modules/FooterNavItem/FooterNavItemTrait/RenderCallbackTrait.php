<?php
/**
 * FooterNavItem::render_callback().
 */

namespace TaschePieneDiviChild\Modules\FooterNavItem\FooterNavItemTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\Utility\HTMLUtility;
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
		$text   = tp_child_chrome_get_string( $attrs, 'text', 'Nuova voce' );
		$url    = tp_child_chrome_get_string( $attrs, 'link', '#' );
		$target = 'on' === tp_child_chrome_get_string( $attrs, 'target', 'off' ) ? '_blank' : '';
		$rel    = '_blank' === $target ? 'noopener' : '';

		$inner_html = sprintf(
			'<a href="%1$s"%2$s%3$s>%4$s</a>',
			esc_url( $url ),
			$target ? ' target="' . esc_attr( $target ) . '"' : '',
			$rel ? ' rel="' . esc_attr( $rel ) . '"' : '',
			esc_html( $text )
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
					$inner_html,
				],
			]
		);
	}
}
