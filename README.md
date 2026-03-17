<!--
 - SPDX-FileCopyrightText: 2026 ElonSmokes and contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
-->
# twofactor_kannel

`twofactor_kannel` is a custom Nextcloud two-factor authentication app that sends one-time login codes by SMS through a Kannel `sendsms` endpoint.

This repository is a standalone Kannel-focused fork derived from the broader `twofactor_gateway` codebase, but trimmed to a single SMS provider flow for Kannel-backed delivery.

## What It Does

- Adds an `SMS verification` two-factor provider to Nextcloud
- Lets users enroll SMS 2FA from the enforced login setup flow and from personal settings
- Sends OTP codes through a configured Kannel `cgi-bin/sendsms` endpoint
- Supports account-profile phone numbers or manual entry during setup
- Validates user phone numbers in international format
- Enforces setup and login code expiry, resend cooldowns, and failed-attempt limits

## Current Behavior

### User setup flow

- If a valid phone number already exists in the user account profile, the user can choose:
  - `Use saved number`
  - `Use another number`
- If the user enters a number manually, the UI provides:
  - searchable country dialing-code selector
  - local number input
  - normalized-number preview before sending the code
- The setup flow includes:
  - resend cooldown
  - code expiry
  - cancel support
  - cooldown preservation after cancel

### Login challenge flow

- Sends a fresh SMS code for the active challenge
- Supports resend after cooldown
- Invalidates codes after expiry
- Invalidates codes after too many failed attempts

## Security Notes

This app currently includes the following hardening:

- setup resend cooldown preserved across cancel
- setup and login OTPs stored as hashes instead of raw codes
- constant-time OTP comparison with `hash_equals`
- international phone normalization and validation
- attempt limits for setup and login challenge verification

Important caveats:

- SMS is weaker than TOTP because of SIM swap and carrier/interception risks
- Kannel transport security depends on your deployment
- If your Kannel endpoint uses plain HTTP, that is not ideal for production

## Kannel Configuration

The app expects a Kannel `sendsms` URL like:

```text
http://10.10.248.62:13013/cgi-bin/sendsms
```

It is configured through `occ`:

```bash
docker exec nextcloud-aio-nextcloud php /var/www/html/occ twofactorauth:kannel:configure
```

Available commands:

```bash
docker exec nextcloud-aio-nextcloud php /var/www/html/occ twofactorauth:kannel:status
docker exec nextcloud-aio-nextcloud php /var/www/html/occ twofactorauth:kannel:test +441234567890
docker exec nextcloud-aio-nextcloud php /var/www/html/occ twofactorauth:kannel:remove
```

## Kannel-Specific Delivery Logic

This app contains one transport-specific customization for some Kannel deployments:

- the phone number is stored and shown in normal international format, for example:
  - `+79169860903`
- but the outgoing Kannel `to` parameter is currently sent as:
  - `++79169860903`

This is implemented in the transport layer only so the UI and stored user data remain normal.

## Requirements

- Nextcloud 32
- PHP 8.1 to 8.5
- reachable Kannel `sendsms` endpoint

## Installation

Copy the app into your Nextcloud `custom_apps` directory, enable it, then configure the Kannel gateway.

Typical live app path in Docker AIO:

```text
/var/lib/docker/volumes/nextcloud_aio_nextcloud/_data/custom_apps/twofactor_kannel
```

Enable with:

```bash
docker exec nextcloud-aio-nextcloud php /var/www/html/occ app:enable twofactor_kannel
```

## Production Recommendations

Before treating this as production-grade, review:

- Kannel endpoint exposure and firewalling
- HTTPS or trusted private-network transport
- log handling on Kannel and reverse proxies
- allowed user enrollment policy
- backup admin access path in case SMS delivery fails

## Repository Scope

The current runtime scope is SMS via Kannel only.

Some legacy files from the original gateway-based project may still exist in the repository, but the active provider registration is limited to the SMS/Kannel path.

## Architecture

`twofactor_kannel` is structured as a small Nextcloud 2FA app with separate layers for provider registration, enrollment state, login challenge handling, and SMS transport.

### 1. App bootstrap

The app is registered with Nextcloud in:

- [`lib/AppInfo/Application.php`](lib/AppInfo/Application.php)

