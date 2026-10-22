<?php
/**
 * Module: TP Theme Footer.
 *
 * Renders the global Theme Builder footer with editable fields
 * while keeping the original markup/classes so the existing CSS
 * stays pixel-perfect.
 */

namespace TaschePieneDiviChild\Modules\ThemeFooter;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class ThemeFooter implements DependencyInterface {

	use ThemeFooterTrait\RenderCallbackTrait;
	use ThemeFooterTrait\ModuleClassnamesTrait;
	use ThemeFooterTrait\ModuleStylesTrait;
	use ThemeFooterTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'theme-footer/';

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
