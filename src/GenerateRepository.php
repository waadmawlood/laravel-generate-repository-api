<?php

namespace Waad\Repository;

use Illuminate\Support\Str;
use Waad\Repository\Commands\Repository;
use Waad\Repository\Helpers\Check;

class GenerateRepository extends Repository
{
    private const PATH_interface = 'app/Interfaces';
    private const PATH_controller_api = 'app/Http/Controllers/Api';
    private const PATH_model = 'app/Models';
    private const PATH_repository = 'app/Repositories';
    private const PATH_request = 'app/Http/Requests';
    private const PATH_properties = 'app/Properties';
    private const PATH_policy = 'app/Policies';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repo:model
                        {name : Repo name}
                        {--a : All model, controller, repository, migration, properties, policy, requests, roles}
                        {--c : Create Controller With Repository}
                        {--m : Create Migration}
                        {--permission : Add permissions To Database}
                        {--r : Create Route}
                        {--route= : Select route file}
                        {--resource : Create Resource Route}
                        {--ns : Not Soft Delete Model}
                        {--p : Create Policy of Controller}
                        {--force : Allows to override existing Repository}
                        {--guard= : Set guard}
                        ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Generate repository : Model, Controller, Repository, Interface, Properties, Policy";

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

        // conver model name to plural and lower latters
        $table = strtolower(Str::plural(Str::snake($this->name)));

        // initial to create (pagination, unlimit) Request
        $this->initial(self::PATH_request);

        // instance force argument
        $this->force = Check::trueOrFalse($this->option('force'), !$this->option('force'), false);

        // instance force argument
        $this->is_resource = Check::trueOrFalse($this->option('resource'), !$this->option('resource'), false);

        // instance soft delete
        $is_soft_delete = Check::trueOrFalse(!$this->option('ns'), $this->option('ns'));

        // instance Policy
        $policy = Check::trueOrFalse($this->option('p'), !$this->option('p'), false);

        // instance guard manual or default
        $this->guard =  $this->option('guard') ?? config('auth.defaults.guard');

        // instance route file
        $route_file = $this->option('route') ?? 'api';

        // --a create all component
        if ($this->option('a')) {

            // create migration
            $this->createMigration($table);

            // create model
            $this->createModel(self::PATH_model, $this->name, $is_soft_delete);

            // create properties of model
            $this->createProperties(self::PATH_properties, $this->name, $properties = true, $related = true, $scope = true, $accessorMutator = true, $is_soft_delete);

            // create policy
            $this->createPolicy(self::PATH_policy, $this->name);

            // create store model request
            $this->createRequest(self::PATH_request, $this->name);

            // update store model request
            $this->createRequest(self::PATH_request, $this->name, 'Update');

            // create controller
            $this->createController(self::PATH_controller_api, $this->name, $is_soft_delete, true);

            // create repository
            $this->createRepository(self::PATH_repository, $this->name);

            // create interface
            $this->createInterface(self::PATH_interface, $this->name);

            // add Route
            $this->CreateRoute($this->name, $route_file);

            // add Permissions
            $this->addPermissions($this->name);

            return 1;
        }

        // create model
        $this->createModel(self::PATH_model, $this->name, $is_soft_delete);

        // create properties of model
        $this->createProperties(self::PATH_properties, $this->name, $properties = true, $related = true, $scope = true, $accessorMutator = true, $is_soft_delete);


        // create controller
        if ($this->option('c')) {
            $this->createController(self::PATH_controller_api, $this->name, $is_soft_delete, $policy);

            // create store model request
            $this->createRequest(self::PATH_request, $this->name);

            // update store model request
            $this->createRequest(self::PATH_request, $this->name, 'Update');

            // create repository
            $this->createRepository(self::PATH_repository, $this->name);

            // create interface
            $this->createInterface(self::PATH_interface, $this->name);
        }

        // create migration
        if ($this->option('m')) {
            $this->createMigration($table);
        }

        // add Route
        if ($this->option('r')) {
            $this->CreateRoute($this->name, $route_file);
        }

        // create policy
        if ($this->option('p')) {
            $this->createPolicy(self::PATH_policy, $this->name, $policy);
        }

        // add Permissions
        if ($this->option('permission') || $this->force) {
            $this->addPermissions($this->name);
        }

        return 1;
    }
}
