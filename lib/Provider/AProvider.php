<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Provider;

use OCA\TwoFactorKannel\AppInfo\Application;
use OCA\TwoFactorKannel\Exception\MessageTransmissionException;
use OCA\TwoFactorKannel\PhoneNumberMask;
use OCA\TwoFactorKannel\Provider\Gateway\Factory as GatewayFactory;
use OCA\TwoFactorKannel\Provider\Gateway\IGateway;
use OCA\TwoFactorKannel\Service\ChallengeService;
use OCA\TwoFactorKannel\Service\StateStorage;
use OCA\TwoFactorKannel\Service\UserPhoneService;
use OCA\TwoFactorKannel\Settings\PersonalSettings;
use OCP\AppFramework\Services\IInitialState;
use OCP\Authentication\TwoFactorAuth\IActivatableAtLogin;
use OCP\Authentication\TwoFactorAuth\IDeactivatableByAdmin;
use OCP\Authentication\TwoFactorAuth\ILoginSetupProvider;
use OCP\Authentication\TwoFactorAuth\IPersonalProviderSettings;
use OCP\Authentication\TwoFactorAuth\IProvider;
use OCP\Authentication\TwoFactorAuth\IProvidesIcons;
use OCP\Authentication\TwoFactorAuth\IProvidesPersonalSettings;
use OCP\IL10N;
use OCP\IRequest;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Server;
use OCP\Template\ITemplate;
use OCP\Template\ITemplateManager;

abstract class AProvider implements IProvider, IProvidesIcons, IDeactivatableByAdmin, IProvidesPersonalSettings, IActivatableAtLogin {
	protected string $gatewayName = '';
	protected IGateway $gateway;

	public function __construct(
		protected GatewayFactory $gatewayFactory,
		protected StateStorage $stateStorage,
		protected IL10N $l10n,
		protected ITemplateManager $templateManager,
		protected IInitialState $initialState,
		protected IRequest $request,
		protected UserPhoneService $userPhoneService,
		protected ChallengeService $challengeService,
	) {
		$this->gateway = $this->gatewayFactory->get($this->getGatewayName());
	}

	private function getGatewayName(): string {
		if ($this->gatewayName) {
			return $this->gatewayName;
		}
		$fqcn = static::class;
		$parts = explode('\\', $fqcn);
		[$name] = array_slice($parts, -2, 1);
		$this->gatewayName = strtolower($name);
		return $this->gatewayName;
	}

	#[\Override]
	public function getId(): string {
		return 'kannel_' . $this->getGatewayName();
	}

	#[\Override]
	public function getTemplate(IUser $user): ITemplate {
		try {
			$identifier = $this->stateStorage->get($user, $this->getGatewayName())->getIdentifier() ?? '';
			if ($identifier === '') {
				$identifier = $this->userPhoneService->getPhoneNumber($user);
			}
			if ($identifier === '') {
				return $this->templateManager->getTemplate('twofactor_kannel', 'error');
			}

			$shouldResend = $this->request->getParam('resend') === '1';
			$challengeState = $this->challengeService->getOrCreate($this->getGatewayName(), $this->gateway, $identifier, $shouldResend);
		} catch (MessageTransmissionException) {
			return $this->templateManager->getTemplate('twofactor_kannel', 'error');
		}

		$tmpl = $this->templateManager->getTemplate('twofactor_kannel', 'challenge');
		$tmpl->assign('phone', PhoneNumberMask::maskNumber($identifier));
		$tmpl->assign('resendAvailableAt', (int)($challengeState['resend_available_at'] ?? 0));
		$tmpl->assign('expiresAt', (int)($challengeState['expires_at'] ?? 0));
		return $tmpl;
	}

	#[\Override]
	public function verifyChallenge(IUser $user, string $challenge): bool {
		return $this->challengeService->verify($this->getGatewayName(), $challenge);
	}

	#[\Override]
	public function isTwoFactorAuthEnabledForUser(IUser $user): bool {
		return $this->stateStorage->get($user, $this->getGatewayName())->getState() === StateStorage::STATE_ENABLED;
	}

	#[\Override]
	public function getPersonalSettings(IUser $user): IPersonalProviderSettings {
		$this->initialState->provideInitialState('settings-' . $this->gateway->getProviderId(), $this->gateway->getSettings());
		return new PersonalSettings(
			$this->getGatewayName(),
			$this->gateway->isComplete(),
			$this->getDisplayName(),
			$this->gateway->getSettings()->instructions ?? '',
			$this->userPhoneService->getPhoneNumber($user),
			$this->userPhoneService->getMaskedPhoneNumber($user),
		);
	}

	#[\Override]
	public function getLoginSetup(IUser $user): ILoginSetupProvider {
		$gatewayName = $this->getGatewayName();
		$gatewaySettings = $this->gateway->getSettings();
		$templateManager = $this->templateManager;
		$displayName = $this->getDisplayName();
		$instructions = $gatewaySettings->instructions ?? '';
		$defaultPhone = $this->userPhoneService->getPhoneNumber($user);
		$maskedPhone = $this->userPhoneService->getMaskedPhoneNumber($user);

		return new class(
			$templateManager,
			$gatewayName,
			$this->getId(),
			$displayName,
			$instructions,
			$defaultPhone,
			$maskedPhone,
		) implements ILoginSetupProvider {
			public function __construct(
				private ITemplateManager $templateManager,
				private string $gatewayName,
				private string $providerId,
				private string $displayName,
				private string $instructions,
				private string $defaultPhone,
				private string $maskedPhone,
			) {
			}

			#[\Override]
			public function getBody(): ITemplate {
				$template = $this->templateManager->getTemplate(Application::APP_ID, 'login_setup');
				$template->assign('gateway', $this->gatewayName);
				$template->assign('providerId', $this->providerId);
				$template->assign('displayName', $this->displayName);
				$template->assign('instructions', $this->instructions);
				$template->assign('defaultPhone', $this->defaultPhone);
				$template->assign('maskedPhone', $this->maskedPhone);
				$template->assign('showProceed', true);
				$baseUrl = '/ocs/v2.php/apps/' . Application::APP_ID . '/settings/' . $this->gatewayName . '/verification';
				$template->assign('stateUrl', $baseUrl);
				$template->assign('startUrl', $baseUrl . '/start');
				$template->assign('finishUrl', $baseUrl . '/finish');
				$template->assign('revokeUrl', $baseUrl);
				return $template;
			}
		};
	}

	#[\Override]
	public function getLightIcon(): String {
		return Server::get(IURLGenerator::class)->imagePath(Application::APP_ID, 'app.svg');
	}

	#[\Override]
	public function getDarkIcon(): String {
		return Server::get(IURLGenerator::class)->imagePath(Application::APP_ID, 'app-dark.svg');
	}

	#[\Override]
	public function disableFor(IUser $user) {
		$state = $this->stateStorage->get($user, $this->getGatewayName());
		if ($state->getState() === StateStorage::STATE_ENABLED) {
			$this->stateStorage->persist($state->disabled($user, $this->getGatewayName()));
		}
	}
}
