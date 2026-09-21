<?php

use App\Http\Middleware\CheckCliniqueAbonnement;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // L'application est servie derrière un reverse proxy (Caddy) qui termine
        // le TLS. Sans cette confiance, Laravel se croirait en HTTP : les URLs
        // générées seraient en clair et les cookies « secure » ne partiraient pas.
        // Le proxy est le seul point d'entrée et se trouve sur le réseau interne
        // Docker, d'où la confiance accordée à l'ensemble des en-têtes transmis.
        $middleware->trustProxies(at: '*');

        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('dashboard'));
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'clinique.active' => CheckCliniqueAbonnement::class,
        ]);
    })

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
