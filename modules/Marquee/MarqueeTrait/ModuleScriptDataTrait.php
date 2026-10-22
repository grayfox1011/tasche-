<?php

namespace TaschePieneDiviChild\Modules\Marquee\MarqueeTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

trait ModuleScriptDataTrait {

	public static function module_script_data( array $args ): void {
		$args['elements']->script_data(
			[
				'attrName' => 'module',
			]
		);
	}
}
