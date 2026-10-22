import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/footer-copyright-line/module.json';
import { FooterCopyrightLineEdit } from './edit';
import { FooterCopyrightLineAttrs } from './types';

export const footerCopyrightLine: ModuleLibrary.Module.RegisterDefinition<FooterCopyrightLineAttrs> = {
	metadata: metadata as Metadata.Values<FooterCopyrightLineAttrs>,
	renderers: {
		edit: FooterCopyrightLineEdit,
	},
};
