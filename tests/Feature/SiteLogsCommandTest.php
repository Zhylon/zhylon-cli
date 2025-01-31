<?php

it('can retrieve logs from sites', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('site')->once()->with(1, 2)->andReturn(
        (object) ['id' => 1, 'name' => 'something.com', 'username' => 'zhylon', 'app' => 'php'],
    );

    $files = [
        '/home/zhylon/something.com/shared/storage/logs/*.log',
        '/home/zhylon/something.com/storage/logs/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), [])
        ->andReturn([0, [
            '[00:01] FOO',
            '[00:02] BAR',
        ]]);

    $this->artisan('site:logs')
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Retrieve The Logs From</>', 2);
});

it('can tail logs from sites', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('site')->once()->with(1, 1)->andReturn(
        (object) ['id' => 1, 'name' => 'pestphp.com', 'username' => 'zhylon', 'app' => 'wordpress'],
    );

    $files = [
        '/home/zhylon/pestphp.com/public/wp-content/*.log',
        '/home/zhylon/pestphp.com/wp-content/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), ['-f'])
        ->andReturn([0, [
            '[00:01] FOO',
            '[00:02] BAR',
        ]]);

    $this->artisan('site:logs', ['--follow' => true])
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Retrieve The Logs From</>', 1);
});

it('exits with 0 exit code on control + c', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('site')->once()->with(1, 1)->andReturn(
        (object) ['id' => 1, 'name' => 'pestphp.com', 'username' => 'zhylon', 'app' => 'wordpress'],
    );

    $files = [
        '/home/zhylon/pestphp.com/public/wp-content/*.log',
        '/home/zhylon/pestphp.com/wp-content/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), ['-f'])
        ->andReturn([255, [
            '[00:01] FOO',
            '[00:02] BAR',
        ]]);

    $this->artisan('site:logs', ['--follow' => true])
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Retrieve The Logs From</>', 1);
});

it('displays errors', function () {
    $this->client->shouldReceive('server')->andReturn(
        (object) ['id' => 1],
    );

    $this->client->shouldReceive('sites')->once()->andReturn([
        (object) ['id' => 1, 'name' => 'pestphp.com'],
        (object) ['id' => 2, 'name' => 'something.com'],
    ]);

    $this->client->shouldReceive('site')->once()->with(1, 2)->andReturn(
        (object) ['id' => 1, 'name' => 'something.com', 'username' => 'user-in-isolation', 'app' => 'php'],
    );

    $files = [
        '/home/user-in-isolation/something.com/shared/storage/logs/*.log',
        '/home/user-in-isolation/something.com/storage/logs/*.log',
    ];

    $this->remote->shouldReceive('tail')
        ->once()
        ->with($files, Mockery::type(Closure::class), ['-f'])
        ->andReturn([1, [
            'ls: error',
        ]]);

    $this->artisan('site:logs', ['--follow' => true])
        ->expectsQuestion('<fg=yellow>‣</> <options=bold>Which Site Would You Like To Retrieve The Logs From</>', 2);
})->throws('The requested logs could not be found or they are empty.');
