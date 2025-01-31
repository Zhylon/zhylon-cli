<?php

namespace App\Clients;

use App\Support\Panic;
use Laravel\Forge\Resources\Site;
use Laravel\Forge\Forge as BaseForge;
use Psr\Http\Message\ResponseInterface;

class Forge extends BaseForge
{
    /**
     * Number of seconds a request is retried.
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * Get the collection of servers.
     *
     * @return \Laravel\Forge\Resources\Server[]
     */
    public function servers()
    {
        return collect(parent::servers())->filter(function ($server) {
            return $server->revoked == false;
        })->values()->all();
    }

    public function teams()
    {
        return collect($this->get('teams')['teams'])->map(fn($team) => (object) $team);
    }

    public function team($teamId)
    {
        return (object) $this->get("teams/$teamId")['team'];
    }

    /**
     * Get the server logs.
     *
     * @param  string|int  $serverId
     * @param  string  $type
     * @return object
     */
    public function logs($serverId, $type)
    {
        return (object) $this->get("servers/$serverId/logs?file=$type");
    }

    /**
     * Get the site logs.
     *
     * @param  string|int  $serverId
     * @param  string|int  $siteId
     * @return object
     */
    public function siteLogs($serverId, $siteId)
    {
        return (object) $this->get("servers/$serverId/sites/$siteId/logs");
    }

    /**
     * Get the site deployments.
     *
     * @param  string|int  $serverId
     * @param  string|int  $siteId
     * @return array
     */
    public function siteDeployments($serverId, $siteId)
    {
        return $this->get("servers/$serverId/sites/$siteId/deployment-history")['deployments'];
    }

    /**
     * Get a site deployment.
     *
     * @param  string|int  $serverId
     * @param  string|int  $siteId
     * @param  string|int  $deploymentId
     * @return object
     */
    public function siteDeployment($serverId, $siteId, $deploymentId)
    {
        return (object) $this->get("servers/$serverId/sites/$siteId/deployment-history/$deploymentId")['deployment'];
    }

    /**
     * Get the site deployment output.
     *
     * @param  string|int  $serverId
     * @param  string|int  $siteId
     * @param  string|int  $deploymentId
     * @return string
     */
    public function siteDeploymentOutput($serverId, $siteId, $deploymentId)
    {
        return $this->get("servers/$serverId/sites/$siteId/deployment-history/$deploymentId/output")['output'];
    }

    public function sites($serverId)
    {
        return $this->transformCollection(
            $this->get("servers/$serverId/sites")['sites'],
            Site::class,
            ['server_id' => $serverId]
        );
    }

    /**
     * Handle the request error.
     *
     * @param  \Psr\Http\Message\ResponseInterface  $response
     * @return void
     */
    protected function handleRequestError(ResponseInterface $response)
    {
        if ($response->getStatusCode() >= 500) {
            Panic::abort($response->getBody());
        }

        abort_if($response->getStatusCode() == 401, 1, 'Your API Token is invalid.');
        abort_if($response->getStatusCode() == 403, 1, 'Forbidden.');

        if ($response->getStatusCode() == 422) {
            $errors = json_decode((string) $response->getBody());

            abort(1, collect($errors)->flatten()->first());
        }

        parent::handleRequestError($response);
    }
}
