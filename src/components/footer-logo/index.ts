import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/footer-logo/module.json';
import { FooterLogoEdit } from './edit';
import { FooterLogoAttrs } from './types';

export const footerLogo: ModuleLibrary.Module.RegisterDefinition<FooterLogoAttrs> = {
	metadata: metadata as Metadata.Values<FooterLogoAttrs>,
	renderers: {
		edit: FooterLogoEdit,
	},
};
