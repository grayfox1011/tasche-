<?php
/**
 * Module: HeaderNavItem (child-module of Header sito): one link of the menu at the top.
 */

namespace TaschePieneDiviChild\Modules\HeaderNavItem;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\ModuleBase;

class HeaderNavItem extends ModuleBase {

	use HeaderNavItemTrait\RenderCallbackTrait;

	protected const JSON_FOLDER = 'header-nav-item';

	protected const CLASS_NAME = 'tp_header_nav_item';
}
