<?php

namespace Waad\Repository\Traits;

use Illuminate\Http\Request;
use Waad\Repository\Helpers\FilterAPI;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

/**
 * Trait FiltersApi
 * @package Waad\laravel-generate-repository-api
 */
trait FiltersApi {

    /**
     * @param Request $request
     * @param EloquentBuilder|QueryBuilder $query
     * @return EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model
     */
    protected function filterApiRequest(Request $request, EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $queryBulder)
    {
        $filter = new FilterAPI($request, $queryBulder);

        return $filter->find();
    }

}
