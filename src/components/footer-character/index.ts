import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/footer-character/module.json';
import { FooterCharacterEdit } from './edit';
import { FooterCharacterAttrs } from './types';

export const footerCharacter: ModuleLibrary.Module.RegisterDefinition<FooterCharacterAttrs> = {
	metadata: metadata as Metadata.Values<FooterCharacterAttrs>,
	renderers: {
		edit: FooterCharacterEdit,
	},
};
