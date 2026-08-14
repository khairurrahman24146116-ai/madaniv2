<?php

namespace App\Providers;

use App\Models\ActiveLetterRequest;
use App\Models\Letter;
use App\Models\Student;
use App\Policies\ActiveLetterRequestPolicy;
use App\Policies\LetterPolicy;
use App\Policies\StudentPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Letter::class, LetterPolicy::class);
        Gate::policy(ActiveLetterRequest::class, ActiveLetterRequestPolicy::class);

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));

        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()));
    }
}
