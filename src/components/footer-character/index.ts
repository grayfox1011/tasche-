import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/footer-character/module.json';
import { FooterCharacterEdit } from './edit';
import { FooterCharacterAttrs } from './types';
import { ASSET_BASE } from '../../utils/asset-base';

export const footerCharacter: ModuleLibrary.Module.RegisterDefinition<FooterCharacterAttrs> = {
	metadata: metadata as Metadata.Values<FooterCharacterAttrs>,
	// A Personaggio added in the builder starts with a drawing of the theme (the man eating), in the free place.
	placeholderContent: {
		image: {
			innerContent: {
				desktop: {
					value: {
						src: `${ASSET_BASE}/images/footer/personaggi-3.webp`,
					},
				},
			},
		},
	},
	renderers: {
		edit: FooterCharacterEdit,
	},
};
