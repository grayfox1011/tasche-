<?php
/**
 * Module: TP Marquee Item.
 */

namespace TaschePieneDiviChild\Modules\FooterNavLeft;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class FooterNavLeft implements DependencyInterface {

	use FooterNavLeftTrait\RenderCallbackTrait;
	use FooterNavLeftTrait\ModuleClassnamesTrait;
	use FooterNavLeftTrait\ModuleStylesTrait;
	use FooterNavLeftTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'footer-nav-left/';

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
