<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Erstellt und konfiguriert die Laravel-Anwendung.
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // Verknüpfung der Routing-Dateien
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Globale Middleware kann hier ergänzt werden.
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Globale Exception-Behandlung kann hier ergänzt werden.
        //
    })->create();
