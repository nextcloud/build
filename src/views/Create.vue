<!--
  - @copyright Copyright (c) 2018 René Gieling <github@dartcafe.de>
  -
  - @author René Gieling <github@dartcafe.de>
  - @author Nick Gallo
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
	<AppContent v-if="isLoadingApp">
		<EmptyContent icon="icon-loading">
			{{ t('build', 'Loading {appTitle} …', { appTitle }) }}
		</EmptyContent>
	</AppContent>

	<AppContent v-else>
		<!-- Show results & sidebar button -->
		<TopBar>
			<button @click="showResults">
				<span class="icon-comment" role="img" />
				{{ t('build', 'Responses') }}
			</button>
			<button v-tooltip="t('build', 'Toggle settings')"
				@click="toggleSidebar">
				<span class="icon-menu-sidebar" role="img" />
			</button>
		</TopBar>

		<!-- Apps title & description-->
		<header>
			<h2>
				<label class="hidden-visually" for="app-title">{{ t('build', 'App title') }}</label>
				<input
					id="app-title"
					ref="title"
					v-model="app.title"
					class="app-title"
					:minlength="0"
					:maxlength="maxStringLengths.appTitle"
					:placeholder="t('build', 'App title')"
					:required="true"
					autofocus
					type="text"
					@click="selectIfUnchanged"
					@keyup="onTitleChange">
			</h2>
			<label class="hidden-visually" for="app-desc">{{ t('build', 'Description') }}</label>
			<textarea
				ref="description"
				v-model="app.description"
				class="app-desc"
				:maxlength="maxStringLengths.appDescription"
				:placeholder="t('build', 'Description')"
				@change="autoSizeDescription"
				@keydown="autoSizeDescription"
				@keyup="onDescChange" />
			<!-- Generate app inappation message-->
			<p class="info-message" v-text="infoMessage" />
		</header>

		<section>
			<!-- Data list -->
			<Draggable v-model="app.data"
				:animation="200"
				tag="ul"
				handle=".data__drag-handle"
				@change="onDataOrderChange"
				@start="isDragging = true"
				@end="isDragging = false">
				<Data
					:is="DataTypes[data.type].component"
					v-for="(data, index) in app.data"
					ref="data"
					:key="data.id"
					:answer-type="DataTypes[data.type]"
					:index="index + 1"
					:max-string-lengths="maxStringLengths"
					v-bind.sync="app.data[index]"
					@delete="deleteData(data)" />
			</Draggable>

			<!-- Add new data toolbar -->
			<div class="data-toolbar" role="toolbar">
				<Actions ref="dataMenu"
					:open.sync="dataMenuOpened"
					:menu-title="t('build', 'Add data type')"
					:primary="true"
					:default-icon="isLoadingData ? 'icon-loading-small' : 'icon-add-primary'">
					<ActionButton v-for="(answer, type) in DataTypes"
						:key="answer.label"
						:close-after-click="true"
						:disabled="isLoadingData"
						:icon="answer.icon"
						class="data-toolbar__data"
						@click="addData(type)">
						{{ answer.label }}
					</ActionButton>
				</Actions>
			</div>
		</section>
	</AppContent>
</template>

<script>
import { generateOcsUrl } from '@nextcloud/router'
import { loadState } from '@nextcloud/initial-state'
import { showError } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'
import debounce from 'debounce'
import Draggable from 'vuedraggable'

import ActionButton from '@nextcloud/vue/dist/Components/ActionButton'
import Actions from '@nextcloud/vue/dist/Components/Actions'
import AppContent from '@nextcloud/vue/dist/Components/AppContent'

import DataTypes from '../models/DataTypes'
import CancelableRequest from '../utils/CancelableRequest'
import EmptyContent from '../components/EmptyContent'
import Data from '../components/Data/Data'
import DataTextLong from '../components/Data/DataTextLong'
import DataTextMultiple from '../components/Data/DataTextMultiple'
import DataTextShort from '../components/Data/DataTextShort'
import TopBar from '../components/TopBar'
import ViewsMixin from '../mixins/ViewsMixin'
import SetWindowTitle from '../utils/SetWindowTitle'

