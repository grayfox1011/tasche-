<?php
/**
 * Register child-theme modules with Divi's dependency tree.
 */

namespace TaschePieneDiviChild\Modules;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

use TaschePieneDiviChild\Modules\FooterCharacter\FooterCharacter;
use TaschePieneDiviChild\Modules\FooterCopyrightLine\FooterCopyrightLine;
use TaschePieneDiviChild\Modules\FooterDecor\FooterDecor;
use TaschePieneDiviChild\Modules\FooterLogo\FooterLogo;
use TaschePieneDiviChild\Modules\FooterNavItem\FooterNavItem;
use TaschePieneDiviChild\Modules\Marquee\Marquee;
use TaschePieneDiviChild\Modules\MarqueeItem\MarqueeItem;
use TaschePieneDiviChild\Modules\ShapeWave\ShapeWave;
use TaschePieneDiviChild\Modules\SplitText\SplitText;
use TaschePieneDiviChild\Modules\ThemeFooter\ThemeFooter;
use TaschePieneDiviChild\Modules\ThemeHeader\ThemeHeader;

add_action(
	'divi_module_library_modules_dependency_tree',
	function ( $dependency_tree ) {
		$dependency_tree->add_dependency( new SplitText() );
		$dependency_tree->add_dependency( new Marquee() );
		$dependency_tree->add_dependency( new MarqueeItem() );
		$dependency_tree->add_dependency( new ShapeWave() );
		$dependency_tree->add_dependency( new ThemeHeader() );
		$dependency_tree->add_dependency( new ThemeFooter() );

		// Footer child modules (modular, parent = tp/theme-footer).
		$dependency_tree->add_dependency( new FooterCharacter() );
		$dependency_tree->add_dependency( new FooterLogo() );
		$dependency_tree->add_dependency( new FooterNavItem() );
		$dependency_tree->add_dependency( new FooterCopyrightLine() );
		$dependency_tree->add_dependency( new FooterDecor() );
	}
);
