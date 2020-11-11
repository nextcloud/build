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

use OCA\Build\Db\Table;
use OCA\Build\Db\TableMappper;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Test\TestCase;

/**
 * @group DB
 */
class TableMapperTest extends TestCase {
	/** @var mixed|\stdClass */
	protected $dbc;
	/** @var TableMappper */
	protected $mapper;
	/** @var string */
	protected const APP_UUID = 'f5e968bf-3dff-47ea-9730-9aab5a983aa2';
	/** @var \PHPUnit\Framework\MockObject\MockObject|LoggerInterface */
	protected $logger;

	public function setUp(): void {
		$this->dbc = \OC::$server->get(IDBConnection::class);
		$this->logger = $this->createMock(LoggerInterface::class);
		$this->mapper = new TableMappper($this->dbc, $this->logger);
	}

	public function tearDown(): void {
		$this->mapper->deleteByAppUuid(self::APP_UUID);
		parent::tearDown();
	}

	public function testCreate() {
		$table = new Table();
		$table->setAppId(self::APP_UUID);
		$table->setName('Main');

		$inserted = $this->mapper->insert($table);
		$this->assertInstanceOf(Table::class, $inserted);
		$this->assertNotEmpty($inserted->getId());
		$this->assertTrue(Uuid::isValid($inserted->getId()));
	}

	public function testInsertOrUpdate() {
		$table = new Table();
		$table->setAppId(self::APP_UUID);
		$table->setName('Main');

		/** @var Table $inserted */
		$inserted = $this->mapper->insertOrUpdate($table);
		$this->assertInstanceOf(Table::class, $inserted);
		$this->assertNotEmpty($inserted->getId());
		$this->assertTrue(Uuid::isValid($inserted->getId()));
		$uuid = $inserted->getId();

		$inserted->setName('Secondary');
		/** @var Table $updated */
		$updated = $this->mapper->insertOrUpdate($inserted);
		$this->assertSame($uuid, $updated->getId());
		$this->assertSame('Secondary', $updated->getName());
	}

	public function testFindTables() {
		$table1 = new Table();
		$table1->setAppId(self::APP_UUID);
		$table1->setName('Main');

		$table2 = new Table();
		$table2->setAppId(self::APP_UUID);
		$table2->setName('Secondary');

		$this->mapper->insert($table1);
		$this->mapper->insert($table2);

		$foundTables = $this->mapper->findTablesOfAppByUuid(self::APP_UUID);
		$this->assertSame(2, \count($foundTables));
		foreach ($foundTables as $foundTable) {
			$this->assertInstanceOf(Table::class, $foundTable);
			$this->assertTrue(Uuid::isValid($foundTable->getId()));
			$this->assertSame(self::APP_UUID, $foundTable->getAppId());
		}
	}

}
