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

namespace OCA\Build\Controller\App;

use OCA\Build\AppInfo\Application;
use OCA\Build\Service\Manifest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\NotFoundResponse;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\OCSController;
use OCP\IRequest;

class AppController extends OCSController {

	/** @var Manifest */
	private $manifestService;

	public function __construct(IRequest $request, Manifest $manifestService) {
		parent::__construct(Application::APP_ID, $request);
		$this->manifestService = $manifestService;
	}

	public function create(array $appData = null): Response {
		// FIXME: pseudo code
		// perhaps also return app data
		return new DataResponse(['buildAppId' => 23]);
	}

	public function import(string $manifest): Response {
		// FIXME: pseudo code
		$response = new DataResponse();

		if ($this->manifestService->isValid($manifest)) {
			$response->setStatus(400);
			$response->setData(['error' => 'Cannot parse manifest']);
			return $response;
		}

		// perhaps also return app data
		$response->setData(['buildAppId' => 42]);
		return $response;
	}

	public function get(int $buildAppId): Response {
		// FIXME: pseudo code
		if (!in_array($buildAppId, [23, 42])) {
			return new NotFoundResponse();
		}

		// fetch real app data
		$appData = [
			'buildAppId' => $buildAppId,
			'appName' => 'Dummy App ' . $buildAppId,
		];

		return new JSONResponse($appData);
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
