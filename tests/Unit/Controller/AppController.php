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

namespace OCA\Build\Test\Controller;

use OCA\Build\Service\AppService;
use OCA\Build\Service\Manifest;
use OCP\IRequest;
use Test\TestCase;

class AppController extends TestCase {

	/** @var IRequest|\PHPUnit\Framework\MockObject\MockObject */
	protected $request;
	/** @var Manifest|\PHPUnit\Framework\MockObject\MockObject */
	protected $manifest;
	/** @var AppService|\PHPUnit\Framework\MockObject\MockObject */
	protected $appService;
	/** @var \OCA\Build\Controller\AppController */
	protected $controller;

	public function setUp(): void {
		$this->request = $this->createMock(IRequest::class);
		$this->manifest = $this->createMock(Manifest::class);
		$this->appService = $this->createMock(AppService::class);

		$this->controller = new \OCA\Build\Controller\AppController(
			$this->request,
			$this->manifest,
			$this->appService,
		);
	}

	public function testCreate() {
		$app = $this->controller->create([]);
	}
}
