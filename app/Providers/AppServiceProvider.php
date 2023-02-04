<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Instance;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use App\Repository\SearchRepository2;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (config('app.protocol') === 'https') {
            \URL::forceScheme('https');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->useStoragePath(config('app.storage_path'));

        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        if (config('netframe.enabled_elasticsearch')) {
            $this->app->bind(Client::class, function ($app) {
                return ClientBuilder::create()
                  ->setHosts(config('services.search.hosts'))
                  ->build();
            });
        }
    }
}
