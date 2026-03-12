<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Provider\Channel\Signal;

use OCA\TwoFactorKannel\Provider\AProvider;

class Provider extends AProvider {

	#[\Override]
	public function getDisplayName(): string {
		return $this->l10n->t('Signal verification');
	}

	#[\Override]
	public function getDescription(): string {
		return $this->l10n->t('Authenticate via Signal');
	}
}
