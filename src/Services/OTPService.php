<?php

namespace YanGusik\TwoFactor\Services;

use Illuminate\Support\Arr;
use YanGusik\TwoFactor\Exceptions\InvalidArgumentException;
use YanGusik\TwoFactor\Exceptions\InvalidOTPCodeException;
use YanGusik\TwoFactor\OTP\Contracts\Notifiable;
use YanGusik\TwoFactor\OTP\Contracts\Notification;
use YanGusik\TwoFactor\OTP\Contracts\Repository;
use YanGusik\TwoFactor\OTP\Notifications\EmailNotification;
use YanGusik\TwoFactor\OTP\Notifications\PhoneNotification;

class OTPService
{
    private array $channel;

    public function __construct(private Repository $repository)
    {
        $this->channel = $this->getDefaultChannel();
    }

    public function send(Notifiable $notifiable, Notification|string $notification): Notifiable
    {
        if (is_string($notification)) {
            switch ($notification) {
                case 'email':
                    $notification = new EmailNotification();
                    break;
                case 'phone':
                    $notification = new PhoneNotification();
                    break;
                default:
                    throw new InvalidArgumentException("The variable `notification` must contain one of two values [email, phone]");
            }
        }

        $code = $this->repository->create($notifiable, $notification);
        $notifiable->sendOTPNotification($code, $this->channel);
        return $notifiable;
    }

    public function validateThrow(Notifiable $notifiable, string $code): Notifiable
    {
        throw_unless($this->repository->exists($notifiable, $code), InvalidOTPCodeException::class);

        $this->repository->deleteExisting($notifiable);
        return $notifiable;
    }

    public function validate(Notifiable $notifiable, string $code): bool
    {
        if ($this->repository->exists($notifiable, $code) === false) {
            return false;
        }
        $this->repository->deleteExisting($notifiable);
        return true;
    }

    private function getDefaultChannel(): array
    {
        $channel = config('two_factor.otp.channel');

        return is_array($channel) ? $channel : Arr::wrap($channel);
    }
}