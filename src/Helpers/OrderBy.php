<?php

namespace Waad\Repository\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

class OrderBy
{
    /**
     * order by elouqent by main model or related of model
     *
     * @param EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query
     * @param array $sortParts
     * @param string|null $sortType
     * @throws \InvalidArgumentException
     * @return EloquentBuilder|\Illuminate\Database\Eloquent\Concerns\QueriesRelationships|mixed
     */
    public static function order(EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query, array $sortParts)
    {
        $isNested = false;
        if (count($sortParts) > 1) {

            $query = static::orderMultiple($query, $sortParts, $isNested);
        } elseif (count($sortParts) === 0) {

            return $query;
        } else {

            $query = static::orderSingle($query, current($sortParts), $isNested);
        }

        if ($isNested) {

            if (request()->filled('select')) {
                $selects = explode(',', str_replace(' ', '', request()->get('select')));
                array_walk($selects, function (&$item) use ($query) {
                    $item = sprintf('%s.%s', $query->getModel()->getTable(), $item);
                });
            } else {
                $selects = sprintf('%s.*', $query->getModel()->getTable());
            }

            $counts = static::getCountIncluded();
            $query = $query->select($selects)->withCount($counts);
        }

        return $query->distinct();
    }

    /**
     * Order Single
     *
     * @param EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query
     * @param string $sortPart
     * @param bool $isNested
     * @return QueryBuilder
     */
    private static function orderSingle(EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query, string $sortPart, bool &$isNested)
    {
        $isDesc = Check::isDesc($sortPart);
        $sortType = $isDesc ? 'DESC' : 'ASC';
        $key = $isDesc ? substr($sortPart, 1) : $sortPart;

        $sortParts = explode('.', $key);
        if (count($sortParts) == 1) {
            return $query->orderBy(current($sortParts), $sortType);
        }

        $isNested = true;

        $model = $query->getModel();
        $column = array_pop($sortParts);
        $relationships = array();
        $joins = array();
        foreach ($sortParts as $value) {
            $joins[] = static::generateJoin($model, $model->$value());
            $model = $model->$value()->getModel();
            $relationships[] = $model->getTable();
        }

        foreach ($joins as $join) {
            $query = $query->join($join['join'], $join['from'], $join['operator'], $join['to']);
        }

        return $query->orderBy(sprintf("%s.%s", end($relationships), $column), $sortType);
    }

    /**
     * Order Multiple
     *
     * @param EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query
     * @param array $sortParts
     * @param bool $isNested
     * @return EloquentBuilder|Model|QueryBuilder|Relation|SpatieQueryBuilder
     */
    private static function orderMultiple(EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query, array $sortParts, bool &$isNested)
    {
        foreach ($sortParts as $sortPart) {
            $query = static::orderSingle($query, $sortPart, $isNested);
        }

        return $query;
    }

    /**
     * Generate Join String Sql
     *
     * @param Model $model
     * @param Relation|string|null $relationship
     * @throws \Exception
     * @return array
     */
    private static function generateJoin(Model $model, Relation|string|null $relationship)
    {
        if ($relationship instanceof Relation) {

            if ($relationship instanceof HasOne || $relationship instanceof HasMany) {

                $parent_column = $relationship->getLocalKeyName();
                $child_column = $relationship->getForeignKeyName();
            } elseif ($relationship instanceof BelongsTo) {

                $parent_column = $relationship->getForeignKeyName();
                $child_column = $relationship->getOwnerKeyName();
            } else {

                throw new \Exception('Relationship must be HasOne or BelongsTo');
            }

            return [
                'join' => $relationship->getModel()->getTable(),
                'from' => sprintf("%s.%s", $model->getTable(), $parent_column),
                'operator' => '=',
                'to' => sprintf("%s.%s", $relationship->getModel()->getTable(), $child_column),
            ];
        } else {

            throw new \Exception('Relationship not found');
        }
    }

    /**
     * Return the array of words ending with "Count"
     *
     * @return array<string>
     */
    private static function getCountIncluded()
    {
        $include = request()->get('include', []);

        if (blank($include))
            return [];

        $words = explode(',', trim($include));

        $wordsWithCount = array();

        foreach ($words as $word) {
            if (substr($word, -5) === "Count") {
                $wordsWithCount[] = substr($word, 0, -5);
            }
        }

        return $wordsWithCount;
    }
}
