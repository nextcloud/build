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
use OCA\Build\Db\ColumnMapper;
use OCA\Build\Db\OptionMapper;
use OCA\Build\Db\TableMappper;
use OCA\Build\Db\ViewConfigurationMapper;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\IDBConnection;
use Ramsey\Uuid\Uuid;

class AppService {
	/** @var AppMapper */
	private $appMapper;
	/** @var IDBConnection */
	private $dbc;
	/** @var TableMappper */
	private $tableMapper;
	/** @var ColumnMapper */
	private $columnMapper;
	/** @var ViewConfigurationMapper */
	private $viewConfigurationMapper;
	/** @var OptionMapper */
	private $optionMapper;

	public function __construct(
		IDBConnection $dbc,
		AppMapper $appMapper,
		TableMappper $tableMapper,
		ColumnMapper $columnMapper,
		ViewConfigurationMapper $viewConfigurationMapper,
		OptionMapper $optionMapper
	) {
		$this->dbc = $dbc;
		$this->appMapper = $appMapper;
		$this->tableMapper = $tableMapper;
		$this->columnMapper = $columnMapper;
		$this->viewConfigurationMapper = $viewConfigurationMapper;
		$this->optionMapper = $optionMapper;
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
	public function getApp(string $uuid): App {
		return $this->appMapper->findByUuid($uuid);
	}

	public function getStructure(string $uuid): array {
		$structure = [];

		$tables = $this->tableMapper->findTablesOfAppByUuid($uuid);
		foreach ($tables as $table) {
			$structure[$table->getId()] = $table->asArray();
			$structure[$table->getId()]['columns'] = [];
		}

		$columns = $this->columnMapper->findColumnsOfAppByUuid($uuid);
		foreach ($columns as $column) {
			$structure[$column->getTableId()]['columns'][$column->getId()] = $column->asArray();
			if ($column->getDatatype() === 'multiple-choice') {
				$options = $this->optionMapper->findOptionsForColumnByUuid($column->getId());
				$structure[$column->getTableId()]['columns'][$column->getId()]['options'] = [];
				foreach ($options as $option) {
					$structure[$column->getTableId()]['columns'][$column->getId()]['options'][] = $option->asArray();
				}
			}
		}

		return $structure;
	}

	public function getViews(string $uuid): array {
		$result = [];
		$views = $this->viewConfigurationMapper->findViewsOfAppByUuid($uuid);
		foreach ($views as $view) {
			$result[$view->getId()] = $view->asArray();
		}
		return $result;
	}

	public function getAppData(string $uuid) {
		$query = $this->dbc->getQueryBuilder();
		$query->select('*')
			->from($this->appMapper->getTableName(), 'metadata')
			->leftJoin('metadata', $this->tableMapper->getTableName(), 'apptable', $query->expr()->eq('metadata.uuid', 'apptable.app_uuid'))
			->leftJoin('metadata', $this->columnMapper->getTableName(), 'columns', $query->expr()->eq('metatada.uuid', 'columns.app_uuid'))
			->leftJoin('metadata', $this->viewConfigurationMapper->getTableName(), 'appviews', $query->expr()->eq('metadata.uuid', 'appviews.app_uuid'))
			->where($query->expr()->eq('metadata.uuid', $query->createNamedParameter($uuid)));
		$stmt = $query->execute();
		$appData = $stmt->fetchAll();
		$stmt->closeCursor();
	}

	public function save(App $app): App {
		$entity = $this->appMapper->insertOrUpdate($app);
		if ($entity instanceof App) {
			return $entity;
		}
		throw new \LogicException('Expected OCA\Build\Db\App, but got ' . get_class($entity));
	}
}
