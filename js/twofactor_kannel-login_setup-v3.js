(function() {
	const root = document.getElementById('twofactor-kannel-login-setup');
	if (!root) {
		return;
	}

	const COUNTRY_OPTIONS = [
		{ code: '+1', label: 'United States / Canada (+1)' },
		{ code: '+7', label: 'Russia / Kazakhstan (+7)' },
		{ code: '+20', label: 'Egypt (+20)' },
		{ code: '+27', label: 'South Africa (+27)' },
		{ code: '+30', label: 'Greece (+30)' },
		{ code: '+31', label: 'Netherlands (+31)' },
		{ code: '+32', label: 'Belgium (+32)' },
		{ code: '+33', label: 'France (+33)' },
		{ code: '+34', label: 'Spain (+34)' },
		{ code: '+36', label: 'Hungary (+36)' },
		{ code: '+39', label: 'Italy (+39)' },
		{ code: '+40', label: 'Romania (+40)' },
		{ code: '+41', label: 'Switzerland (+41)' },
		{ code: '+43', label: 'Austria (+43)' },
		{ code: '+44', label: 'United Kingdom (+44)' },
		{ code: '+45', label: 'Denmark (+45)' },
		{ code: '+46', label: 'Sweden (+46)' },
		{ code: '+47', label: 'Norway (+47)' },
		{ code: '+48', label: 'Poland (+48)' },
		{ code: '+49', label: 'Germany (+49)' },
		{ code: '+51', label: 'Peru (+51)' },
		{ code: '+52', label: 'Mexico (+52)' },
		{ code: '+54', label: 'Argentina (+54)' },
		{ code: '+55', label: 'Brazil (+55)' },
		{ code: '+56', label: 'Chile (+56)' },
		{ code: '+57', label: 'Colombia (+57)' },
		{ code: '+61', label: 'Australia (+61)' },
		{ code: '+62', label: 'Indonesia (+62)' },
		{ code: '+63', label: 'Philippines (+63)' },
		{ code: '+64', label: 'New Zealand (+64)' },
		{ code: '+65', label: 'Singapore (+65)' },
		{ code: '+66', label: 'Thailand (+66)' },
		{ code: '+81', label: 'Japan (+81)' },
		{ code: '+82', label: 'South Korea (+82)' },
		{ code: '+84', label: 'Vietnam (+84)' },
		{ code: '+86', label: 'China (+86)' },
		{ code: '+90', label: 'Turkey (+90)' },
		{ code: '+91', label: 'India (+91)' },
		{ code: '+92', label: 'Pakistan (+92)' },
		{ code: '+94', label: 'Sri Lanka (+94)' },
		{ code: '+98', label: 'Iran (+98)' },
		{ code: '+211', label: 'South Sudan (+211)' },
		{ code: '+212', label: 'Morocco (+212)' },
		{ code: '+213', label: 'Algeria (+213)' },
		{ code: '+216', label: 'Tunisia (+216)' },
		{ code: '+218', label: 'Libya (+218)' },
		{ code: '+220', label: 'Gambia (+220)' },
		{ code: '+221', label: 'Senegal (+221)' },
		{ code: '+223', label: 'Mali (+223)' },
		{ code: '+225', label: 'Ivory Coast (+225)' },
		{ code: '+230', label: 'Mauritius (+230)' },
		{ code: '+233', label: 'Ghana (+233)' },
		{ code: '+234', label: 'Nigeria (+234)' },
		{ code: '+243', label: 'DR Congo (+243)' },
		{ code: '+351', label: 'Portugal (+351)' },
		{ code: '+352', label: 'Luxembourg (+352)' },
		{ code: '+353', label: 'Ireland (+353)' },
		{ code: '+354', label: 'Iceland (+354)' },
		{ code: '+358', label: 'Finland (+358)' },
		{ code: '+359', label: 'Bulgaria (+359)' },
		{ code: '+370', label: 'Lithuania (+370)' },
		{ code: '+371', label: 'Latvia (+371)' },
		{ code: '+372', label: 'Estonia (+372)' },
		{ code: '+380', label: 'Ukraine (+380)' },
		{ code: '+385', label: 'Croatia (+385)' },
		{ code: '+386', label: 'Slovenia (+386)' },
		{ code: '+420', label: 'Czech Republic (+420)' },
		{ code: '+421', label: 'Slovakia (+421)' },
		{ code: '+852', label: 'Hong Kong (+852)' },
		{ code: '+880', label: 'Bangladesh (+880)' },
		{ code: '+886', label: 'Taiwan (+886)' },
		{ code: '+961', label: 'Lebanon (+961)' },
		{ code: '+962', label: 'Jordan (+962)' },
		{ code: '+963', label: 'Syria (+963)' },
		{ code: '+966', label: 'Saudi Arabia (+966)' },
		{ code: '+971', label: 'United Arab Emirates (+971)' },
		{ code: '+972', label: 'Israel (+972)' },
		{ code: '+973', label: 'Bahrain (+973)' },
		{ code: '+974', label: 'Qatar (+974)' },
		{ code: '+975', label: 'Bhutan (+975)' },
		{ code: '+976', label: 'Mongolia (+976)' },
		{ code: '+977', label: 'Nepal (+977)' },
		{ code: '+995', label: 'Georgia (+995)' },
		{ code: '+998', label: 'Uzbekistan (+998)' },
	];

	const els = {
		message: document.getElementById('twofactor-kannel-login-setup-message'),
		error: document.getElementById('twofactor-kannel-login-setup-error'),
		meta: document.getElementById('twofactor-kannel-login-setup-meta'),
		identifierStep: document.getElementById('twofactor-kannel-login-setup-step-identifier'),
		savedChoice: document.getElementById('twofactor-kannel-login-setup-saved-choice'),
		useSaved: document.getElementById('twofactor-kannel-login-setup-use-saved'),
		useOther: document.getElementById('twofactor-kannel-login-setup-use-other'),
		manualFields: document.getElementById('twofactor-kannel-login-setup-manual-fields'),
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
	const maskedPhone = root.dataset.maskedPhone || '';
	const showProceed = root.dataset.showProceed === '1';
	const urls = {
		state: root.dataset.stateUrl,
		start: root.dataset.startUrl,
		finish: root.dataset.finishUrl,
		revoke: root.dataset.revokeUrl,
	};
	const texts = {
		savedIntro: root.dataset.textSavedIntro || 'Use your saved phone number or choose another number for SMS verification.',
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
	};

	const STATE_DISABLED = 0;
	const STATE_VERIFYING = 2;
	const STATE_ENABLED = 3;
	let countdownTimer = null;
	let activePhoneNumber = '';
	let startCooldownUntil = 0;
	let useSavedNumber = defaultPhone !== '';
	let selectedCountry = null;

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
		if (els.useSaved) {
			els.useSaved.disabled = isBusy;
		}
		if (els.useOther) {
			els.useOther.disabled = isBusy;
		}
		if (els.countryInput) {
			els.countryInput.disabled = isBusy;
		}
		els.nationalNumber.disabled = isBusy && !useSavedNumber;

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

			return option.label.toLowerCase().includes(normalizedFilter) || option.code.includes(normalizedFilter);
		});
	}

	function hideCountryDropdown() {
		if (!els.countryDropdown) {
			return;
		}

		els.countryDropdown.hidden = true;
		if (els.countryInput) {
			els.countryInput.setAttribute('aria-expanded', 'false');
		}
	}

	function showCountryDropdown() {
		if (!els.countryDropdown) {
			return;
		}

		els.countryDropdown.hidden = false;
		if (els.countryInput) {
			els.countryInput.setAttribute('aria-expanded', 'true');
		}
	}

	function selectCountry(option, preserveFilter) {
		selectedCountry = option || null;
		els.countryCode.value = option ? option.code : '';
		if (els.countryInput) {
			els.countryInput.value = option ? option.label : (preserveFilter ? els.countryInput.value : '');
		}
		if (option) {
			hideCountryDropdown();
		}
		updatePreview();
	}

	function renderCountryDropdown(filter) {
		if (!els.countryDropdown) {
			return;
		}

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
				if (els.nationalNumber && !useSavedNumber) {
					els.nationalNumber.focus();
				}
			});
			els.countryDropdown.appendChild(item);
		});

		showCountryDropdown();
	}

	function setPhoneFields(phone) {
		const parts = splitPhoneNumber(phone);
		if (!parts) {
			selectedCountry = null;
			els.countryCode.value = '';
			if (els.countryInput) {
				els.countryInput.value = '';
			}
			els.nationalNumber.value = '';
			return;
		}

		const option = COUNTRY_OPTIONS.find(function(candidate) {
			return candidate.code === parts.countryCode;
		}) || null;

		selectedCountry = option;
		els.countryCode.value = parts.countryCode;
		if (els.countryInput) {
			els.countryInput.value = option ? option.label : parts.countryCode;
		}
		els.nationalNumber.value = parts.nationalNumber;
	}

	function buildInternationalPhone() {
		if (useSavedNumber && defaultPhone) {
			return defaultPhone;
		}

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

	function applyIdentifierMode() {
		const showSavedChoice = defaultPhone !== '';
		els.savedChoice.hidden = !showSavedChoice;
		els.manualFields.hidden = useSavedNumber;

		if (useSavedNumber && defaultPhone) {
			setPhoneFields(defaultPhone);
			els.nationalNumber.readOnly = true;
			els.start.textContent = maskedPhone ? 'Send code to ' + maskedPhone : 'Send code';
			hideCountryDropdown();
			els.message.textContent = texts.savedIntro;
		} else {
			els.nationalNumber.readOnly = false;
			els.start.textContent = 'Send code';
			if (showSavedChoice && buildInternationalPhone() === '') {
				setPhoneFields('');
			}
			els.message.textContent = texts.manualIntro;
		}

		updatePreview();
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
		applyIdentifierMode();

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
		els.message.textContent = formatText(texts.configured, { phone: phoneNumber || maskedPhone || defaultPhone || '' });
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
				showVerifying(data.phoneNumber || maskedPhone || defaultPhone, data.resendAvailableAt, data.expiresAt);
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
			showEnabled(activePhoneNumber || buildInternationalPhone() || maskedPhone || defaultPhone);
		} catch (error) {
			setError(error.message);
		} finally {
			setBusy(false);
		}
	});

	if (els.cancel) {
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
	}

	if (els.useSaved) {
		els.useSaved.addEventListener('click', function() {
			useSavedNumber = true;
			applyIdentifierMode();
			syncStartAvailability();
		});
	}

	if (els.useOther) {
		els.useOther.addEventListener('click', function() {
			useSavedNumber = false;
			setPhoneFields('');
			if (els.countryInput) {
				els.countryInput.value = '';
				renderCountryDropdown('');
				showCountryDropdown();
				els.countryInput.focus();
			}
			applyIdentifierMode();
			syncStartAvailability();
		});
	}

	if (els.countryInput) {
		els.countryInput.placeholder = texts.countryPlaceholder;

		els.countryInput.addEventListener('focus', function() {
			if (!useSavedNumber) {
				renderCountryDropdown(els.countryInput.value);
			}
		});

		els.countryInput.addEventListener('input', function() {
			if (useSavedNumber) {
				return;
			}

			selectedCountry = null;
			els.countryCode.value = '';
			renderCountryDropdown(els.countryInput.value);
			updatePreview();
		});

		els.countryInput.addEventListener('keydown', function(event) {
			if (event.key === 'Escape') {
				hideCountryDropdown();
			}
		});
	}

	document.addEventListener('click', function(event) {
		if (!els.countryDropdown || !els.countryInput || useSavedNumber) {
			return;
		}

		if (event.target === els.countryInput || els.countryDropdown.contains(event.target)) {
			return;
		}

		hideCountryDropdown();
	});

	els.nationalNumber.addEventListener('input', function() {
		if (!useSavedNumber) {
			els.nationalNumber.value = els.nationalNumber.value.replace(/\D+/g, '');
		}
		updatePreview();
	});

	if (defaultPhone) {
		setPhoneFields(defaultPhone);
	}
	updatePreview();
	loadState();
})();
