import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/theme-footer-shell/module.json';
import { ThemeFooterShellEdit } from './edit';
import { ThemeFooterShellAttrs } from './types';

export const themeFooterShell: ModuleLibrary.Module.RegisterDefinition<ThemeFooterShellAttrs> = {
	metadata: metadata as Metadata.Values<ThemeFooterShellAttrs>,
	renderers: {
		edit: ThemeFooterShellEdit,
	},
};
