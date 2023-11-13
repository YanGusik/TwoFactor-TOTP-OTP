<?php

namespace YanGusik\TwoFactor\OTP\Contracts;

interface Notification
{
    public function getSenderToken(Notifiable $notifiable);
}