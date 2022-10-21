<?php

namespace App\Providers;

use App\Clients\Forge;
use App\Repositories\ConfigRepository;
use App\Repositories\ForgeRepository;
use Illuminate\Support\ServiceProvider;

class ForgeServiceProvider extends ServiceProvider
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
        $this->app->singleton(ForgeRepository::class, function () {
            $config = resolve(ConfigRepository::class);
            $token = $config->get('token', $_SERVER['ZHYLON_API_TOKEN'] ?? getenv('ZHYLON_API_TOKEN') ?: null);

            $guzzle = new \GuzzleHttp\Client([
                'base_uri' => $_SERVER['ZHYLON_API_ENDPOINT'] ?? getenv('ZHYLON_API_ENDPOINT') ?: 'https://zhylon.de/api/v2/',
                'http_errors' => false,
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);

            $client = new Forge($token);

            return new ForgeRepository($config, $client);
        });
    }
}
