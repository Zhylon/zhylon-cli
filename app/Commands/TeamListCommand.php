<?php

namespace App\Commands;

class TeamListCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'team:list';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List the teams';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->step('Retrieving the list of teams');

        $this->table([
            'ID', 'Name', 'Owner', 'Personal',
        ], collect($this->forge->teams())->map(function ($team) {

            $personal = $team->personal_team ? '<fg=green>√</>' : '<fg=red>x</>';

            return [
                $team->id,
                $team->name,
                $team->owner['name'],
                $personal,
            ];
        })->all());
    }
}
