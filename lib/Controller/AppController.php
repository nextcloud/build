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
		return new DataResponse(['buildAppId' => $buildApp->getId()]);
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
		$response->setData(['buildAppId' => 42]);
		return $response;
	}

	public function get(string $uuid): Response {
		try {
			$buildApp = $this->appService->get($uuid);
		} catch (DoesNotExistException $e) {
			return new NotFoundResponse();
		}

		//FIXME: this is just the meta data. We need also the structure and the views.

		return new JSONResponse([
			'metadata' => $buildApp->asArray(),
		]);
	}

	public function export(int $buildAppId): Response {
		// FIXME: pseudo code
		if (!in_array($buildAppId, [23, 42])) {
			return new NotFoundResponse();
		}

		// fetch real app data
		$appData = [
			'buildAppId' => $buildAppId,
			'appName' => 'Dummy App ' . $buildAppId,
		];

		return new JSONResponse(['manifest' => $this->manifestService->appDataToXML($appData)]);
	}

	public function update(int $buildAppId, string $key, string $value) {
		// FIXME: pseudo code
		// check valid id
		// get app representation
		// validate and apply changes
		// save
		return new Response();
	}

	public function delete(int $buildAppId) {
		// FIXME: pseudo code
		if (!in_array($buildAppId, [23, 42])) {
			return new NotFoundResponse();
		}
		// delete app definition from DB
		return new Response();
	}
}
