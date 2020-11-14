<!--
  - @copyright Copyright (c) 2020 John Molakvoæ <skjnldsv@protonmail.com>
  -
  - @author John Molakvoæ <skjnldsv@protonmail.com>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program. If not, see <http://www.gnu.org/licenses/>.
  -
  -->

<template>
	<AppSidebarTab :id="id" :name="name" :icon="icon">
		Settings
	</AppSidebarTab>
</template>

<script>
import AppSidebarTab from '@nextcloud/vue/dist/Components/AppSidebarTab'

import TabMixin from '../../mixins/TabMixin'
export default {
	name: 'TabSettings',

	components: {
		AppSidebarTab,
	},
	mixins: [TabMixin],

	computed: {
		icon: () => 'icon-settings',
		id: () => 'build-settings',
		name: () => t('build', 'Settings'),
	},
}
</script>

<style lang="scss">
.empty-content {
	margin-top: 20vh;
	display: flex;
	flex-direction: column;
	align-items: center;
	text-align: center;

	&__icon {
		width: 64px;
		height: 64px;
		margin: 0 auto 15px;
		opacity: .4;
		background-size: 64px;
		background-repeat: no-repeat;
		background-position: center;
	}

	&__title {
		margin-bottom: 8px;
	}

	&__desc {
		margin-bottom: 16px;
	}
}

</style>
