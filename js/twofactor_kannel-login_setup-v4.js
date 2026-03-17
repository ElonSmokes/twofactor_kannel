(function() {
	const root = document.getElementById('twofactor-kannel-login-setup');
	if (!root) {
		return;
	}

	const COUNTRY_OPTIONS = [
		{ code: '+1', label: '🇺🇸🇨🇦 +1', search: 'united states canada us usa ca' },
		{ code: '+7', label: '🇷🇺🇰🇿 +7', search: 'russia kazakhstan ru kz' },
		{ code: '+20', label: '🇪🇬 +20', search: 'egypt eg' },
		{ code: '+27', label: '🇿🇦 +27', search: 'south africa za' },
		{ code: '+30', label: '🇬🇷 +30', search: 'greece gr' },
		{ code: '+31', label: '🇳🇱 +31', search: 'netherlands nl holland' },
		{ code: '+32', label: '🇧🇪 +32', search: 'belgium be' },
		{ code: '+33', label: '🇫🇷 +33', search: 'france fr' },
		{ code: '+34', label: '🇪🇸 +34', search: 'spain es' },
		{ code: '+36', label: '🇭🇺 +36', search: 'hungary hu' },
		{ code: '+39', label: '🇮🇹 +39', search: 'italy it' },
		{ code: '+40', label: '🇷🇴 +40', search: 'romania ro' },
		{ code: '+41', label: '🇨🇭 +41', search: 'switzerland ch' },
		{ code: '+43', label: '🇦🇹 +43', search: 'austria at' },
		{ code: '+44', label: '🇬🇧 +44', search: 'united kingdom uk britain great britain gb england' },
		{ code: '+45', label: '🇩🇰 +45', search: 'denmark dk' },
		{ code: '+46', label: '🇸🇪 +46', search: 'sweden se' },
		{ code: '+47', label: '🇳🇴 +47', search: 'norway no' },
		{ code: '+48', label: '🇵🇱 +48', search: 'poland pl' },
		{ code: '+49', label: '🇩🇪 +49', search: 'germany de deutschland' },
		{ code: '+51', label: '🇵🇪 +51', search: 'peru pe' },
		{ code: '+52', label: '🇲🇽 +52', search: 'mexico mx' },
		{ code: '+54', label: '🇦🇷 +54', search: 'argentina ar' },
		{ code: '+55', label: '🇧🇷 +55', search: 'brazil br brasil' },
		{ code: '+56', label: '🇨🇱 +56', search: 'chile cl' },
		{ code: '+57', label: '🇨🇴 +57', search: 'colombia co' },
		{ code: '+61', label: '🇦🇺 +61', search: 'australia au' },
		{ code: '+62', label: '🇮🇩 +62', search: 'indonesia id' },
		{ code: '+63', label: '🇵🇭 +63', search: 'philippines ph' },
		{ code: '+64', label: '🇳🇿 +64', search: 'new zealand nz' },
		{ code: '+65', label: '🇸🇬 +65', search: 'singapore sg' },
		{ code: '+66', label: '🇹🇭 +66', search: 'thailand th' },
		{ code: '+81', label: '🇯🇵 +81', search: 'japan jp' },
		{ code: '+82', label: '🇰🇷 +82', search: 'south korea kr' },
		{ code: '+84', label: '🇻🇳 +84', search: 'vietnam vn' },
		{ code: '+86', label: '🇨🇳 +86', search: 'china cn' },
		{ code: '+90', label: '🇹🇷 +90', search: 'turkey tr' },
		{ code: '+91', label: '🇮🇳 +91', search: 'india in' },
		{ code: '+92', label: '🇵🇰 +92', search: 'pakistan pk' },
		{ code: '+94', label: '🇱🇰 +94', search: 'sri lanka lk' },
		{ code: '+98', label: '🇮🇷 +98', search: 'iran ir' },
		{ code: '+211', label: '🇸🇸 +211', search: 'south sudan ss' },
		{ code: '+212', label: '🇲🇦 +212', search: 'morocco ma' },
		{ code: '+213', label: '🇩🇿 +213', search: 'algeria dz' },
		{ code: '+216', label: '🇹🇳 +216', search: 'tunisia tn' },
		{ code: '+218', label: '🇱🇾 +218', search: 'libya ly' },
		{ code: '+220', label: '🇬🇲 +220', search: 'gambia gm' },
		{ code: '+221', label: '🇸🇳 +221', search: 'senegal sn' },
		{ code: '+223', label: '🇲🇱 +223', search: 'mali ml' },
		{ code: '+225', label: '🇨🇮 +225', search: 'ivory coast cote divoire ci' },
		{ code: '+230', label: '🇲🇺 +230', search: 'mauritius mu' },
		{ code: '+233', label: '🇬🇭 +233', search: 'ghana gh' },
		{ code: '+234', label: '🇳🇬 +234', search: 'nigeria ng' },
		{ code: '+243', label: '🇨🇩 +243', search: 'dr congo cd democratic republic of the congo' },
		{ code: '+351', label: '🇵🇹 +351', search: 'portugal pt' },
		{ code: '+352', label: '🇱🇺 +352', search: 'luxembourg lu' },
		{ code: '+353', label: '🇮🇪 +353', search: 'ireland ie' },
		{ code: '+354', label: '🇮🇸 +354', search: 'iceland is' },
		{ code: '+358', label: '🇫🇮 +358', search: 'finland fi' },
		{ code: '+359', label: '🇧🇬 +359', search: 'bulgaria bg' },
		{ code: '+370', label: '🇱🇹 +370', search: 'lithuania lt' },
		{ code: '+371', label: '🇱🇻 +371', search: 'latvia lv' },
		{ code: '+372', label: '🇪🇪 +372', search: 'estonia ee' },
		{ code: '+380', label: '🇺🇦 +380', search: 'ukraine ua' },
		{ code: '+385', label: '🇭🇷 +385', search: 'croatia hr' },
		{ code: '+386', label: '🇸🇮 +386', search: 'slovenia si' },
		{ code: '+420', label: '🇨🇿 +420', search: 'czech republic cz czechia' },
		{ code: '+421', label: '🇸🇰 +421', search: 'slovakia sk' },
		{ code: '+852', label: '🇭🇰 +852', search: 'hong kong hk' },
		{ code: '+880', label: '🇧🇩 +880', search: 'bangladesh bd' },
		{ code: '+886', label: '🇹🇼 +886', search: 'taiwan tw' },
		{ code: '+961', label: '🇱🇧 +961', search: 'lebanon lb' },
		{ code: '+962', label: '🇯🇴 +962', search: 'jordan jo' },
		{ code: '+963', label: '🇸🇾 +963', search: 'syria sy' },
		{ code: '+966', label: '🇸🇦 +966', search: 'saudi arabia sa' },
		{ code: '+971', label: '🇦🇪 +971', search: 'united arab emirates uae ae' },
		{ code: '+972', label: '🇮🇱 +972', search: 'israel il' },
		{ code: '+973', label: '🇧🇭 +973', search: 'bahrain bh' },
		{ code: '+974', label: '🇶🇦 +974', search: 'qatar qa' },
		{ code: '+975', label: '🇧🇹 +975', search: 'bhutan bt' },
		{ code: '+976', label: '🇲🇳 +976', search: 'mongolia mn' },
		{ code: '+977', label: '🇳🇵 +977', search: 'nepal np' },
		{ code: '+995', label: '🇬🇪 +995', search: 'georgia ge' },
		{ code: '+998', label: '🇺🇿 +998', search: 'uzbekistan uz' },
	];

	const els = {
		message: document.getElementById('twofactor-kannel-login-setup-message'),
		error: document.getElementById('twofactor-kannel-login-setup-error'),
		meta: document.getElementById('twofactor-kannel-login-setup-meta'),
		identifierStep: document.getElementById('twofactor-kannel-login-setup-step-identifier'),
		profileAction: document.getElementById('twofactor-kannel-login-setup-profile-action'),
		useProfile: document.getElementById('twofactor-kannel-login-setup-use-profile'),
		countryInput: document.getElementById('twofactor-kannel-login-setup-country-input'),
		countryCode: document.getElementById('twofactor-kannel-login-setup-country-code'),
		countryDropdown: document.getElementById('twofactor-kannel-login-setup-country-dropdown'),
		nationalNumber: document.getElementById('twofactor-kannel-login-setup-national-number'),
		preview: document.getElementById('twofactor-kannel-login-setup-preview'),
		start: document.getElementById('twofactor-kannel-login-setup-start'),
		codeStep: document.getElementById('twofactor-kannel-login-setup-step-code'),
		code: document.getElementById('twofactor-kannel-login-setup-code'),
		finish: document.getElementById('twofactor-kannel-login-setup-finish'),
		cancel: document.getElementById('twofactor-kannel-login-setup-cancel'),
		proceed: document.getElementById('twofactor-kannel-login-setup-proceed'),
	};

	const defaultPhone = root.dataset.defaultPhone || '';
	const showProceed = root.dataset.showProceed === '1';
	const urls = {
		state: root.dataset.stateUrl,
		start: root.dataset.startUrl,
		finish: root.dataset.finishUrl,
		revoke: root.dataset.revokeUrl,
	};
	const texts = {
		manualIntro: root.dataset.textManualIntro || 'Choose your country and enter your number to receive login codes by SMS.',
		defaultPreview: root.dataset.textDefaultPreview || 'Choose a country and enter the phone number in international format.',
		preview: root.dataset.textPreview || 'Code will be sent to {phone}.',
		sent: root.dataset.textSent || 'A confirmation code was sent to {phone}.',
		configured: root.dataset.textConfigured || 'SMS verification is configured for {phone}.',
		invalidPhone: root.dataset.textInvalidPhone || 'Choose a country and enter a valid phone number in international format.',
		codeRequired: root.dataset.textCodeRequired || 'Confirmation code is required',
		noCountry: root.dataset.textNoCountry || 'No matching country',
		countryPlaceholder: root.dataset.textCountryPlaceholder || 'Type country or code',
		resend: root.dataset.textResend || 'Resend available in {seconds}s',
		expiry: root.dataset.textExpiry || 'Code expires in {seconds}s',
		profileButton: root.dataset.textProfileButton || 'Use profile number {phone}',
	};

	const STATE_DISABLED = 0;
	const STATE_VERIFYING = 2;
	const STATE_ENABLED = 3;
	let countdownTimer = null;
	let activePhoneNumber = '';
	let startCooldownUntil = 0;

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

	function splitPhoneNumber(phone) {
		const normalized = normalizeInternationalPhone(phone);
		if (!normalized) {
			return null;
		}

		const match = [...COUNTRY_OPTIONS]
			.sort(function(a, b) { return b.code.length - a.code.length; })
			.find(function(option) { return normalized.startsWith(option.code); });
		if (!match) {
			return null;
		}

		return {
			countryCode: match.code,
			nationalNumber: normalized.slice(match.code.length),
		};
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
			els.start.disabled = false;
			return;
		}

		els.start.disabled = startCooldownUntil > (Date.now() / 1000);
	}

	function setBusy(isBusy) {
		els.start.disabled = isBusy ? true : els.start.disabled;
		els.finish.disabled = isBusy;
		if (els.cancel) {
			els.cancel.disabled = isBusy;
		}
		if (els.useProfile) {
			els.useProfile.disabled = isBusy;
		}
		els.countryInput.disabled = isBusy;
		els.nationalNumber.disabled = isBusy;

		if (!isBusy) {
			syncStartAvailability();
		}
	}

	function getFilteredCountries(filter) {
		const normalizedFilter = String(filter || '').trim().toLowerCase();
		return COUNTRY_OPTIONS.filter(function(option) {
			if (!normalizedFilter) {
				return true;
			}

			return option.label.toLowerCase().includes(normalizedFilter)
				|| option.code.includes(normalizedFilter)
				|| option.search.includes(normalizedFilter);
		});
	}

	function hideCountryDropdown() {
		els.countryDropdown.hidden = true;
		els.countryInput.setAttribute('aria-expanded', 'false');
	}

	function showCountryDropdown() {
		els.countryDropdown.hidden = false;
		els.countryInput.setAttribute('aria-expanded', 'true');
	}

	function selectCountry(option, preserveFilter) {
		els.countryCode.value = option ? option.code : '';
		els.countryInput.value = option ? option.label : (preserveFilter ? els.countryInput.value : '');
		if (option) {
			hideCountryDropdown();
		}
		updatePreview();
	}

	function renderCountryDropdown(filter) {
		const filtered = getFilteredCountries(filter).slice(0, 12);
		els.countryDropdown.innerHTML = '';

		if (filtered.length === 0) {
			const empty = document.createElement('div');
			empty.className = 'twofactor-kannel-setup__dropdown-empty';
			empty.textContent = texts.noCountry;
			els.countryDropdown.appendChild(empty);
			showCountryDropdown();
			return;
		}

		filtered.forEach(function(option) {
			const item = document.createElement('button');
			item.type = 'button';
			item.className = 'twofactor-kannel-setup__dropdown-item';
			item.role = 'option';
			item.textContent = option.label;
			item.addEventListener('click', function() {
				selectCountry(option);
				els.nationalNumber.focus();
			});
			els.countryDropdown.appendChild(item);
		});

		showCountryDropdown();
	}

	function setPhoneFields(phone) {
		const parts = splitPhoneNumber(phone);
		if (!parts) {
			els.countryCode.value = '';
			els.countryInput.value = '';
			els.nationalNumber.value = '';
			return;
		}

		const option = COUNTRY_OPTIONS.find(function(candidate) {
			return candidate.code === parts.countryCode;
		}) || null;

		els.countryCode.value = parts.countryCode;
		els.countryInput.value = option ? option.label : parts.countryCode;
		els.nationalNumber.value = parts.nationalNumber;
		updatePreview();
	}

	function buildInternationalPhone() {
		const countryCode = els.countryCode.value || '';
		const nationalNumber = (els.nationalNumber.value || '').replace(/\D+/g, '');
		if (!countryCode || !nationalNumber) {
			return '';
		}

		return normalizeInternationalPhone(countryCode + nationalNumber);
	}

	function updatePreview() {
		const normalized = buildInternationalPhone();
		if (normalized) {
			els.preview.textContent = formatText(texts.preview, { phone: normalized });
			return;
		}

		els.preview.textContent = texts.defaultPreview;
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

	function showDisabled(resendAvailableAt) {
		if (countdownTimer) {
			window.clearInterval(countdownTimer);
			countdownTimer = null;
		}

		activePhoneNumber = '';
		startCooldownUntil = resendAvailableAt || 0;
		els.code.value = '';
		els.identifierStep.hidden = false;
		els.codeStep.hidden = true;
		els.proceed.hidden = true;
		els.message.textContent = texts.manualIntro;
		updatePreview();

		if ((resendAvailableAt || 0) > (Date.now() / 1000)) {
			startCountdown(resendAvailableAt, 0, function(resendSeconds) {
				els.start.disabled = resendSeconds > 0;
			});
		} else {
			setMeta('');
			syncStartAvailability();
		}
	}

	function showVerifying(phoneNumber, resendAvailableAt, expiresAt) {
		activePhoneNumber = phoneNumber || '';
		startCooldownUntil = 0;
		els.message.textContent = formatText(texts.sent, { phone: phoneNumber });
		els.identifierStep.hidden = true;
		els.codeStep.hidden = false;
		els.proceed.hidden = true;
		startCountdown(resendAvailableAt, expiresAt);
	}

	function showEnabled(phoneNumber) {
		if (countdownTimer) {
			window.clearInterval(countdownTimer);
			countdownTimer = null;
		}

		startCooldownUntil = 0;
		els.message.textContent = formatText(texts.configured, { phone: phoneNumber || buildInternationalPhone() || '' });
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
				showEnabled(data.phoneNumber || '');
			} else if (data.state === STATE_VERIFYING) {
				showVerifying(data.phoneNumber || buildInternationalPhone(), data.resendAvailableAt, data.expiresAt);
			} else {
				showDisabled(data.resendAvailableAt);
			}
		} catch (error) {
			showDisabled();
		} finally {
			setBusy(false);
		}
	}

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
			showVerifying(data.phoneNumber || identifier, data.resendAvailableAt, data.expiresAt);
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
			showEnabled(activePhoneNumber || buildInternationalPhone());
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
			showDisabled(data.resendAvailableAt);
		} catch (error) {
			setError(error.message);
		} finally {
			setBusy(false);
		}
	});

	if (defaultPhone && els.useProfile) {
		els.profileAction.hidden = false;
		els.useProfile.textContent = formatText(texts.profileButton, { phone: defaultPhone });
		els.useProfile.addEventListener('click', function() {
			setPhoneFields(defaultPhone);
			hideCountryDropdown();
			els.nationalNumber.focus();
		});
	}

	els.countryInput.placeholder = texts.countryPlaceholder;
	els.countryInput.addEventListener('focus', function() {
		renderCountryDropdown(els.countryInput.value);
	});
	els.countryInput.addEventListener('input', function() {
		els.countryCode.value = '';
		renderCountryDropdown(els.countryInput.value);
		updatePreview();
	});
	els.countryInput.addEventListener('keydown', function(event) {
		if (event.key === 'Escape') {
			hideCountryDropdown();
		}
	});

	document.addEventListener('click', function(event) {
		if (event.target === els.countryInput || els.countryDropdown.contains(event.target)) {
			return;
		}
		hideCountryDropdown();
	});

	els.nationalNumber.addEventListener('input', function() {
		els.nationalNumber.value = els.nationalNumber.value.replace(/\D+/g, '');
		updatePreview();
	});

	updatePreview();
	loadState();
})();
