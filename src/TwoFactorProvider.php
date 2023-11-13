<?php

namespace YanGusik\TwoFactor;

use Illuminate\Support\ServiceProvider;
use YanGusik\TwoFactor\OTP\Contracts\Repository;
use YanGusik\TwoFactor\OTP\Repositories\RepositoryFactory;

class TwoFactorProvider extends ServiceProvider
{
    public const CONFIG = __DIR__ . '/../config/config.php';
    public const DB     = __DIR__ . '/../database/migrations';
    public const LANG   = __DIR__ . '/../lang';

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG, 'two_factor');
        $this->registerBindingsTOTP();
        $this->registerBindingsOTP();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishFiles();
    }

    protected function registerBindingsTOTP(): void
    {

    }

    protected function registerBindingsOTP(): void
    {
        $this->app->singleton('otp.repository', fn($app) => new RepositoryFactory($app));
        $this->app->singleton(Repository::class, fn($app) => $app['otp.repository']->driver());
    }

    protected function publishFiles(): void
    {
        $this->publishes([self::CONFIG => config_path('two_factor.php')], 'config');
        $this->publishes([self::DB => database_path('migrations')], 'migrations');
    }
}
