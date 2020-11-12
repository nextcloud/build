<?php
/**
 * @copyright Copyright (c) 2017 Vinzenz Rosenkranz <vinzenz.rosenkranz@gmail.com>
 *
 * @author affan98 <affan98@gmail.com>
 * @author John Molakvoæ (skjnldsv) <skjnldsv@protonmail.com>
 * @author Jonas Rittershofer <jotoeri@users.noreply.github.com>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
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

namespace OCA\Build\Controller;

use OCA\Build\AppInfo\Application;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\Util;
use OCP\IInitialStateService;

class PageController extends Controller {
	private const TEMPLATE_MAIN = 'main';

	/** @var IInitialStateService */
	private $initialStateService;

	/** @var Array
	 *
	 * Maximum String lengths, the database is set to store.
	 */
	private $maxStringLengths = [
		'formTitle' => 256,
		'formDescription' => 8192,
		'questionText' => 2048,
		'optionText' => 1024,
		'answerText' => 4096,
	];

	public function __construct(IRequest $request,
								IInitialStateService $initialStateService) {
		parent::__construct(Application::APP_ID, $request);
		$this->initialStateService = $initialStateService;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return TemplateResponse
	 */
	public function index(): TemplateResponse {
		Util::addScript(Application::APP_ID, 'build-main');
		Util::addStyle(Application::APP_ID, 'build');
		$this->initialStateService->provideInitialState($this->appName, 'maxStringLengths', $this->maxStringLengths);

		return new TemplateResponse(Application::APP_ID, self::TEMPLATE_MAIN);
	}
}
