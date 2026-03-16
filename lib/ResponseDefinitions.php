<?php

declare(strict_types=1);
/**
 * SPDX-FileCopyrightText: 2025 LibreCode coop and contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\TwoFactorKannel;

/**
 * @psalm-type TwoFactorKannelState = array{
 *     gatewayName: string,
 *     state: int,
 *     phoneNumber: ?string,
 *     resendAvailableAt: ?int,
 *     expiresAt: ?int,
 *     failedAttempts: int,
 * }
 * @psalm-type TwoFactorKannelCapabilities = array{
 *     features: list<string>,
 *     config: array{
 *     },
 *     version: string,
 * }
 */
final class ResponseDefinitions {
}
