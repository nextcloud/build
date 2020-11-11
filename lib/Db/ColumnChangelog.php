<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2020 Arthur Schiwon <blizzz@arthur-schiwon.de>
 *
 * @author Arthur Schiwon <blizzz@arthur-schiwon.de>
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

namespace OCA\Build\Db;

/**
 * @method string getValueUuid()
 * @method void setValueUuid(string $uuid)
 * @method string getEditorType()
 * @method void setEditorType(string $editorType)
 * @method string getEditorId()
 * @method void setEditorId(string $editorId)
 * @method string getOldValue()
 * @method void setOldValue(string $value)
 * @method string getNewValue()
 * @method void setNewValue(string $value)
 * @method int getMtime()
 * @method void setMtime(int $mtime)
 */
class ColumnChangelog extends ABuildEntity {
	protected $valueUuid;
	protected $editorType;
	protected $editorId;
	protected $oldValue;
	protected $newValue;
	protected $mtime;

	public function __construct() {
		parent::__construct();
		$this->addType('oldValue', 'text');
		$this->addType('newValue', 'text');
		$this->addType('mtime', 'integer');
	}
}