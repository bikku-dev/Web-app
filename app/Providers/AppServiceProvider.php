<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Support\TextHelper;

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
        if (
            app()->environment('production') ||
            request()->header('x-forwarded-proto') === 'https' ||
            str_contains(request()->getHost() ?? '', 'onrender.com') ||
            str_starts_with(config('app.url') ?? '', 'https://')
        ) {
            URL::forceScheme('https');
        }

        // Directives
        Blade::directive('linkify', function ($expression) {
            return "<?php echo \App\Support\TextHelper::linkify($expression); ?>";
        });
    }
}
