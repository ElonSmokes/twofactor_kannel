<?php
declare(strict_types=1);

use OCP\Util;

Util::addScript('twofactor_kannel', 'twofactor_kannel-login_setup-v2');
?>
<div
	id="twofactor-kannel-login-setup"
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
>
	<?php if ($_['instructions'] !== ''): ?>
		<p><?php p($_['instructions']); ?></p>
	<?php endif; ?>
	<p id="twofactor-kannel-login-setup-message"></p>
	<p id="twofactor-kannel-login-setup-error" class="warning" hidden></p>

	<div id="twofactor-kannel-login-setup-step-identifier">
		<div id="twofactor-kannel-login-setup-saved-choice" hidden>
			<p><?php p($l->t('Choose which number to use')) ?></p>
			<button id="twofactor-kannel-login-setup-use-saved" type="button">
				<?php p($l->t('Use saved number')) ?>
			</button>
			<button id="twofactor-kannel-login-setup-use-other" type="button">
				<?php p($l->t('Use another number')) ?>
			</button>
		</div>
		<div id="twofactor-kannel-login-setup-manual-fields" hidden>
			<label for="twofactor-kannel-login-setup-country-search">
				<?php p($l->t('Find country')) ?>
			</label>
			<input id="twofactor-kannel-login-setup-country-search" type="text" autocomplete="off" />
		</div>
		<label for="twofactor-kannel-login-setup-country-code">
			<?php p($l->t('Country')) ?>
		</label>
		<select id="twofactor-kannel-login-setup-country-code" autocomplete="tel-country-code"></select>
		<label for="twofactor-kannel-login-setup-national-number">
			<?php p($l->t('Phone number')) ?>
		</label>
		<input id="twofactor-kannel-login-setup-national-number" type="text" inputmode="tel" autocomplete="tel-national" />
		<p id="twofactor-kannel-login-setup-preview"></p>
		<button id="twofactor-kannel-login-setup-start" type="button">
			<?php p($l->t('Send code')) ?>
		</button>
	</div>

	<div id="twofactor-kannel-login-setup-step-code" hidden>
		<label for="twofactor-kannel-login-setup-code">
			<?php p($l->t('Confirmation code')) ?>
		</label>
		<input id="twofactor-kannel-login-setup-code" type="text" inputmode="numeric" autocomplete="one-time-code" />
		<button id="twofactor-kannel-login-setup-finish" type="button">
			<?php p($l->t('Confirm')) ?>
		</button>
		<button id="twofactor-kannel-login-setup-cancel" type="button">
			<?php p($l->t('Cancel')) ?>
		</button>
	</div>
	<p id="twofactor-kannel-login-setup-meta"></p>

	<form id="twofactor-kannel-login-setup-proceed" method="POST" hidden>
		<button type="submit">
			<?php p($l->t('Proceed')) ?>
		</button>
	</form>
</div>
