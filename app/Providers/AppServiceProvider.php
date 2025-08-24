<?php

namespace App\Providers;

use Illuminate\Support\Number;
use App\Actions\ValidateCartStock;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contract\CartServiceInterface::class,
            \App\Service\SessionCartService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Number::useCurrency('IDR');

        Gate::define('product-available', function (User $user = null) {
            try {
                ValidateCartStock::run();
                return true;
            } catch (\Illuminate\Validation\ValidationException $e) {
                session()->flash('error', $e->getMessage());
                return false;
            }
        });
    }
}
