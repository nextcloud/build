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

use InvalidArgumentException;
use OCP\AppFramework\Db\Entity;
use Ramsey\Uuid\Uuid;

/**
 * Class ABuildEntity
 *
 * - IDs are RFC4122 version 4-type UUIDs, not auto-incremented integers
 *
 * @package OCA\Build\Db
 *
 * @method string getId()
 * @method void setId(string $id)
 */
abstract class ABuildEntity extends Entity {
	protected $id;

	public function __construct() {
		$this->addType('id', 'string');
	}

	public function setId(string $id) {
		if (!Uuid::isValid($id)) {
			throw new InvalidArgumentException('Not a valid UUID according to RFC4122');
		}
		/** @noinspection PhpStrictTypeCheckingInspection */
		// the parent only describes a virtual method asking for int, but we set id to string
		parent::setId($id);
	}

	public function asArray(): array {
		$properties = get_object_vars($this);

		// Entity class uses properties as key names for the attributes. Other,
		// private properties are prefixed with an underscore.
		$properties = array_filter($properties, function (string $name): bool {
			return preg_match('/^(?!_).*/', $name) === 1;
		}, ARRAY_FILTER_USE_KEY);

		if (isset($properties['id'])) {
			$properties['uuid'] = $properties['id'];
			unset($properties['id']);
		}

		return $properties;
	}
}
