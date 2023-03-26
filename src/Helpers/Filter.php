<?php

namespace Waad\Repository\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Filter
{

    /**
     * where Find same filter params but not `LIKE` it just `=`
     *
     * @param Model $model
     * @param string $needle
     * @param mixed $value
     * @return Model|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Concerns\QueriesRelationships
     */
    public static function whereFind(Model $model, string $needle, mixed $value)
    {
        $keys = explode('.', $needle);

        if(count($keys) == 0){
            return $model;
        }

        if(count($keys) == 1){
            return $model->where($needle, $value);
        }

        return self::useWhereRelation($model, collect($keys), $value);
    }


    /**
     * use WhereRelation used in laravel 8 and above
     *
     * @param Model $model
     * @param Collection $keys
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Concerns\QueriesRelationships
     */
    private static function useWhereRelation(Model $model, Collection $keys, mixed $value)
    {
        $last_item = $keys->pop();

        return $model->whereRelation(implode('.', $keys->toArray()), $last_item, $value);
    }
}
