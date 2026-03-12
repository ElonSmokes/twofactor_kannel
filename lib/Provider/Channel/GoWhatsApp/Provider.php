<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Provider\Channel\GoWhatsApp;

use OCA\TwoFactorKannel\Provider\AProvider;

class Provider extends AProvider {

	#[\Override]
	public function getDisplayName(): string {
		return $this->l10n->t('WhatsApp verification');
	}

	#[\Override]
	public function getDescription(): string {
		return $this->l10n->t('Authenticate via WhatsApp');
	}
}
