<?php
/**
 * Shared structure of the child-theme modules: registration from module.json,
 * wrapper classnames, styles and script data. Each module sets its module.json
 * folder, its wrapper class and its optional design groups, and keeps its own
 * render callback.
 */

namespace TaschePieneDiviChild\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use ET\Builder\Framework\DependencyManagement\Interfaces\DependencyInterface;
use ET\Builder\FrontEnd\Module\Style;
use ET\Builder\Packages\Module\Options\Css\CssStyle;
use ET\Builder\Packages\Module\Options\Element\ElementClassnames;
use ET\Builder\Packages\ModuleLibrary\ModuleRegistration;
use WP_Block_Type_Registry;

abstract class ModuleBase implements DependencyInterface {

	/** Folder of the module.json inside modules-json/. */
	protected const JSON_FOLDER = '';

	/** Class added to the module wrapper. */
	protected const CLASS_NAME = '';

	/** Optional design groups (attribute names) printed by OptionalDesignStyles. */
	protected const DESIGN_GROUPS = [];

	public function load() {
		$module_json_folder_path = TP_CHILD_MODULES_JSON_PATH . static::JSON_FOLDER . '/';
		$render_callback         = [ static::class, 'render_callback' ];

		add_action(
			'init',
			function () use ( $module_json_folder_path, $render_callback ) {
				ModuleRegistration::register_module(
					$module_json_folder_path,
					[
						'render_callback' => $render_callback,
					]
				);
			}
		);
	}

	public static function module_classnames( array $args ): void {
		$classnames_instance = $args['classnamesInstance'];
		$attrs               = $args['attrs'];

		$classnames_instance->add( static::CLASS_NAME, true );

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

	/** Style props of the module element; a module can add its own selectors or filters. */
	protected static function module_style_props( array $args ): array {
		return [
			'disabledOn' => [
				'disabledModuleVisibility' => $args['settings']['disabledModuleVisibility'] ?? null,
			],
		];
	}

	public static function module_styles( array $args ): void {
		$elements = $args['elements'];
		$block    = WP_Block_Type_Registry::get_instance()->get_registered( $args['name'] ?? '' );
		$styles   = [
			$elements->style(
				[
					'attrName'   => 'module',
					'styleProps' => static::module_style_props( $args ),
				]
			),
			// "CSS personalizzato" (Advanced tab), printed as Divi's own modules do.
			CssStyle::style(
				[
					'selector'  => $args['orderClass'] ?? '',
					'attr'      => $args['attrs']['css'] ?? [],
					'cssFields' => $block->customCssFields ?? [],
				]
			),
		];

		if ( static::DESIGN_GROUPS ) {
			$styles[] = OptionalDesignStyles::styles( $elements, $args['attrs'] ?? $elements->module_attrs ?? [], static::DESIGN_GROUPS );
		}

		Style::add(
			[
				'id'            => $args['id'],
				'name'          => $args['name'],
				'orderIndex'    => $args['orderIndex'],
				'storeInstance' => $args['storeInstance'],
				'styles'        => $styles,
			]
		);
	}

	public static function module_script_data( array $args ): void {
		$args['elements']->script_data(
			[
				'attrName' => 'module',
			]
		);
	}
}
