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

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\IDBConnection;

class AppMapper extends ABuildMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'build_apps', App::class);
	}

	/**
	 * @throws DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function findByUuid(string $uuid): App {
		$app = parent::_findByUuid($uuid);
		if ($app instanceof App) {
			return $app;
		}
		throw new DoesNotExistException('App does not exist on DB');
	}

	public function insert(Entity $entity): Entity {
		$this->preModifyCheck($entity);
		return parent::insert($entity);
	}

	public function insertOrUpdate(Entity $entity): Entity {
		$this->preModifyCheck($entity);
		return parent::insertOrUpdate($entity);
	}

	public function update(Entity $entity): Entity {
		$this->preModifyCheck($entity);
		return parent::update($entity);
	}

	protected function preModifyCheck(Entity $app): void {
		if (!$app instanceof App) {
			throw new \InvalidArgumentException('App expected, but got ' . get_class($app));
		}
		$currentTimestamp = time();
		if (empty($app->getCreated())) {
			$app->setCreated($currentTimestamp);
		}
		$app->setLastModified($currentTimestamp);
	}

}
