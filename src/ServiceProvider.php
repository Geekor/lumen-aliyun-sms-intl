<?php
/**
 * Created by PhpStorm.
 * User: zyxcba
 * Date: 2017/2/17
 * Time: 下午4:12
 */

namespace Geekor\LaravelAliyunSmsIntl;

use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;


class ServiceProvider extends LaravelServiceProvider
{

    public function boot()
    {

        $this->publishes([
            __DIR__.'/config.php' => config_path('aliyunsms.php'),
        ], 'config');

    }

    public function register()
    {

        $this->mergeConfigFrom(__DIR__.'/config.php', 'aliyunsms');

        $this->app->bind(AliyunSmsIntl::class, function() {
            return new AliyunSmsIntl();
        });
    }

    protected function configPath()
    {
        return __DIR__ . '/config.php';
    }

}