<?php

namespace Waad\Repository\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Waad\Repository\Helpers\DtoHelper;
use Waad\Repository\Helpers\Path;

class Validation extends Command
{

    /**
     * @var string
     */
    protected string $model;
    protected array $result = array();
    protected array $resultDto = array();

    private const Blocked = array('created_at', 'updated_at', 'email_verified_at', 'remember_token', 'deleted_at');
    private const Images_array = array('image', 'images', 'avatar', 'cover');
    private const Files_array = array('file', 'files', 'file_path');


    /**
     * Check model is exists.
     *
     * @return bool
     */
    private function modelExists()
    {
        return file_exists(Path::getPath("app/Models/{$this->model}.php"));
    }

    /**
     * get model class
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    private function modelClass()
    {
        return new ("App\\Models\\{$this->model}");
    }

    /**
     * generate model
     *
     * @return Validation
     */
    public function createDto($dir, $dto = true)
    {
        if (blank($this->resultDto) || !$dto)
            return $this;

        Path::createDir(Path::getPath("{$dir}/{$this->model}"));
        $path = Path::getPath("{$dir}/{$this->model}/{$this->model}Dto.php");

        $contents = DtoHelper::getClassDto($this->model, $this->resultDto);

        if (!$contents)
            return $this;

        if (!file_exists($path)) {
            if (Path::createFile($path, $contents));
            $this->comment("Create DTO of Model {$this->model} => path : {$path}");
        }

        return $this;
    }

    /**
     * get table name using model
     *
     * @return string
     */
    private function tableName()
    {
        return $this->modelClass()->getTable();
    }

    /**
     * get Columns of table
     *
     * @return array
     */
    private function getColumns()
    {
        return DB::select("describe {$this->tableName()}");
    }

    /**
     * generate Validation
     *
     * @param string $path
     * @return bool
     */
    public function generateValidation($path)
    {
        $path = Path::getPath($path);

        if (!$this->modelExists())
            return false;

        $columns = $this->getColumns();
        $this->processColumns($columns);

        return (bool) count($this->result);
    }


    /**
     * Create And Put validations in Request File.
     *
     * @param string $dir
     * @param string|null $operation
     * @return bool
     */
    public function createPutRequest(string $dir, string|null $operation = 'Store')
    {
        if (blank($this->result))
            return false;

        $path = Path::getPath("{$dir}/{$this->model}/{$operation}{$this->model}Request.php");
        Path::createDir("{$dir}/{$this->model}");

        // if file no exists
        if (!file_exists($path)) {
            $contents = Path::getContentStub("request-{$operation}.stub");
            if (!$contents)
                return false;

            $options = [
                "{{name}}" => $this->model,
            ];

            Path::replaceContents($contents, $options);
            Path::createFile($path, $contents);
        }

        // put validation
        $contents = file_get_contents($path);
        if (!$contents)
            return false;

        $textValidations = '';
        $index = 0;
        foreach ($this->result as $validation) {
            $validation = $operation == 'Update' ?
                str_replace('required', 'nullable', $validation) :
                $validation;

            $validation = str_replace("=>", "' => '", $validation);
            $newLine = $index == (count($this->result) - 1) ? "" : "\n";
            $textValidations .= $index == 0 ? "'$validation',$newLine" : "\t\t\t'$validation',$newLine";
            $index++;
        }

        $options = [
            "// do not remove this comment before repo:validation" => $textValidations,
        ];

        Path::replaceContents($contents, $options);

        if (file_exists($path)) {
            file_put_contents($path, $contents);
        }

        $this->line("Put Validations {$operation} {$this->model} Request => path : {$path}");

        return true;
    }


    /**
     * get column type.
     *
     * @param string $column
     * @return array|bool
     */
    public function getColumnType($column)
    {
        if (str_contains($column, 'datetime') || str_contains($column, 'timestamp')) return ['date_format:Y-m-d H:i:s', 'stringDto'];
        if (str_contains($column, 'tinyint')) return ['boolean', true];
        if (str_contains($column, 'year')) return ['date_format:Y', "stringDto"];
        if (str_contains($column, 'date')) return ['date_format:Y-m-d', "stringDto"];
        if (str_contains($column, 'time')) return ['date_format:H:i:s', "stringDto"];
        if (str_contains($column, 'float') || str_contains($column, 'double') || str_contains($column, 'decimal')) return ['numeric', 1.1];
        if (str_contains($column, 'varchar') || str_contains($column, 'char') || str_contains($column, 'binary') || str_contains($column, 'tinyblob')) return ['string|max:255', "stringDto"];
        if (str_contains($column, 'bigint') || str_contains($column, 'mediumint') || str_contains($column, 'bit') || str_contains($column, 'real') || str_contains($column, 'int') || str_contains($column, 'int')) return ['integer', 2];
        if (str_contains($column, 'blob') || str_contains($column, 'longtext') || str_contains($column, 'mediumText') || str_contains($column, 'text') || str_contains($column, 'multiLineString') || str_contains($column, 'enum')) return ['string|max:65535', "stringDto"];
        return false;
    }

    /**
     * process Columns
     * @param array $columns
     * @return Validation
     */
    private function processColumns(array $columns)
    {
        $count = count($columns);

        $this->line("");
        $this->comment("fillable columns => table $count Counts columns");
        $this->comment("[");

        foreach ($columns as $column) {

            if ($column->Key == 'PRI' || in_array($column->Field, self::Blocked))
                continue;

            $validation = array();

            if ($column->Null == 'YES' || $column->Default != null) {
                array_push($validation, "nullable");
            } else {
                array_push($validation, "required");
            }

            if (in_array($column->Field, self::Images_array)) {
                array_push($validation, 'image|mimes:jpg,bmp,png');
                $this->resultDto = array_merge($this->resultDto, [$column->Field => 'stringDto']);
            } else if (in_array($column->Field, self::Files_array)) {
                array_push($validation, 'file');
                $this->resultDto = array_merge($this->resultDto, [$column->Field => 'stringDto']);
            } else if ($column->Field == 'email|max:255') {
                array_push($validation, 'email');
                $this->resultDto = array_merge($this->resultDto, [$column->Field => 'stringDto']);
            } else if ($column->Field == 'password') {
                array_push($validation, 'min:6|max:255');
                $this->resultDto = array_merge($this->resultDto, [$column->Field => 'stringDto']);
            } else {
                $valedate = $this->getColumnType($column->Type);
                $valedate == false ? null : array_push($validation, $valedate[0]);
                $this->resultDto = array_merge($this->resultDto, [$column->Field => $valedate[1]]);
            }

            if ($column->Key == 'UNI') {
                array_push($validation, "unique:" . $this->tableName() . "," . $column->Field . "");
            }

            if ($column->Key == 'MUL') {
                $col = Str::before($column->Field, "_id");
                $table_name = Str::plural($col);
                $mul_value = "exists:{$table_name},id";
                array_push($validation, $mul_value);
            }

            array_push($this->result, "$column->Field=>" . implode("|", $validation) . "");

            $this->comment("    '$column->Field',");
        }

        // Add created_at and updated_at inside DTO.
        if($this->modelClass()->timestamps){
            $this->resultDto = array_merge($this->resultDto, ['created_at' => 'stringDto']);
            $this->resultDto = array_merge($this->resultDto, ['updated_at' => 'stringDto']);
        }

        $this->comment("]");
        $this->line("");

        return $this;
    }
}
