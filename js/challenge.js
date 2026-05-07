/**
 * SPDX-FileCopyrightText: 2026 ElonSmokes and contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */
(function() {
	'use strict';

	const timer = document.getElementById('twofactor-kannel-challenge-timer');
	const button = document.getElementById('twofactor-kannel-resend');
	if (!timer || !button) {
		return;
	}

	function format(template, seconds) {
		return String(template || '').replace('{seconds}', String(seconds));
	}

	function update() {
		const now = Date.now() / 1000;
		const resendAt = Number(timer.dataset.resendAt || '0');
		const expiresAt = Number(timer.dataset.expiresAt || '0');
		const resendSeconds = Math.max(0, Math.ceil(resendAt - now));
		const expirySeconds = Math.max(0, Math.ceil(expiresAt - now));
		const parts = [];

		if (resendSeconds > 0) {
			parts.push(format(timer.dataset.textResend, resendSeconds));
			button.disabled = true;
		} else {
			button.disabled = false;
		}

		if (expirySeconds > 0) {
			parts.push(format(timer.dataset.textExpiry, expirySeconds));
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
