<?php
/**
 * Module: TP Marquee Item.
 */

namespace TaschePieneDiviChild\Modules\FooterCopyright;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class FooterCopyright implements DependencyInterface {

	use FooterCopyrightTrait\RenderCallbackTrait;
	use FooterCopyrightTrait\ModuleClassnamesTrait;
	use FooterCopyrightTrait\ModuleStylesTrait;
	use FooterCopyrightTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'footer-copyright/';

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
