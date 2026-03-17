<?php
declare(strict_types=1);

use OCP\Util;

Util::addScript('twofactor_kannel', 'twofactor_kannel-login_setup-v7');
Util::addStyle('twofactor_kannel', 'setup-v5');
Util::addStyle('twofactor_kannel', 'flag-icons.min');
?>
<div
	id="twofactor-kannel-login-setup"
	class="twofactor-kannel-setup"
	data-gateway="<?php p($_['gateway']); ?>"
	data-provider-id="<?php p($_['providerId']); ?>"
	data-display-name="<?php p($_['displayName']); ?>"
	data-default-phone="<?php p($_['defaultPhone']); ?>"
	data-masked-phone="<?php p($_['maskedPhone']); ?>"
	data-state-url="<?php p($_['stateUrl']); ?>"
	data-start-url="<?php p($_['startUrl']); ?>"
	data-finish-url="<?php p($_['finishUrl']); ?>"
	data-revoke-url="<?php p($_['revokeUrl']); ?>"
	data-cancel-mode="reset"
	data-show-proceed="<?php p($_['showProceed'] ? '1' : '0'); ?>"
	data-text-manual-intro="<?php p($l->t('Choose your country and enter your number to receive login codes by SMS.')); ?>"
	data-text-default-preview="<?php p($l->t('Choose a country code and enter your phone number.')); ?>"
	data-text-sent="<?php p($l->t('A confirmation code was sent to {phone}.')); ?>"
	data-text-invalid-phone="<?php p($l->t('Choose a country and enter a valid phone number in international format.')); ?>"
	data-text-code-required="<?php p($l->t('Confirmation code is required')); ?>"
	data-text-code-placeholder="<?php p($l->t('Enter the code from SMS')); ?>"
	data-text-success="<?php p($l->t('SMS verification was activated successfully.')); ?>"
	data-text-resend="<?php p($l->t('Resend available in {seconds}s')); ?>"
	data-text-expiry="<?php p($l->t('Code expires in {seconds}s')); ?>"
>
	<div class="twofactor-kannel-setup__hero">
		<h2 class="twofactor-kannel-setup__title"><?php p($l->t('Set up SMS verification')) ?></h2>
		<p class="twofactor-kannel-setup__lead"><?php p($l->t('Add your mobile number to receive one-time sign-in codes by SMS.')) ?></p>
		<p id="twofactor-kannel-login-setup-message" class="twofactor-kannel-setup__message"></p>
	</div>

	<p id="twofactor-kannel-login-setup-error" class="warning twofactor-kannel-setup__error" hidden></p>

	<div id="twofactor-kannel-login-setup-step-identifier" class="twofactor-kannel-setup__step twofactor-kannel-setup__panel twofactor-kannel-setup__panel--entry">
		<div class="twofactor-kannel-setup__phone-row">
			<div class="twofactor-kannel-setup__country-selector">
				<button
					id="twofactor-kannel-login-setup-country-toggle"
					class="twofactor-kannel-setup__country-button"
					type="button"
					aria-haspopup="listbox"
					aria-expanded="false"
					aria-controls="twofactor-kannel-login-setup-country-dropdown"
				></button>
				<input id="twofactor-kannel-login-setup-country-code" type="hidden" autocomplete="tel-country-code" />
				<div
					id="twofactor-kannel-login-setup-country-dropdown"
					class="twofactor-kannel-setup__dropdown"
					role="listbox"
					hidden
				></div>
			</div>

			<input
				id="twofactor-kannel-login-setup-national-number"
				class="twofactor-kannel-setup__phone-input"
				type="text"
				inputmode="tel"
				autocomplete="tel-national"
				placeholder="<?php p($l->t('Phone number')) ?>"
			/>
		</div>

		<div class="twofactor-kannel-setup__actions">
			<button id="twofactor-kannel-login-setup-start" class="primary" type="button">
				<?php p($l->t('Send code')) ?>
			</button>
		</div>
	</div>

	<div id="twofactor-kannel-login-setup-step-code" class="twofactor-kannel-setup__step twofactor-kannel-setup__panel twofactor-kannel-setup__panel--verify" hidden>
		<input
			id="twofactor-kannel-login-setup-code"
			class="twofactor-kannel-setup__code-input"
			type="text"
			inputmode="numeric"
			autocomplete="one-time-code"
			placeholder="<?php p($l->t('Enter the code from SMS')) ?>"
		/>
		<div class="twofactor-kannel-setup__actions">
			<button id="twofactor-kannel-login-setup-finish" class="primary" type="button" disabled>
				<?php p($l->t('Confirm')) ?>
			</button>
			<button id="twofactor-kannel-login-setup-cancel" class="twofactor-kannel-setup__secondary" type="button">
				<?php p($l->t('Cancel')) ?>
			</button>
		</div>
	</div>

	<p id="twofactor-kannel-login-setup-meta" class="twofactor-kannel-setup__meta"></p>

	<form id="twofactor-kannel-login-setup-proceed" class="twofactor-kannel-setup__panel twofactor-kannel-setup__panel--success" method="POST" hidden>
		<p class="twofactor-kannel-setup__success"><?php p($l->t('SMS verification was activated successfully.')) ?></p>
		<div class="twofactor-kannel-setup__actions">
			<button class="primary" type="submit">
				<?php p($l->t('Proceed')) ?>
			</button>
		</div>
	</form>
</div>
