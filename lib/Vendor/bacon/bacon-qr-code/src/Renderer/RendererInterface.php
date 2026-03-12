<?php

declare (strict_types=1);
namespace OCA\TwoFactorKannel\Vendor\BaconQrCode\Renderer;

use OCA\TwoFactorKannel\Vendor\BaconQrCode\Encoder\QrCode;
/** @internal */
interface RendererInterface
{
    public function render(QrCode $qrCode) : string;
}
