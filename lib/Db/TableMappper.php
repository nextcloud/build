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

use OCP\AppFramework\Db\Entity;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class TableMappper extends ABuildMapper {
	/** @var LoggerInterface */
	private $logger;

	public function __construct(IDBConnection $db, LoggerInterface $logger) {
		parent::__construct($db, 'build_tables', Table::class);
		$this->logger = $logger;
	}

	/**
	 * @return Table[]
	 */
	public function findTablesOfAppByUuid(string $uuid): array {
		return $this->findEntities($this->getFindEntitiesByAppUuidQuery($uuid));
	}

	public function deleteByAppUuid(string $appUuid): bool {
		$cleanDelete = true;
		$tables = $this->findTablesOfAppByUuid($appUuid);
		foreach ($tables as $table) {
			try {
				$this->delete($table);
			} catch (\Exception $e) {
				$cleanDelete = false;
				$this->logger->warning(
					'Could not delete row from {table} with id {id}',
					[
						'app' => 'build',
						'table' => $this->getTableName(),
						'id' => $table->getId()
					]
				);
			}
		}
		return $cleanDelete;
	}

	/**
	 * @throws \Exception
	 */
	public function insert(Entity $entity): Entity {
		$this->preInsertCheck($entity);
		return parent::insert($entity);
	}

	/**
	 * @throws \Exception
	 */
	public function insertOrUpdate(Entity $entity): Entity {
		$this->preInsertCheck($entity);
		return parent::insertOrUpdate($entity);
	}

	/**
	 * @throws \Exception
	 */
	protected function preInsertCheck(Entity $table): void {
		if (!$table instanceof Table) {
			throw new \InvalidArgumentException('Table expected, but got ' . get_class($table));
		}
		if (empty($table->getId())) {
			/** @noinspection PhpStrictTypeCheckingInspection */
			// overridden virtual method
			$table->setId(Uuid::uuid4()->toString());
		}
	}
}
