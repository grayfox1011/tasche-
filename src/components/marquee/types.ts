import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number };
	innerContent?: { desktop?: { value?: string | number } };
};

export interface MarqueeAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	speed?: ResponsiveAttr;
	direction?: ResponsiveAttr;
	repeatCount?: ResponsiveAttr;
	pauseOnHover?: ResponsiveAttr;
	variant?: ResponsiveAttr;
	separator?: ResponsiveAttr;
}

export type MarqueeEditProps = ModuleEditProps<MarqueeAttrs> & {
	childModules?: Array<{ props?: { attrs?: { phrase?: ResponsiveAttr } } }>;
	childrenIds?: string[];
};
