<?php

namespace App\Commands;

class TeamCurrentCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'team:current';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Determine your current team';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->ensureCurrentTeamIsSet();

        $team = $this->forge->team(
            $this->config->get('team')
        );

        $this->successfulStep(
            'You are currently within the <comment>['.$team->name.']</comment> team context.'
        );
    }
}
