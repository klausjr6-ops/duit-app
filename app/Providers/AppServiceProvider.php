<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Force HTTPS URLs when running in production (e.g. on Railway)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Pastikan symlink storage (public/storage -> storage/app/public) selalu
        // ada, karena filesystem container Railway di-reset tiap redeploy dan
        // symlink ini tidak ikut tersimpan di volume. Tanpa ini, file yang
        // sudah di-upload (misal avatar) jadi tidak bisa diakses lewat browser
        // walau file aslinya masih aman di volume.
        $this->ensureStorageLinkExists();
    }

    private function ensureStorageLinkExists(): void
    {
        $link   = public_path('storage');
        $target = storage_path('app/public');

        if (!file_exists($link) && is_dir($target)) {
            @symlink($target, $link);
        }
    }
}
