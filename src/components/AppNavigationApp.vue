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
	<AppNavigationItem
		ref="navigationItem"
		:icon="icon"
		:title="appTitle"
		:to="{ name: 'designer', params: { uuid: app.uuid } }"
		@click="mobileCloseNavigation">
		<template #actions>
			<ActionCheckbox
				:disabled="loading"
				@check="onInstall"
				@uncheck="onUninstall">
				{{ installStatus }}
			</ActionCheckbox>
			<ActionLink
				:close-after-click="true"
				:href="appLink"
				icon="icon-external"
				target="_blank">
				{{ t('build', 'Open app') }}
			</ActionLink>
			<ActionLink
				:href="appExportLink"
				icon="icon-download">
				{{ t('build', 'Export app') }}
			</ActionLink>
			<ActionSeparator />
			<ActionButton
				:close-after-click="true"
				:disabled="loading"
				icon="icon-delete"
				@click="onDeleteApp">
				{{ t('build', 'Delete app') }}
			</ActionButton>
		</template>
	</AppNavigationItem>
</template>

<script>
import { generateUrl, generateOcsUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'
import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import ActionLink from '@nextcloud/vue/dist/Components/ActionLink'
import ActionCheckbox from '@nextcloud/vue/dist/Components/ActionCheckbox'
import ActionSeparator from '@nextcloud/vue/dist/Components/ActionSeparator'
import AppNavigationItem from '@nextcloud/vue/dist/Components/AppNavigationItem'
import axios from '@nextcloud/axios'
import Clipboard from 'v-clipboard'
import Vue from 'vue'

import AppMixin from '../mixins/AppMixin'

Vue.use(Clipboard)

export default {
	name: 'AppNavigationApp',

	components: {
		AppNavigationItem,
		ActionButton,
		ActionLink,
		ActionCheckbox,
		ActionSeparator,
	},

	mixins: [AppMixin],

	props: {
		app: {
			type: Object,
			required: true,
		},
	},

	data() {
		return {
			copySuccess: true,
			copied: false,
			loading: false,
		}
	},

	computed: {
		icon() {
			if (this.loading) {
				return 'icon-loading-small'
			}
			return 'icon-forms'
		},

		installStatus() {
			if (this.app.installed) {
				return t('build', 'Uninstall app')
			}
			return t('build', 'Install app')
		},

		/**
		 * Return the app share link
		 * @returns {string}
		 */
		appLink() {
			return window.location.protocol + '//' + window.location.host + generateUrl(`/apps/build/${this.app.uuid}`)
		},

		/**
		 * Return the app export link
		 * @returns {string}
		 */
		appExportLink() {
			return generateOcsUrl('apps/build/api/v1', 2) + `app/export/${this.app.uuid}`
		},
	},

	methods: {
		/**
		 * Closes the App-Navigation on mobile-devices
		 */
		mobileCloseNavigation() {
			this.$emit('mobile-close-navigation')
		},

		async onDeleteApp() {
			if (!confirm(t('build', 'Are you sure you want to delete {title}?', { title: this.appTitle }))) {
				return
			}

			// All good, let's delete
			this.loading = true
			try {
				await axios.delete(generateOcsUrl('apps/build/api/v1', 2) + `app/${this.app.uuid}`)
				this.$emit('delete', this.app.uuid)
			} catch (error) {
				showError(t('build', 'Error while deleting {title}', { title: this.appTitle }))
				console.error(error.response)
			} finally {
				this.loading = false
			}
		},

		async onInstall() {
			this.loading = true
			try {
				await axios.post(generateOcsUrl('apps/build/api/v1', 2) + `app/install/${this.app.uuid}`)
			} catch (error) {
				showError(t('build', 'Error while installing the app'))
				console.error(error.response)
			} finally {
				this.loading = false
			}
		},

		async onUninstall() {
			this.loading = true
			try {
				await axios.delete(generateOcsUrl('apps/build/api/v1', 2) + `app/uninstall/${this.app.uuid}`)
			} catch (error) {
				showError(t('build', 'Error while uninstalling the app'))
				console.error(error.response)
			} finally {
				this.loading = false
			}
		},

	},

}
</script>
