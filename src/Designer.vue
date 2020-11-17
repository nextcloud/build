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
			<AppNavigationNew button-class="icon-add" :text="t('build', 'New app')" @click="onNewApp" />
			<template #list>
				<AppNavigationApp v-for="app in apps"
					:key="app.appinfo.uuid"
					:app="app"
					@mobile-close-navigation="mobileCloseNavigation"
					@delete="onDeleteApp" />
			</template>
		</AppNavigation>

		<!-- No apps & loading emptycontents -->
		<AppContent v-if="loading || noApps || (!routeUuid && $route.name !== 'create')">
			<EmptyContent v-if="loading" icon="icon-loading">
				{{ t('build', 'Loading apps …') }}
			</EmptyContent>
			<EmptyContent v-else-if="noApps">
				{{ t('build', 'No apps created yet') }}
				<template #action>
					<button class="primary" @click="onNewApp">
						{{ t('build', 'Create an app') }}
					</button>
				</template>
			</EmptyContent>

			<EmptyContent v-else>
				{{ t('build', 'Select an app or create a new one') }}
				<template #action>
					<button class="primary" @click="onNewApp">
						{{ t('build', 'Create new app') }}
					</button>
				</template>
			</EmptyContent>
		</AppContent>

		<!-- No errors show router content -->
		<template v-else>
			<router-view :app.sync="selectedApp" />
			<router-view :app="selectedApp"
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
import EmptyContent from '@nextcloud/vue/dist/Components/EmptyContent'
import isMobile from '@nextcloud/vue/src/mixins/isMobile'

import AppNavigationApp from './components/AppNavigationApp'
import AppFormat from './utils/AppFormat'

export default {
	name: 'Designer',

	components: {
		AppNavigationApp,
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

		routeUuid() {
			return this.$route.params.uuid
		},

		selectedApp: {
			get() {
				return this.apps.find(app => app.appinfo.uuid === this.routeUuid)
			},
			set(app) {
				const index = this.apps.findIndex(search => search.appinfo.uuid === this.routeUuid)
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
				this.apps = [
					{
						metadata: {
							version: '1.0.0',
						},
						appinfo: {
							uuid: '52365aa0-e808-4d65-8e25-8875ce20fcc6',
							name: 'Customers',
							icon: 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIGhlaWdodD0iMjQiIHdpZHRoPSIyNCI+PHBhdGggZD0iTTAgMGgyNHYyNEgweiIgZmlsbD0ibm9uZSIvPjxwYXRoIGQ9Ik0xNiAxMWMxLjY2IDAgMi45OS0xLjM0IDIuOTktM1MxNy42NiA1IDE2IDVhMyAzIDAgMTAwIDZ6bS04IDBjMS42NiAwIDIuOTktMS4zNCAyLjk5LTNTOS42NiA1IDggNWEzIDMgMCAxMDAgNnptMCAyYy0yLjMzIDAtNyAxLjE3LTcgMy41VjE5aDE0di0yLjVjMC0yLjMzLTQuNjctMy41LTctMy41em04IDBjLS4yOSAwLS42Mi4wMi0uOTcuMDUgMS4xNi44NCAxLjk3IDEuOTcgMS45NyAzLjQ1VjE5aDZ2LTIuNWMwLTIuMzMtNC42Ny0zLjUtNy0zLjV6Ii8+PC9zdmc+',
							description: 'Young bleat call collared Sauron\'s answer then? Mood rising blessings Sam criminal? Many that live deserve death. Some that die deserve life. Lightest addled Buckland massacre tomato\'s confounded. Greyhame night hanging caverns predicted member. Diamond fierce champ let\'s anyway squash light visiting Legolas. Smash signature be got called thirsty Boromir\'s horses. Mereth marshaling need wrath wider caution 60 tumble worry ill-favored bars sniff. Repel Mungo\'s parent cracked second faded knots.',
							version: '1.0',
							created: 1604287762,
							lastModified: 1604487762,
						},
						structure: {
							table: {
								uuid: 'a6da4e92-b67d-40cc-a778-1b8342ab1a03',
								name: 'default',
								columns: [
									{
										uuid: '676f0614-4306-42a9-b963-514624f1774d',
										name: 'Product',
										type: 'text',
										required: true,
										default: 'Default placeholder',
									},
									{
										uuid: '84979eeb-fb3c-4304-a28f-225d8cfd621e',
										name: 'Icon',
										type: 'image',
										required: false,
									},
									{
										uuid: '0317e9e8-1b2c-4e36-958c-01429a0b98c8',
										name: 'Maintainer',
										type: 'contact',
										required: true,
										'max-values': 2,
									},
									{
										uuid: '9f420efa-2d61-4682-adf7-d639d513348c',
										name: 'State',
										type: 'text',
										required: true,
										choices: {
											'9f420efa-2d61-4682-adf7-d639d513348c': 'Not test',
											'6aa2af7b-999d-4107-abcb-56fa0f53606b': 'testing',
											'537da2ab-0917-4546-871b-ec260485128d': 'Test failed',
											'93a938e9-a392-4de3-8f1e-683266c7b296': 'Test succeeded',
										},
									},
								],
							},
						},
						views: {
							navigation: {
								type: 'list',
								title: '676f0614-4306-42a9-b963-514624f1774d',
								image: '84979eeb-fb3c-4304-a28f-225d8cfd621e',
							},
							details: {
								type: 'avatar-header',
								title: '676f0614-4306-42a9-b963-514624f1774d',
								image: '84979eeb-fb3c-4304-a28f-225d8cfd621e',
							},
						},
					},
				].map(AppFormat)
				// const response = await axios.get(generateOcsUrl('apps/build/api/v1', 2) + 'build')
				// this.apps = response.data
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
		async onNewApp() {
			try {
				// Request a new empty app
				const response = await axios.post(generateOcsUrl('apps/build/api/v1', 2) + 'app')
				const newApp = response.data
				this.apps.unshift(newApp)
				this.$router.push({ name: 'edit', params: { uuid: newApp.appinfo.uuid } })
				this.mobileCloseNavigation()
			} catch (error) {
				showError(t('build', 'Unable to create a new app'))
				console.error(error)
			}
		},

		/**
		 * Remove app from apps list after successful server deletion request
		 *
		 * @param {Number} uuid the app uuid
		 */
		async onDeleteApp(uuid) {
			const appIndex = this.apps.findIndex(app => app.appinfo.uuid === uuid)
			const deletedUuid = this.apps[appIndex].uuid

			this.apps.splice(appIndex, 1)

			// Redirect if current app has been deleted
			if (deletedUuid === this.routeUuid) {
				this.$router.push({ name: 'root' })
			}
		},
	},
}
</script>
