<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\EnsurePasswordChanged;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: '',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'auth/login',
            'auth/logout',
            'auth/me',
            'classrooms',
            'classrooms/*',
            'subjects',
            'subjects/*',
            'students',
            'students/*',
            'schedules',
            'schedules/*',
            'teacher-subjects',
            'teacher-subjects/*',
            'attendances',
            'attendances/*',
            'scores',
            'scores/*',
            'score-components',
            'score-components/*',
        ]);

        $middleware->alias([
            'role' => CheckRole::class,
            'password.changed' => EnsurePasswordChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->expectsJson(),
        );
    })->create();
