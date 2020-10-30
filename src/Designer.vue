<!--
  - @copyright Copyright (c) 2018 René Gieling <github@dartcafe.de>
  -
  - @author René Gieling <github@dartcafe.de>
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
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -
  -->

<template>
	<Content app-name="apps">
		<AppNavigation>
			<AppNavigationNew button-class="icon-add" :text="t('build', 'New app')" @click="onNewForm" />
			<template #list>
				<AppNavigationForm v-for="app in apps"
					:key="app.id"
					:app="app"
					@mobile-close-navigation="mobileCloseNavigation"
					@delete="onDeleteForm" />
			</template>
		</AppNavigation>

		<!-- No apps & loading emptycontents -->
		<AppContent v-if="loading || noApps || (!routeHash && $route.name !== 'create')">
			<EmptyContent v-if="loading" icon="icon-loading">
				{{ t('build', 'Loading apps …') }}
			</EmptyContent>
			<EmptyContent v-else-if="noApps">
				{{ t('build', 'No apps created yet') }}
				<template #action>
					<button class="primary" @click="onNewForm">
						{{ t('build', 'Create an app') }}
					</button>
				</template>
			</EmptyContent>

			<EmptyContent v-else>
				{{ t('build', 'Select an app or create a new one') }}
				<template #action>
					<button class="primary" @click="onNewForm">
						{{ t('build', 'Create new app') }}
					</button>
				</template>
			</EmptyContent>
		</AppContent>

		<!-- No errors show router content -->
		<template v-else>
			<router-view :app.sync="selectedForm" />
			<router-view v-if="!selectedForm.partial"
				:app="selectedForm"
				name="sidebar" />
		</template>
	</Content>
</template>

<script>
import { emit } from '@nextcloud/event-bus'
import { showError } from '@nextcloud/dialogs'
import { generateOcsUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'

import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
import AppNavigationNew from '@nextcloud/vue/dist/Components/AppNavigationNew'
import Content from '@nextcloud/vue/dist/Components/Content'
import isMobile from '@nextcloud/vue/src/mixins/isMobile'

import AppNavigationForm from './components/AppNavigationForm'
import EmptyContent from './components/EmptyContent'

export default {
	name: 'Designer',

	components: {
		AppNavigationForm,
		AppContent,
		AppNavigation,
		AppNavigationNew,
		Content,
		EmptyContent,
	},

	mixins: [isMobile],

	data() {
		return {
			loading: true,
			apps: [],
		}
	},

	computed: {
		noApps() {
			return this.apps && this.apps.length === 0
		},

		routeHash() {
			return this.$route.params.hash
		},

		selectedForm: {
			get() {
				return this.apps.find(app => app.hash === this.routeHash)
			},
			set(app) {
				const index = this.apps.findIndex(search => search.hash === this.routeHash)
				if (index > -1) {
					this.$set(this.apps, index, app)
				}
			},
		},
	},

	beforeMount() {
		this.loadApps()
	},

	methods: {
		/**
		 * Closes the App-Navigation on mobile-devices
		 */
		mobileCloseNavigation() {
			if (this.isMobile) {
				emit('toggle-navigation', { open: false })
			}
		},

		/**
		 * Initial apps load
		 */
		async loadApps() {
			this.loading = true
			try {
				const response = await axios.get(generateOcsUrl('apps/build/api/v1', 2) + 'build')
				this.apps = response.data
			} catch (error) {
				showError(t('build', 'An error occurred while loading the apps list'))
				console.error(error)
			} finally {
				this.loading = false
			}
		},

		/**
		 *
		 */
		async onNewForm() {
			try {
				// Request a new empty app
				const response = await axios.post(generateOcsUrl('apps/build/api/v1', 2) + 'app')
				const newApp = response.data
				this.apps.unshift(newApp)
				this.$router.push({ name: 'edit', params: { hash: newApp.hash } })
				this.mobileCloseNavigation()
			} catch (error) {
				showError(t('build', 'Unable to create a new app'))
				console.error(error)
			}
		},

		/**
		 * Remove app from apps list after successful server deletion request
		 *
		 * @param {Number} id the app id
		 */
		async onDeleteForm(id) {
			const appIndex = this.apps.findIndex(app => app.id === id)
			const deletedHash = this.apps[appIndex].hash

			this.apps.splice(appIndex, 1)

			// Redirect if current app has been deleted
			if (deletedHash === this.routeHash) {
				this.$router.push({ name: 'root' })
			}
		},
	},
}
</script>
