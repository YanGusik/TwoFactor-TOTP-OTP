<?php

namespace YanGusik\TwoFactor\OTP\Contracts;

interface Repository
{
    public function create(Notifiable $notifiable, Notification $notification): string;

    public function exists(Notifiable $notifiable, string $code): bool;

    public function deleteExisting(Notifiable $notifiable): bool;
}