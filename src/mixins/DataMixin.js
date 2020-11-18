/**
 * @copyright Copyright (c) 2020 John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @author John Molakvoæ <skjnldsv@protonmail.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
import { debounce } from 'debounce'
import { generateOcsUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

import Data from '../components/Data/Data'

export default {
	inheritAttrs: false,
	props: {

		/**
		 * Question-Id
		 */
		id: {
			type: Number,
			required: true,
		},

		/**
		 * The question title
		 */
		text: {
			type: String,
			required: true,
		},

		/**
		 * Mandatory-Setting
		 */
		mandatory: {
			type: Boolean,
			required: true,
		},

		/**
		 * The user answers
		 */
		values: {
			type: Array,
			default() {
				return []
			},
		},

		/**
		 * The question list of answers
		 */
		options: {
			type: Array,
			required: true,
		},

		/**
		 * Answer type model object
		 */
		answerType: {
			type: Object,
			required: true,
		},

		/**
		 * Submission or Edit-Mode
		 */
		readOnly: {
			type: Boolean,
			default: false,
		},

		/**
		 * Database-Restrictions
		 */
		maxStringLengths: {
			type: Object,
			required: true,
		},
	},

	components: {
		Data,
	},

	data() {
		return {
			// Do we display this question in edit or fill mode
			edit: false,
		}
	},

	methods: {
		/**
		 * Forward the title change to the parent and store to db
		 *
		 * @param {string} text the title
		 */
		onTitleChange: debounce(function(text) {
			this.$emit('update:text', text)
			this.saveQuestionProperty('text', text)
		}, 200),

		/**
		 * Forward the mandatory change to the parent and store to db
		 *
		 * @param {Boolean} mandatoryValue new mandatory Value
		 */
		onMandatoryChange: debounce(function(mandatoryValue) {
			this.$emit('update:mandatory', mandatoryValue)
			this.saveQuestionProperty('mandatory', mandatoryValue)
		}, 200),

		/**
		 * Forward the answer(s) change to the parent
		 *
		 * @param {Array} values the array of answers
		 */
		onValuesChange(values) {
			this.$emit('update:values', values)
		},

		/**
		 * Delete this question
		 */
		onDelete() {
			this.$emit('delete')
		},

		/**
		 * Don't automatically submit form on Enter, parent will handle that
		 * To be called with prevent: @keydown.enter.prevent="onKeydownEnter"
		 * @param {Object} event The fired event
		 */
		onKeydownEnter(event) {
			this.$emit('keydown', event)
		},

		/**
		 * Focus the first focusable element
		 */
		focus() {
			this.edit = true
			this.$el.scrollIntoView({ behavior: 'smooth' })
			this.$nextTick(() => {
				const title = this.$el.querySelector('.question__header-title')
				if (title) {
					title.select()
				}
			})
		},

		async saveQuestionProperty(key, value) {
			try {
				// TODO: add loading status feedback ?
				await axios.post(generateOcsUrl('apps/build/api/v1', 2) + 'question/update', {
					id: this.id,
					keyValuePairs: {
						[key]: value,
					},
				})
			} catch (error) {
				showError(t('build', 'Error while saving question'))
				console.error(error)
			}
		},
	},
}
