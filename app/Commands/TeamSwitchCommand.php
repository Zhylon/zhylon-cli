<?php

namespace App\Commands;

use Spatie\Once;

class TeamSwitchCommand extends Command
{
    use Concerns\InteractsWithEnvironmentFiles;

    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'team:switch {team? : The team name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Switch to a different team';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $teamId = $this->askForTeam('Which team would you like to switch to');

        $team = $this->forge->team($teamId);

        $this->config->set('team', $team->id);

        Once\Cache::getInstance()->flush();

        $this->successfulStep(
            'Current team context changed successfully to <comment>['.$team->name.']</comment>'
        );
    }
}
