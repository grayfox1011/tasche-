import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/theme-footer/module.json';
import { ThemeFooterEdit } from './edit';
import { SettingsContent } from './settings-content';
import { ThemeFooterAttrs } from './types';
import { placeholderContent } from './placeholder-content';

export const themeFooter: ModuleLibrary.Module.RegisterDefinition<ThemeFooterAttrs> = {
	metadata: metadata as Metadata.Values<ThemeFooterAttrs>,
	placeholderContent,
	settings: {
		content: SettingsContent,
	},
	renderers: {
		edit: ThemeFooterEdit,
	},
};
