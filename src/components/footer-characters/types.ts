import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number };
	innerContent?: { desktop?: { value?: string | number | { src?: string; url?: string } } };
};

type UploadAttr = {
	innerContent?: { desktop?: { value?: string | { src?: string; url?: string } } };
	desktop?: { value?: string | { src?: string; url?: string } };
};

export interface FooterCharactersAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	characterOneImage?: UploadAttr;
	characterFourImage?: UploadAttr;
	characterThreeImage?: UploadAttr;
}

export type FooterCharactersEditProps = ModuleEditProps<FooterCharactersAttrs>;
