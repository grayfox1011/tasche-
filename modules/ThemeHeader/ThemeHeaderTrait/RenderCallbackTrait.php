<?php
/**
 * ThemeHeader::render_callback().
 */

namespace TaschePieneDiviChild\Modules\ThemeHeader\ThemeHeaderTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\Utility\HTMLUtility;
use ET\Builder\FrontEnd\BlockParser\BlockParserStore;
use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use ET\Builder\Packages\Module\Module;
use TaschePieneDiviChild\Modules\ThemeHeader\ThemeHeader;
use WP_Block;

trait RenderCallbackTrait {

	public static function render_callback(
		array $attrs,
		string $content,
		WP_Block $block,
		ModuleElements $elements,
		array $default_printed_style_attrs
	): string {
		$logo_alt  = esc_attr( tp_child_chrome_get_string( $attrs, 'logoAlt', 'Tasche Piene' ) );
		$logo_link = esc_url( tp_child_resolve_nav_url( tp_child_chrome_get_string( $attrs, 'logoLink', '#home' ) ) );

		$burger_open_label  = esc_attr( tp_child_chrome_get_string( $attrs, 'burgerOpenLabel', 'Apri menu' ) );
		$burger_close_label = esc_attr( tp_child_chrome_get_string( $attrs, 'burgerCloseLabel', 'Chiudi menu' ) );

		$menu_location = sanitize_key( tp_child_chrome_get_string( $attrs, 'menuLocation', 'tp_main_nav' ) );
		$nav_items     = tp_child_chrome_resolve_nav_items(
			$attrs,
			'navItem',
			6,
			$menu_location,
			[
				[ 'title' => 'Home', 'url' => '#home', 'target' => '', 'xfn' => '' ],
				[ 'title' => 'I tramezzini', 'url' => '#menu', 'target' => '', 'xfn' => '' ],
				[ 'title' => 'La nostra idea', 'url' => '#chi-siamo', 'target' => '', 'xfn' => '' ],
				[ 'title' => 'Zena', 'url' => '#materie', 'target' => '', 'xfn' => '' ],
				[ 'title' => 'Dove siamo', 'url' => '#location', 'target' => '', 'xfn' => '' ],
			]
		);
		$nav_links = tp_child_chrome_render_header_links( $nav_items );

		$nav_style = tp_child_chrome_style_from_vars(
			[
				'--tp-nav-bg'         => tp_child_chrome_get_string( $attrs, 'navBackgroundColor', '' ),
				'--tp-nav-link-color' => tp_child_chrome_get_string( $attrs, 'navLinkColor', '' ),
			]
		);

		$logo_html = tp_child_chrome_render_image_element(
			$elements,
			$attrs,
			'logoImage',
			'logoUrl',
			TP_CHILD_URL . 'assets/svg/logo-tasche-piene-nav.svg',
			[
				'alt' => $logo_alt,
			]
		);

		$inner_html = HTMLUtility::render(
			[
				'tag'               => 'header',
				'attributes'        => [
					'class' => 'site-header tp-theme-header',
					'id'    => 'site-header',
				],
				'childrenSanitizer' => 'et_core_esc_previously',
				'children'          => HTMLUtility::render(
					[
						'tag'               => 'nav',
						'attributes'        => array_filter(
							[
								'class'       => 'tp-theme-nav',
								'aria-label'  => 'Principale',
								'style'       => $nav_style ?: null,
							]
						),
						'childrenSanitizer' => 'et_core_esc_previously',
						'children'          => HTMLUtility::render(
							[
								'tag'               => 'a',
								'attributes'        => [
									'href'       => $logo_link,
									'class'      => 'tp-theme-nav-logo',
									'aria-label' => $logo_alt,
								],
								'childrenSanitizer' => 'et_core_esc_previously',
								'children'          => $logo_html,
							]
						)
							. HTMLUtility::render(
								[
									'tag'               => 'button',
									'attributes'        => [
										'class'            => 'tp-theme-nav-toggle',
										'type'             => 'button',
										'aria-label'       => $burger_open_label,
										'aria-expanded'    => 'false',
										'aria-controls'    => 'primary-navigation',
										'data-open-label'  => $burger_open_label,
										'data-close-label' => $burger_close_label,
									],
									'childrenSanitizer' => 'et_core_esc_previously',
									'children'          => '<span></span><span></span><span></span>',
								]
							)
							. sprintf(
								'<ul id="primary-navigation" class="tp-theme-nav-links">%s</ul>',
								$nav_links
							),
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
				'classnamesFunction'       => [ ThemeHeader::class, 'module_classnames' ],
				'stylesComponent'          => [ ThemeHeader::class, 'module_styles' ],
				'scriptDataComponent'      => [ ThemeHeader::class, 'module_script_data' ],
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
