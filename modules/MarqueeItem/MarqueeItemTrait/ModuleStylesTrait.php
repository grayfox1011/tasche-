<?php

namespace TaschePieneDiviChild\Modules\MarqueeItem\MarqueeItemTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\FrontEnd\Module\Style;

trait ModuleStylesTrait {

	public static function module_styles( array $args ): void {
		$elements = $args['elements'];

		Style::add(
			[
				'id'            => $args['id'],
				'name'          => $args['name'],
				'orderIndex'    => $args['orderIndex'],
				'storeInstance' => $args['storeInstance'],
				'styles'        => [
					$elements->style(
						[
							'attrName'   => 'module',
							'styleProps' => [
								'disabledOn' => [
									'disabledModuleVisibility' => $args['settings']['disabledModuleVisibility'] ?? null,
								],
							],
						]
					),
				],
			]
		);
	}
}
