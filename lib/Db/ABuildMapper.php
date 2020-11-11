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
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

abstract class ABuildMapper extends QBMapper {

	/** @var LoggerInterface */
	private $logger;

	public function __construct(
		IDBConnection $db,
		LoggerInterface $logger,
		string $tableName,
		string $entityClass = null
	) {
		$this->logger = $logger;
		parent::__construct($db, $tableName, $entityClass);
	}

	public function insert(Entity $entity): Entity {
		$this->preInsertCheck($entity);
		return parent::insert($entity);
	}

	public function insertOrUpdate(Entity $entity): Entity {
		$this->preInsertCheck($entity);
		return parent::insertOrUpdate($entity);
	}

	/**
	 * deletes entity rows by comporing app_id against the provided UUID
	 */
	protected function _deleteByAppUuid(string $appUuid, array $entities): bool {
		$cleanDelete = true;
		foreach ($entities as $entity) {
			try {
				$this->delete($entity);
			} catch (\Exception $e) {
				$cleanDelete = false;
				$this->logger->warning(
					'Could not delete row from {table} with id {id}',
					[
						'app' => 'build',
						'table' => $this->getTableName(),
						'id' => $entity->getId()
					]
				);
			}
		}
		return $cleanDelete;
	}

	/**
	 * @throws DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 */
	protected function _findByUuid(string $uuid): Entity {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($uuid))
			);

		$entity = $this->mapRowToEntity($this->findOneQuery($qb));
		return $entity;
	}

	protected function getFindEntitiesByAppUuidQuery(string $uuid): IQueryBuilder {
		return $this->getFindEntitiesBySomeUuid('app_id', $uuid);
	}

	protected function getFindEntitiesBySomeUuid(string $column, string $uuid): IQueryBuilder {
		// FIXME: ensure $column exists as property
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq($column, $qb->createNamedParameter($uuid))
			);

		return $qb;
	}

	/**
	 * @throws \Exception
	 */
	protected function preInsertCheck(Entity $entity): void {
		if (empty($entity->getId())) {
			/** @noinspection PhpStrictTypeCheckingInspection */
			// overridden virtual method
			$entity->setId(Uuid::uuid4()->toString());
		}
	}
}
