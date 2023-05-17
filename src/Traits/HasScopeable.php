<?php

namespace Waad\Repository\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;
use Illuminate\Support\Facades\Cache;

trait HasScopeable
{


    /**
     * get Table All Columns
     *
     * @return array
     */
    public function getTableColumnsExists()
    {
        $cacheKey = 'MigrMod:' . filemtime(database_path('migrations')) . ':' . $this->getTable();

        return Cache::rememberForever($cacheKey, function () {
            return $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable());
        });
    }

    /**
     * Scope a query to only exclude specific Columns.
     *
     * @param EloquentBuilder|QueryBuilder|SpatieQueryBuilder|Model $query
     * @param array $exceptColumns
     * @throws \Exception
     * @return EloquentBuilder|QueryBuilder|SpatieQueryBuilder|Model
     */
    public function scopeExcept(EloquentBuilder|QueryBuilder|SpatieQueryBuilder|Model $query, array $exceptColumns)
    {
        $existingColumns = $this->getTableColumnsExists();

        if ($exceptColumns == $existingColumns) {
            throw new \Exception('You can not exclude all columns!');
        }

        return $query->select(array_diff($existingColumns, $exceptColumns));
    }
}
