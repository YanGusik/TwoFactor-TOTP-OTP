<?php

namespace YanGusik\TwoFactor\OTP\Repositories;

use Illuminate\Support\Manager;
use YanGusik\TwoFactor\OTP\Contracts\Repository;
use Illuminate\Contracts\Cache\Repository as Cache;

// TODO: i hate RepositoryManager naming (oh this laravel)
class RepositoryFactory extends Manager
{
    public function getDefaultDriver()
    {
        return $this->config->get('two_factor.otp.driver', 'cache');
    }

    protected function createCacheDriver(): Repository
    {
        return new CacheRepository(
            $this->container->make(Cache::class),
            $this->config->get('two_factor.otp.token_lifetime', 5),
            $this->config->get('two_factor.otp.token_length', 5),
            $this->config->get('two_factor.otp.prefix'),
        );
    }
}