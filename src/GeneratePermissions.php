<?php

namespace Waad\Repository;

use Waad\Repository\Commands\Repository;

class GeneratePermissions extends Repository
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repo:permission
                        {name : Model name}
                        {--guard= : Set guard}
                        ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generate permissions of policy model";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // get name of model with upper first letter and instance name model
        $this->name = ucfirst(trim($this->argument('name')));

        if (!$this->name)
            return 0;

        // instance guard manual or default
        $this->guard =  $this->option('guard') ?? config('auth.defaults.guard');

        // add Permissions
        $this->addPermissions($this->name);

        return 1;
    }
}
