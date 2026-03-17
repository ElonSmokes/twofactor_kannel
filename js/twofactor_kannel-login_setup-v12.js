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
		profilePhone: document.getElementById('twofactor-kannel-login-setup-profile-phone'),
		profileHint: document.getElementById('twofactor-kannel-login-setup-profile-hint'),
		start: document.getElementById('twofactor-kannel-login-setup-start'),
		codeStep: document.getElementById('twofactor-kannel-login-setup-step-code'),
		code: document.getElementById('twofactor-kannel-login-setup-code'),
		finish: document.getElementById('twofactor-kannel-login-setup-finish'),
		cancel: document.getElementById('twofactor-kannel-login-setup-cancel'),
		enabledStep: document.getElementById('twofactor-kannel-login-setup-step-enabled'),
		enabledStatus: document.getElementById('twofactor-kannel-login-setup-enabled-status'),
		enabledPhone: document.getElementById('twofactor-kannel-login-setup-enabled-phone'),
		enabledHint: document.getElementById('twofactor-kannel-login-setup-enabled-hint'),
		disable: document.getElementById('twofactor-kannel-login-setup-disable'),
		proceed: document.getElementById('twofactor-kannel-login-setup-proceed'),
	};

	const urls = {
		state: root.dataset.stateUrl,
		start: root.dataset.startUrl,
		finish: root.dataset.finishUrl,
		revoke: root.dataset.revokeUrl,
	};

	const showProceed = root.dataset.showProceed === '1';
	const cancelMode = root.dataset.cancelMode || 'reset';
	const texts = {
		intro: root.dataset.textIntro || 'SMS verification uses the phone number saved in your profile.',
		sent: root.dataset.textSent || 'A confirmation code was sent to {phone}.',
		success: root.dataset.textSuccess || 'SMS verification was activated successfully.',
		enabledStatus: root.dataset.textEnabledStatus || 'SMS verification is currently active.',
		currentPhone: root.dataset.textCurrentPhone || 'Current phone number: {phone}',
		profileHint: root.dataset.textProfileHint || 'To change the phone number, update it in your profile settings.',
		missingPhone: root.dataset.textMissingPhone || 'Add a valid phone number to your profile before enabling SMS verification.',
		codeRequired: root.dataset.textCodeRequired || 'Confirmation code is required',
		codePlaceholder: root.dataset.textCodePlaceholder || 'Enter the code from SMS',
		resend: root.dataset.textResend || 'Resend available in {seconds}s',
		expiry: root.dataset.textExpiry || 'Code expires in {seconds}s',
	};

	const STATE_ENABLED = 3;
	const STATE_VERIFYING = 2;
	const defaultPhone = root.dataset.defaultPhone || '';
	const maskedPhone = root.dataset.maskedPhone || '';
	let activePhoneNumber = defaultPhone;
	let countdownTimer = null;
	let startCooldownUntil = 0;

	function formatText(template, replacements) {
		return Object.keys(replacements || {}).reduce(function(result, key) {
			return result.replaceAll('{' + key + '}', replacements[key]);
		}, template);
	}

	function maskPhone(phone) {
		return maskedPhone || phone || '';
	}

	function setError(message) {
		if (!message) {
			els.error.hidden = true;
			els.error.textContent = '';
			return;
		}
		els.error.hidden = false;
		els.error.textContent = message;
	}

	function setMeta(message) {
		els.meta.textContent = message || '';
	}

	function stopCountdown() {
		if (countdownTimer) {
			window.clearInterval(countdownTimer);
			countdownTimer = null;
		}
	}

	function syncStartAvailability() {
		if (els.identifierStep.hidden) {
			return;
		}
		els.start.disabled = defaultPhone === '' || startCooldownUntil > (Date.now() / 1000);
	}

	function setBusy(isBusy) {
		els.start.disabled = isBusy ? true : els.start.disabled;
		els.finish.disabled = isBusy || els.code.value.trim() === '';
		els.cancel.disabled = isBusy;
		els.code.disabled = isBusy;
		if (els.disable) {
			els.disable.disabled = isBusy;
		}
		if (!isBusy) {
			syncStartAvailability();
		}
	}

	function showIdentifierStep(resendAvailableAt) {
		stopCountdown();
		els.message.textContent = defaultPhone === '' ? texts.missingPhone : texts.intro;
		els.identifierStep.hidden = false;
		els.codeStep.hidden = true;
		els.enabledStep.hidden = true;
		els.proceed.hidden = true;
		els.code.value = '';
		els.finish.disabled = true;
		if (els.profilePhone) {
			els.profilePhone.textContent = defaultPhone === '' ? '' : formatText(texts.currentPhone, { phone: maskPhone(defaultPhone) });
		}
		if (els.profileHint) {
			els.profileHint.textContent = defaultPhone === '' ? '' : texts.profileHint;
			els.profileHint.hidden = defaultPhone === '';
		}
		startCooldownUntil = resendAvailableAt || 0;
		if ((resendAvailableAt || 0) > (Date.now() / 1000)) {
			startCountdown(resendAvailableAt, 0, function(resendSeconds) {
				els.start.disabled = defaultPhone === '' || resendSeconds > 0;
			});
		} else {
			setMeta('');
			syncStartAvailability();
		}
	}

	function showVerificationStep(phoneNumber, resendAvailableAt, expiresAt) {
		stopCountdown();
		activePhoneNumber = phoneNumber || defaultPhone;
		startCooldownUntil = 0;
		els.message.textContent = formatText(texts.sent, { phone: maskPhone(activePhoneNumber) });
		els.identifierStep.hidden = true;
		els.codeStep.hidden = false;
		els.enabledStep.hidden = true;
		els.proceed.hidden = true;
		els.code.value = '';
		els.finish.disabled = true;
		startCountdown(resendAvailableAt, expiresAt);
	}

	function showEnabled() {
		stopCountdown();
		activePhoneNumber = defaultPhone || activePhoneNumber;
		els.identifierStep.hidden = true;
		els.codeStep.hidden = true;
		setMeta('');
		if (showProceed) {
			els.message.textContent = '';
			els.enabledStep.hidden = true;
			els.proceed.hidden = false;
			return;
		}

		els.message.textContent = '';
		els.enabledStatus.textContent = texts.enabledStatus;
		els.enabledPhone.textContent = formatText(texts.currentPhone, { phone: maskPhone(activePhoneNumber) });
		if (els.enabledHint) {
			els.enabledHint.textContent = texts.profileHint;
		}
		els.enabledStep.hidden = false;
		els.proceed.hidden = true;
	}

	function startCountdown(resendAvailableAt, expiresAt, onTick) {
		stopCountdown();

		function tick() {
			const now = Date.now() / 1000;
			const resendSeconds = Math.max(0, Math.ceil((resendAvailableAt || 0) - now));
			const expirySeconds = Math.max(0, Math.ceil((expiresAt || 0) - now));
			if (onTick) {
				onTick(resendSeconds, expirySeconds);
			}
			const parts = [];
			if (resendSeconds > 0) {
				parts.push(formatText(texts.resend, { seconds: String(resendSeconds) }));
			}
			if (expirySeconds > 0) {
				parts.push(formatText(texts.expiry, { seconds: String(expirySeconds) }));
			}
			setMeta(parts.join(' | '));
			if (resendSeconds <= 0 && expirySeconds <= 0) {
				stopCountdown();
			}
		}

		tick();
		countdownTimer = window.setInterval(tick, 1000);
	}

	function getPayload(payload) {
		if (payload && payload.ocs && payload.ocs.data) {
			return payload.ocs.data;
		}
		return payload || {};
	}

	async function request(url, options) {
		const headers = {
			Accept: 'application/json',
			'OCS-APIRequest': 'true',
			requesttoken: (window.OC && OC.requestToken) ? OC.requestToken : '',
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
				showEnabled();
			} else if (data.state === STATE_VERIFYING) {
				showVerificationStep(data.phoneNumber || defaultPhone, data.resendAvailableAt, data.expiresAt);
			} else {
				showIdentifierStep(data.resendAvailableAt);
			}
		} catch (error) {
			showIdentifierStep();
		} finally {
			setBusy(false);
		}
	}

	els.code.addEventListener('input', function() {
		els.finish.disabled = els.code.value.trim() === '';
	});

	els.start.addEventListener('click', async function() {
		if (defaultPhone === '') {
			setError(texts.missingPhone);
			return;
		}
		setBusy(true);
		setError('');
		try {
			const data = await request(urls.start, {
				method: 'POST',
				body: new URLSearchParams({ identifier: '' }),
			});
			showVerificationStep(data.phoneNumber || defaultPhone, data.resendAvailableAt, data.expiresAt);
			window.setTimeout(function() {
				window.location.reload();
			}, 50);
		} catch (error) {
			setError(error.message);
		} finally {
			setBusy(false);
		}
	});

	els.finish.addEventListener('click', async function() {
		const verificationCode = els.code.value.trim();
		if (!verificationCode) {
			setError(texts.codeRequired);
			return;
		}
		setBusy(true);
		setError('');
		try {
			await request(urls.finish, {
				method: 'POST',
				body: new URLSearchParams({ verificationCode }),
			});
			showEnabled();
		} catch (error) {
			setError(error.message);
		} finally {
			setBusy(false);
		}
	});

	els.cancel.addEventListener('click', async function() {
		setBusy(true);
		setError('');
		try {
			const data = await request(urls.revoke, { method: 'DELETE' });
			if (cancelMode === 'back' && window.history.length > 1) {
				window.history.back();
				return;
			}
			showIdentifierStep(data.resendAvailableAt);
			window.setTimeout(function() {
				window.location.reload();
			}, 50);
		} catch (error) {
			setError(error.message);
		} finally {
			setBusy(false);
		}
	});

	if (els.disable) {
		els.disable.addEventListener('click', async function() {
			setBusy(true);
			setError('');
			try {
				await request(urls.revoke, { method: 'DELETE' });
				window.setTimeout(function() {
					window.location.reload();
				}, 50);
			} catch (error) {
				setError(error.message);
			} finally {
				setBusy(false);
			}
		});
	}

	els.code.placeholder = texts.codePlaceholder;
	loadState();
})();
