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
        $this->app->singleton(\Amreljako\SecureCore\Security\SecureId::class, function ($app) {
        return new \Amreljako\SecureCore\Security\SecureId();
    });
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
        if (! $this->app->bound(\Spatie\LaravelIgnition\FlareMiddleware\AddSolutions::class)) {
            return;
        }

        try {
            $flare = $this->app->make(\Spatie\FlareClient\Flare::class);

            $auditor = new EnvironmentAuditor();
            $maskedKeys = $auditor->getSensitiveEnvKeys(config('secure-core.logging.masked_fields', []));

            if (method_exists($flare, 'maskSensitiveAttributes')) {
                $flare->maskSensitiveAttributes($maskedKeys);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::debug('SecureCore: Could not register Flare masking.');
        }
    }
}