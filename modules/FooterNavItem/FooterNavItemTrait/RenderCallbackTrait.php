<?php
/**
 * FooterNavItem::render_callback().
 *
 * The links of the two menus are printed by Footer sito, from the attributes of its Voci (see
 * tp_child_footer_nav_items_from_block()): a Voce prints nothing of its own.
 */

namespace TaschePieneDiviChild\Modules\FooterNavItem\FooterNavItemTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Packages\Module\Layout\Components\ModuleElements\ModuleElements;
use WP_Block;

trait RenderCallbackTrait {

	public static function render_callback(
		array $attrs,
		string $content,
		WP_Block $block,
		ModuleElements $elements,
		array $default_printed_style_attrs
	): string {
		// Footer sito prints the link from the Voce's fields; the Voce itself prints nothing, so that the
		// content of Footer sito holds the Personaggi only (0.6.8).
		return '';
	}
}
