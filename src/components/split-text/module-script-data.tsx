import React, { ReactElement } from 'react';
import { useElementScriptData } from '@divi/module';

export const ModuleScriptData = (props: any): ReactElement | null => {
	const { attrs, id, selector } = props;

	useElementScriptData({
		attrs: attrs?.module?.decoration ?? {},
		selector,
		id,
	});

	return null;
};
