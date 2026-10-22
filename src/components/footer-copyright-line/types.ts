import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number | { src?: string; id?: number } };
	innerContent?: { desktop?: { value?: string | number | { src?: string; id?: number } } };
};

export interface FooterCopyrightLineAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	text?: ResponsiveAttr;
	link?: ResponsiveAttr;
}

export type FooterCopyrightLineEditProps = ModuleEditProps<FooterCopyrightLineAttrs>;
