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
	<section class="layout">
		<h3>{{ t('build', 'Style') }}</h3>
		<ul class="layout__styles">
			<li v-for="layout in layouts" :key="layout.id">
				<button
					class="layout__style"
					:class="{
						[layout.icon]: true,
						'layout__style--active': selected.id === layout.id
					}">
					{{ layout.name }}
				</button>
			</li>
		</ul>

		<div class="layout__props">
			<h3>{{ t('build', 'Data') }}</h3>
			<!-- <li v-for="layout in layouts" :key="layout.id">

			</li> -->
		</div>
	</section>
</template>

<script>
export default {
	name: 'LayoutPropsPicker',

	props: {
		layouts: {
			type: Array,
			required: true,
		},
		selectedId: {
			type: String,
			required: true,
		},
	},

	computed: {
		/**
		 * Return the selected layout. Fallback to default if none or invalid
		 * @returns {Object}
		 */
		selected() {
			return this.layouts.find(layout => layout.id === this.selectedId)
				|| this.layouts.find(layout => layout.default)
		},
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
