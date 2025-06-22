<?php

namespace App\Providers;

use App\Models\AcademicDocument;
use App\Models\ApprovalDocument;
use App\Observers\WorkflowObserver;
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
        // Register workflow observer for specialized document models
        AcademicDocument::observe(WorkflowObserver::class);
        ApprovalDocument::observe(WorkflowObserver::class);
    }
}
