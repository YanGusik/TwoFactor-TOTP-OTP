<?php

namespace YanGusik\TwoFactor\TOTP\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ParagonIE\ConstantTime\Base32;

class TOTPAuthentication extends Model
{
    protected $table = 'totp_authentications';

    protected $casts = [
        'shared_secret'               => 'encrypted',
        'authenticatable_id'          => 'int',
        'digits'                      => 'int',
        'seconds'                     => 'int',
        'recovery_codes'              => 'encrypted:collection',
        'enabled_at'                  => 'datetime',
        'recovery_codes_generated_at' => 'datetime',
    ];

    protected $fillable = ['digits', 'seconds', 'algorithm'];

    public function authenticatable(): MorphTo
    {
        return $this->morphTo('authenticatable');
    }

    protected function setAlgorithmAttribute($value): void
    {
        $this->attributes['algorithm'] = strtolower($value);
    }

    public function isEnabled(): bool
    {
        return $this->enabled_at !== null;
    }

    public function isDisabled(): bool
    {
        return !$this->isEnabled();
    }

    public function flushAuth(): static
    {
        $this->recovery_codes_generated_at = null;
        $this->safe_devices                = null;
        $this->enabled_at                  = null;

        $this->attributes = array_merge($this->attributes, config('two_factor.totp.default'));

        $this->shared_secret  = static::generateRandomSecret();
        $this->recovery_codes = null;

        return $this;
    }

    public function enableTwoFactorAuth(): void
    {
        $this->enabled_at = now();

        if (config('two_factor.totp.recovery.enabled')) {
            [
                'two-factor.recovery.codes' => $amount,
                'two-factor.recovery.length' => $length
            ] = config()->get([
                'two-factor.recovery.codes', 'two-factor.recovery.length',
            ]);

            $this->recovery_codes = $this->generateRecoveryCodes($amount, $length);
            $this->recovery_codes_generated_at = now();
        }
        $this->save();
    }

    public static function generateRandomSecret(): string
    {
        return Base32::encodeUpper(random_bytes(config('two_factor.totp.secret_length')));
    }

    public function containsUnusedRecoveryCodes(): bool
    {
        return (bool) $this->recovery_codes?->contains('used_at', '==', null);
    }

    protected function getUnusedRecoveryCodeIndex(string $code): int|null|bool
    {
        return $this->recovery_codes?->search([
            'code'    => $code,
            'used_at' => null,
        ], true);
    }

    public function setRecoveryCodeAsUsed(string $code): bool
    {
        $index = $this->getUnusedRecoveryCodeIndex($code);

        if (!is_int($index)) {
            return false;
        }

        $this->recovery_codes = $this->recovery_codes->put($index, [
            'code'    => $code,
            'used_at' => now(),
        ]);

        return true;
    }

    public static function generateRecoveryCodes(int $amount, int $length): Collection
    {
        return Collection::times($amount, fn() => [
            'code'    => strtoupper(Str::random($length)),
            'used_at' => null,
        ]);
    }
}