<?php
/**
 * Module: TP Split Text.
 */

namespace TaschePieneDiviChild\Modules\SplitText;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class SplitText implements DependencyInterface {

	use SplitTextTrait\RenderCallbackTrait;
	use SplitTextTrait\ModuleClassnamesTrait;
	use SplitTextTrait\ModuleStylesTrait;
	use SplitTextTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'split-text/';

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
