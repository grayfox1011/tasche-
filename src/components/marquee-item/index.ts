import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/marquee-item/module.json';
import { MarqueeItemEdit } from './edit';
import { MarqueeItemAttrs } from './types';
import { placeholderContent } from './placeholder-content';

import './style.scss';
import './module.scss';

export const marqueeItem: ModuleLibrary.Module.RegisterDefinition<MarqueeItemAttrs> = {
	metadata: metadata as Metadata.Values<MarqueeItemAttrs>,
	placeholderContent,
	renderers: {
		edit: MarqueeItemEdit,
	},
};
