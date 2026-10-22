import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number };
	innerContent?: { desktop?: { value?: string | number } };
};

export interface FooterCopyrightAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	copyrightLine1?: ResponsiveAttr;
	copyrightLine2?: ResponsiveAttr;
	copyrightLine3?: ResponsiveAttr;
	copyrightLine4?: ResponsiveAttr;
	copyrightLink1?: ResponsiveAttr;
	copyrightLink2?: ResponsiveAttr;
	copyrightLink3?: ResponsiveAttr;
	copyrightLink4?: ResponsiveAttr;
}

export type FooterCopyrightEditProps = ModuleEditProps<FooterCopyrightAttrs>;
