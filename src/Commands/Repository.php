<?php

namespace Waad\Repository\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Waad\Repository\Helpers\Path;

class Repository extends Command
{

    protected $name;
    protected $guard;
    protected $force;
    protected $is_resource;
    protected const PATH_migrations = "database/migrations";

    public $result = [];


    /**
     * initial function check (pagination, unlimit) Request Exists
     *
     * @return void
     */
    public function initial($dir_request)
    {
        $this->comment("Generate repository......");
        $this->comment("");
        $this->createPaginationRequest($dir_request);
        $this->createUnlimitRequest($dir_request);
    }


    /**
     * generate migration
     *
     * @param string $table
     * @return Repository
     */
    public function createMigration(string $table)
    {
        $files = scandir(Path::getPath(self::PATH_migrations));

        $exist = false;
        foreach ($files as $file) {
            if (str_contains($file, "create_{$table}_table")) {
                if ($this->force)
                    unlink(Path::getPath(self::PATH_migrations . "/{$file}"));

                $exist = true;
                break;
            }
        }

        if ($this->force || !$exist) {
            try {
                Artisan::call("make:migration create_{$table}_table");
                $this->comment("Create migration create_{$table}_table");
            } catch (\Throwable $th) {
                return $this;
            }
        }

        return $this;
    }

    /**
     * generate model
     *
     * @return Repository
     */
    public function createModel($dir, $name, $is_soft_delete = true)
    {
        $path = Path::getPath("{$dir}/{$name}.php");

        $contents = Path::getContentStub("model.stub");

        if (!$contents)
            return $this;

        $options = [
            "{{use Illuminate\Database\Eloquent\SoftDeletes;}}" => $is_soft_delete ? "use Illuminate\Database\Eloquent\SoftDeletes;" : '',
            "{{use SoftDeletes;}}" => $is_soft_delete ? "use SoftDeletes;" : '',
            "{{name}}" => $name,
        ];

        Path::replaceContents($contents, $options);

        if (!file_exists($path) || $this->force) {
            if (Path::createFile($path, $contents));
            $this->comment("Create Model {$name} => path : {$path}");
        }

        return $this;
    }


    /**
     * create Properties of Model
     *
     * @param string $dir
     * @param string $name
     * @param bool|null $properties
     * @param bool|null $related
     * @param bool|null $scope
     * @param bool|null $accessorMutator
     * @param bool|null $is_soft_delete
     * @return Repository
     */
    public function createProperties(string $dir, string $name, bool|null $properties = true, bool|null $related = true, bool|null $scope = true, bool|null $accessorMutator = true, bool|null $is_soft_delete = true)
    {
        // create {Properties / Model Name} Director in {app}
        $path = Path::getPath("{$dir}/{$name}");
        Path::createDir($path);

        if ($properties)
            $this->createPropertable($dir, $name, $is_soft_delete);

        if ($related)
            $this->createRelatable($dir, $name);

        if ($scope)
            $this->createScopable($dir, $name);

        if ($accessorMutator) {
            $this->createAccessorable($dir, $name);
            $this->createMutatorable($dir, $name);
        }

        return $this;
    }


    /**
     * create Propertable Trait
     *
     * @param string $dir
     * @param string $name
     * @param bool $is_soft_delete
     * @return Repository
     */
    private function createPropertable(string $dir, string $name, bool $is_soft_delete)
    {
        $path = Path::getPath("{$dir}/{$name}/{$name}Propertable.php");

        $contents = Path::getContentStub("propertable.stub");

        if (!$contents)
            return $this;

        $options = [
            "{{name_plural}}" => Str::of($name)->snake()->plural()->toString(),
            "{{'deleted_at'}}" => $is_soft_delete ? "'deleted_at'" : '',
            "{{name}}" => $name,
        ];

        Path::replaceContents($contents, $options);

        if (!file_exists($path) || $this->force) {
            if (Path::createFile($path, $contents));
            $this->comment("Create Propertable Model {$name} => path : {$path}");
        }

        return $this;
    }


    private function createRelatable($dir, $name)
    {
        $path = Path::getPath("{$dir}/{$name}/{$name}Relatable.php");

        $contents = Path::getContentStub("relatable.stub");

        if (!$contents)
            return $this;

        $options = [
            "{{name}}" => $name,
        ];

        Path::replaceContents($contents, $options);

        if (!file_exists($path) || $this->force) {
            if (Path::createFile($path, $contents));
            $this->comment("Create Relatable Model {$name} => path : {$path}");
        }

        return $this;
    }


