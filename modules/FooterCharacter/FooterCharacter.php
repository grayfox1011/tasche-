<?php
/**
 * Module: FooterCharacter (child-module).
 */

namespace TaschePieneDiviChild\Modules\FooterCharacter;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;

class FooterCharacter implements DependencyInterface {

	use FooterCharacterTrait\RenderCallbackTrait;
	use FooterCharacterTrait\ModuleClassnamesTrait;
	use FooterCharacterTrait\ModuleStylesTrait;
	use FooterCharacterTrait\ModuleScriptDataTrait;

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . 'footer-character/';

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
