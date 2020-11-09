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
 * @method string getAppUuid()
 * @method void setAppUuid(string $uuid)
 * @method string getTableUuid()
 * @method void setTableUuid(string $uuid)
 * @method string getName()
 * @method void setName(string $name)
 * @method string getDatatype()
 * @method void setDatatype(string $datatype)
 * @method bool getMandatory()
 * @method void setMandatory(bool $mandatory)
 * @method string getDefault()
 * @method void setDefault(string $default)
 */
class Column extends ABuildEntity {
	protected $id;
	protected $appUuid;
	protected $tableUuid;
	protected $name;
	protected $datatype;
	protected $mandatory;
	protected $default;

	public function __construct() {
		parent::__construct();
		$this->addType('mandatory', 'bool');
	}
}
