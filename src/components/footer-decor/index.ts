import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/footer-decor/module.json';
import { FooterDecorEdit } from './edit';
import { FooterDecorAttrs } from './types';

export const footerDecor: ModuleLibrary.Module.RegisterDefinition<FooterDecorAttrs> = {
	metadata: metadata as Metadata.Values<FooterDecorAttrs>,
	renderers: {
		edit: FooterDecorEdit,
	},
};
