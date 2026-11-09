import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/theme-header/module.json';
import { ThemeHeaderEdit } from './edit';
import { SettingsContent } from './settings-content';
import { ThemeHeaderAttrs } from './types';
import { placeholderContent } from './placeholder-content';
import { headerNavTemplate } from '../header-nav-item/values';

export const themeHeader: ModuleLibrary.Module.RegisterDefinition<ThemeHeaderAttrs> = {
	metadata: metadata as Metadata.Values<ThemeHeaderAttrs>,
	placeholderContent,
	// Added by Divi with a new Header sito: its Voci (0.8.0).
	template: headerNavTemplate(),
	settings: {
		content: SettingsContent,
	},
	renderers: {
		edit: ThemeHeaderEdit,
	},
};
