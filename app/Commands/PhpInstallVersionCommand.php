<?php

namespace App\Commands;

use App\Support\PhpVersion;

class PhpInstallVersionCommand extends Command
{
    use Concerns\InteractsWithPhp;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:install {version}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Install a new php version';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->ensurePhpExists();

        $server = $this->currentServer();

        $version = $this->argument('version');
        $versions = ['7.4', '8.0', '8.1', '8.2', '8.3', '8.4'];

        if (! is_null($version) && ! in_array($version, $versions, true)) {
            abort(1, 'PHP version needs to be one of these values: '.implode(', ', $versions).'.');
        }

        $version = $version ?: PhpVersion::of($server->phpVersion)->release();

        if(collect($this->forge->phpVersions($server->id))->firstWhere('binaryName', 'php'.$version)) {
            abort(1, 'This PHP version is already installed on your server.');
        }

        $this->step('Queuing installation of PHP '.$version);
        $this->step('Check you installed PHP versions via php:list');
    }
}
