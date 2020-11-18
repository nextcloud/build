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
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Build\Test\Db;

use OCA\Build\Db\Column;
use OCA\Build\Db\ColumnMapper;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Test\TestCase;

/**
 * @group DB
 */
class ColumnMapperTest extends TestCase {
	protected const APP_UUID = 'f5e968bf-3dff-47ea-9730-9aab5a983aa2';
	protected const TABLE_UUID = '2ddab72d-1097-4a18-9858-9ebba842c136';

	/** @var IDBConnection */
	protected $dbc;
	/** @var ColumnMapper */
	protected $mapper;
	/** @var \PHPUnit\Framework\MockObject\MockObject|LoggerInterface */
	protected $logger;


	public function setUp(): void {
		$this->dbc = \OC::$server->get(IDBConnection::class);
		$this->logger = $this->createMock(LoggerInterface::class);

		$this->mapper = new ColumnMapper($this->dbc, $this->logger);
	}

	public function tearDown(): void {
		$this->mapper->deleteByAppUuid(self::APP_UUID);
		parent::tearDown();
	}

	public function testInsert() {
		$inserted = $this->mapper->insert($this->makeColumn());
		$this->assertInstanceOf(Column::class, $inserted);
		$this->assertNotEmpty($inserted->getId());
		$this->assertTrue(Uuid::isValid($inserted->getId()));
	}

	public function testInsertOrUpdate() {
		/** @var Column $inserted */
		$inserted = $this->mapper->insertOrUpdate($this->makeColumn());
		$this->assertInstanceOf(Column::class, $inserted);
		$this->assertNotEmpty($inserted->getId());
		$this->assertTrue(Uuid::isValid($inserted->getId()));
		$uuid = $inserted->getId();

		$inserted->setName('Ship\'s name');
		/** @var Column $updated */
		$updated = $this->mapper->insertOrUpdate($inserted);
		$this->assertSame($uuid, $updated->getId());
		$this->assertSame('Ship\'s name', $updated->getName());
	}

	public function testUpdate() {
		/** @var Column $inserted */
		$inserted = $this->mapper->insert($this->makeColumn());
		$this->assertInstanceOf(Column::class, $inserted);
		$this->assertNotEmpty($inserted->getId());
		$this->assertTrue(Uuid::isValid($inserted->getId()));
		$uuid = $inserted->getId();

		$inserted->setName('Ship\'s name');
		/** @var Column $updated */
		$updated = $this->mapper->update($inserted);
		$this->assertSame($uuid, $updated->getId());
		$this->assertSame('Ship\'s name', $updated->getName());
	}

	public function testFindTables() {
		$column1 = $this->makeColumn();
		$column2 = $this->makeColumn();
		$column2->setName('Captain');
		$column2->setDatatype('contacts');
		$column3 = $this->makeColumn();
		$column3->setName('Launching');
		$column3->setDatatype('integer');

		$this->mapper->insert($column1);
		$this->mapper->insert($column2);
		$this->mapper->insert($column3);

		$foundColumns = $this->mapper->findColumnsOfAppByUuid(self::APP_UUID);
		$this->assertSame(3, \count($foundColumns));
		foreach ($foundColumns as $foundColumn) {
			$this->assertInstanceOf(Column::class, $foundColumn);
			$this->assertTrue(Uuid::isValid($foundColumn->getId()));
			$this->assertSame(self::APP_UUID, $foundColumn->getAppId());
		}
	}

	protected function makeColumn(): Column {
		$column = new Column();
		$column->setName('Ship name');
		$column->setAppId(self::APP_UUID);
		$column->setDatatype('string');
		$column->setMandatory(true);
		$column->setTableId(self::TABLE_UUID);
		return $column;
	}
}
