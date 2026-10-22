import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number };
	innerContent?: { desktop?: { value?: string | number } };
};

export interface FooterNavLeftAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	footerLeft1Text?: ResponsiveAttr;
	footerLeft1Link?: ResponsiveAttr;
	footerLeft2Text?: ResponsiveAttr;
	footerLeft2Link?: ResponsiveAttr;
	footerLeft3Text?: ResponsiveAttr;
	footerLeft3Link?: ResponsiveAttr;
	navAriaLabel?: ResponsiveAttr;
	menuLeftLocation?: ResponsiveAttr;
}

export type FooterNavLeftEditProps = ModuleEditProps<FooterNavLeftAttrs>;

export type NavPreviewItem = {
	title: string;
	url: string;
};
