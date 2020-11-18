<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c] 2017 Vinzenz Rosenkranz <vinzenz.rosenkranz@gmail.com>
 *
 * @author affan98 <affan98@gmail.com>
 * @author John Molakvoæ (skjnldsv) <skjnldsv@protonmail.com>
 * @author Jonas Rittershofer <jotoeri@users.noreply.github.com>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
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

return [
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],

		// Before /{uuid} to avoid conflict
		['name' => 'page#index', 'url' => '/new', 'verb' => 'GET', 'postfix' => 'create'],
		['name' => 'page#index', 'url' => '/{uuid}', 'verb' => 'GET', 'postfix' => 'edit'],
	],
	'ocs' => [
		// Questions
		['name' => 'api#newQuestion', 'url' => '/api/v1/question', 'verb' => 'POST'],
		['name' => 'api#updateQuestion', 'url' => '/api/v1/question/update', 'verb' => 'POST'],
		['name' => 'api#reorderQuestions', 'url' => '/api/v1/question/reorder', 'verb' => 'POST'],
		['name' => 'api#deleteQuestion', 'url' => '/api/v1/question/{id}', 'verb' => 'DELETE'],

		// Answers
		['name' => 'api#newOption', 'url' => '/api/v1/option', 'verb' => 'POST'],
		['name' => 'api#updateOption', 'url' => '/api/v1/option/update', 'verb' => 'POST'],
		['name' => 'api#deleteOption', 'url' => '/api/v1/option/{id}', 'verb' => 'DELETE'],

		// Submissions
		['name' => 'api#getSubmissions', 'url' => '/api/v1/submissions/{hash}', 'verb' => 'GET'],
		['name' => 'api#deleteAllSubmissions', 'url' => '/api/v1/submissions/{formId}', 'verb' => 'DELETE'],

		['name' => 'api#insertSubmission', 'url' => '/api/v1/submission/insert', 'verb' => 'POST'],
		['name' => 'api#deleteSubmission', 'url' => '/api/v1/submission/{id}', 'verb' => 'DELETE'],
	
		[
			'name' => 'App#create',
			'url' => '/api/v1/app',
			'verb' => 'POST',
		],
		[
			'name' => 'App#import',
			'url' => '/api/v1/app/import',
			'verb' => 'POST',
		],
		[
			'name' => 'App#get',
			'url' => '/api/v1/app/{uuid}',
			'verb' => 'GET',
		],
		[
			'name' => 'App#export',
			'url' => '/api/v1/app/export/{uuid}',
			'verb' => 'GET',
		],
		[
			'name' => 'App#update',
			'url' => '/api/v1/app/{uuid}',
			'verb' => 'PUT',
		],
		[
			'name' => 'App#delete',
			'url' => '/api/v1/app/{uuid}',
			'verb' => 'GET',
		],
		[
			'name' => 'App#install',
			'url' => '/api/v1/app/install/{uuid}',
			'verb' => 'POST',
		],
		[
			'name' => 'App#uninstall',
			'url' => '/api/v1/app/install/{uuid}',
			'verb' => 'DELETE',
		]
	]
];
