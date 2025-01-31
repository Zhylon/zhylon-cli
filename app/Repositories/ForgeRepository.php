<?php

namespace App\Repositories;

use Exception;
use GuzzleHttp;
use Laravel\Forge\Exceptions\NotFoundException;

/**
 * @mixin \App\Clients\Forge
 */
class ForgeRepository
{
    /**
     * The configuration repository.
     *
     * @var \App\Repositories\ConfigRepository
     */
    protected $config;

    /**
     * The client.
     *
     * @var \Laravel\Forge\Forge
     */
    protected $client;

    /**
     * Creates a new repository instance.
     *
     * @param  \App\Repositories\ConfigRepository  $config
     * @param  \Laravel\Forge\Forge  $client
     * @return void
     */
    public function __construct($config, $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Sets the client.
     *
     * @param  \Laravel\Forge\Forge  $client
     * @return $this
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Pass other method calls down to the underlying client.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $this->ensureApiToken();
        $this->ensureCurrentTeamIsSet();

        try {
            return $this->client->{$method}(...$parameters);
        } catch (Exception $e) {
            if ($e instanceof NotFoundException) {
                abort(1, $e->getMessage());
            }

            throw $e;
        }
    }

    /**
     * Ensure an api token is defined on the client.
     *
     * @return void
     */
    protected function ensureApiToken()
    {
        $token = $this->config->get('token', $_SERVER['ZHYLON_API_TOKEN'] ?? getenv('ZHYLON_API_TOKEN') ?: null);
        $teamId = $this->config->get('team', $_SERVER['ZHYLON_TEAM_ID'] ?? getenv('ZHYLON_TEAM_ID') ?: null);

        abort_if($token == null, 1, 'Please authenticate using the \'login\' command before proceeding.');

        $guzzle = new GuzzleHttp\Client([
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

        $this->client->setApiKey($token, $guzzle);
    }

    /**
     * Ensure the current team is set in the configuration file.
     *
     * @return void
     */
    protected function ensureCurrentTeamIsSet()
    {
        if (! $this->config->get('server', false)) {
            $server = collect($this->client->servers())->first();

            abort_if($server == null, 1, 'Please create a server first.');

            $this->config->set('server', $server->id);
        }
    }
}
