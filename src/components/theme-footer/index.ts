import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/theme-footer/module.json';
import { ThemeFooterEdit } from './edit';
import { SettingsContent } from './settings-content';
import { ThemeFooterAttrs } from './types';
import { placeholderContent } from './placeholder-content';
import { footerCharacterTemplate } from '../footer-character/values';

export const themeFooter: ModuleLibrary.Module.RegisterDefinition<ThemeFooterAttrs> = {
	metadata: metadata as Metadata.Values<ThemeFooterAttrs>,
	placeholderContent,
	// Added by Divi with a new Footer sito: its Personaggi (0.6.8).
	template: footerCharacterTemplate(),
	settings: {
		content: SettingsContent,
	},
	renderers: {
		edit: ThemeFooterEdit,
	},
};
