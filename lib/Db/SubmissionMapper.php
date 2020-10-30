<?php
/**
 * @copyright Copyright (c) 2020 Jonas Rittershofer <jotoeri@users.noreply.github.com>
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

namespace OCA\Build\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class SubmissionMapper extends QBMapper {
	private $answerMapper;

	/**
	 * SubmissionMapper constructor.
	 * @param IDBConnection $db
	 * @param AnswerMapper $answerMapper
	 */
	public function __construct(IDBConnection $db, AnswerMapper $answerMapper) {
		parent::__construct($db, 'forms_v2_submissions', Submission::class);

		$this->answerMapper = $answerMapper;
	}

	/**
	 * @param int $formId
	 * @throws DoesNotExistException if not found
	 * @return Submission[]
	 */
	public function findByForm(int $formId): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('form_id', $qb->createNamedParameter($formId, IQueryBuilder::PARAM_INT))
			)
			//Newest submissions first
			->orderBy('timestamp', 'DESC');

		return $this->findEntities($qb);
	}

	/**
	 * @param Integer $id
	 * @return Submission
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
	 * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
	 */
	public function findById(int $id): Submission {
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity($qb);
	}

	/**
	 * @param int $formId
	 * @throws DoesNotExistException if not found
	 * @return array
	 */
	public function findParticipantsByForm(int $formId): array {
		$qb = $this->db->getQueryBuilder();

		$qb->select('user_id')
			->from($this->getTableName())
			->where(
				$qb->expr()->eq('form_id', $qb->createNamedParameter($formId, IQueryBuilder::PARAM_INT))
			);

		$submissionEntities = $this->findEntities($qb);

		// From array of submissionEntities produce array of userIds.
		$userIds = array_map(function ($submissionEntity) {
			return $submissionEntity->getUserId();
		}, $submissionEntities);

		return $userIds;
	}

	/**
	 * @param int $formId
	 */
	public function deleteByForm(int $formId): void {
		$qb = $this->db->getQueryBuilder();

		// First delete corresponding answers.
		$submissionEntities = $this->findByForm($formId);
		foreach ($submissionEntities as $submissionEntity) {
			$this->answerMapper->deleteBySubmission($submissionEntity->id);
		}

		//Delete Submissions
		$qb->delete($this->getTableName())
		->where(
			$qb->expr()->eq('form_id', $qb->createNamedParameter($formId, IQueryBuilder::PARAM_INT))
		);

		$qb->execute();
	}
}
