<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\WechatApi;

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
        //
        $this->app->singleton('App\WechatApi', function ($app){
            return new WechatApi();
        });
    }

    //delay the boot
    protected $defer = true;

    public function provides()
    {
        return ['App\WechatApi'];
    }
}
