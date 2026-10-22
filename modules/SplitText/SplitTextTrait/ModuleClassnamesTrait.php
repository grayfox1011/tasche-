<?php

namespace TaschePieneDiviChild\Modules\SplitText\SplitTextTrait;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Packages\Module\Options\Element\ElementClassnames;

trait ModuleClassnamesTrait {

	public static function module_classnames( array $args ): void {
		$classnames_instance = $args['classnamesInstance'];
		$attrs               = $args['attrs'];

		$classnames_instance->add( 'tp_split_text', true );

		$classnames_instance->add(
			ElementClassnames::classnames(
				[
					'attrs' => array_merge(
						$attrs['module']['decoration'] ?? [],
						[
							'link' => $attrs['module']['advanced']['link'] ?? [],
						]
					),
				]
			)
		);
	}
}
