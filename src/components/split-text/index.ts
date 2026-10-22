import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/split-text/module.json';
import { SplitTextEdit } from './edit';
import { SplitTextAttrs } from './types';
import { placeholderContent } from './placeholder-content';

import './style.scss';
import './module.scss';

export const splitText: ModuleLibrary.Module.RegisterDefinition<SplitTextAttrs> = {
	metadata: metadata as Metadata.Values<SplitTextAttrs>,
	placeholderContent,
	renderers: {
		edit: SplitTextEdit,
	},
};
