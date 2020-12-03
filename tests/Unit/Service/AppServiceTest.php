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
use OCA\Build\Db\Column;
use OCA\Build\Db\ColumnMapper;
use OCA\Build\Db\OptionMapper;
use OCA\Build\Db\Table;
use OCA\Build\Db\TableMappper;
use OCA\Build\Db\ViewConfiguration;
use OCA\Build\Db\ViewConfigurationMapper;
use OCA\Build\Service\AppService;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\Files\IAppData;
use OCP\Files\NotFoundException;
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
	/** @var IAppData|\PHPUnit\Framework\MockObject\MockObject */
	protected $appData;

	public function setUp(): void {
		$this->dbc = $this->createMock(IDBConnection::class);
		$this->appMapper = $this->createMock(AppMapper::class);
		$this->tableMapper = $this->createMock(TableMappper::class);
		$this->columnMapper = $this->createMock(ColumnMapper::class);
		$this->viewMapper = $this->createMock(ViewConfigurationMapper::class);
		$this->optionMapper = $this->createMock(OptionMapper::class);
		$this->appData = $this->createMock(IAppData::class);

		$this->service = new AppService(
			$this->dbc,
			$this->appMapper,
			$this->tableMapper,
			$this->columnMapper,
			$this->viewMapper,
			$this->optionMapper,
			$this->appData
		);
	}

	public function testGetApp() {
		$uuid = '8d2d7771-4bc9-4c17-a446-72717fb931d7';
		$appDbInfo = [
			'uuid' => $uuid,
			'name' => 'Rocket launcher',
			'version' => '13.3.7',
			'created' => time() - 31536000, // a year agp
			'lastModified' => time() - 604800, // 7 days ago
			'description' => 'Log of rocket launches',
		];
		$appCompleteInfo = \array_merge($appDbInfo,
			[
				'icon' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHBhdGggZD0iTTIuMiwxNi4wNkwzLjg4LDEyTDIuMiw3Ljk0TDYuMjYsNi4yNkw3Ljk0LDIuMkwxMiwzLjg4TDE2LjA2LDIuMkwxNy43NCw2LjI2TDIxLjgsNy45NEwyMC4xMiwxMkwyMS44LDE2LjA2TDE3Ljc0LDE3Ljc0TDE2LjA2LDIxLjhMMTIsMjAuMTJMNy45NCwyMS44TDYuMjYsMTcuNzRMMi4yLDE2LjA2WiIgZmlsbD0iI2ZmZiIgLz48L3N2Zz4='
			]
		);

		$appMock = $this->createMock(App::class);
		$appMock->expects($this->atLeastOnce())
			->method('asArray')
			->willReturn($appDbInfo);

		$this->appMapper->expects($this->once())
			->method('findByUuid')
			->with($uuid)
			->willReturn($appMock);

		$this->appData->expects($this->any())
			->method('getFolder')
			->with('designer')
			->willThrowException(new NotFoundException());

		$appInfo = $this->service->getAppInfo($uuid);

		$this->assertSame($appCompleteInfo, $appInfo);
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

	public function testGetStructure() {
		$uuid = '8d2d7771-4bc9-4c17-a446-72717fb931d7';
		$tableUuid = 'd0d957ba-8ffc-46da-aca7-470828c9425e';
		$colUuids = [
			'3868e332-0053-4c4c-a988-352470df089f',
			'6be3b017-092c-4fb0-9b0d-fddb2f65916d',
			'fca8c0dc-b68b-4e30-95c7-68823462491d',
		];

		$table = new Table();
		$table->setName('Main');
		$table->setId($tableUuid);
		$table->setAppId($uuid);

		$column1 = new Column();
		$column1->setName('Ship name');
		$column1->setAppId($uuid);
		$column1->setDatatype('string');
		$column1->setMandatory(true);
		$column1->setTableId($tableUuid);
		$column1->setId($colUuids[0]);
		$column2 = clone $column1;
		$column2->setName('Captain');
		$column2->setDatatype('contacts');
		$column2->setId($colUuids[1]);
		$column3 = clone $column1;
		$column3->setName('Launching');
		$column3->setDatatype('integer');
		$column3->setId($colUuids[2]);

		$this->tableMapper->expects($this->once())
			->method('findTablesOfAppByUuid')
			->with($uuid)
			->willReturn([$table]);

		$this->columnMapper->expects($this->once())
			->method('findColumnsOfAppByUuid')
			->with($uuid)
			->willReturn([$column1, $column2, $column3]);

		$structure = $this->service->getStructure($uuid);
		$this->assertArrayHasKey($tableUuid, $structure);
		$this->assertIsArray($structure[$tableUuid]['columns']);
		foreach ($colUuids as $colUuid) {
			$this->assertArrayHasKey($colUuid, $structure[$tableUuid]['columns']);
			$this->assertIsArray($structure[$tableUuid]['columns'][$colUuid]);
			$this->assertArrayHasKey('name', $structure[$tableUuid]['columns'][$colUuid]);
			$this->assertNotEmpty($colUuid, $structure[$tableUuid]['columns'][$colUuid]['name']);
			$this->assertArrayHasKey('datatype', $structure[$tableUuid]['columns'][$colUuid]);
			$this->assertNotEmpty($colUuid, $structure[$tableUuid]['columns'][$colUuid]['datatype']);
		}
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

	public function testGetViews() {
		$uuid = '8d2d7771-4bc9-4c17-a446-72717fb931d7';

		$navigationViewArray = $detailViewArray = [
			'appId' => $uuid,
			'viewId' => '',
			'configuration' => [
				'type' => 'list',
				'headline' =>
					[
						'uuid' => '15ac48bd-745d-404b-aa52-b94914f4b3f8',
						'type' => 'displayname',
					],
				'subheading' =>
					[
						'uuid' => 'a581e566-1e0b-4c90-927d-3ad51780b4ef',
						'type' => 'qr',
					],
				'icon' =>
					[
						'uuid' => '9ed11803-97ff-493b-a10c-b47808506514',
					],
			],
			'uuid' => '',
		];
		$navigationViewArray['viewId'] = ViewConfiguration::ID_NAVIGATION;
		$navigationViewArray['uuid'] = '40ef85e6-3745-4416-ae48-e4272a12934a';
		$detailViewArray['viewId'] = ViewConfiguration::ID_DETAIL;
		$detailViewArray['uuid'] = 'e9c6f0b8-c3b8-4686-ac7c-803b14b83804';

		$navigationView = $this->createMock(ViewConfiguration::class);
		$navigationView->expects($this->any())
			->method('getViewId')
			->willReturn(ViewConfiguration::ID_NAVIGATION);
		$navigationView->expects($this->any())
			->method('asArray')
			->willReturn($navigationViewArray);

		$detailView = $this->createMock(ViewConfiguration::class);
		$detailView->expects($this->any())
			->method('getViewId')
			->willReturn(ViewConfiguration::ID_DETAIL);
		$detailView->expects($this->any())
			->method('asArray')
			->willReturn($detailViewArray);

		$this->viewMapper->expects($this->once())
			->method('findViewsOfAppByUuid')
			->with($uuid)
			->willReturn([$navigationView, $detailView]);

		$views = $this->service->getViews($uuid);
		$this->assertSame(2, count('views'));
		$this->assertArrayHasKey(ViewConfiguration::ID_NAVIGATION, $views);
		$this->assertArrayHasKey(ViewConfiguration::ID_DETAIL, $views);
		$this->assertIsArray($views[ViewConfiguration::ID_NAVIGATION]);
		$this->assertIsArray($views[ViewConfiguration::ID_DETAIL]);
	}
}
