<?php
/**
 * Module: FooterNavItem (child-module).
 */

namespace TaschePieneDiviChild\Modules\FooterNavItem;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class FooterNavItem extends ModuleBase {

	use FooterNavItemTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'footer-nav-item';

	protected const CLASS_NAME = 'tp_footer_nav_item';

	protected const DESIGN_GROUPS = [ 'tpDesignText' ];
}
