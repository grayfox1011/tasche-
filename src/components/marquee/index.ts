import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/marquee/module.json';
import { MarqueeEdit } from './edit';
import { MarqueeAttrs } from './types';
import { placeholderContent } from './placeholder-content';

export const marquee: ModuleLibrary.Module.RegisterDefinition<MarqueeAttrs> = {
	metadata: metadata as Metadata.Values<MarqueeAttrs>,
	placeholderContent,
	renderers: {
		edit: MarqueeEdit,
	},
};
