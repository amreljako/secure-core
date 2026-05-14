<?php

namespace Amreljako\SecureCore;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Amreljako\SecureCore\Http\Middleware\SecurityHeaders;
use Amreljako\SecureCore\Http\Middleware\HoneyPot;
use Amreljako\SecureCore\Http\Middleware\VerifySignature;
use Illuminate\Support\Facades\Log;

class SecureCoreServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/secure-core.php', 'secure-core');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/secure-core.php' => config_path('secure-core.php'),
            ], 'secure-core-config');
        }

        $kernel = $this->app->make(Kernel::class);

        $kernel->pushMiddleware(SecurityHeaders::class);

        if (config('secure-core.honeypot.enabled', true)) {
            $kernel->pushMiddleware(HoneyPot::class);
        }

        $this->app['router']->aliasMiddleware('secure.signature', VerifySignature::class);

        if (app()->environment('production') && config('app.debug')) {
            config(['app.debug' => false]);
            Log::critical('SECURITY: APP_DEBUG was forced to FALSE in production.');
        }

        $this->setupErrorPageMasking();
    }

    protected function setupErrorPageMasking()
    {
        if (! class_exists(\Spatie\LaravelIgnition\Facades\Flare::class)) {
            return;
        }

        $auditor = new EnvironmentAuditor();
        $maskedKeys = $auditor->getSensitiveEnvKeys(config('secure-core.logging.masked_fields', []));

        \Spatie\LaravelIgnition\Facades\Flare::maskSensitiveAttributes($maskedKeys);
    }
}