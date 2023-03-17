<?php

namespace App\Commands;

use App\Support\PhpVersion;

class TinkerCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'tinker {site? : The site id or name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Tinker with a site';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $siteId = $this->askForSite('Which site would you like to tinker with');

        $site = $this->forge->site($this->currentServer()->id, $siteId);

        $this->step('Establishing Tinker Connection');

        // @phpstan-ignore-next-line
        $phpVersion = $site->phpVersion;
        $sitePath = $site->sitePath ?? '/home/'.$site->username.'/'.$site->name;

        return $this->remote->passthru(sprintf(
            'cd %s && %s artisan tinker',
            $sitePath,
            PhpVersion::of($phpVersion)->binary()
        ));
    }
}