    private function createScopable($dir, $name)
    {
        $path = Path::getPath("{$dir}/{$name}/{$name}Scopable.php");

        $contents = Path::getContentStub("scopable.stub");

        if (!$contents)
            return $this;

        $options = [
            "{{name}}" => $name,
        ];

        Path::replaceContents($contents, $options);

        if (!file_exists($path) || $this->force) {
            if (Path::createFile($path, $contents));
            $this->comment("Create Scopable Model {$name} => path : {$path}");
        }

        return $this;
    }


    private function createAccessorable($dir, $name)
    {
        $path = Path::getPath("{$dir}/{$name}/{$name}Accessorable.php");

        $contents = Path::getContentStub("accessor.stub");

        if (!$contents)
            return $this;

        $options = [
            "{{name}}" => $name,
        ];

        Path::replaceContents($contents, $options);

        if (!file_exists($path) || $this->force) {
            if (Path::createFile($path, $contents));
            $this->comment("Create Accessorable Model {$name} => path : {$path}");
        }

        return $this;
    }


    private function createMutatorable($dir, $name)
    {
        $path = Path::getPath("{$dir}/{$name}/{$name}Mutatorable.php");

        $contents = Path::getContentStub("mutatorable.stub");

        if (!$contents)
            return $this;

        $options = [
            "{{name}}" => $name,
        ];

        Path::replaceContents($contents, $options);

        if (!file_exists($path) || $this->force) {
            if (Path::createFile($path, $contents));
            $this->comment("Create Mutatorable Model {$name} => path : {$path}");
        }

        return $this;
    }

    /**
     * generate Policy of model, controller
     *
     * @return Repository
     */
    public function createPolicy($dir, $name, $policy = true)
    {
        if (!$policy)
            return $this;

        $path = Path::getPath("{$dir}/{$name}Policy.php");
        Path::createDir($dir);

        $contents = Path::getContentStub("policy.stub");

        if (!$contents)
            return $this;

        $guards = array_map('trim', explode(',', trim($this->guard)));

        $options = [
            "{{name}}" => $name,
            "{{name_snake}}" => Str::snake($name),
            "{{guard}}" => sprintf("'%s'", implode("', '",$guards)),
        ];

        Path::replaceContents($contents, $options);

        if (!file_exists($path) || $this->force) {
            if (Path::createFile($path, $contents));
            $this->comment("Create Policy {$name} => path : {$path}");
        }

        return $this;
    }


    /**
     * generate Controller
     *
     * @param string $dir
     * @param string $name
     * @param bool|null $is_soft_delete
     * @param bool|null $is_policy
     * @return Repository
     */
    public function createController(string $dir, string $name, bool|null $is_soft_delete = true, bool|null $is_policy = false)
    {
        $path = Path::getPath("{$dir}/{$name}Controller.php");
        Path::createDir($dir);

        if ($is_soft_delete)
            $contents = Path::getContentStub("controller-api.stub");
        else
            $contents = Path::getContentStub("controller-api-no-soft.stub");


        if (!$contents)
            return $this;

        $policyContent = $is_policy ? '$this->authorizeResource('. $name .'::class, \''. Str::snake($name) .'\');' : "";
        $options = [
            "{{name}}" => $name,
            "{{name_snake}}" => Str::snake($name),
            "{{policy}}" => $policyContent,
        ];

        Path::replaceContents($contents, $options);

        if (!file_exists($path) || $this->force) {
            if (Path::createFile($path, $contents));
            $this->comment("Create Controller Api {$name}Controller => path : {$path}");
        }

        return $this;
    }


    /**
     * generate Repository
     *
     * @param string $dir
     * @param string $name
     * @return Repository
     */
    public function createRepository(string $dir, string $name)
    {
        $path = Path::getPath("{$dir}/{$name}Repository.php");
        Path::createDir($dir);

        $contents = Path::getContentStub("repository.stub");

        if (!$contents)
            return $this;

        $options = [
            "{{name}}" => $name,
        ];

        Path::replaceContents($contents, $options);

        if (!file_exists($path) || $this->force) {
            if (Path::createFile($path, $contents));
            $this->comment("Create Repository {$name}Repository => path : {$path}");
        }

        return $this;
    }

