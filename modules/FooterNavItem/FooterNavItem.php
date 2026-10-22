<?php
/**
 * Module: FooterNavItem (child-module).
 */

namespace TaschePieneDiviChild\Modules\FooterNavItem;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class FooterNavItem implements DependencyInterface {

	use FooterNavItemTrait\RenderCallbackTrait;
	use FooterNavItemTrait\ModuleClassnamesTrait;
	use FooterNavItemTrait\ModuleStylesTrait;
	use FooterNavItemTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'footer-nav-item/';

		add_action(
			'init',
			function () use ( $module_json_folder_path ) {
				ModuleRegistration::register_module(
					$module_json_folder_path,
					[
						'render_callback' => [ self::class, 'render_callback' ],
					]
				);
			}
		);
	}
}
