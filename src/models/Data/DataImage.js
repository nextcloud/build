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
import b64toBlob from 'b64-to-blob'
import DataImage from '../../components/Data/DataImage'

const b64Regex = /image\/([a-z]+);base64,(.+)/i;

/**
 * Export the base64 string uri to a usable blob
 * e.g data:image/png;base64,iVBORw0K...
 *
 * @param {string} source the image base64 data
 * @returns {string} a blob url
 */
const getUrlFromImage = (source = '') => {
	const [, type, data] = b64Regex.exec(source)
	const blob = b64toBlob(data, `image/${type}`)
	return URL.createObjectURL(blob)
}

export default {
	icon: 'icon-type-image',
	name: t('build', 'Image'),
	provides: [
		{
			name: t('build', 'Default'),
			type: Image,
			get: data => {
				const img = new Image()
				img.src = getUrlFromImage(data)
				return img
			},
		},
		{
			name: t('build', 'Url'),
			type: URL,
			get: data => new URL(getUrlFromImage(data)),
		},
	],

	component: DataImage,
}
