<?php

/**
 * SPDX-FileCopyrightText: 2025 LibreCode coop and contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

\OCP\Util::addStyle('twofactor_kannel', 'login');
?>

<img class="two-factor-icon two-factor-gateway-icon" src="<?php print_unescaped(image_path('twofactor_kannel', 'app.svg')); ?>" alt="">

<form method="POST" class="twofactor_kannel-form">
	<input type="text"
		   class="challenge"
		   name="challenge"
		   required="required"
		   autofocus
		   autocomplete="off"
		   inputmode="numeric"
		   autocapitalize="off"
		   value="<?php echo isset($_['secret']) ? $_['secret'] : '' ?>"
		   placeholder="<?php p($l->t('Authentication code')) ?>">
	<button class="primary two-factor-submit" type="submit">
		<?php p($l->t('Submit')); ?>
	</button>
	<p><?php p($l->t('An access code has been sent to %s', [$_['phone']])); ?></p>
	<p id="twofactor-kannel-challenge-timer"
	   data-resend-at="<?php p((string)$_['resendAvailableAt']); ?>"
	   data-expires-at="<?php p((string)$_['expiresAt']); ?>"></p>
	<button id="twofactor-kannel-resend" type="button" disabled="disabled">
		<?php p($l->t('Resend code')); ?>
	</button>
</form>
<script>
(function() {
	const timer = document.getElementById('twofactor-kannel-challenge-timer');
	const button = document.getElementById('twofactor-kannel-resend');
	if (!timer || !button) {
		return;
	}
	function update() {
		const now = Date.now() / 1000;
		const resendAt = Number(timer.dataset.resendAt || '0');
		const expiresAt = Number(timer.dataset.expiresAt || '0');
		const resendSeconds = Math.max(0, Math.ceil(resendAt - now));
		const expirySeconds = Math.max(0, Math.ceil(expiresAt - now));
		const parts = [];
		if (resendSeconds > 0) {
			parts.push('Resend available in ' + resendSeconds + 's');
			button.disabled = true;
		} else {
			button.disabled = false;
		}
		if (expirySeconds > 0) {
			parts.push('Code expires in ' + expirySeconds + 's');
		}
		timer.textContent = parts.join(' | ');
	}
	button.addEventListener('click', function() {
		const url = new URL(window.location.href);
		url.searchParams.set('resend', '1');
		window.location.href = url.toString();
	});
	update();
	window.setInterval(update, 1000);
})();
</script>
