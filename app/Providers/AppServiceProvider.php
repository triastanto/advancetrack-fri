<?php

namespace App\Providers;

use App\Models\Document;
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
        // Register workflow observer for models that use workflows
        Document::observe(WorkflowObserver::class);
    }
}
