<?php
/**
 * Module: FooterDecor (child-module).
 */

namespace TaschePieneDiviChild\Modules\FooterDecor;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class FooterDecor implements DependencyInterface {

	use FooterDecorTrait\RenderCallbackTrait;
	use FooterDecorTrait\ModuleClassnamesTrait;
	use FooterDecorTrait\ModuleStylesTrait;
	use FooterDecorTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'footer-decor/';

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
