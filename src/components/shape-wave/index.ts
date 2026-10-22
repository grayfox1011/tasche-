import { type Metadata, type ModuleLibrary } from '@divi/types';
import metadata from '../../../modules-json/shape-wave/module.json';
import { ShapeWaveEdit } from './edit';
import { ShapeWaveAttrs } from './types';

import './style.scss';
import './module.scss';

export const shapeWave: ModuleLibrary.Module.RegisterDefinition<ShapeWaveAttrs> = {
	metadata: metadata as Metadata.Values<ShapeWaveAttrs>,
	renderers: {
		edit: ShapeWaveEdit,
	},
};
