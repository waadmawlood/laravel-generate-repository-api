<?php

namespace Waad\Repository;

use Waad\Repository\Commands\Validation;
use Waad\Repository\Helpers\Check;

class GenerateValidation extends Validation
{

    private const PATH_dto = 'app/DTO';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repo:validation
                            {model : Model Name}
                            {--ndto : Not Create DTO}
                            ';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto generate validation of model';

    /**
     * path of requests files
     */
    private const PATH_request = 'app/Http/Requests';


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->model = ucfirst($this->argument('model'));

        $isDto = Check::trueOrFalse(!$this->option('ndto'), $this->option('ndto'));

        /**
         * create fields of validations model.
         */
        $validation = $this->generateValidation(self::PATH_request);
        $this->createPutRequest(self::PATH_request);
        $this->createPutRequest(self::PATH_request, 'Update');
        $this->createDto(self::PATH_dto, $isDto);

        return $validation ? 1 : 0;
    }
}
