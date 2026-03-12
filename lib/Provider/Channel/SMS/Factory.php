<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Provider\Channel\SMS;

use OCA\TwoFactorKannel\Exception\InvalidProviderException;
use OCA\TwoFactorKannel\Provider\AFactory;
use OCA\TwoFactorKannel\Provider\Channel\SMS\Provider\AProvider;
use OCA\TwoFactorKannel\Provider\Channel\SMS\Provider\Drivers\Kannel;

/** @extends AFactory<AProvider> */
class Factory extends AFactory {
	/** @var array<AProvider> */
	private array $instancesByFqcn = [];

	#[\Override]
	public function getFqcnList(): array {
		return [
			Kannel::class,
		];
	}

	#[\Override]
	protected function getPrefix(): string {
		return 'OCA\\TwoFactorKannel\\Provider\\Channel\\SMS\\Provider\\Drivers\\';
	}

	#[\Override]
	protected function getSuffix(): string {
		return '';
	}

	#[\Override]
	protected function getBaseClass(): string {
		return AProvider::class;
	}

	#[\Override]
	public function get(string $name): object {
		if (isset($this->instancesByFqcn[$name])) {
			return $this->instancesByFqcn[$name];
		}
		if (isset($this->instances[$name])) {
			return $this->instances[$name];
		}
		foreach ($this->getFqcnList() as $fqcn) {
			$instance = \OCP\Server::get($fqcn);
			$settings = $instance->getSettings();
			if ($fqcn === $name || $settings->id === $name) {
				$instance->setAppConfig(\OCP\Server::get(\OCP\IAppConfig::class));
				$this->instances[$settings->id] = $instance;
				$this->instancesByFqcn[$fqcn] = $instance;
				return $instance;
			}
		}
		throw new InvalidProviderException("Provider <$name> does not exist");
	}
}
