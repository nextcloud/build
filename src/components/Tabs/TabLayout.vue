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
		<!-- Home layout style selector -->
		<ul class="layout__styles">
			<li v-for="layout in Layouts" :key="layout.id">
				<button :class="{ [layout.icon]: true,'layout__style--active': isSelected(layout) }"
					class="layout__style">
					{{ layout.name }}
				</button>
			</li>
		</ul>

		<!-- Required prop picker for the selected layout -->
	</AppSidebarTab>
</template>

<script>
import AppSidebarTab from '@nextcloud/vue/dist/Components/AppSidebarTab'

import AppMixin from '../../mixins/AppMixin'
import * as Layouts from '../../models/HomeLayouts'

export default {
	name: 'TabLayout',

	components: {
		AppSidebarTab,
	},
	mixins: [AppMixin],

	data() {
		return {
			Layouts,
		}
	},

	computed: {
		icon: () => 'icon-square-foot',
		id: () => 'build-layout',
		name: () => t('build', 'Layout'),
	},

	methods: {
		isSelected(layout) {
			return this.app.layout === layout.id
		},
	},
}
</script>

<style lang="scss">

</style>
