import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type SplitTextAttrValue = string | number | Record<string, unknown>;

interface SplitTextResponsiveAttr {
	desktop?: { value?: SplitTextAttrValue };
	innerContent?: { desktop?: { value?: SplitTextAttrValue } };
}

export interface SplitTextAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	lineOne?: SplitTextResponsiveAttr;
	lineTwo?: SplitTextResponsiveAttr;
	lineThree?: SplitTextResponsiveAttr;
	lineFour?: SplitTextResponsiveAttr;
	lineFive?: SplitTextResponsiveAttr;
	lineSix?: SplitTextResponsiveAttr;
	tagName?: SplitTextResponsiveAttr;
	variant?: SplitTextResponsiveAttr;
	splitType?: SplitTextResponsiveAttr;
	trigger?: SplitTextResponsiveAttr;
	responsiveMode?: SplitTextResponsiveAttr;
	duration?: SplitTextResponsiveAttr;
	stagger?: SplitTextResponsiveAttr;
	delay?: SplitTextResponsiveAttr;
	yPercent?: SplitTextResponsiveAttr;
	ease?: SplitTextResponsiveAttr;
	css?: Record<string, unknown>;
}

export type SplitTextEditProps = ModuleEditProps<SplitTextAttrs>;