This bootstrap layer:

- registers the two-factor provider class
- wires app services into Nextcloud
- defines the runtime app ID: `twofactor_kannel`

### 2. Provider layer

The provider layer exposes the app to Nextcloud as an authentication method:

- [`lib/Provider/AProvider.php`](lib/Provider/AProvider.php)
- [`lib/Provider/Factory.php`](lib/Provider/Factory.php)
- [`lib/Provider/Channel/SMS/Provider.php`](lib/Provider/Channel/SMS/Provider.php)

Responsibilities:

- register `SMS verification` as a 2FA provider
- render the login challenge template
- render the setup UI for login-time enrollment and personal settings
- delegate actual challenge creation and verification to services

### 3. Enrollment and persistent state

The setup flow is handled by:

- [`lib/Service/SetupService.php`](lib/Service/SetupService.php)
- [`lib/Service/StateStorage.php`](lib/Service/StateStorage.php)
- [`lib/Provider/State.php`](lib/Provider/State.php)
- [`lib/Controller/SettingsController.php`](lib/Controller/SettingsController.php)

Responsibilities:

- start SMS setup
- send the first verification code
- persist pending setup state in Nextcloud user config
- enforce resend cooldown, expiry, and failed-attempt limits
- finish verification and enable the provider for the user
- disable or cancel setup while preserving cooldown

Stored persistent setup state includes:

- selected phone number
- hashed setup verification code
- resend availability timestamp
- expiry timestamp
- failed attempt counter

### 4. Login challenge state

The actual 2FA login challenge is handled by:

- [`lib/Service/ChallengeService.php`](lib/Service/ChallengeService.php)

Responsibilities:

- issue a login-time OTP
- store challenge state in Nextcloud session
- verify entered codes
- invalidate codes on success, expiry, or too many failed attempts

This state is session-scoped, not persisted as long-term user config.

### 5. Phone handling

Phone parsing and normalization are handled in:

- [`lib/Service/UserPhoneService.php`](lib/Service/UserPhoneService.php)

Responsibilities:

- read the account profile phone from Nextcloud
- normalize phone numbers to international format
- reject invalid phone formats
- provide masked versions of saved phone numbers for display

### 6. SMS transport

The Kannel transport is implemented in:

- [`lib/Provider/Channel/SMS/Gateway.php`](lib/Provider/Channel/SMS/Gateway.php)
- [`lib/Provider/Channel/SMS/Provider/Drivers/Kannel.php`](lib/Provider/Channel/SMS/Provider/Drivers/Kannel.php)

Responsibilities:

- load configured Kannel credentials and URL
- normalize the final destination
- apply the Kannel-specific outbound `to=++<digits>` format
- send the OTP message through the Kannel `cgi-bin/sendsms` endpoint
- interpret the Kannel HTTP response

### 7. User interface

The setup UI is rendered from:

- [`templates/login_setup.php`](templates/login_setup.php)
- [`templates/personal_settings.php`](templates/personal_settings.php)
- [`js/twofactor_kannel-login_setup-v2.js`](js/twofactor_kannel-login_setup-v2.js)

Responsibilities:

- allow users to choose between saved phone and another number
- provide a searchable country-code selector
- build and preview the normalized international number before sending
- display resend cooldown and code expiry timing
- handle setup confirmation and cancellation

The login challenge page is rendered separately in:

- [`templates/challenge.php`](templates/challenge.php)

### 8. Data flow summary

The typical setup flow is:

1. User opens SMS setup.
2. UI chooses saved phone or manual international number entry.
3. `SettingsController` calls `SetupService::startSetup`.
4. `SetupService` validates the number and asks the Kannel driver to send the OTP.
5. Pending setup state is saved in user config.
6. User enters the code.
7. `SettingsController` calls `SetupService::finishSetup`.
8. Provider is enabled for the user.

The typical login flow is:

1. Nextcloud asks the provider for a challenge template.
2. `AProvider` calls `ChallengeService`.
3. `ChallengeService` issues or reuses an OTP and sends it through Kannel.
4. User submits the code.
5. `ChallengeService` verifies the hashed OTP and clears the session challenge state.

## License

AGPL-3.0-or-later
