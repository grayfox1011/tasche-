<?php
/**
 * Module: TP Shape Wave.
 */

namespace TaschePieneDiviChild\Modules\ShapeWave;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class ShapeWave implements DependencyInterface {

	use ShapeWaveTrait\RenderCallbackTrait;
	use ShapeWaveTrait\ModuleClassnamesTrait;
	use ShapeWaveTrait\ModuleStylesTrait;
	use ShapeWaveTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'shape-wave/';

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
