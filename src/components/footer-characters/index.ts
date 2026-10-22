import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/footer-characters/module.json';
import { FooterCharactersEdit } from './edit';
import { FooterCharactersAttrs } from './types';

export const footerCharacters: ModuleLibrary.Module.RegisterDefinition<FooterCharactersAttrs> = {
	metadata: metadata as Metadata.Values<FooterCharactersAttrs>,
	renderers: {
		edit: FooterCharactersEdit,
	},
};
