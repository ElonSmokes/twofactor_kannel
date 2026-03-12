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
		<label for="twofactor-kannel-login-setup-identifier">
			<?php p($l->t('Phone number')) ?>
		</label>
		<input id="twofactor-kannel-login-setup-identifier" type="text" autocomplete="tel" />
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
