<?php
/**
 * Module: TP Marquee Item.
 */

namespace TaschePieneDiviChild\Modules\ThemeFooterShell;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class ThemeFooterShell implements DependencyInterface {

	use ThemeFooterShellTrait\RenderCallbackTrait;
	use ThemeFooterShellTrait\ModuleClassnamesTrait;
	use ThemeFooterShellTrait\ModuleStylesTrait;
	use ThemeFooterShellTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'theme-footer-shell/';

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
