<?php
declare(strict_types=1);

use OCP\Util;

Util::addScript('twofactor_kannel', 'twofactor_kannel-login_setup-v3');
Util::addStyle('twofactor_kannel', 'setup');
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
	data-show-proceed="<?php p($_['showProceed'] ? '1' : '0'); ?>"
	data-text-saved-intro="<?php p($l->t('Use your saved phone number or choose another number for SMS verification.')); ?>"
	data-text-manual-intro="<?php p($l->t('Choose your country and enter your number to receive login codes by SMS.')); ?>"
	data-text-default-preview="<?php p($l->t('Choose a country and enter the phone number in international format.')); ?>"
	data-text-preview="<?php p($l->t('Code will be sent to {phone}.')); ?>"
	data-text-sent="<?php p($l->t('A confirmation code was sent to {phone}.')); ?>"
	data-text-configured="<?php p($l->t('SMS verification is configured for {phone}.')); ?>"
	data-text-invalid-phone="<?php p($l->t('Choose a country and enter a valid phone number in international format.')); ?>"
	data-text-code-required="<?php p($l->t('Confirmation code is required')); ?>"
	data-text-no-country="<?php p($l->t('No matching country')); ?>"
	data-text-country-placeholder="<?php p($l->t('Type country or code')); ?>"
	data-text-resend="<?php p($l->t('Resend available in {seconds}s')); ?>"
	data-text-expiry="<?php p($l->t('Code expires in {seconds}s')); ?>"
>
	<div class="twofactor-kannel-setup__card">
		<p class="twofactor-kannel-setup__eyebrow"><?php p($l->t('SMS verification')) ?></p>
		<h2 class="twofactor-kannel-setup__title"><?php p($l->t('Set up SMS verification')) ?></h2>
		<p class="twofactor-kannel-setup__lead"><?php p($l->t('Protect your account with one-time login codes sent by SMS.')) ?></p>

		<p id="twofactor-kannel-login-setup-message" class="twofactor-kannel-setup__message"></p>
		<p id="twofactor-kannel-login-setup-error" class="warning twofactor-kannel-setup__error" hidden></p>

		<div id="twofactor-kannel-login-setup-step-identifier" class="twofactor-kannel-setup__step">
			<div id="twofactor-kannel-login-setup-saved-choice" class="twofactor-kannel-setup__saved-choice" hidden>
				<p class="twofactor-kannel-setup__section-label"><?php p($l->t('Choose which number to use')) ?></p>
				<div class="twofactor-kannel-setup__choice-actions">
					<button id="twofactor-kannel-login-setup-use-saved" class="twofactor-kannel-setup__secondary" type="button">
						<?php p($l->t('Use saved number')) ?>
					</button>
					<button id="twofactor-kannel-login-setup-use-other" class="twofactor-kannel-setup__secondary" type="button">
						<?php p($l->t('Use another number')) ?>
					</button>
				</div>
			</div>

			<div id="twofactor-kannel-login-setup-manual-fields" class="twofactor-kannel-setup__manual-fields" hidden>
				<label for="twofactor-kannel-login-setup-country-input" class="twofactor-kannel-setup__label">
					<?php p($l->t('Country')) ?>
				</label>
				<div class="twofactor-kannel-setup__combobox">
					<input
						id="twofactor-kannel-login-setup-country-input"
						class="twofactor-kannel-setup__input"
						type="text"
						autocomplete="off"
						role="combobox"
						aria-autocomplete="list"
						aria-expanded="false"
						aria-controls="twofactor-kannel-login-setup-country-dropdown"
						placeholder="<?php p($l->t('Type country or code')) ?>"
					/>
					<input id="twofactor-kannel-login-setup-country-code" type="hidden" autocomplete="tel-country-code" />
					<div
						id="twofactor-kannel-login-setup-country-dropdown"
						class="twofactor-kannel-setup__dropdown"
						role="listbox"
						hidden
					></div>
				</div>
			</div>

			<div class="twofactor-kannel-setup__field">
				<label for="twofactor-kannel-login-setup-national-number" class="twofactor-kannel-setup__label">
					<?php p($l->t('Phone number')) ?>
				</label>
				<input
					id="twofactor-kannel-login-setup-national-number"
					class="twofactor-kannel-setup__input"
					type="text"
					inputmode="tel"
					autocomplete="tel-national"
				/>
			</div>

			<p id="twofactor-kannel-login-setup-preview" class="twofactor-kannel-setup__preview"></p>

			<div class="twofactor-kannel-setup__actions">
				<button id="twofactor-kannel-login-setup-start" class="primary" type="button">
					<?php p($l->t('Send code')) ?>
				</button>
			</div>
		</div>

		<div id="twofactor-kannel-login-setup-step-code" class="twofactor-kannel-setup__step" hidden>
			<div class="twofactor-kannel-setup__field">
				<label for="twofactor-kannel-login-setup-code" class="twofactor-kannel-setup__label">
					<?php p($l->t('Confirmation code')) ?>
				</label>
				<input
					id="twofactor-kannel-login-setup-code"
					class="twofactor-kannel-setup__input"
					type="text"
					inputmode="numeric"
					autocomplete="one-time-code"
				/>
			</div>
			<div class="twofactor-kannel-setup__actions">
				<button id="twofactor-kannel-login-setup-finish" class="primary" type="button">
					<?php p($l->t('Confirm')) ?>
				</button>
				<button id="twofactor-kannel-login-setup-cancel" class="twofactor-kannel-setup__secondary" type="button">
					<?php p($l->t('Cancel')) ?>
				</button>
			</div>
		</div>

		<p id="twofactor-kannel-login-setup-meta" class="twofactor-kannel-setup__meta"></p>

		<form id="twofactor-kannel-login-setup-proceed" method="POST" hidden>
			<div class="twofactor-kannel-setup__actions">
				<button class="primary" type="submit">
					<?php p($l->t('Proceed')) ?>
				</button>
			</div>
		</form>
	</div>
</div>
