import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/footer-copyright/module.json';
import { FooterCopyrightEdit } from './edit';
import { FooterCopyrightAttrs } from './types';

export const footerCopyright: ModuleLibrary.Module.RegisterDefinition<FooterCopyrightAttrs> = {
	metadata: metadata as Metadata.Values<FooterCopyrightAttrs>,
	renderers: {
		edit: FooterCopyrightEdit,
	},
};
