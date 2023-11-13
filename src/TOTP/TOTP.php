<?php

namespace YanGusik\TwoFactor\TOTP;

use Carbon\Carbon;
use DateTimeInterface;
use ParagonIE\ConstantTime\Base32;
use YanGusik\TwoFactor\TOTP\Models\TOTPAuthentication;

class TOTP
{
    public function makeCodeFromModel(TOTPAuthentication $authentication, DateTimeInterface|int|string $at = 'now', int $offset = 0): string
    {
        return $this->generateCode(
            $authentication->seconds,
            $authentication->digits,
            $authentication->algorithm,
            $this->getTimestampFromPeriod($authentication->seconds, $at, $offset),
            $authentication->shared_secret);
    }

    public function makeCode(
        string $secret,
        int $seconds = 30,
        int $digits = 6,
        string $algorithm = 'sha1',
        DateTimeInterface|int|string $at = 'now',
        int $offset = 0
    ): string {
        return $this->generateCode($seconds, $digits, $algorithm, $this->getTimestampFromPeriod($seconds, $at, $offset), $secret);
    }

    protected function generateCode(int $seconds, int $digits, string $algorithm, int $timestamp, string $secret): string
    {
        $hmac = hash_hmac(
            $algorithm,
            $this->timestampToBinary($this->getPeriodsFromTimestamp($seconds, $timestamp)),
            $this->getBinarySecret($secret),
            true
        );

        $offset = ord($hmac[strlen($hmac) - 1]) & 0xF;

        $number = (
                ((ord($hmac[$offset + 0]) & 0x7F) << 24) |
                ((ord($hmac[$offset + 1]) & 0xFF) << 16) |
                ((ord($hmac[$offset + 2]) & 0xFF) << 8) |
                (ord($hmac[$offset + 3]) & 0xFF)
            ) % (10 ** $digits);

        return str_pad((string) $number, $digits, '0', STR_PAD_LEFT);
    }

    protected function timestampToBinary(int $timestamp): string
    {
        return pack('N*', 0) . pack('N*', $timestamp);
    }

    protected function getPeriodsFromTimestamp(int $seconds, int $timestamp): int
    {
        return (int) floor($timestamp / $seconds);
    }

    protected function getBinarySecret(string $secret): string
    {
        return Base32::decodeUpper($secret);
    }

    protected function getTimestampFromPeriod(int $seconds, DatetimeInterface|int|string|null $at, int $period): int
    {
        $periods = ($this->parseTimestamp($at) / $seconds) + $period;
        return (int) $periods * $seconds;
    }

    protected function parseTimestamp(DatetimeInterface|int|string $at): int
    {
        return is_int($at) ? $at : Carbon::parse($at)->getTimestamp();
    }
}
