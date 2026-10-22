<?php
/**
 * Module: TP Marquee.
 */

namespace TaschePieneDiviChild\Modules\Marquee;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class Marquee implements DependencyInterface {

	use MarqueeTrait\RenderCallbackTrait;
	use MarqueeTrait\ModuleClassnamesTrait;
	use MarqueeTrait\ModuleStylesTrait;
	use MarqueeTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'marquee/';

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