window.axios = axios

export default {
	name: 'Create',
	components: {
		ActionButton,
		Actions,
		AppContent,
		Draggable,
		EmptyContent,
		Data,
		DataTextLong,
		DataTextMultiple,
		DataTextShort,
		TopBar,
	},

	mixins: [ViewsMixin],

	data() {
		return {
			maxStringLengths: loadState('build', 'maxStringLengths'),

			dataMenuOpened: false,
			DataTypes,

			// Various states
			isLoadingApp: true,
			isLoadingData: false,
			errorApp: false,

			isDragging: false,

			// storage for axios cancel function
			cancelFetchFullApp: () => {},
		}
	},

	computed: {

		hasData() {
			return this.app?.data && this.app.data.length === 0
		},

		isMandatoryUsed() {
			return this.hasData && this.app.data.reduce((isUsed, data) => isUsed || data.mandatory, false)
		},

		infoMessage() {
			let message = ''
			if (this.app.isAnonymous) {
				message += t('build', 'Responses are anonymous.')
			} else {
				message += t('build', 'Responses are connected to your Nextcloud account.')
			}
			if (this.isMandatoryUsed) {
				message += ' ' + t('build', 'An asterisk (*) indicates mandatory data.')
			}
			return message
		},
	},

	watch: {
		// Fetch full app on change
		hash() {
			this.fetchFullApp(this.app.uuid)
		},

		// Update Window-Title on title change
		'app.title'() {
			SetWindowTitle(this.appTitle)
		},
	},

	beforeMount() {
		this.fetchFullApp(this.app.uuid)
		SetWindowTitle(this.appTitle)
	},

	updated() {
		this.autoSizeDescription()
	},

	methods: {
		/**
		 * Fetch the full app data and update parent
		 *
		 * @param {number} id the unique app hash
		 */
		async fetchFullApp(id) {
			this.isLoadingApp = true

			// Cancel previous request
			this.cancelFetchFullApp('New request pending')

			// Output after cancelling previous request for logical order.
			console.debug('Loading app', id)

			// Create new cancelable get request
			const { request, cancel } = CancelableRequest(async function(url, requestOptions) {
				return axios.get(url, requestOptions)
			})
			// Store cancel-function
			this.cancelFetchFullApp = cancel

			try {
				const app = await request(generateOcsUrl('apps/build/api/v1', 2) + `app/${id}`)
				this.$emit('update:app', app.data)
				this.isLoadingApp = false
			} catch (error) {
				if (axios.isCancel(error)) {
					console.debug('The request for app', id, 'has been canceled.', error)
				} else {
					console.error(error)
					this.errorApp = true
					this.isLoadingApp = false
				}
			} finally {
				if (this.app.title === '') {
					this.focusTitle()
				}
			}
		},

		/**
		 * Focus title after app load
		 */
		focusTitle() {
			this.$nextTick(() => {
				this.$refs.title.focus()
			})
		},

		/**
		 * Title & description save methods
		 */
		onTitleChange: debounce(function() {
			this.saveAppProperty('title')
		}, 200),
		onDescChange: debounce(function() {
			this.saveAppProperty('description')
		}, 200),

		/**
		 * Add a new data to the current app
		 *
		 * @param {string} type the data type, see DataTypes
		 */
		async addData(type) {
			const text = ''
			this.isLoadingData = true

			try {
				const response = await axios.post(generateOcsUrl('apps/build/api/v1', 2) + 'data', {
					appId: this.app.uuid,
					type,
					text,
				})
				const data = response.data

				// Add newly created data
				this.app.data.push(Object.assign({
					text,
					type,
					answers: [],
				}, data))

				// Focus newly added data
				this.$nextTick(() => {
					const lastData = this.$refs.data[this.$refs.data.length - 1]
					lastData.focus()
				})

			} catch (error) {
				console.error(error)
				showError(t('build', 'There was an error while adding the new data'))
			} finally {
				this.isLoadingData = false
			}
		},

		/**
		 * Delete a data
		 *
		 * @param {Object} data the data to delete
		 * @param {number} data.id the data id to delete
		 */
		async deleteData({ id }) {
			this.isLoadingData = true

			try {
				await axios.delete(generateOcsUrl('apps/build/api/v1', 2) + `data/${id}`)
				const index = this.app.data.findIndex(search => search.id === id)
				this.app.data.splice(index, 1)
			} catch (error) {
				console.error(error)
				showError(t('build', 'There was an error while removing the data'))
			} finally {
				this.isLoadingData = false
			}
		},

		/**
		 * Reorder data on dragEnd
		 */
		async onDataOrderChange() {
			this.isLoadingData = true
			const newOrder = this.app.data.map(data => data.id)

			try {
				await axios.post(generateOcsUrl('apps/build/api/v1', 2) + 'data/reorder', {
					appId: this.app.uuid,
					newOrder,
				})
			} catch (error) {
				showError(t('build', 'Error while saving app'))
				console.error(error)
			} finally {
				this.isLoadingData = false
			}
		},

		/**
		 * Add data methods
		 */
		openDataMenu() {
			// TODO: fix the vue components to allow external click triggers without
			// conflicting with the click outside directive
			setTimeout(() => {
				this.dataMenuOpened = true
				this.$nextTick(() => {
					this.$refs.dataMenu.focusFirstAction()
				})
			}, 10)
		},

		/**
		 * Select the text in the input if it is still set to 'App title'
		 * @param {Event} e the click event
		 */
		selectIfUnchanged(e) {
			if (e.target && e.target.value === t('build', 'App title')) {
				e.target.select()
			}
		},

		/**
		 * Auto adjust the description height based on lines number
		 */
		autoSizeDescription() {
			const textarea = this.$refs.description
			if (textarea) {
				textarea.style.cssText = 'height:auto; padding:0'
				textarea.style.cssText = `height: ${textarea.scrollHeight + 20}px`
			}
		},
	},
}
</script>

