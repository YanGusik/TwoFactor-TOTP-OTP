<?php

namespace YanGusik\TwoFactor\OTP\Contracts;

interface Notifiable
{
    public function getId();
    public function getEmail();
    public function getPhone();
    public function sendOTPNotification(string $code, array $channel): void;
}