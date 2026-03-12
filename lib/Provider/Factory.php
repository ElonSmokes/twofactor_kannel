<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Provider;

use OCA\TwoFactorKannel\Provider\Channel\SMS\Provider as SMSProvider;

/** @extends AFactory<AProvider> */
class Factory extends AFactory {
	#[\Override]
	public function getFqcnList(): array {
		return [
			SMSProvider::class,
		];
	}

	#[\Override]
	protected function getPrefix(): string {
		return 'OCA\\TwoFactorKannel\\Provider\\Channel\\';
	}

	#[\Override]
	protected function getSuffix(): string {
		return 'Provider';
	}

	#[\Override]
	protected function getBaseClass(): string {
		return AProvider::class;
	}
}
