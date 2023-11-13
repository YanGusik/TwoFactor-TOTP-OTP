<?php

namespace YanGusik\TwoFactor\TOTP\Contracts;

use Illuminate\Support\Collection;
use YanGusik\TwoFactor\TOTP\Models\TOTPAuthentication;

interface TwoFactorAuthenticatable
{
    public function hasTwoFactorEnabled(): bool;
    public function enableTwoFactorAuth(): void;
    public function disableTwoFactorAuth(): void;
    public function createTwoFactorAuth(); //: TwoFactorTotp;
    public function confirmTwoFactorAuth(string $code): bool;
    public function validateTwoFactorCode(?string $code = null, bool $useRecoveryCodes = true): bool;
    public function getRecoveryCodes(): Collection;
    public function generateRecoveryCodes(): Collection;
}