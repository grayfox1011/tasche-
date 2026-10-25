import React, { ReactElement } from 'react';
import { useDynamicData } from '@divi/dynamic-data';

/**
 * A text field can hold Divi dynamic content (`$variable(...)$`): the site resolves it when it renders the
 * page, the builder resolves it here with Divi's own hook. Nothing shows while it loads.
 */
export const useDynamicText = (value: string): string => {
	const data = typeof useDynamicData === 'function' ? useDynamicData(value) : null;

	if (!data || !data.isDynamic) {
		return value;
	}

	return data.isLoading || data.resolvedValue === null || data.resolvedValue === undefined ? '' : String(data.resolvedValue);
};

export const DynamicText = ({ value }: { value: string }): ReactElement => <>{useDynamicText(value)}</>;

/** For labels built from several fields, where a hook per field does not fit: the dynamic parts are left out. */
export const withoutDynamicContent = (value: string): string => value.replace(/\$variable\(.*?\)\$/g, '').trim();
