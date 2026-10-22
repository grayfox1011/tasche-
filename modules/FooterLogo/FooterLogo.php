<?php
/**
 * Module: FooterLogo (child-module).
 */

namespace TaschePieneDiviChild\Modules\FooterLogo;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class FooterLogo implements DependencyInterface {

	use FooterLogoTrait\RenderCallbackTrait;
	use FooterLogoTrait\ModuleClassnamesTrait;
	use FooterLogoTrait\ModuleStylesTrait;
	use FooterLogoTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'footer-logo/';

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
