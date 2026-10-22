import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/footer-nav-right/module.json';
import { FooterNavRightEdit } from './edit';
import { FooterNavRightAttrs } from './types';

export const footerNavRight: ModuleLibrary.Module.RegisterDefinition<FooterNavRightAttrs> = {
	metadata: metadata as Metadata.Values<FooterNavRightAttrs>,
	renderers: {
		edit: FooterNavRightEdit,
	},
};
