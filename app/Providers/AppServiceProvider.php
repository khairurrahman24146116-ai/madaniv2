<?php

namespace App\Providers;

use App\Models\ActiveLetterRequest;
use App\Models\Letter;
use App\Models\Student;
use App\Policies\ActiveLetterRequestPolicy;
use App\Policies\LetterPolicy;
use App\Policies\StudentPolicy;
use Illuminate\Support\Facades\Gate;
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
    }
}
