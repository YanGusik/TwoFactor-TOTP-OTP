<?php

namespace YanGusik\TwoFactor\Services;

use YanGusik\TwoFactor\TOTP\Contracts\TwoFactorAuthenticatable;
use YanGusik\TwoFactor\TOTP\TOTP;

class TOTPService
{
    public function __construct(protected TOTP $totp)
    {

    }

    public function validate(TwoFactorAuthenticatable $user, ?string $code, bool $useRecoveryCodes = true): bool
    {
        return null !== $code
            && $user->hasTwoFactorEnabled()
            && ($this->validateCode($user, $code) || ($useRecoveryCodes && $this->useRecoveryCode($user, $code)));
    }

    public function validateCode(TwoFactorAuthenticatable $user, string $code): bool
    {
        $totpAuthentication = $user->twoFactorAuth;
        return $this->totp->makeCodeFromModel($totpAuthentication) === $code;
    }

    public function enableTwoFactorIfConfirm(TwoFactorAuthenticatable $user, string $code): bool
    {
        if ($user->hasTwoFactorEnabled()) {
            return true;
        }

        if ($this->validateCode($user, $code)) {
            $user->enableTwoFactorAuth();
            return true;
        }
        return false;
    }

    public function useRecoveryCode(TwoFactorAuthenticatable $user, string $code): bool
    {
        $totpAuthentication = $user->twoFactorAuth;
        if (!$totpAuthentication->setRecoveryCodeAsUsed($code)) {
            return false;
        }

        $totpAuthentication->save();
        return true;
    }
}