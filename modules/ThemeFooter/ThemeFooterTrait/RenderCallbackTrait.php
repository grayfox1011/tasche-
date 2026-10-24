<?php
/**
 * ThemeFooter::render_callback() — monolithic footer.
 */

namespace TaschePieneDiviChild\Modules\ThemeFooter\ThemeFooterTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module;
use TaschePieneDiviChild\Modules\ThemeFooter\ThemeFooter;
use WP_Block;

trait RenderCallbackTrait {

	public static function render_callback(
		array $attrs,
		string $content,
		WP_Block $block,
		ModuleElements $elements,
		array $default_printed_style_attrs
	): string {
		$footer_style = tp_child_chrome_style_from_vars(
			[
				'--tp-footer-bg'         => tp_child_chrome_get_color( $attrs, 'footerBackgroundColor', '' ),
				'--tp-footer-link-color' => tp_child_chrome_get_color( $attrs, 'footerLinkColor', '' ),
			]
		);

		$characters_html = tp_child_footer_render_characters_html( $attrs, $elements );
		$logo_html       = tp_child_footer_render_logo_html( $attrs, $elements );
		$nav_html        = tp_child_footer_render_nav_monolith_html( $attrs );
		$copyright_html  = tp_child_footer_render_copyright_html( $attrs );

		$container_children = $characters_html . $logo_html . $nav_html . $copyright_html;

		$inner_html = HTMLUtility::render(
			[
				'tag'               => 'footer',
				'attributes'        => array_filter(
					[
						'class' => 'site-footer tp-theme-footer',
						'style' => $footer_style ?: null,
					]
				),
				'childrenSanitizer' => 'et_core_esc_previously',
				'children'          => HTMLUtility::render(
					[
						'tag'               => 'div',
						'attributes'        => [ 'class' => 'tp-theme-footer-container' ],
						'childrenSanitizer' => 'et_core_esc_previously',
						'children'          => $container_children,
					]
				),
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
				'classnamesFunction'       => [ ThemeFooter::class, 'module_classnames' ],
				'stylesComponent'          => [ ThemeFooter::class, 'module_styles' ],
				'scriptDataComponent'      => [ ThemeFooter::class, 'module_script_data' ],
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
