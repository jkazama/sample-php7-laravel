<?php

namespace App\Providers;

use App\Context\DomainHelper;
use App\Usecases\AccountService;
use App\Usecases\AssetService;
use App\Usecases\ServiceHelper;
use Illuminate\Support\ServiceProvider;

/**
 * アプリケーションにおける DI 定義を行います。
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DomainHelper::class, function ($app) {
            return new DomainHelper();
        });
        $this->app->singleton(ServiceHelper::class, function ($app) {
            return new ServiceHelper($app[DomainHelper::class]);
        });

        // Service
        $this->app->singleton(AccountService::class, function ($app) {
            return new AccountService($app[ServiceHelper::class]);
        });
        $this->app->singleton(AssetService::class, function ($app) {
            return new AssetService($app[ServiceHelper::class]);
        });
    }
}
