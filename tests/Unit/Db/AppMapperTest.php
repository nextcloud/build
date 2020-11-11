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

use OCA\Build\Db\App;
use OCA\Build\Db\AppMapper;
use OCP\IDBConnection;
use Psr\Log\LoggerInterface;
use Test\TestCase;

/**
 * @group DB
 */
class AppMapperTest extends TestCase {
	/** @var IDBConnection */
	protected $dbc;
	/** @var AppMapper */
	protected $mapper;
	/** @var \PHPUnit\Framework\MockObject\MockObject|LoggerInterface */
	protected $logger;

	public function setUp(): void {
		$this->dbc = \OC::$server->get(IDBConnection::class);
		$this->logger = $this->createMock(LoggerInterface::class);
		$this->mapper = new AppMapper($this->dbc, $this->logger);
	}

	public function tearDown(): void {
		$app = new App();
		$app->setId('525082f9-747d-4818-b6f9-30e8434ee89e');
		$this->mapper->delete($app);

		parent::tearDown();
	}

	public function appProvider() {
		return [
			[
				'525082f9-747d-4818-b6f9-30e8434ee89e',
				'Pirate ships of the Scotia',
				'Register of shipping dedicated to pirate ships of the Scotia sea',
				'16.9.7'
			]
		];
	}

	/**
	 * @dataProvider appProvider
	 */
	public function testCreate(string $uuid, string $name, string $description, string $version) {
		$app = new App();
		$app->setId($uuid);
		$app->setName($name);
		$app->setDescription($description);
		$app->setVersion($version);

		$result = $this->mapper->insert($app);
		$this->assertInstanceOf(App::class, $result);
		/** @var App $result */
		$this->assertNotEmpty($result->getCreated());
		$this->assertNotEmpty($result->getLastModified());
	}

	/**
	 * @dataProvider appProvider
	 */
	public function testUpdate(string $uuid, string $name, string $description, string $version) {
		$yesterday = time() - 24 * 60 * 60;

		$app = new App();
		$app->setId($uuid);
		$app->setName($name);
		$app->setDescription($description);
		$app->setVersion($version);
		$app->setLastModified($yesterday);

		$result = $this->mapper->update($app);
		$this->assertInstanceOf(App::class, $result);
		/** @var App $result */
		$this->assertNotEmpty($result->getCreated());
		$this->assertNotEmpty($result->getLastModified());
		$this->assertTrue($result->getLastModified() > $yesterday);
	}

	/**
	 * @dataProvider appProvider
	 */
	public function testUpdateOrInsert(string $uuid, string $name, string $description, string $version) {
		$app = new App();
		$app->setId($uuid);
		$app->setName($name);
		$app->setDescription($description);
		$app->setVersion($version);

		$result = $this->mapper->insertOrUpdate($app);
		$this->assertInstanceOf(App::class, $result);
		/** @var App $result */
		$this->assertNotEmpty($result->getLastModified());
		$this->assertNotEmpty($result->getCreated());
		$oldLastCreated = $result->getCreated();
		$oldLastModified = $result->getLastModified();

		$result->setName($name . ' 4.0'); // always sounds so innovative
		sleep(1);
		$result2 = $this->mapper->insertOrUpdate($result);
		/** @var App $result2 */
		$this->assertTrue($result2->getLastModified() > $oldLastModified);
		$this->assertSame($oldLastCreated, $result2->getCreated());
	}

	/**
	 * @dataProvider appProvider
	 */
	public function testFindApp(string $uuid, string $name, string $description, string $version) {
		$app = new App();
		$app->setId($uuid);
		$app->setName($name);
		$app->setDescription($description);
		$app->setVersion($version);

		$result = $this->mapper->insert($app);

		$foundApp = $this->mapper->findByUuid($uuid);
		$this->assertInstanceOf(App::class, $result);
		/** @var App $foundApp */
		$this->assertNotEmpty($foundApp->getLastModified());
		$this->assertNotEmpty($foundApp->getCreated());
		$this->assertSame($name, $foundApp->getName());
		$this->assertSame($description, $foundApp->getDescription());
		$this->assertSame($version, $foundApp->getVersion());
	}
}
