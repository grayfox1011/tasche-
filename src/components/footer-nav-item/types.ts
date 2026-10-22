import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number | { src?: string; id?: number } };
	innerContent?: { desktop?: { value?: string | number | { src?: string; id?: number } } };
};

export interface FooterNavItemAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	column?: ResponsiveAttr;
	text?: ResponsiveAttr;
	link?: ResponsiveAttr;
	target?: ResponsiveAttr;
}

export type FooterNavItemEditProps = ModuleEditProps<FooterNavItemAttrs>;
