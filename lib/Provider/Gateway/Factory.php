<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Provider\Gateway;

use OCA\TwoFactorKannel\Provider\AFactory;
use OCA\TwoFactorKannel\Provider\Channel\SMS\Gateway as SMSGateway;

/** @extends AFactory<AGateway> */
class Factory extends AFactory {
	#[\Override]
	public function getFqcnList(): array {
		return [
			SMSGateway::class,
		];
	}

	#[\Override]
	protected function getPrefix(): string {
		return 'OCA\\TwoFactorKannel\\Provider\\Channel\\';
	}

	#[\Override]
	protected function getSuffix(): string {
		return 'Gateway';
	}

	#[\Override]
	protected function getBaseClass(): string {
		return AGateway::class;
	}
}
