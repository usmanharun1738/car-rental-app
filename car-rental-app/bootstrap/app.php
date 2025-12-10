<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Configure rate limiting for payment routes
            RateLimiter::for('payment', function (Request $request) {
                return Limit::perMinute(5)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function () {
                        return redirect()->back()
                            ->with('error', 'Too many payment attempts. Please wait a minute and try again.');
                    });
            });
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude Paystack webhook from CSRF verification
        // This is required because Paystack sends server-to-server POST requests
        $middleware->validateCsrfTokens(except: [
            'webhook/paystack',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

