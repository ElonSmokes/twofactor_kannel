(function() {
	const root = document.getElementById('twofactor-kannel-login-setup');
	if (!root) {
		return;
	}

	const els = {
		message: document.getElementById('twofactor-kannel-login-setup-message'),
		error: document.getElementById('twofactor-kannel-login-setup-error'),
		meta: document.getElementById('twofactor-kannel-login-setup-meta'),
		identifierStep: document.getElementById('twofactor-kannel-login-setup-step-identifier'),
		identifier: document.getElementById('twofactor-kannel-login-setup-identifier'),
		start: document.getElementById('twofactor-kannel-login-setup-start'),
		codeStep: document.getElementById('twofactor-kannel-login-setup-step-code'),
		code: document.getElementById('twofactor-kannel-login-setup-code'),
		finish: document.getElementById('twofactor-kannel-login-setup-finish'),
		proceed: document.getElementById('twofactor-kannel-login-setup-proceed'),
	};

	const displayName = root.dataset.displayName || 'SMS';
	const defaultPhone = root.dataset.defaultPhone || '';
	const maskedPhone = root.dataset.maskedPhone || '';
	const showProceed = root.dataset.showProceed === '1';
	const urls = {
		state: root.dataset.stateUrl,
		start: root.dataset.startUrl,
		finish: root.dataset.finishUrl,
	};

	const STATE_DISABLED = 0;
	const STATE_VERIFYING = 2;
	const STATE_ENABLED = 3;
	let countdownTimer = null;

	function setError(message) {
		if (!message) {
			els.error.hidden = true;
			els.error.textContent = '';
			return;
		}
		els.error.hidden = false;
		els.error.textContent = message;
	}

	function setBusy(isBusy) {
		els.start.disabled = isBusy;
		els.finish.disabled = isBusy;
	}

	function setMeta(message) {
		els.meta.textContent = message || '';
	}

	function startCountdown(resendAvailableAt, expiresAt) {
		if (countdownTimer) {
			window.clearInterval(countdownTimer);
		}

		function tick() {
			const now = Date.now() / 1000;
			const resendSeconds = Math.max(0, Math.ceil((resendAvailableAt || 0) - now));
			const expirySeconds = Math.max(0, Math.ceil((expiresAt || 0) - now));
			const parts = [];
			if (resendSeconds > 0) {
				parts.push('Resend available in ' + resendSeconds + 's');
			}
			if (expirySeconds > 0) {
				parts.push('Code expires in ' + expirySeconds + 's');
			}
			setMeta(parts.join(' | '));
			if (resendSeconds <= 0 && expirySeconds <= 0) {
				window.clearInterval(countdownTimer);
				countdownTimer = null;
			}
		}

		tick();
		countdownTimer = window.setInterval(tick, 1000);
	}

	function showDisabled() {
		if (defaultPhone) {
			els.message.textContent = 'Use the phone number stored in your account to enable ' + displayName + '.';
			els.identifier.value = defaultPhone;
			els.identifier.readOnly = true;
			els.start.textContent = 'Send code to ' + (maskedPhone || defaultPhone);
		} else {
			els.message.textContent = 'Enter your phone number to enable ' + displayName + '.';
			els.identifier.readOnly = false;
			els.start.textContent = 'Send code';
		}
		els.identifierStep.hidden = false;
		els.codeStep.hidden = true;
		els.proceed.hidden = true;
		setMeta('');
	}

	function showVerifying(phoneNumber, resendAvailableAt, expiresAt) {
		els.message.textContent = 'A confirmation code was sent to ' + phoneNumber + '.';
		els.identifierStep.hidden = true;
		els.codeStep.hidden = false;
		els.proceed.hidden = true;
		startCountdown(resendAvailableAt, expiresAt);
	}

	function showEnabled(phoneNumber) {
		const suffix = phoneNumber ? ' for ' + phoneNumber : '';
		els.message.textContent = displayName + ' is configured' + suffix + '.';
		els.identifierStep.hidden = true;
		els.codeStep.hidden = true;
		els.proceed.hidden = !showProceed;
		setMeta('');
	}

	function getPayload(payload) {
		if (payload && payload.ocs && payload.ocs.data) {
			return payload.ocs.data;
		}
		return payload || {};
	}

	async function request(url, options) {
		const headers = {
			'Accept': 'application/json',
			'OCS-APIRequest': 'true',
			'requesttoken': (window.OC && OC.requestToken) ? OC.requestToken : '',
		};
		if (!(options && options.body instanceof URLSearchParams)) {
			headers['Content-Type'] = 'application/json';
		}
		const response = await fetch(url, {
			credentials: 'same-origin',
			headers,
			...options,
		});

		let payload = {};
		try {
			payload = await response.json();
		} catch (e) {
			payload = {};
		}

		if (!response.ok) {
			const data = getPayload(payload);
			throw new Error(data.message || 'Request failed');
		}

		return getPayload(payload);
	}

	async function loadState() {
		setBusy(true);
		setError('');
		try {
			const data = await request(urls.state, { method: 'GET' });
			if (data.state === STATE_ENABLED) {
				showEnabled(data.phoneNumber || '');
			} else if (data.state === STATE_VERIFYING) {
				showVerifying(data.phoneNumber || maskedPhone || defaultPhone, data.resendAvailableAt, data.expiresAt);
			} else {
				showDisabled();
			}
		} catch (error) {
			showDisabled();
		} finally {
			setBusy(false);
		}
	}

	els.start.addEventListener('click', async function() {
		const identifier = defaultPhone ? '' : els.identifier.value.trim();
		if (!defaultPhone && !identifier) {
			setError('Phone number is required');
			return;
		}

		setBusy(true);
		setError('');
		try {
			const data = await request(urls.start, {
				method: 'POST',
				body: new URLSearchParams({ identifier }),
			});
			showVerifying(data.phoneNumber || maskedPhone || defaultPhone || identifier, data.resendAvailableAt, data.expiresAt);
		} catch (error) {
			setError(error.message);
		} finally {
			setBusy(false);
		}
	});

	els.finish.addEventListener('click', async function() {
		const verificationCode = els.code.value.trim();
		if (!verificationCode) {
			setError('Confirmation code is required');
			return;
		}

		setBusy(true);
		setError('');
		try {
			await request(urls.finish, {
				method: 'POST',
				body: new URLSearchParams({ verificationCode }),
			});
			showEnabled(els.identifier.value.trim());
		} catch (error) {
			setError(error.message);
		} finally {
			setBusy(false);
		}
	});

	loadState();
})();
