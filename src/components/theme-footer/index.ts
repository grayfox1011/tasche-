import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/theme-footer/module.json';
import { ThemeFooterEdit } from './edit';
import { SettingsContent } from './settings-content';
import { ThemeFooterAttrs } from './types';
import { placeholderContent } from './placeholder-content';
import { footerCharacterTemplate } from '../footer-character/values';
import { footerNavTemplate } from '../footer-nav-item/values';

export const themeFooter: ModuleLibrary.Module.RegisterDefinition<ThemeFooterAttrs> = {
	metadata: metadata as Metadata.Values<ThemeFooterAttrs>,
	placeholderContent,
	// Added by Divi with a new Footer sito: its Voci (0.8.0) and its Personaggi (0.6.8), in the order of the
	// theme's footer.
	template: [...footerNavTemplate(), ...footerCharacterTemplate()],
	settings: {
		content: SettingsContent,
	},
	renderers: {
		edit: ThemeFooterEdit,
	},
};
