import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number | { src?: string; id?: number } };
	innerContent?: { desktop?: { value?: string | number | { src?: string; id?: number } } };
};

export interface FooterCharacterAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	slot?: ResponsiveAttr;
	image?: ResponsiveAttr;
	imageUrl?: ResponsiveAttr;
}

export type FooterCharacterEditProps = ModuleEditProps<FooterCharacterAttrs>;
