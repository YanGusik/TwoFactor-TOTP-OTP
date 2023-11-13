<?php

namespace YanGusik\TwoFactor\OTP\Repositories;

use Illuminate\Contracts\Cache\Repository as Cache;
use YanGusik\TwoFactor\OTP\Contracts\Notifiable;
use YanGusik\TwoFactor\OTP\Contracts\Notification;

class CacheRepository extends AbstractRepository
{
    public function __construct(
        protected Cache $cache,
        protected int $expires,
        protected int $codeLength,
        protected string $prefix
    ) {
        parent::__construct($codeLength);
    }

    public function exists(Notifiable $notifiable, string $code): bool
    {
        $signature = $this->getSignatureKey($notifiable->getId());
        return $this->cache->has($signature) && $this->cache->get($signature)['code'] === $code;
    }

    public function deleteExisting(Notifiable $notifiable): bool
    {
        return $this->cache->forget($this->getSignatureKey($notifiable->getId()));
    }

    protected function save(string $code, Notifiable $notifiable, Notification $notification): bool
    {
        return $this->cache->add(
            $this->getSignatureKey($notifiable),
            $this->getPayload($code, $notification->getSenderToken($notifiable)),
            now()->addMinutes($this->expires)
        );
    }

    protected function getSignatureKey(Notifiable $notifiable): string
    {
        return $this->prefix . $notifiable->getId();
    }
}