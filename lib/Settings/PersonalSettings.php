<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Settings;

use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Server;
use OCP\Template\ITemplate;
use OCP\Template\ITemplateManager;

class PersonalSettings implements IPersonalProviderSettings {

	public function __construct(
		private string $gateway,
		private bool $isComplete,
		private string $displayName,
		private string $instructions,
		private string $defaultPhone,
		private string $maskedPhone,
	) {
	}

	#[\Override]
	public function getBody(): ITemplate {
		$template = Server::get(ITemplateManager::class)->getTemplate('twofactor_kannel', 'personal_settings');
		$template->assign('gateway', $this->gateway);
		$template->assign('isComplete', $this->isComplete);
		$template->assign('providerId', 'kannel_' . $this->gateway);
		$template->assign('displayName', $this->displayName);
		$template->assign('instructions', $this->instructions);
		$template->assign('defaultPhone', $this->defaultPhone);
		$template->assign('maskedPhone', $this->maskedPhone);
		$baseUrl = '/ocs/v2.php/apps/twofactor_kannel/settings/' . $this->gateway . '/verification';
		$template->assign('stateUrl', $baseUrl);
		$template->assign('startUrl', $baseUrl . '/start');
		$template->assign('finishUrl', $baseUrl . '/finish');
		$template->assign('revokeUrl', $baseUrl);
		$template->assign('showProceed', false);
		return $template;
	}
}
