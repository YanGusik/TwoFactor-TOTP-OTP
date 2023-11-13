<?php

namespace YanGusik\TwoFactor\TOTP\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Collection;
use YanGusik\TwoFactor\TOTP\Models\TOTPAuthentication;

trait TwoFactorAuthentication
{
    public function initializeTwoFactorAuthentication(): void
    {
        $this->makeHidden('twoFactorAuth');
    }

    public function twoFactorAuth(): MorphOne
    {
        return $this->morphOne(TOTPAuthentication::class, 'authenticatable')
            ->withDefault(static function (TOTPAuthentication $model): TOTPAuthentication {
                return $model->fill(config('two_factor.totp.default'));
            });
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->twoFactorAuth->isEnabled();
    }

    public function enableTwoFactorAuth(): void
    {
        $this->twoFactorAuth->enableTwoFactorAuth();
    }

    public function disableTwoFactorAuth(): void
    {
        $this->twoFactorAuth->flushAuth()->delete();
    }

    public function createTwoFactorAuth(): TOTPAuthentication
    {
        $this->twoFactorAuth->flushAuth()->save();
        return $this->twoFactorAuth;
    }

    protected function hasRecoveryCodes(): bool
    {
        return $this->twoFactorAuth->containsUnusedRecoveryCodes();
    }

    public function getRecoveryCodes(): Collection
    {
        return $this->twoFactorAuth->recovery_codes ?? collect();
    }
}