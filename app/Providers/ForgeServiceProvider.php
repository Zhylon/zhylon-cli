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
            $teamId = $config->get('team', $_SERVER['ZHYLON_TEAM_ID'] ?? getenv('ZHYLON_TEAM_ID') ?: null);

            $guzzle = new \GuzzleHttp\Client([
                'base_uri' => $uri = ($_SERVER['ZHYLON_API_BASE'] ?? 'https://zhylon.net/api/v2/'),
                'http_errors' => false,
                'verify' => !str_contains($uri, '.test'), // don't verify SSL for local environments
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-Team-Id' => $teamId,
                    'User-Agent' => 'Zhylon CLI/v'.config('app.version'),
                ],
            ]);

            $client = new Forge($token, $guzzle);

            return new ForgeRepository($config, $client);
        });
    }
}