<style lang="scss">
.app-content {
	display: flex;
	align-items: center;
	flex-direction: column;

	header,
	section {
		width: 100%;
		max-width: 750px;
	}

	// Title & description header
	header {
		display: flex;
		flex-direction: column;
		margin-top: 44px;
		margin-bottom: 24px;

		h2 {
			margin-bottom: 0; // because the input field has enough padding
		}

		.app-title,
		.app-desc,
		.info-message {
			width: 100%;
			padding: 0 16px;
			border: none;
		}
		.app-title {
			font-size: 28px;
			font-weight: bold;
			color: var(--color-main-text);
			min-height: 36px;
			margin: 32px 0;
			padding-left: 14px; // align with description (compensate font size diff)
			padding-bottom: 6px; // align with h2 of .app-title on submit page
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
		.app-desc {
			font-size: 100%;
			line-height: 150%;
			padding-bottom: 20px;
			resize: none;
		}

		.info-message {
			font-size: 100%;
			padding-bottom: 20px;
			resize: none;
			color: var(--color-text-maxcontrast);
		}
	}

	.empty-content__button {
		margin: 5px;
		> span {
			margin-right: 5px;
			cursor: pointer;
			opacity: 1;
		}
	}

	// Data container
	section {
		position: relative;
		display: flex;
		flex-direction: column;
		margin-bottom: 250px;

		.data-toolbar {
			position: sticky;
			// Above other menus
			z-index: 55;
			bottom: 0px;
			padding-bottom: 16px;
			display: flex;
			align-items: center;
			align-self: flex-start;

			// To align with Drag-Handle
			margin-left: 16px;

			.icon-add-white {
				opacity: 1;
			}
		}
	}
}
</style>
