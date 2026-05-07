<?php

/**
 * SPDX-FileCopyrightText: 2025 LibreCode coop and contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

\OCP\Util::addStyle('twofactor_kannel', 'login');
\OCP\Util::addScript('twofactor_kannel', 'challenge');
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
		   value="<?php p(isset($_['secret']) ? (string)$_['secret'] : '') ?>"
		   placeholder="<?php p($l->t('Authentication code')) ?>">
	<button class="primary two-factor-submit" type="submit">
		<?php p($l->t('Submit')); ?>
	</button>
	<p><?php p($l->t('An access code has been sent to %s', [$_['phone']])); ?></p>
	<p id="twofactor-kannel-challenge-timer"
	   data-resend-at="<?php p((string)$_['resendAvailableAt']); ?>"
	   data-expires-at="<?php p((string)$_['expiresAt']); ?>"
	   data-text-resend="<?php p($l->t('Resend available in {seconds}s')); ?>"
	   data-text-expiry="<?php p($l->t('Code expires in {seconds}s')); ?>"></p>
	<button id="twofactor-kannel-resend" type="button" disabled="disabled">
		<?php p($l->t('Resend code')); ?>
	</button>
</form>
