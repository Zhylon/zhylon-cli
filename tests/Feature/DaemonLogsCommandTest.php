<?php

it('can retrieve logs from daemons', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed'],
        (object) ['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed'],
    ]);

    $this->client->shouldReceive('daemon')->andReturn(
        (object) ['id' => 1, 'user' => 'zhylon'],
    );

    $this->remote->shouldReceive('tail')
        ->once()
        ->with('/home/zhylon/.zhylon/daemon-1.log', Mockery::type(Closure::class), [])
        ->andReturn([0, [
            '[00:01] FOO',
            '[00:02] BAR',
        ]]);

    $this->artisan('daemon:logs')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Daemon Would You Like To Retrieve The Logs From</>', 2);
});

it('can tail logs from daemons', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed'],
        (object) ['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed'],
    ]);

    $this->client->shouldReceive('daemon')->andReturn(
        (object) ['id' => 1, 'user' => 'zhylon'],
    );

    $this->remote->shouldReceive('tail')
        ->once()
        ->with('/home/zhylon/.zhylon/daemon-1.log', Mockery::type(Closure::class), ['-f'])
        ->andReturn([0, [
            '[00:01] FOO',
            '[00:02] BAR',
        ]]);

    $this->artisan('daemon:logs', ['--follow' => true])
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Daemon Would You Like To Retrieve The Logs From</>', 1);
});

it('exits with 0 exit code on control + c', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed'],
        (object) ['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed'],
    ]);

    $this->client->shouldReceive('daemon')->andReturn(
        (object) ['id' => 1, 'user' => 'zhylon'],
    );

    $this->remote->shouldReceive('tail')
        ->once()
        ->with('/home/zhylon/.zhylon/daemon-1.log', Mockery::type(Closure::class), ['-f'])
        ->andReturn([255, [
            '[00:01] FOO',
            '[00:02] BAR',
        ]]);

    $this->artisan('daemon:logs', ['--follow' => true])
        ->assertExitCode(0)
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Daemon Would You Like To Retrieve The Logs From</>', 1);
});

it('displays errors', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed'],
        (object) ['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed'],
    ]);

    $this->client->shouldReceive('daemon')->andReturn(
        (object) ['id' => 1, 'user' => 'zhylon'],
    );

    $this->remote->shouldReceive('tail')
        ->once()
        ->with('/home/zhylon/.zhylon/daemon-1.log', Mockery::type(Closure::class), ['-f'])
        ->andReturn(1);

    $this->artisan('daemon:logs', ['--follow' => true])
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Daemon Would You Like To Retrieve The Logs From</>', 1);
})->throws('The requested logs could not be found or they are empty.');

it('can not retrieve or tail logs from daemons run by root', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('daemons')->andReturn([
        (object) ['id' => 1, 'command' => 'php7.4 artisan websockets:serve', 'status' => 'installed'],
        (object) ['id' => 2, 'command' => 'php8.1 artisan queue:work', 'status' => 'installed'],
    ]);

    $this->client->shouldReceive('daemon')->andReturn(
        (object) ['id' => 1, 'user' => 'root'],
    );

    $this->artisan('daemon:logs')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Daemon Would You Like To Retrieve The Logs From</>', 1);
})->throws('Requesting logs from daemons run by [root] is not supported.');
