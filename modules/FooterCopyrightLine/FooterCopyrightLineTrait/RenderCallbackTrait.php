<?php
/**
 * FooterCopyrightLine::render_callback().
 */

namespace TaschePieneDiviChild\Modules\FooterCopyrightLine\FooterCopyrightLineTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module;
use TaschePieneDiviChild\Modules\FooterCopyrightLine\FooterCopyrightLine;
use WP_Block;

trait RenderCallbackTrait {

	public static function render_callback(
		array $attrs,
		string $content,
		WP_Block $block,
		ModuleElements $elements,
		array $default_printed_style_attrs
	): string {
		$text = tp_child_chrome_get_string( $attrs, 'text', 'Nuova riga' );
		$link = tp_child_chrome_get_string( $attrs, 'link', '' );

		$inner_html = HTMLUtility::render(
			[
				'tag'               => 'span',
				'childrenSanitizer' => 'et_core_esc_previously',
				'children'          => tp_child_chrome_render_copyright_line( esc_html( $text ), esc_url( $link ) ),
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
				'classnamesFunction'       => [ FooterCopyrightLine::class, 'module_classnames' ],
				'stylesComponent'          => [ FooterCopyrightLine::class, 'module_styles' ],
				'scriptDataComponent'      => [ FooterCopyrightLine::class, 'module_script_data' ],
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
