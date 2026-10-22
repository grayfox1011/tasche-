import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number | { src?: string; id?: number } };
	innerContent?: { desktop?: { value?: string | number | { src?: string; id?: number } } };
};

export interface FooterDecorAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	image?: ResponsiveAttr;
	imageUrl?: ResponsiveAttr;
	alt?: ResponsiveAttr;
	posTop?: ResponsiveAttr;
	posLeft?: ResponsiveAttr;
	width?: ResponsiveAttr;
	zIndex?: ResponsiveAttr;
}

export type FooterDecorEditProps = ModuleEditProps<FooterDecorAttrs>;
