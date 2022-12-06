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

namespace OCA\Build\Db;

use InvalidArgumentException;

/**
 * @method string getAppId()
 * @method void setAppId(string $uuid)
 * @method string getViewId()
 */
class ViewConfiguration extends ABuildEntity {
	public const ID_NAVIGATION = 'navigation';
	public const ID_DETAIL = 'detail';

	protected const KEY_TYPE = 'type';
	protected const KEY_HEADLINE = 'headline';
	protected const KEY_SUBHEADING = 'subheading';
	protected const KEY_ICON = 'icon';

	protected $appId;
	protected $viewId;
	protected $configuration;

	/**
	 * @throws InvalidArgumentException
	 */
	public function setViewId(string $viewId) {
		if (!in_array($viewId, [
			self::ID_DETAIL,
			self::ID_NAVIGATION,
		])) {
			throw new InvalidArgumentException('Unsupported view id');
		}
		/** @noinspection PhpUndefinedMethodInspection */
		parent::setViewId($viewId);
	}

	public function getHeadline(): array {
		$configuration = $this->getConfigurationAsArray();
		return $configuration[self::KEY_HEADLINE] ?? [];
	}

	protected function getConfigurationAsArray(): array {
		/** @noinspection PhpUndefinedMethodInspection */
		$value = parent::getConfiguration() ?? '[]';
		return \json_decode($value, true);
	}

	public function setHeadline(array $headline): void {
		$configuration = $this->getConfigurationAsArray();
		$configuration[self::KEY_HEADLINE] = $headline;
		$this->setConfigurationFromArray($configuration);
	}

	protected function setConfigurationFromArray(array $value): void {
		/** @noinspection PhpUndefinedMethodInspection */
		parent::setConfiguration(\json_encode($value));
	}

	public function getSubheading(): array {
		$configuration = $this->getConfigurationAsArray();
		return $configuration[self::KEY_SUBHEADING] ?? [];
	}

	public function setSubheading(array $subheading): void {
		$configuration = $this->getConfigurationAsArray();
		$configuration[self::KEY_SUBHEADING] = $subheading;
		$this->setConfigurationFromArray($configuration);
	}

	public function getIcon(): array {
		$configuration = $this->getConfigurationAsArray();
		return $configuration[self::KEY_ICON] ?? [];
	}

	public function setIcon(array $icon): void {
		$configuration = $this->getConfigurationAsArray();
		$configuration[self::KEY_ICON] = $icon;
		$this->setConfigurationFromArray($configuration);
	}

	public function getType(): string {
		$configuration = $this->getConfigurationAsArray();
		return $configuration[self::KEY_TYPE] ?? '';
	}

	public function setType(string $type): void {
		$configuration = $this->getConfigurationAsArray();
		$configuration[self::KEY_TYPE] = $type;
		$this->setConfigurationFromArray($configuration);
	}

	public function asArray(): array {
		$data = parent::asArray();
		$data['configuration'] = $this->getConfigurationAsArray();
		return $data;
	}
}
