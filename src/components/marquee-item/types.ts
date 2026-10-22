import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type ResponsiveAttr = {
	desktop?: { value?: string | number };
	innerContent?: { desktop?: { value?: string | number } };
};

export interface MarqueeItemAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	phrase?: ResponsiveAttr;
}

export type MarqueeItemEditProps = ModuleEditProps<MarqueeItemAttrs>;