    /**
     * generate Interface
     *
     * @param string $dir
     * @param string $name
     * @return Repository
     */
    public function createInterface(string $dir, string $name)
    {
        $path = Path::getPath("{$dir}/{$name}Interface.php");
        Path::createDir($dir);

        $contents = Path::getContentStub("interface.stub");

        if (!$contents)
            return $this;

        $options = [
            "{{name}}" => $name,
        ];

        Path::replaceContents($contents, $options);

        if (!file_exists($path) || $this->force) {
            if (Path::createFile($path, $contents));
            $this->comment("Create Interface {$name}Interface => path : {$path}");
        }

        return $this;
    }

    /**
     * generate Request
     *
     * @param string $dir
     * @param string $name
     * @param string|null $operation
     * @return Repository
     */
    public function createRequest(string $dir, string $name, string|null $operation = 'Store')
    {
        $path = Path::getPath("{$dir}/{$name}/{$operation}{$name}Request.php");
        Path::createDir("{$dir}/{$name}");

        $contents = Path::getContentStub("request-{$operation}.stub");

        if (!$contents)
            return $this;

        $options = [
            "{{name}}" => $name,
        ];

        Path::replaceContents($contents, $options);

        if (!file_exists($path) || $this->force) {
            if (Path::createFile($path, $contents));
            $this->comment("Create {$operation} {$name} Request => path : {$path}");
        }

        return $this;
    }


    /**
     * Create Route
     *
     * @param string $name
     * @param string $file_route
     * @return Repository
     */
    public function CreateRoute(string $name, string $file_route)
    {
        $path = Path::getPath("routes/{$file_route}.php");

        $kebabName = Str::plural(Str::kebab($name));

        if ($this->is_resource) {
            $route = "Route::resource('{$kebabName}', '{$name}Controller');";
        } else {
            $route = "Route::apiResource('{$kebabName}', '{$name}Controller');";
        }

        $contents = file_get_contents($path);
        $route_exists = Str::contains($contents, $route);

        if (file_exists($path) && !$route_exists) {
            $contents .= "\n" . $route;
            $status = file_put_contents($path, $contents);
            $status ? $this->comment('Add Route inside api routes/api.php') : null;
        }

        return $this;
    }



    /**
     * generate pagination request form validation
     *
     * @return Repository
     */
    public function createPaginationRequest($dir)
    {
        $path = Path::getPath("$dir/Pagination.php");

        Path::createDir($dir);

        if (!file_exists($path)) {

            $contents = Path::getContentStub("paginationRequest.stub");
            Path::createFile($path, $contents);
            $this->comment('Create Pagination Request Form file');
        }

        return $this;
    }


    /**
     * generate unlimit request form validation
     *
     * @return Repository
     */
    public function createUnlimitRequest($dir)
    {
        $path = Path::getPath("$dir/Unlimit.php");

        Path::createDir($dir);

        if (!file_exists($path)) {

            $contents = Path::getContentStub("unlimitRequest.stub");
            Path::createFile($path, $contents);
            $this->comment('Create Unlimit Request Form file');
        }

        return $this;
    }

    /**
     * add Permissions to Database
     *
     * @param string $name
     * @return Repository
     */
    public function addPermissions(string $name)
    {
        $permissionModel = new (config('permission.models.permission', \Spatie\Permission\Models\Permission::class));

        if (blank($permissionModel))
            return $this;

        // Check is a string of name model
        if (is_string($permissionModel))
            $permissionModel =  new $permissionModel;

        // Check model is exists
        if (!is_object($permissionModel))
            return $this;

        // Reset cached roles and permissions
        if (class_exists(\Spatie\Permission\PermissionRegistrar::class))
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $snake_name = Str::snake($name);

        $permissions = [
            "{$snake_name}_list",
            "{$snake_name}_create",
            "{$snake_name}_update",
            "{$snake_name}_delete",
            "{$snake_name}_restore",
            "{$snake_name}_forcedelete",
        ];

        $result_permission = array();
        $guards = explode(',', trim($this->guard));

        foreach ($guards as $guard) {
            if (blank($guard))
                continue;

            $guard = trim($guard);

            foreach ($permissions as $permission) {
                if (!$permissionModel->where('name', $permission)->where('guard_name', $guard)->exists()) {
                    Artisan::call("permission:create-permission '{$permission}' {$guard}");
                    $result_permission[] = sprintf('%s (%s)', $permission, $guard);
                }
            }
        }

        $result_permission = implode(', ', $result_permission);
        blank($result_permission) ? null : $this->comment("Add Permissions to database : `$result_permission`");

        return $this;
    }
}
