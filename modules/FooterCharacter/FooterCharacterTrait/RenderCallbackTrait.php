<?php
/**
 * FooterCharacter::render_callback(): the module's box is the character's place (its classes come from
 * FooterCharacter::module_classnames()), the drawing inside it. Without a drawing nothing is printed.
 */

namespace TaschePieneDiviChild\Modules\FooterCharacter\FooterCharacterTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module;
use TaschePieneDiviChild\Modules\FooterCharacter\FooterCharacter;
use WP_Block;

trait RenderCallbackTrait {

	public static function render_callback(
		array $attrs,
		string $content,
		WP_Block $block,
		ModuleElements $elements,
		array $default_printed_style_attrs
	): string {
		$url = tp_child_chrome_get_image_url( $attrs, 'image', 'imageUrl', '' );

		if ( '' === $url ) {
			return '';
		}

		// A drawing, not information: no alternative text, hidden from screen readers (as the theme's characters).
		$img = tp_child_chrome_build_image_tag(
			$url,
			[
				'class'       => 'tp-theme-footer-character',
				'aria-hidden' => 'true',
				'loading'     => 'lazy',
				'decoding'    => 'async',
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
				'classnamesFunction'       => [ FooterCharacter::class, 'module_classnames' ],
				'stylesComponent'          => [ FooterCharacter::class, 'module_styles' ],
				'scriptDataComponent'      => [ FooterCharacter::class, 'module_script_data' ],
				'id'                       => $block_id,
				'name'                     => $block->block_type->name,
				'moduleCategory'           => $block->block_type->category,
				'childrenIds'              => [],
				'parentAttrs'              => $parent->attrs ?? [],
				'parentId'                 => $parent->id ?? '',
				'parentName'               => $parent->blockName ?? '',
				'children'                 => [
					$elements->style_components( [ 'attrName' => 'module' ] ),
					$img,
				],
			]
		);
	}
}
