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

## License

AGPL-3.0-or-later
