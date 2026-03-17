(function() {
	const root = document.getElementById('twofactor-kannel-login-setup');
	if (!root) {
		return;
	}

	const COUNTRY_OPTIONS = [
		{ code: '+1', label: '🇺🇸🇨🇦 +1' },
		{ code: '+7', label: '🇷🇺🇰🇿 +7' },
		{ code: '+20', label: '🇪🇬 +20' },
		{ code: '+27', label: '🇿🇦 +27' },
		{ code: '+30', label: '🇬🇷 +30' },
		{ code: '+31', label: '🇳🇱 +31' },
		{ code: '+32', label: '🇧🇪 +32' },
		{ code: '+33', label: '🇫🇷 +33' },
		{ code: '+34', label: '🇪🇸 +34' },
		{ code: '+36', label: '🇭🇺 +36' },
		{ code: '+39', label: '🇮🇹 +39' },
		{ code: '+40', label: '🇷🇴 +40' },
		{ code: '+41', label: '🇨🇭 +41' },
		{ code: '+43', label: '🇦🇹 +43' },
		{ code: '+44', label: '🇬🇧 +44' },
		{ code: '+45', label: '🇩🇰 +45' },
		{ code: '+46', label: '🇸🇪 +46' },
		{ code: '+47', label: '🇳🇴 +47' },
		{ code: '+48', label: '🇵🇱 +48' },
		{ code: '+49', label: '🇩🇪 +49' },
		{ code: '+51', label: '🇵🇪 +51' },
		{ code: '+52', label: '🇲🇽 +52' },
		{ code: '+54', label: '🇦🇷 +54' },
		{ code: '+55', label: '🇧🇷 +55' },
		{ code: '+56', label: '🇨🇱 +56' },
		{ code: '+57', label: '🇨🇴 +57' },
		{ code: '+61', label: '🇦🇺 +61' },
		{ code: '+62', label: '🇮🇩 +62' },
		{ code: '+63', label: '🇵🇭 +63' },
		{ code: '+64', label: '🇳🇿 +64' },
		{ code: '+65', label: '🇸🇬 +65' },
		{ code: '+66', label: '🇹🇭 +66' },
		{ code: '+81', label: '🇯🇵 +81' },
		{ code: '+82', label: '🇰🇷 +82' },
		{ code: '+84', label: '🇻🇳 +84' },
		{ code: '+86', label: '🇨🇳 +86' },
		{ code: '+90', label: '🇹🇷 +90' },
		{ code: '+91', label: '🇮🇳 +91' },
		{ code: '+92', label: '🇵🇰 +92' },
		{ code: '+94', label: '🇱🇰 +94' },
		{ code: '+98', label: '🇮🇷 +98' },
		{ code: '+211', label: '🇸🇸 +211' },
		{ code: '+212', label: '🇲🇦 +212' },
		{ code: '+213', label: '🇩🇿 +213' },
		{ code: '+216', label: '🇹🇳 +216' },
		{ code: '+218', label: '🇱🇾 +218' },
		{ code: '+220', label: '🇬🇲 +220' },
		{ code: '+221', label: '🇸🇳 +221' },
		{ code: '+223', label: '🇲🇱 +223' },
		{ code: '+225', label: '🇨🇮 +225' },
		{ code: '+230', label: '🇲🇺 +230' },
		{ code: '+233', label: '🇬🇭 +233' },
		{ code: '+234', label: '🇳🇬 +234' },
		{ code: '+243', label: '🇨🇩 +243' },
		{ code: '+351', label: '🇵🇹 +351' },
		{ code: '+352', label: '🇱🇺 +352' },
		{ code: '+353', label: '🇮🇪 +353' },
		{ code: '+354', label: '🇮🇸 +354' },
		{ code: '+358', label: '🇫🇮 +358' },
		{ code: '+359', label: '🇧🇬 +359' },
		{ code: '+370', label: '🇱🇹 +370' },
		{ code: '+371', label: '🇱🇻 +371' },
		{ code: '+372', label: '🇪🇪 +372' },
		{ code: '+380', label: '🇺🇦 +380' },
		{ code: '+385', label: '🇭🇷 +385' },
		{ code: '+386', label: '🇸🇮 +386' },
		{ code: '+420', label: '🇨🇿 +420' },
		{ code: '+421', label: '🇸🇰 +421' },
		{ code: '+852', label: '🇭🇰 +852' },
		{ code: '+880', label: '🇧🇩 +880' },
		{ code: '+886', label: '🇹🇼 +886' },
		{ code: '+961', label: '🇱🇧 +961' },
		{ code: '+962', label: '🇯🇴 +962' },
		{ code: '+963', label: '🇸🇾 +963' },
		{ code: '+966', label: '🇸🇦 +966' },
		{ code: '+971', label: '🇦🇪 +971' },
		{ code: '+972', label: '🇮🇱 +972' },
		{ code: '+973', label: '🇧🇭 +973' },
		{ code: '+974', label: '🇶🇦 +974' },
		{ code: '+975', label: '🇧🇹 +975' },
		{ code: '+976', label: '🇲🇳 +976' },
		{ code: '+977', label: '🇳🇵 +977' },
		{ code: '+995', label: '🇬🇪 +995' },
		{ code: '+998', label: '🇺🇿 +998' },
	];

	const els = {
		message: document.getElementById('twofactor-kannel-login-setup-message'),
		error: document.getElementById('twofactor-kannel-login-setup-error'),
		meta: document.getElementById('twofactor-kannel-login-setup-meta'),
		identifierStep: document.getElementById('twofactor-kannel-login-setup-step-identifier'),
		countryToggle: document.getElementById('twofactor-kannel-login-setup-country-toggle'),
		countryCode: document.getElementById('twofactor-kannel-login-setup-country-code'),
		countryDropdown: document.getElementById('twofactor-kannel-login-setup-country-dropdown'),
		nationalNumber: document.getElementById('twofactor-kannel-login-setup-national-number'),
		start: document.getElementById('twofactor-kannel-login-setup-start'),
		codeStep: document.getElementById('twofactor-kannel-login-setup-step-code'),
		code: document.getElementById('twofactor-kannel-login-setup-code'),
		finish: document.getElementById('twofactor-kannel-login-setup-finish'),
		cancel: document.getElementById('twofactor-kannel-login-setup-cancel'),
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
		manualIntro: root.dataset.textManualIntro || 'Choose your country and enter your number to receive login codes by SMS.',
		defaultPreview: root.dataset.textDefaultPreview || 'Choose a country code and enter your phone number.',
		sent: root.dataset.textSent || 'A confirmation code was sent to {phone}.',
		invalidPhone: root.dataset.textInvalidPhone || 'Choose a country and enter a valid phone number in international format.',
		codeRequired: root.dataset.textCodeRequired || 'Confirmation code is required',
		codePlaceholder: root.dataset.textCodePlaceholder || 'Enter the code from SMS',
		resend: root.dataset.textResend || 'Resend available in {seconds}s',
		expiry: root.dataset.textExpiry || 'Code expires in {seconds}s',
	};

	const STATE_ENABLED = 3;
	const STATE_VERIFYING = 2;
	let countdownTimer = null;
	let startCooldownUntil = 0;
	let activePhoneNumber = '';
	let selectedCountry = COUNTRY_OPTIONS[1] || COUNTRY_OPTIONS[0];

	function formatText(template, replacements) {
		return Object.keys(replacements || {}).reduce(function(result, key) {
			return result.replaceAll('{' + key + '}', replacements[key]);
		}, template);
	}

	function normalizeInternationalPhone(phone) {
		const cleaned = String(phone || '').trim().replace(/[^\d+]/g, '');
		if (!cleaned) {
			return '';
		}
		const normalized = cleaned.startsWith('00') ? '+' + cleaned.slice(2) : cleaned;
		return /^\+[1-9]\d{7,14}$/.test(normalized) ? normalized : '';
	}

	function maskPhone(phone) {
		const normalized = normalizeInternationalPhone(phone);
		if (!normalized) {
			return phone || '';
		}
		if (normalized.length <= 6) {
			return normalized;
		}
		return normalized.slice(0, 3) + '***' + normalized.slice(-4);
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

	function syncStartAvailability() {
		if (els.identifierStep.hidden) {
			return;
		}
		els.start.disabled = startCooldownUntil > (Date.now() / 1000);
	}

	function setBusy(isBusy) {
		els.start.disabled = isBusy ? true : els.start.disabled;
		els.finish.disabled = isBusy || els.code.value.trim() === '';
		els.cancel.disabled = isBusy;
		els.countryToggle.disabled = isBusy;
		els.nationalNumber.disabled = isBusy;
		els.code.disabled = isBusy;
		if (!isBusy) {
			syncStartAvailability();
		}
	}

	function applyCountrySelection(option) {
		selectedCountry = option;
		els.countryCode.value = option.code;
		els.countryToggle.textContent = option.label;
		els.countryToggle.setAttribute('aria-expanded', 'false');
		els.countryDropdown.hidden = true;
	}

	function renderCountryDropdown() {
		els.countryDropdown.innerHTML = '';
		COUNTRY_OPTIONS.forEach(function(option) {
			const item = document.createElement('button');
			item.type = 'button';
			item.className = 'twofactor-kannel-setup__dropdown-item';
			item.role = 'option';
			item.textContent = option.label;
			item.addEventListener('click', function() {
				applyCountrySelection(option);
				els.nationalNumber.focus();
			});
			els.countryDropdown.appendChild(item);
		});
	}

	function buildInternationalPhone() {
		const countryCode = els.countryCode.value || '';
		const nationalNumber = (els.nationalNumber.value || '').replace(/\D+/g, '');
		if (!countryCode || !nationalNumber) {
			return '';
		}
		return normalizeInternationalPhone(countryCode + nationalNumber);
	}

	function showIdentifierStep(resendAvailableAt) {
		els.message.textContent = texts.manualIntro;
		els.identifierStep.hidden = false;
		els.codeStep.hidden = true;
		els.proceed.hidden = true;
		els.code.value = '';
		els.finish.disabled = true;
		startCooldownUntil = resendAvailableAt || 0;
		if ((resendAvailableAt || 0) > (Date.now() / 1000)) {
			startCountdown(resendAvailableAt, 0, function(resendSeconds) {
				els.start.disabled = resendSeconds > 0;
			});
		} else {
			if (countdownTimer) {
				window.clearInterval(countdownTimer);
				countdownTimer = null;
			}
			setMeta('');
			syncStartAvailability();
		}
	}

	function showVerificationStep(phoneNumber, resendAvailableAt, expiresAt) {
		activePhoneNumber = phoneNumber;
		startCooldownUntil = 0;
		els.message.textContent = formatText(texts.sent, { phone: maskPhone(phoneNumber) });
		els.identifierStep.hidden = true;
		els.codeStep.hidden = false;
		els.proceed.hidden = true;
		els.code.value = '';
		els.finish.disabled = true;
		startCountdown(resendAvailableAt, expiresAt);
	}

	function showEnabled() {
		els.identifierStep.hidden = true;
		els.codeStep.hidden = true;
		els.proceed.hidden = !showProceed;
		setMeta('');
	}

	function startCountdown(resendAvailableAt, expiresAt, onTick) {
		if (countdownTimer) {
			window.clearInterval(countdownTimer);
		}

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
				window.clearInterval(countdownTimer);
				countdownTimer = null;
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
				showVerificationStep(data.phoneNumber || activePhoneNumber, data.resendAvailableAt, data.expiresAt);
			} else {
				showIdentifierStep(data.resendAvailableAt);
			}
		} catch (error) {
			showIdentifierStep();
		} finally {
			setBusy(false);
		}
	}

	els.countryToggle.addEventListener('click', function() {
		const expanded = els.countryToggle.getAttribute('aria-expanded') === 'true';
		els.countryToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
		els.countryDropdown.hidden = expanded;
	});

	document.addEventListener('click', function(event) {
		if (event.target === els.countryToggle || els.countryDropdown.contains(event.target)) {
			return;
		}
		els.countryDropdown.hidden = true;
		els.countryToggle.setAttribute('aria-expanded', 'false');
	});

	els.nationalNumber.addEventListener('input', function() {
		els.nationalNumber.value = els.nationalNumber.value.replace(/\D+/g, '');
	});

	els.code.addEventListener('input', function() {
		els.finish.disabled = els.code.value.trim() === '';
	});

	els.start.addEventListener('click', async function() {
		const identifier = buildInternationalPhone();
		if (!identifier) {
			setError(texts.invalidPhone);
			return;
		}
		setBusy(true);
		setError('');
		try {
			const data = await request(urls.start, {
				method: 'POST',
				body: new URLSearchParams({ identifier }),
			});
			showVerificationStep(data.phoneNumber || identifier, data.resendAvailableAt, data.expiresAt);
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
		} catch (error) {
			setError(error.message);
		} finally {
			setBusy(false);
		}
	});

	renderCountryDropdown();
	applyCountrySelection(selectedCountry);
	els.code.placeholder = texts.codePlaceholder;
	loadState();
})();
