import { addAction } from '@wordpress/hooks';
import { registerModule } from '@divi/module-library';

import { splitText } from './components/split-text';
import { marquee } from './components/marquee';
import { marqueeItem } from './components/marquee-item';
import { shapeWave } from './components/shape-wave';
import { themeHeader } from './components/theme-header';
import { themeFooter } from './components/theme-footer';
import { footerCharacter } from './components/footer-character';
import { footerLogo } from './components/footer-logo';
import { footerNavItem } from './components/footer-nav-item';
import { footerCopyrightLine } from './components/footer-copyright-line';
import { footerDecor } from './components/footer-decor';

addAction('divi.moduleLibrary.registerModuleLibraryStore.after', 'taschePieneChild', () => {
	[
		splitText,
		marqueeItem,
		marquee,
		shapeWave,
		themeHeader,
		themeFooter,
		footerCharacter,
		footerLogo,
		footerNavItem,
		footerCopyrightLine,
		footerDecor,
	].forEach((moduleDef) => {
		const { metadata, ...definition } = moduleDef;

		registerModule(metadata, definition);
	});
});
