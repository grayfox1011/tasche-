import React, { ReactElement } from 'react';
import { ModuleGroups } from '@divi/module';
import { type Module } from '@divi/types';

import { ThemeHeaderAttrs } from './types';

export const SettingsContent = ({
	groupConfiguration,
}: Module.Settings.Panel.Props<ThemeHeaderAttrs>): ReactElement => (
	<ModuleGroups groups={groupConfiguration} />
);
