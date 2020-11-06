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

namespace OCA\Build\Service;

use OCA\Build\Db\App;
use OCA\Build\Db\AppMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use Ramsey\Uuid\Uuid;

class AppService {
	/** @var AppMapper */
	private $mapper;

	public function __construct(AppMapper $mapper) {
		$this->mapper = $mapper;
	}

	public function newApp(): App {
		$app = new App();

		$app->setId(Uuid::uuid4()->toString());

		return $app;
	}

	/**
	 * @throws DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	public function get(string $uuid): App {
		return $this->mapper->findByUuid($uuid);
	}

	public function save(App $app): App {
		$entity = $this->mapper->insertOrUpdate($app);
		if ($entity instanceof App) {
			return $entity;
		}
		throw new \LogicException('Expected OCA\Build\Db\App, but got ' . get_class($entity));
	}
}