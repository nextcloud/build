
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

import { generateOcsUrl } from '@nextcloud/router'
import { showError } from '@nextcloud/dialogs'
import axios from '@nextcloud/axios'

export default {
	props: {
		app: {
			type: Object,
			required: true,
		},
	},

	computed: {
		/**
		 * Return app title or placeholder if not set
		 * @returns {string}
		 */
		appTitle() {
			if (this.app.title) {
				return this.app.title
			}
			return t('build', 'New app')
		},
	},

	methods: {
		async saveAppProperty(key) {
			try {
				// TODO: add loading status feedback ?
				await axios.post(generateOcsUrl('apps/build/api/v1', 2) + 'app/update', {
					buildAppId: this.app.uuid,
					key,
					value: this.app[key],
				})
			} catch (error) {
				showError(t('build', 'Error while saving app'))
				console.error(error)
			}
		},
	},

}
