<?php
/**
 * FooterDecor::render_callback().
 */

namespace TaschePieneDiviChild\Modules\FooterDecor\FooterDecorTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module;
use TaschePieneDiviChild\Modules\FooterDecor\FooterDecor;
use WP_Block;

trait RenderCallbackTrait {

	public static function render_callback(
		array $attrs,
		string $content,
		WP_Block $block,
		ModuleElements $elements,
		array $default_printed_style_attrs
	): string {
		$url     = tp_child_chrome_get_image_url( $attrs, 'image', 'imageUrl', '' );
		$alt     = tp_child_chrome_get_string( $attrs, 'alt', '' );
		$top     = tp_child_chrome_get_string( $attrs, 'posTop', '0' );
		$left    = tp_child_chrome_get_string( $attrs, 'posLeft', '0' );
		$width   = tp_child_chrome_get_string( $attrs, 'width', '120' );
		$z_index = tp_child_chrome_get_string( $attrs, 'zIndex', '20' );

		$img = '' !== $url
			? tp_child_chrome_build_image_tag( $url, [ 'alt' => $alt ] )
			: '<span class="tp-footer-decor-placeholder">Decor</span>';

		$style = sprintf( 'top:%s;left:%s;width:%spx;z-index:%s', $top, $left, $width, $z_index );

		$inner_html = HTMLUtility::render(
			[
				'tag'               => 'div',
				'attributes'        => [
					'class' => 'tp-theme-footer-decor-item',
					'style' => $style,
				],
				'childrenSanitizer' => 'et_core_esc_previously',
				'children'          => $img,
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
				'classnamesFunction'       => [ FooterDecor::class, 'module_classnames' ],
				'stylesComponent'          => [ FooterDecor::class, 'module_styles' ],
				'scriptDataComponent'      => [ FooterDecor::class, 'module_script_data' ],
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
