<?php
/**
 * Module: FooterCopyrightLine (child-module).
 */

namespace TaschePieneDiviChild\Modules\FooterCopyrightLine;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class FooterCopyrightLine implements DependencyInterface {

	use FooterCopyrightLineTrait\RenderCallbackTrait;
	use FooterCopyrightLineTrait\ModuleClassnamesTrait;
	use FooterCopyrightLineTrait\ModuleStylesTrait;
	use FooterCopyrightLineTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'footer-copyright-line/';

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
