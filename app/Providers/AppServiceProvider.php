<?php

namespace App\Providers;

use App\Context\DomainHelper;
use App\Usecases\AccountService;
use Illuminate\Support\ServiceProvider;

/**
 * アプリケーションにおける DI 定義を行います。
 */
class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DomainHelper::class, function ($app) {
            return new DomainHelper();
        });

        // Service
        $this->app->singleton(AccountService::class, function ($app) {
            return new AccountService($app[DomainHelper::class]);
        });
        $this->app->singleton(AssetService::class, function ($app) {
            return new AssetService($app[DomainHelper::class]);
        });
    }
}
