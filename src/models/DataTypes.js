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
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

import QuestionTextMultiple from '../components/Data/DataTextMultiple'
import QuestionTextDropdown from '../components/Data/DataTextDropdown'
import QuestionTextShort from '../components/Data/DataTextShort'
import QuestionTextLong from '../components/Data/DataTextLong'

/**
 * @typedef {Object} AnswerTypes
 * @property {string} multiple
 * @property {string} multiple_unique
 * @property {string} dropdown
 * @property {string} short
 * @property {string} long
 */
export default {
	/**
	 * Specifying Question-Models in a common place
	 * Further type-specific parameters are possible.
	 * @prop component The vue-component this text-type relies on
	 * @prop icon The icon corresponding to this text-type
	 * @prop label The text-type label, that users will see as text-type.
	 * @prop validate *optional* Define conditions where this data is not ok
	 *
	 * @prop titlePlaceholder The placeholder users see as empty data-title in edit-mode
	 * @prop createPlaceholder *optional* The placeholder that is visible in edit-mode, to indicate a submission form-input field
	 * @prop submitPlaceholder *optional* The placeholder that is visible in submit-mode, to indicate a form input-field
	 * @prop warningInvalid The warning users see in edit mode, if the data is invalid.
	 */

	multiple: {
		component: QuestionTextMultiple,
		icon: 'icon-text-checkbox',
		label: t('build', 'Checkboxes'),
		validate: data => data.options.length > 0,

		titlePlaceholder: t('build', 'Checkbox data title'),
		warningInvalid: t('build', 'This data needs a title and at least one text!'),
	},

	multiple_unique: {
		component: QuestionTextMultiple,
		icon: 'icon-text-multiple',
		label: t('build', 'Multiple choice'),
		validate: data => data.options.length > 0,

		titlePlaceholder: t('build', 'Multiple choice data title'),
		warningInvalid: t('build', 'This data needs a title and at least one text!'),

		// Using the same vue-component as multiple, this specifies that the component renders as multiple_unique.
		unique: true,
	},

	dropdown: {
		component: QuestionTextDropdown,
		icon: 'icon-text-dropdown',
		label: t('build', 'Dropdown'),
		validate: data => data.options.length > 0,

		titlePlaceholder: t('build', 'Dropdown data title'),
		createPlaceholder: t('build', 'People can pick one option'),
		submitPlaceholder: t('build', 'Pick an option'),
		warningInvalid: t('build', 'This data needs a title and at least one text!'),
	},

	short: {
		component: QuestionTextShort,
		icon: 'icon-text-short',
		label: t('build', 'Short text'),

		titlePlaceholder: t('build', 'Short text data title'),
		createPlaceholder: t('build', 'People can enter a short text'),
		submitPlaceholder: t('build', 'Enter a short text'),
		warningInvalid: t('build', 'This data needs a title!'),
	},

	long: {
		component: QuestionTextLong,
		icon: 'icon-text-long',
		label: t('build', 'Long text'),

		titlePlaceholder: t('build', 'Long text data title'),
		createPlaceholder: t('build', 'People can enter a long text'),
		submitPlaceholder: t('build', 'Enter a long text'),
		warningInvalid: t('build', 'This data needs a title!'),
	},

}
