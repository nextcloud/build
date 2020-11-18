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

use OCA\Build\AppInfo\Application;
use OCA\Build\Service\AppService;
use OCA\Build\Service\Manifest;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use Test\TestCase;

class AppControllerTest extends TestCase {

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

	public function testGet() {
		$uuid = '23436d7e-1dad-4a7c-9b6a-0140d43af4ec';
		$appInfo = [
			'uuid' => $uuid,
			'name' => 'Rocket launcher',
			'version' => '13.3.7',
			'created' => time() - 31536000, // a year agp
			'lastModified' => time() - 604800, // 7 days ago
			'description' => 'Log of rocket launches',
			'icon' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCI+PHBhdGggZD0iTTIuMiwxNi4wNkwzLjg4LDEyTDIuMiw3Ljk0TDYuMjYsNi4yNkw3Ljk0LDIuMkwxMiwzLjg4TDE2LjA2LDIuMkwxNy43NCw2LjI2TDIxLjgsNy45NEwyMC4xMiwxMkwyMS44LDE2LjA2TDE3Ljc0LDE3Ljc0TDE2LjA2LDIxLjhMMTIsMjAuMTJMNy45NCwyMS44TDYuMjYsMTcuNzRMMi4yLDE2LjA2WiIgZmlsbD0iI2ZmZiIgLz48L3N2Zz4='
		];
		$structure = [
			'table' => [
				'a6da4e92-b67d-40cc-a778-1b8342ab1a03' => [
					'uuid' => 'a6da4e92-b67d-40cc-a778-1b8342ab1a03',
					'name' => 'Main',
					'columns' => [
						'190abcc5-3023-457f-96a3-683404326956' => [
							'uuid' => '190abcc5-3023-457f-96a3-683404326956',
							'name' => 'Name',
							'type' => 'text',
							'required' => 'true'
						],
						'c4e8610f-9c55-47c0-8506-18c548dae25e' => [
							'uuid' => 'c4e8610f-9c55-47c0-8506-18c548dae25e',
							'name' => 'Launch date',
							'type' => 'date',
							'required' => 'true'
						],
						'20556c1f-9937-453f-895c-2baffe5d213f' => [
							'uuid' => '20556c1f-9937-453f-895c-2baffe5d213f',
							'name' => 'Cosmonauts',
							'type' => 'contact',
							'required' => false,
							'maxValues' => 5
						],
						'1aa648a7-7129-44ba-b96d-bb2c5cac10b2' => [
							'uuid' => '1aa648a7-7129-44ba-b96d-bb2c5cac10b2',
							'name' => 'Emblem',
							'type' => 'image',
							'required' => true,
						]
					]
				]
			]
		];
		$views = [
			'navigation' => [
				'type' => 'list',
				'title' => '190abcc5-3023-457f-96a3-683404326956',
				'image' => '1aa648a7-7129-44ba-b96d-bb2c5cac10b2',
			],
			'details' => [
				'type' => 'avatar-header',
				'title' => '190abcc5-3023-457f-96a3-683404326956',
				'image' => '1aa648a7-7129-44ba-b96d-bb2c5cac10b2',
				'subtitle' => 'c4e8610f-9c55-47c0-8506-18c548dae25e',
			],
		];
		$expected = \json_encode([
			'metadata' => [
				'version' => Application::SCHEMA_VERSION,
			],
			'appinfo' => $appInfo,
			'structure' => $structure,
			'views' => $views,
		], JSON_HEX_TAG);

		$this->appService->expects($this->once())
			->method('getAppInfo')
			->with($uuid)
			->willReturn($appInfo);
		$this->appService->expects($this->any())
			->method('getStructure')
			->willReturn($structure);
		$this->appService->expects($this->any())
			->method('getViews')
			->willReturn($views);

		$response = $this->controller->get($uuid);
		$this->assertInstanceOf(JSONResponse::class, $response);
		$this->assertSame($expected, $response->render());
	}
}
