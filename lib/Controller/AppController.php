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

namespace OCA\Build\Controller;

use OCA\Build\AppInfo\Application;
use OCA\Build\Service\AppService;
use OCA\Build\Service\Manifest;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\NotFoundResponse;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\OCSController;
use OCP\IRequest;
use function array_walk;

class AppController extends OCSController {

	/** @var Manifest */
	private $manifestService;
	/** @var AppService */
	private $appService;

	public function __construct(IRequest $request, Manifest $manifestService, AppService $appService) {
		parent::__construct(Application::APP_ID, $request);
		$this->manifestService = $manifestService;
		$this->appService = $appService;
	}

	public function create(array $appData = null): Response {
		$buildApp = $this->appService->newApp();

		try {
			array_walk($appData, function ($value, $key) use ($buildApp) {
				if ($key === 'id') {
					return;
				}
				$setter = 'set' . ucfirst($key);
				$buildApp->$setter($value);
			});
		} catch (\InvalidArgumentException $e) {
			$response = new DataResponse(['error' => $e->getMessage()]);
			$response->setStatus(Http::STATUS_BAD_REQUEST);
			return $response;
		}

		$buildApp = $this->appService->save($buildApp);

		// perhaps also return app data
		return new DataResponse(['uuid' => $buildApp->getId()]);
	}

	public function import(string $manifest): Response {
		// FIXME: pseudo code
		$response = new DataResponse();

		if ($this->manifestService->isValid($manifest)) {
			$response->setStatus(Http::STATUS_BAD_REQUEST);
			$response->setData(['error' => 'Cannot parse manifest']);
			return $response;
		}

		// perhaps also return app data
		$response->setData(['uuid' => 42]);
		return $response;
	}

	public function get(string $uuid): Response {
		try {
			$appInfo = $this->appService->getAppInfo($uuid);
			$appData = [
				'metadata' => [
					'version' => Application::SCHEMA_VERSION,
				],
				'appinfo' => $appInfo,
				'structure' => [],
				'views' => [],
			];
		} catch (DoesNotExistException $e) {
			return new NotFoundResponse();
		} catch (MultipleObjectsReturnedException $e) {
			$response = new Response();
			$response->setStatus(Http::STATUS_CONFLICT);
		}

		try {
			$appData['structure'] = $this->appService->getStructure($uuid);
		} catch (DoesNotExistException $e) {
			// OK when not configured yet
		}

		try {
			$appData['views'] = $this->appService->getViews($uuid);
		} catch (DoesNotExistException $e) {
			// OK when not configured yet
		}

		$response = new JSONResponse($appData);
		$d = \DateTime::createFromFormat('U', (string)$appData['appinfo']['lastModified']);
		if ($d instanceof \DateTime) {
			$response->setLastModified($d);
		}
		return $response;
	}

	public function export(int $uuid): Response {
		// FIXME: pseudo code
		if (!in_array($uuid, [23, 42])) {
			return new NotFoundResponse();
		}

		// fetch real app data
		$appData = [
			'uuid' => $uuid,
			'appName' => 'Dummy App ' . $uuid,
		];

		return new JSONResponse(['manifest' => $this->manifestService->appDataToXML($appData)]);
	}

	public function update(int $uuid, string $key, string $value) {
		// FIXME: pseudo code
		// check valid id
		// get app representation
		// validate and apply changes
		// save
		return new Response();
	}

	public function delete(int $uuid) {
		// FIXME: pseudo code
		if (!in_array($uuid, [23, 42])) {
			return new NotFoundResponse();
		}
		// delete app definition from DB
		return new Response();
	}
}
