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
		<!-- Layout styling picker if multiple available -->
		<h3 v-if="layouts.length === 1 && false">
			{{ t('build', 'Selected style: {style}', {style: layouts[0].name}) }}
		</h3>
		<h3 v-else>
			{{ t('build', 'Style') }}
		</h3>
		<ul v-if="layouts.length > 1 && false" class="layout__styles">
			<li v-for="layout in layouts" :key="layout.id">
				<button
					class="layout__style-button"
					:class="{ 'layout__style-button--active': selected.id === layout.id }">
					<span class="layout__icon" :class="layout.icon" />
					{{ layout.name }}
				</button>
			</li>
		</ul>

		<h3>{{ t('build', 'Data') }}</h3>
		<ul class="layout__props">
			<li v-for="(prop, id) in selected.props"
				:key="id"
				:class="{'layout__prop--required': prop.required}"
				class="layout__prop">
				<label>
					<span>{{ prop.name }} <span v-if="prop.required" role="note">({{ t('build', 'required') }})</span></span>
					<Multiselect
						:options="dataForType(prop.type)"
						:placeholder="t('build', 'Select matching data')"
						:required="prop.required"
						:user-select="true"
						:value="selectedLayoutData[id]"
						class="layout__prop-picker"
						label="name"
						track-by="uuid">
						<template #noOptions>
							{{ t('build', 'Please add a data matching the type {type}', { type: prop.type.name }) }}
						</template>
					</Multiselect>
				</label>
			</li>
		</ul>
	</section>
</template>

<script>
import Multiselect from '@nextcloud/vue/dist/Components/Multiselect'
import getPropIcon from '../utils/PropIcon'

export default {
	name: 'LayoutPropsPicker',

	components: {
		Multiselect,
	},

	props: {
		layouts: {
			type: Array,
			required: true,
		},
		selectedLayout: {
			type: String,
			default: null,
		},
		appColumns: {
			type: Array,
			default: () => [],
		},
		selectedLayoutData: {
			type: Object,
			default: () => ({}),
		},
	},
	data() {
		return {
			getPropIcon,
		}
	},

	computed: {
		/**
		 * Return the selected layout. Fallback to default if none or invalid
		 * @returns {Object}
		 */
		selected() {
			return this.layouts.find(layout => layout.id === this.selectedLayout)
				|| this.layouts.find(layout => layout.default)
		},
	},

	methods: {
		/**
		 * Pull out every column that have a data that can provide the desired type
		 * @param {any} type a js type
		 * @returns {Array}
		 */
		dataForType(type) {
			return this.appColumns.map(column => {
				const ressources = column.data.provides.filter(provider => provider.type === type)

				// If we have available ressources, format them properly
				if (ressources.length > 0) {
					// Format option text, e.g 'Customer (Display name)'
					return ressources.map(ressource => {
						ressource = Object.assign({}, ressource, {
							// using the AvatarSelectOption layout to display icons and second lines
							displayName: column.name,
							desc: ressource?.name,
							isNoUser: true,
							icon: column.data.icon,
						})
						console.debug(ressource)
						return ressource
					})
				}
				return []
			}).flat()
		},
	},
}
</script>

<style lang="scss" scoped>
$layout-width: 96px;
$layout-padding: 16px;
$icon-size: 32px;

.layout {
	margin-bottom: 44px;

	&__styles,
	&__props {
		display: flex;
		flex-wrap: wrap;
		padding: 0 $layout-padding;
	}

	&__style-button {
		display: flex;
		align-items: center;
		flex-direction: column;
		width: $layout-width;
		padding: $layout-padding;
		border: none;
		border-radius: var(--border-radius-large);
		background-color: transparent;
		font-weight: normal;
		&--active {
			background-color: var(--color-background-dark);
			.layout__icon {
				opacity: .7;
			}
		}

		&:hover,
		&:focus {
			background-color: var(--color-background-darker);
			.layout__icon {
				opacity: .7;
			}
		}
	}

	&__icon {
		width: $icon-size;
		height: $icon-size;
		margin-bottom: 5px;
		opacity: .5;
		background-size: $icon-size;
	}

	&__prop {
		width: 100%;
		margin-bottom: 4px;

		> label {
			display: flex;
			align-items: center;
			justify-content: space-between;
			user-select: none;
		}

		span[role='note'] {
			color: var(--color-text-maxcontrast);
		}
	}

	&__prop-picker {
		// grow up to 250px, shrink if sidebar gets narrower
		flex: 0 1 250px;
		width: 250px;
		margin-left: 10px;
	}
}

</style>
