<?php

namespace App\Commands;

class PhpVersionListCommand extends Command
{
    use Concerns\InteractsWithPhp;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'php:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the php versions';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->ensurePhpExists();

        $server = $this->currentServer();
        $this->step('Retrieving the list of php versions');

        $this->table([
            'Version', 'Status', 'On CLI', 'Is Default',
        ], collect($this->forge->phpVersions($server->id))->map(function ($phpVersion) {

            $status = $phpVersion->usedOnCli ? '<fg=green>√</>' : '<fg=red>x</>';
            $default = $phpVersion->usedAsDefault ? '<fg=green>√</>' : '<fg=red>x</>';

            return [
                $phpVersion->displayableVersion,
                $phpVersion->status,
                $status,
                $default,
            ];
        })->all());
    }
}
