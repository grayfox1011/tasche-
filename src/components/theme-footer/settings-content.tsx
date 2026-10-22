import React, { ReactElement } from 'react';
import { ModuleGroups } from '@divi/module';
import { type Module } from '@divi/types';

import { ThemeFooterAttrs } from './types';

export const SettingsContent = ({
	groupConfiguration,
}: Module.Settings.Panel.Props<ThemeFooterAttrs>): ReactElement => (
	<ModuleGroups groups={groupConfiguration} />
);
