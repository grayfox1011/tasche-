import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number };
	innerContent?: { desktop?: { value?: string | number } };
};

export interface ShapeWaveAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	variant?: ResponsiveAttr;
	shapeWidth?: ResponsiveAttr;
	shapeHeight?: ResponsiveAttr;
	offsetY?: ResponsiveAttr;
	align?: ResponsiveAttr;
	fillColor?: ResponsiveAttr;
	backgroundColor?: ResponsiveAttr;
	flipHorizontal?: ResponsiveAttr;
	flipVertical?: ResponsiveAttr;
}

export type ShapeWaveEditProps = ModuleEditProps<ShapeWaveAttrs>;

export type ShapePath = {
	className?: string;
	path: string;
	fill: string;
};

export type ShapeDefinition = {
	viewBox: string;
	path?: string;
	paths?: ShapePath[];
	clipPath?: string;
	fill: string;
	background: string;
	width: number;
	height: number;
};
