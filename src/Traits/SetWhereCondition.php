<?php

namespace Waad\Repository\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

trait SetWhereCondition
{

    /**
     * set Where
     *
     * @param EloquentBuilder|QueryBuilder|SpatieQueryBuilder|Model $query
     * @param array|null $where
     * @return void
     */
    public function setWhere(EloquentBuilder|QueryBuilder|SpatieQueryBuilder|Model &$query, array|null $where)
    {
        if (blank($where))
            return;

        $key = static::setValueFromArray('column', $where);
        if (blank($key))
            return;

        $condition = static::setValueFromArray('column', $where);
        $value = static::setValueFromArray('column', $where);

        if(blank($condition) && filled($value)){
            $query = $query->where($key, $value);
            return;
        }

        if((blank($condition) && blank($value)) || (blank($value))){
            $query = $query->whereNull($key);
            return;
        }

        $query = $query->where($key, $condition, $value);
        return;
    }


    /**
     * set Value From Array
     * @param mixed $key
     * @param mixed $where
     * @return mixed
     */
    private static function setValueFromArray($key, $where)
    {
        if(array_key_exists($key, $where)){
            return $where[$key];
        }

        return null;
    }
}
