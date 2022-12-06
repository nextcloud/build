/**
 * @copyright Copyright (c) 2019 Marco Ambrosini <marcoambrosini@pm.me>
 *
 * @author Marco Ambrosini <marcoambrosini@pm.me>
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */
import Contact from '../models/Data/DataContact'
import Image from '../models/Data/DataImage'
import Text from '../models/Data/DataText'

export default function(app) {
	if (app?.structure?.table?.columns) {
		app.structure.table.columns = app.structure.table.columns.map(column => {
			switch (column.type) {
			case 'text':
				column.data = Text
				break

			case 'contact':
				column.data = Contact
				break

			case 'image':
				column.data = Image
				break

				// case 'user':
				// column.data = User
				// break

			default:
				column.data = Text
				break
			}

			return column
		})
	}
	return app
}
