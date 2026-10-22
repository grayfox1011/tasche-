import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/theme-header/module.json';
import { ThemeHeaderEdit } from './edit';
import { SettingsContent } from './settings-content';
import { ThemeHeaderAttrs } from './types';
import { placeholderContent } from './placeholder-content';

export const themeHeader: ModuleLibrary.Module.RegisterDefinition<ThemeHeaderAttrs> = {
	metadata: metadata as Metadata.Values<ThemeHeaderAttrs>,
	placeholderContent,
	settings: {
		content: SettingsContent,
	},
	renderers: {
		edit: ThemeHeaderEdit,
	},
};
