import { ModuleEditProps } from '@divi/module-library';
import { InternalAttrs } from '@divi/types';

type TextAttr = {
	desktop?: { value?: string };
	innerContent?: { desktop?: { value?: string } };
};

export interface HeaderNavItemAttrs extends InternalAttrs {
	module?: Record<string, unknown>;
	text?: TextAttr;
	link?: TextAttr;
	target?: TextAttr;
}

export type HeaderNavItemEditProps = ModuleEditProps<HeaderNavItemAttrs>;
