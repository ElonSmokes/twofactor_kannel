<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Provider\Channel\Telegram\Provider;

use OCA\TwoFactorKannel\AppInfo\Application;
use OCA\TwoFactorKannel\Exception\MessageTransmissionException;
use OCA\TwoFactorKannel\Provider\Gateway\TConfigurable;
use OCA\TwoFactorKannel\Provider\Settings;
use OCP\IAppConfig;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AProvider implements IProvider {
	use TConfigurable;
	public IAppConfig $appConfig;
	protected ?Settings $settings = null;

	/**
	 * @throws MessageTransmissionException
	 */
	#[\Override]
	abstract public function send(string $identifier, string $message);

	#[\Override]
	public function setAppConfig(IAppConfig $appConfig): void {
		$this->appConfig = $appConfig;
	}

	#[\Override]
	public function getSettings(): Settings {
		if ($this->settings !== null) {
			return $this->settings;
		}
		return $this->settings = $this->createSettings();
	}

	#[\Override]
	public static function idOverride(): ?string {
		return null;
	}

	#[\Override]
	public function getProviderId(): string {
		$settings = $this->getSettings();
		if (!empty($settings->id)) {
			return $settings->id;
		}
		$id = self::getIdFromProviderFqcn(static::class);
		if ($id === null) {
			throw new \LogicException('Cannot derive gateway id from FQCN: ' . static::class);
		}
		return $id;
	}

	#[\Override]
	abstract public function cliConfigure(InputInterface $input, OutputInterface $output): int;

	public function isComplete(): bool {
		$settings = $this->getSettings();
		$savedKeys = $this->appConfig->getKeys(Application::APP_ID);
		$providerId = $settings->id ?? $this->getProviderId();
		$fields = [];
		foreach ($settings->fields as $field) {
			$fields[] = self::keyFromFieldName($providerId, $field->field);
		}
		$intersect = array_intersect($fields, $savedKeys);
		return count($intersect) === count($fields);
	}

	private static function getIdFromProviderFqcn(string $fqcn): ?string {
		$prefix = 'OCA\\TwoFactorKannel\\Provider\\Channel\\Telegram\\Provider\\Drivers\\';
		if (strncmp($fqcn, $prefix, strlen($prefix)) !== 0) {
			return null;
		}
		$type = substr($fqcn, strlen($prefix));
		if (strpos($type, '\\') !== false) {
			return null;
		}
		return $type !== '' ? strtolower($type) : null;
	}
}
