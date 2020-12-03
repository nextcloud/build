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

namespace OCA\Build\Test\Db;

use OCA\Build\Db\ViewConfiguration;
use OCA\Build\Db\ViewConfigurationMapper;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

/**
 * @group DB
 */
class ViewConfigurationMapperTest extends \Test\TestCase {
	protected const APP_UUID = 'f5e968bf-3dff-47ea-9730-9aab5a983aa2';

	/** @var ViewConfigurationMapper */
	protected $mapper;
	/** @var mixed|\stdClass */
	protected $dbc;
	/** @var \PHPUnit\Framework\MockObject\MockObject */
	protected $logger;

	public function setUp(): void {
		$this->dbc = \OC::$server->get(IDBConnection::class);
		$this->logger = $this->createMock(LoggerInterface::class);
		$this->mapper = new ViewConfigurationMapper($this->dbc, $this->logger);
	}

	public function tearDown(): void {
		$this->mapper->deleteByAppUuid(self::APP_UUID);
		parent::tearDown();
	}

	public function viewConfigurationProvider(): array {
		return [
			[ # 0
				[ # Headline data
					'uuid' => '15ac48bd-745d-404b-aa52-b94914f4b3f8',
					'type' => 'displayname',
				],
				[ # Subheading data
					'uuid' => 'a581e566-1e0b-4c90-927d-3ad51780b4ef',
					'type' => 'qr',
				],
				[ # Icon data
					'uuid' => '9ed11803-97ff-493b-a10c-b47808506514',
				],
				'left'
			]
		];
	}

	/**
	 * @dataProvider viewConfigurationProvider
	 */
	public function testCreate(array $headlineData, array $subheadingData, array $iconData, string $viewType) {
		$viewConfig = new ViewConfiguration();
		$viewConfig->setAppId(self::APP_UUID);
		$viewConfig->setViewId(ViewConfiguration::ID_NAVIGATION);
		$viewConfig->setType($viewType);
		$viewConfig->setHeadline($headlineData);
		$viewConfig->setSubheading($subheadingData);
		$viewConfig->setIcon($iconData);

		/** @var ViewConfiguration $inserted */
		$inserted = $this->mapper->insert($viewConfig);
		$this->assertInstanceOf(ViewConfiguration::class, $inserted);
		$this->assertNotEmpty($inserted->getId());
		$this->assertTrue(Uuid::isValid($inserted->getId()));
		$this->assertSame($headlineData, $inserted->getHeadline());
		$this->assertSame($subheadingData, $inserted->getSubheading());
		$this->assertSame($iconData, $inserted->getIcon());
		$this->assertSame($viewType, $inserted->getType());
	}

	/**
	 * @dataProvider viewConfigurationProvider
	 */
	public function testInsertOrUpdate(array $headlineData, array $subheadingData, array $iconData, string $viewType) {
		$viewConfig = new ViewConfiguration();
		$viewConfig->setAppId(self::APP_UUID);
		$viewConfig->setViewId(ViewConfiguration::ID_NAVIGATION);
		$viewConfig->setType($viewType);
		$viewConfig->setHeadline($headlineData);
		$viewConfig->setSubheading($subheadingData);
		$viewConfig->setIcon($iconData);

		/** @var ViewConfiguration $inserted */
		$inserted = $this->mapper->insertOrUpdate($viewConfig);
		$this->assertInstanceOf(ViewConfiguration::class, $inserted);
		$this->assertNotEmpty($inserted->getId());
		$this->assertTrue(Uuid::isValid($inserted->getId()));
		$this->assertSame($headlineData, $inserted->getHeadline());
		$this->assertSame($subheadingData, $inserted->getSubheading());
		$this->assertSame($iconData, $inserted->getIcon());
		$this->assertSame($viewType, $inserted->getType());

		$updatedSubheadingData = $subheadingData;
		$updatedSubheadingData['type'] = 'address';
		$inserted->setSubheading($updatedSubheadingData);

		/** @var ViewConfiguration $updated */
		$updated = $this->mapper->insertOrUpdate($viewConfig);
		$this->assertInstanceOf(ViewConfiguration::class, $updated);
		$this->assertSame($inserted->getId(), $updated->getId());
		$this->assertSame($updatedSubheadingData, $updated->getSubheading());
	}

	/**
	 * @dataProvider viewConfigurationProvider
	 */
	public function testFindConfiguration(array $headlineData, array $subheadingData, array $iconData, string $viewType) {
		$viewConfig = new ViewConfiguration();
		$viewConfig->setAppId(self::APP_UUID);
		$viewConfig->setHeadline($headlineData);
		$viewConfig->setSubheading($subheadingData);
		$viewConfig->setIcon($iconData);
		$viewConfig->setType($viewType);
		$viewConfig->setViewId(ViewConfiguration::ID_NAVIGATION);

		$viewConfig2 = clone $viewConfig;
		$viewConfig2->setViewId(ViewConfiguration::ID_DETAIL);

		$this->mapper->insert($viewConfig);
		$this->mapper->insert($viewConfig2);

		$navigationFound = $detailFound = false;
		$viewConfigurations = $this->mapper->findViewsOfAppByUuid(self::APP_UUID);
		$this->assertSame(2, \count($viewConfigurations));
		foreach ($viewConfigurations as $viewConfiguration) {
			$this->assertInstanceOf(ViewConfiguration::class, $viewConfiguration);
			$navigationFound = $navigationFound || ($viewConfiguration->getViewId() === ViewConfiguration::ID_NAVIGATION);
			$detailFound = $detailFound || ($viewConfiguration->getViewId() === ViewConfiguration::ID_DETAIL);
		}
		$this->assertTrue($navigationFound);
		$this->assertTrue($detailFound);
	}

	/**
	 * @dataProvider viewConfigurationProvider
	 */
	public function testDeleteConfigurationsByAppId(array $headlineData, array $subheadingData, array $iconData, string $viewType) {
		$viewConfig = new ViewConfiguration();
		$viewConfig->setAppId(self::APP_UUID);
		$viewConfig->setHeadline($headlineData);
		$viewConfig->setSubheading($subheadingData);
		$viewConfig->setIcon($iconData);
		$viewConfig->setType($viewType);
		$viewConfig->setViewId(ViewConfiguration::ID_NAVIGATION);

		$viewConfig2 = clone $viewConfig;
		$viewConfig2->setViewId(ViewConfiguration::ID_DETAIL);

		$this->mapper->insert($viewConfig);
		$this->mapper->insert($viewConfig2);

		$this->mapper->deleteByAppUuid(self::APP_UUID);

		$viewConfigurations = $this->mapper->findViewsOfAppByUuid(self::APP_UUID);
		$this->assertSame(0, \count($viewConfigurations));
	}
}
