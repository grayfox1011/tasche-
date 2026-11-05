import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/header-nav-item/module.json';
import { HeaderNavItemEdit } from './edit';
import { HeaderNavItemAttrs } from './types';

export const headerNavItem: ModuleLibrary.Module.RegisterDefinition<HeaderNavItemAttrs> = {
	metadata: metadata as Metadata.Values<HeaderNavItemAttrs>,
	renderers: {
		edit: HeaderNavItemEdit,
	},
};
