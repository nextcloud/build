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
 * @method string getName()
 * @method void setName(string $name)
 * @method string getDescription()
 * @method void setDescription(string $description)
 * @method string getVersion()
 * @method void setVersion(string $version)
 * @method int getCreated()
 * @method void setCreated(int $created)
 * @method int getLastModified()
 * @method void setLastModified(int $lastModified)
 */
class App extends ABuildEntity {
	protected $name;
	protected $description;
	protected $version;
	protected $created;
	protected $lastModified;

	public function __construct() {
		parent::__construct();
		$this->addType('created', 'integer');
		$this->addType('lastModified', 'integer');
	}
}
