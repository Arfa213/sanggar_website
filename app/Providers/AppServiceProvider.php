<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\SanggarProfile;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Paksa HTTPS di production / online server
        if (config('app.env') === 'production' || env('FORCE_HTTPS', false)) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Share $siteProfil ke semua view — navbar, footer, title bisa pakai
        View::composer('*', function ($view) {
            try {
                $view->with('siteProfil', SanggarProfile::getInstance());
            } catch (\Exception $e) {
                // tabel belum ada (sebelum migrate), abaikan
            }
        });
    }
}
