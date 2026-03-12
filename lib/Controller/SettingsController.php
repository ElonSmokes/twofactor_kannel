<?php

/**
 * SPDX-FileCopyrightText: 2018 Christoph Wurst <christoph@winzerhof-wurst.at>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel\Controller;

use OCA\TwoFactorKannel\Exception\VerificationException;
use OCA\TwoFactorKannel\Provider\Gateway\Factory as GatewayFactory;
use OCA\TwoFactorKannel\ResponseDefinitions;
use OCA\TwoFactorKannel\Service\SetupService;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\ApiRoute;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\OCSController;
use OCP\IRequest;
use OCP\IUserSession;

/**
 * @psalm-import-type TwoFactorKannelState from ResponseDefinitions
 */
class SettingsController extends OCSController {

	public function __construct(
		IRequest $request,
		private IUserSession $userSession,
		private SetupService $setup,
		private GatewayFactory $gatewayFactory,
	) {
		parent::__construct('twofactor_kannel', $request);
	}

	/**
	 * @NoTwoFactorRequired
	 *
	 * Check if the gateway was configured
	 *
	 * @param string $gateway The gateway name
	 * @return JSONResponse<Http::STATUS_OK, TwoFactorKannelState, array{}>|JSONResponse<Http::STATUS_BAD_REQUEST, array{message: string}, array{}>|JSONResponse<Http::STATUS_SERVICE_UNAVAILABLE, array{}, array{}>
	 *
	 * 200: OK
	 * 400: User not found
	 * 503: Gateway wasn't configured yed
	 */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'GET', url: '/settings/{gateway}/verification')]
	public function getVerificationState(string $gateway): JSONResponse {
		$user = $this->userSession->getUser();

		if (is_null($user)) {
			return new JSONResponse(['message' => 'User not found'], Http::STATUS_BAD_REQUEST);
		}

		if (!$this->gatewayFactory->get($gateway)->isComplete()) {
			return new JSONResponse([], Http::STATUS_SERVICE_UNAVAILABLE);
		}

		return new JSONResponse($this->setup->getState($user, $gateway)->jsonSerialize());
	}

	/**
	 * @NoTwoFactorRequired
	 *
	 * Send out confirmation message and save current identifier in user settings
	 *
	 * @param string $gateway The gateway type
	 * @param string $identifier The identifier to use this gateway
	 *
	 * @return JSONResponse<Http::STATUS_OK, array{phoneNumber: ?string, resendAvailableAt: ?int, expiresAt: ?int}, array{}>|JSONResponse<Http::STATUS_BAD_REQUEST, array{message: string}, array{}>
	 *
	 * 200: OK
	 * 400: User not found
	 */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'POST', url: '/settings/{gateway}/verification/start')]
	public function startVerification(string $gateway, string $identifier): JSONResponse {
		$user = $this->userSession->getUser();

		if (is_null($user)) {
			return new JSONResponse(['message' => 'User not found'], Http::STATUS_BAD_REQUEST);
		}

		try {
			$state = $this->setup->startSetup($user, $gateway, $identifier);
		} catch (VerificationException $e) {
			return new JSONResponse(['message' => $e->getMessage()], Http::STATUS_BAD_REQUEST);
		}

		return new JSONResponse([
			'phoneNumber' => $state->getIdentifier(),
			'resendAvailableAt' => $state->getResendAvailableAt(),
			'expiresAt' => $state->getExpiresAt(),
		]);
	}

	/**
	 * @NoTwoFactorRequired
	 *
	 * Send out confirmation message and save current identifier in user settings
	 *
	 * @param string $gateway The gateway type
	 * @param string $verificationCode Verification code
	 *
	 * @return JSONResponse<Http::STATUS_OK, array{}, array{}>|JSONResponse<Http::STATUS_BAD_REQUEST, array{message: string}, array{}>
	 *
	 * 200: OK
	 * 400: User not found
	 */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'POST', url: '/settings/{gateway}/verification/finish')]
	public function finishVerification(string $gateway, string $verificationCode): JSONResponse {
		$user = $this->userSession->getUser();

		if (is_null($user)) {
			return new JSONResponse(['message' => 'User not found'], Http::STATUS_BAD_REQUEST);
		}

		try {
			$this->setup->finishSetup($user, $gateway, $verificationCode);
		} catch (VerificationException) {
			return new JSONResponse([], Http::STATUS_BAD_REQUEST);
		}

		return new JSONResponse([]);
	}

	/**
	 * @NoTwoFactorRequired
	 *
	 * Disable a gateway
	 *
	 * @param string $gateway The gateway name
	 * @return JSONResponse<Http::STATUS_OK, array{}, array{}>|JSONResponse<Http::STATUS_BAD_REQUEST, array{message: string}, array{}>>
	 *
	 * 200: OK
	 * 400: User not found
	 */
	#[NoAdminRequired]
	#[ApiRoute(verb: 'DELETE', url: '/settings/{gateway}/verification')]
	public function revokeVerification(string $gateway): JSONResponse {
		$user = $this->userSession->getUser();

		if (is_null($user)) {
			return new JSONResponse(['message' => 'User not found'], Http::STATUS_BAD_REQUEST);
		}

		return new JSONResponse($this->setup->disable($user, $gateway));
	}
}
