import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number };
	innerContent?: { desktop?: { value?: string | number } };
};

type UploadAttr = {
	innerContent?: { desktop?: { value?: string | { src?: string; url?: string } } };
};

export interface ThemeHeaderAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	logoImage?: UploadAttr;
	logoUrl?: ResponsiveAttr;
	logoAlt?: ResponsiveAttr;
	logoLink?: ResponsiveAttr;
	phone?: ResponsiveAttr;
	ctaLabel?: ResponsiveAttr;
	menuLocation?: ResponsiveAttr;
	burgerOpenLabel?: ResponsiveAttr;
	burgerCloseLabel?: ResponsiveAttr;
}

export type ThemeHeaderEditProps = ModuleEditProps<ThemeHeaderAttrs>;

export type NavPreviewItem = {
	title: string;
	url: string;
};
