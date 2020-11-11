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

namespace OCA\Build\Test\Service;

use OCA\Build\Db\App;
use OCA\Build\Db\AppMapper;
use OCA\Build\Db\ColumnMapper;
use OCA\Build\Db\OptionMapper;
use OCA\Build\Db\Table;
use OCA\Build\Db\TableMappper;
use OCA\Build\Db\ViewConfigurationMapper;
use OCA\Build\Service\AppService;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\IDBConnection;
use Test\TestCase;

class AppServiceTest extends TestCase {
	/** @var IDBConnection|\PHPUnit\Framework\MockObject\MockObject */
	protected $dbc;
	/** @var AppMapper|\PHPUnit\Framework\MockObject\MockObject */
	protected $appMapper;
	/** @var TableMappper|\PHPUnit\Framework\MockObject\MockObject */
	protected $tableMapper;
	/** @var ColumnMapper|\PHPUnit\Framework\MockObject\MockObject */
	protected $columnMapper;
	/** @var ViewConfigurationMapper|\PHPUnit\Framework\MockObject\MockObject */
	protected $viewMapper;
	/** @var OptionMapper|\PHPUnit\Framework\MockObject\MockObject */
	protected $optionMapper;
	/** @var AppService */
	protected $service;

	public function setUp(): void {
		$this->dbc = $this->createMock(IDBConnection::class);
		$this->appMapper = $this->createMock(AppMapper::class);
		$this->tableMapper = $this->createMock(TableMappper::class);
		$this->columnMapper = $this->createMock(ColumnMapper::class);
		$this->viewMapper = $this->createMock(ViewConfigurationMapper::class);
		$this->optionMapper = $this->createMock(OptionMapper::class);

		$this->service = new AppService(
			$this->dbc,
			$this->appMapper,
			$this->tableMapper,
			$this->columnMapper,
			$this->viewMapper,
			$this->optionMapper
		);
	}

	public function testGetApp() {
		$uuid = '8d2d7771-4bc9-4c17-a446-72717fb931d7';

		$appMock = $this->createMock(App::class);

		$this->appMapper->expects($this->once())
			->method('findByUuid')
			->with($uuid)
			->willReturn($appMock);

		$app = $this->service->getApp($uuid);

		$this->assertSame($appMock, $app);
	}

	public function testGetStructureTableNotFound() {
		$uuid = '8d2d7771-4bc9-4c17-a446-72717fb931d7';

		$this->tableMapper->expects($this->once())
			->method('findTablesOfAppByUuid')
			->with($uuid)
			->willThrowException(new DoesNotExistException('I looked everywhere, but it is not here'));

		$this->expectException(DoesNotExistException::class);
		$this->service->getStructure($uuid);
	}

	public function testGetStructureColumnNotFound() {
		$uuid = '8d2d7771-4bc9-4c17-a446-72717fb931d7';
		$tableUuid = 'd0d957ba-8ffc-46da-aca7-470828c9425e';

		$table = new Table();
		$table->setName('Main');
		$table->setId($tableUuid);
		$table->setAppId($uuid);

		$this->tableMapper->expects($this->once())
			->method('findTablesOfAppByUuid')
			->with($uuid)
			->willReturn([$table]);

		$this->columnMapper->expects($this->once())
			->method('findColumnsOfAppByUuid')
			->with($uuid)
			->willThrowException(new DoesNotExistException('I looked everywhere, but it is not here'));

		$this->expectException(DoesNotExistException::class);
		$this->service->getStructure($uuid);
	}

	public function testGetViewsNotFound() {
		$uuid = '8d2d7771-4bc9-4c17-a446-72717fb931d7';

		$this->viewMapper->expects($this->once())
			->method('findViewsOfAppByUuid')
			->with($uuid)
			->willThrowException(new DoesNotExistException('I looked everywhere, but it is not here'));

		$this->expectException(DoesNotExistException::class);
		$this->service->getViews($uuid);
	}
}
