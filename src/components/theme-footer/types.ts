import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number | { src?: string; id?: number; url?: string } };
	innerContent?: { desktop?: { value?: string | number | { src?: string; id?: number; url?: string } } };
};

type UploadAttr = ResponsiveAttr;

export interface ThemeFooterAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	footerBackgroundColor?: ResponsiveAttr;
	footerLinkColor?: ResponsiveAttr;
	navAriaLabel?: ResponsiveAttr;
	logoImage?: UploadAttr;
	logoUrl?: ResponsiveAttr;
	logoAlt?: ResponsiveAttr;
	logoLink?: ResponsiveAttr;
	characterOneImage?: UploadAttr;
	characterFourImage?: UploadAttr;
	characterThreeImage?: UploadAttr;
	footerLeft1Text?: ResponsiveAttr;
	footerLeft1Link?: ResponsiveAttr;
	footerLeft2Text?: ResponsiveAttr;
	footerLeft2Link?: ResponsiveAttr;
	footerLeft3Text?: ResponsiveAttr;
	footerLeft3Link?: ResponsiveAttr;
	footerRight1Text?: ResponsiveAttr;
	footerRight1Link?: ResponsiveAttr;
	footerRight2Text?: ResponsiveAttr;
	footerRight2Link?: ResponsiveAttr;
	footerRight3Text?: ResponsiveAttr;
	footerRight3Link?: ResponsiveAttr;
	copyrightLine1?: ResponsiveAttr;
	copyrightLine2?: ResponsiveAttr;
	copyrightLine3?: ResponsiveAttr;
	copyrightLine4?: ResponsiveAttr;
	copyrightLine5?: ResponsiveAttr;
	copyrightLink1?: ResponsiveAttr;
	copyrightLink2?: ResponsiveAttr;
	copyrightLink3?: ResponsiveAttr;
	copyrightLink4?: ResponsiveAttr;
	copyrightLink5?: ResponsiveAttr;
}

export type ThemeFooterEditProps = ModuleEditProps<ThemeFooterAttrs>;
