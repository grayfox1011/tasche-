import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number | { src?: string; id?: number } };
	innerContent?: { desktop?: { value?: string | number | { src?: string; id?: number } } };
};

export interface FooterLogoAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	image?: ResponsiveAttr;
	imageUrl?: ResponsiveAttr;
	alt?: ResponsiveAttr;
	link?: ResponsiveAttr;
}

export type FooterLogoEditProps = ModuleEditProps<FooterLogoAttrs>;
