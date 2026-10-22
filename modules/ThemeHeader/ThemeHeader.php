<?php
/**
 * Module: TP Theme Header.
 *
 * Renders the global Theme Builder header with editable fields
 * while keeping the original markup/classes so the existing CSS
 * stays pixel-perfect.
 */

namespace TaschePieneDiviChild\Modules\ThemeHeader;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class ThemeHeader implements DependencyInterface {

	use ThemeHeaderTrait\RenderCallbackTrait;
	use ThemeHeaderTrait\ModuleClassnamesTrait;
	use ThemeHeaderTrait\ModuleStylesTrait;
	use ThemeHeaderTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'theme-header/';

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
