<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Zentraler Service Provider für globale Registrierungen und Boot-Logik.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Registriert Services im Container.
     */
    public function register(): void
    {
        //
    }

    /**
     * Führt Initialisierung beim Start der Anwendung aus.
     */
    public function boot(): void
    {
        //
    }
}
