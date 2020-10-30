<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2019 Inigo Jiron <ijiron@terpmail.umd.edu>
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

use OCP\AppFramework\Db\Entity;

/**
 * @method integer getFormId()
 * @method void setFormId(integer $value)
 * @method integer getOrder()
 * @method void setOrder(integer $value)
 * @method string getType()
 * @method void setType(string $value)
 * @method string getText()
 * @method void setText(string $value)
 */
class Question extends Entity {
	protected $formId;
	protected $order;
	protected $type;
	protected $mandatory;
	protected $text;

	public const TYPES = [
		'short',
		'long',
		'multiple',
		'multiple_unique',
		'dropdown'
	];

	public function __construct() {
		$this->addType('formId', 'integer');
		$this->addType('order', 'integer');
		$this->addType('type', 'string');
		$this->addType('mandatory', 'bool');
		$this->addType('text', 'string');
	}

	public function read(): array {
		return [
			'id' => $this->getId(),
			'formId' => $this->getFormId(),
			'order' => $this->getOrder(),
			'type' => htmlspecialchars_decode($this->getType()),
			'mandatory' => $this->getMandatory(),
			'text' => htmlspecialchars_decode($this->getText()),
		];
	}
}
