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
	<AppSidebarTab :id="id"
		:name="name"
		:icon="icon"
		class="layout">
		<LayoutPropsPicker
			:app-data="app.data"
			:layouts="HomeLayouts"
			:selected-layout="app.homelayout"
			:selected-layout-data="app.homelayoutData" />
		<LayoutPropsPicker
			:app-data="app.data"
			:layouts="DetailsLayouts"
			:selected-layout="app.detailslayout"
			:selected-layout-data="app.detailslayoutData" />
	</AppSidebarTab>
</template>

<script>
import AppSidebarTab from '@nextcloud/vue/dist/Components/AppSidebarTab'

import TabMixin from '../../mixins/TabMixin'
import DetailsLayouts from '../../models/DetailsLayouts'
import HomeLayouts from '../../models/HomeLayouts'
import LayoutPropsPicker from '../../components/LayoutPropsPicker'

export default {
	name: 'TabLayout',

	components: {
		AppSidebarTab,
		LayoutPropsPicker,
	},
	mixins: [TabMixin],

	data() {
		return {
			DetailsLayouts,
			HomeLayouts,
		}
	},

	computed: {
		icon: () => 'icon-square-foot',
		id: () => 'build-layout',
		name: () => t('build', 'Layout'),
	},
}
</script>

<style lang="scss">

</style>
