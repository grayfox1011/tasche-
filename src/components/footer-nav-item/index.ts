import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/footer-nav-item/module.json';
import { FooterNavItemEdit } from './edit';
import { FooterNavItemAttrs } from './types';

export const footerNavItem: ModuleLibrary.Module.RegisterDefinition<FooterNavItemAttrs> = {
	metadata: metadata as Metadata.Values<FooterNavItemAttrs>,
	renderers: {
		edit: FooterNavItemEdit,
	},
};
