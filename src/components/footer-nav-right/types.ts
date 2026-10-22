import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number };
	innerContent?: { desktop?: { value?: string | number } };
};

export interface FooterNavRightAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	footerRight1Text?: ResponsiveAttr;
	footerRight1Link?: ResponsiveAttr;
	footerRight2Text?: ResponsiveAttr;
	footerRight2Link?: ResponsiveAttr;
	footerRight3Text?: ResponsiveAttr;
	footerRight3Link?: ResponsiveAttr;
	menuRightLocation?: ResponsiveAttr;
}

export type FooterNavRightEditProps = ModuleEditProps<FooterNavRightAttrs>;

export type NavPreviewItem = {
	title: string;
	url: string;
};
