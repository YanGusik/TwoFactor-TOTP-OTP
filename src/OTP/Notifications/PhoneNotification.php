<?php

namespace YanGusik\TwoFactor\OTP\Notifications;

use YanGusik\TwoFactor\OTP\Contracts\Notifiable;
use YanGusik\TwoFactor\OTP\Contracts\Notification;

class PhoneNotification implements Notification
{
    public function getSenderToken(Notifiable $notifiable)
    {
        return $notifiable->getPhone();
    }
}