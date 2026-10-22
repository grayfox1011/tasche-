import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/footer-nav-left/module.json';
import { FooterNavLeftEdit } from './edit';
import { FooterNavLeftAttrs } from './types';

export const footerNavLeft: ModuleLibrary.Module.RegisterDefinition<FooterNavLeftAttrs> = {
	metadata: metadata as Metadata.Values<FooterNavLeftAttrs>,
	renderers: {
		edit: FooterNavLeftEdit,
	},
};
