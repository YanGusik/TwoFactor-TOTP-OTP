<?php

namespace YanGusik\TwoFactor\OTP\Repositories;

use Illuminate\Support\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use YanGusik\TwoFactor\OTP\Contracts\Notifiable;
use YanGusik\TwoFactor\OTP\Contracts\Notification;
use YanGusik\TwoFactor\OTP\Contracts\Repository;

abstract class AbstractRepository implements Repository
{
    public function __construct(protected int $codeLength)
    {
    }

    public function create(Notifiable $notifiable, Notification $notification): string
    {
        $this->deleteExisting($notifiable);

        $code = $this->createNewCode($this->codeLength);

        $this->save($code, $notifiable, $notification);

        return $code;
    }

    protected function createNewCode(int $codeLength): string
    {
        return (string) random_int(10 ** ($codeLength - 1), (10 ** $codeLength) - 1);
    }

    protected function codeExpired(string $expiresAt): bool
    {
        return Carbon::parse($expiresAt)->isPast();
    }

    #[ArrayShape(['notification_token' => "string", 'code' => "string", 'sent_at' => "string"])]
    protected function getPayload(string $code, string $notificationToken): array
    {
        return ['notification_token' => $notificationToken, 'code' => $code, 'sent_at' => now()->toDateTimeString()];
    }

    abstract protected function save(string $code, Notifiable $notifiable, Notification $notification): bool;
}