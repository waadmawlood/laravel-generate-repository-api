<?php

namespace Waad\Repository\Helpers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

/**
 * Class FilterAPI
 * @package Waad\laravel-generate-repository-api
 */
class FilterAPI
{

    /**
     * @var Request
     */
    private $request;

    /**
     * @var EloquentBuilder|QueryBuilder
     */
    private $query;

    /**
     * @param Request $request
     * @param EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query
     */
    public function __construct(Request $request, EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query)
    {
        $this->request = $request;
        $this->query = $query;
    }

    /**
     * @return EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model
     */
    public function find()
    {
        if ($this->request->has('find')) {
            foreach ($this->request->input('find') as $field => $value) {
                $this->query = $this->applyFieldFilter($this->query, $field, $value);
            }
        }

        return $this->query;
    }

    /**
     * Resolves :or(||): and then :and(&&): links
     *
     * @param EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query
     * @param string $field
     * @param mixed $value
     * @return EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model
     */
    private function applyFieldFilter(EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query, string $field, mixed $value)
    {
        $query = $this->resolveOrLinks($query, $field, $value);

        return $query;
    }

    /**
     * Resolves :or: links and then resolves the :and: links in the resulting sections
     *
     * @param EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query
     * @param string $field
     * @param mixed $value
     * @return EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model
     */
    private function resolveOrLinks(EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query, string $field, mixed $value)
    {
        $filters = explode(':or:', $value);
        if (count($filters) > 1) {

            $that = $this;
            $query->where(function ($query) use ($filters, $field, $that) {
                $first = true;
                foreach ($filters as $filter) {
                    $verb = $first ? 'where' : 'orWhere';
                    $query->$verb(function ($query) use ($field, $filter, $that) {
                        $query = $that->resolveAndLinks($query, $field, $filter);
                    });
                    $first = false;
                }
            });
        } else {

            $query = $this->resolveAndLinks($query, $field, $value);
        }

        return $query;
    }

    /**
     * @param EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query
     * @param string $field
     * @param mixed $value
     * @return EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model
     */
    private function resolveAndLinks(EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query, string $field, mixed $value)
    {
        $filters = explode(':and:', $value);
        foreach ($filters as $filter) {
            $query = $this->applyFilter($query, $field, $filter);
        }

        return $query;
    }

    /**
     * Applies a single filter to the query
     *
     * @param EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query
     * @param string $field
     * @param string $filter
     * @param bool|null $or
     * @return EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model
     */
    private function applyFilter(EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query, string $field, string $filter, bool|null $or = false)
    {
        $filter = explode(':', $filter);
        if (count($filter) > 1) {
            $operator = $this->getFilterOperator($filter[0]);
            $value = $this->replaceWildcards($filter[1]);
        } else {
            $operator = '=';
            $value = $this->replaceWildcards($filter[0]);
        }

        $fields = explode('.', $field);
        if (count($fields) > 1) {
            return $this->applyNestedFilter($query, $fields, $operator, $value, $or);
        } else {
            return $this->applyWhereClause($query, $field, $operator, $value, $or);
        }
    }

    /**
     * Applies a nested filter
     * Nested filters are filters on field on related models
     *
     * @param EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query
     * @param array $fields
     * @param string $operator
     * @param mixed $value
     * @param bool|null $or
     * @return EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model
     */
    private function applyNestedFilter(EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query, array $fields, string $operator, mixed $value, bool|null $or = false)
    {
        $relation_name = implode('.', array_slice($fields, 0, count($fields) - 1));
        $relation_field = end($fields);
        if ($relation_name[0] == '!') {
            $relation_name = substr($relation_name, 1, strlen($relation_name));

            $that = $this;

            return $query->whereHas($relation_name, function ($query) use ($relation_field, $operator, $value, $that, $or) {
                $query = $that->applyWhereClause($query, $relation_field, $operator, $value, $or);
            }, '=', 0);
        }

        $that = $this;

        return $query->whereHas($relation_name, function ($query) use ($relation_field, $operator, $value, $that, $or) {
            $query = $that->applyWhereClause($query, $relation_field, $operator, $value, $or);
        });
    }

    /**
     * Applies a where clause
     * Is used by applyFilter and applyNestedFilter to apply the clause to the query
     *
     * @param EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @param bool|null $or
     * @return EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model
     */
    private function applyWhereClause(EloquentBuilder|QueryBuilder|Relation|SpatieQueryBuilder|Model $query, string $field, string $operator, mixed $value, bool|null $or = false)
    {
        $verb = $or ? 'orWhere' : 'where';
        $in_verb = $or ? 'orWhereIn' : 'whereIn';
        $not_in_verb = $or ? 'orWhereNotIn' : 'whereNotIn';
        $null_verb = $or ? 'orWhereNull' : 'whereNull';
        $not_null_verb = $or ? 'orWhereNotNull' : 'whereNotNull';

        $value = $this->base64decodeIfNecessary($value);

        switch ($value) {
            case 'today':
                return $query->$verb($field, 'like', Carbon::now()->format('Y-m-d') . '%');
            case 'nottoday':
                return $query->$verb(function ($q) use ($field) {
                    $q->where($field, 'not like', Carbon::now()->format('Y-m-d') . '%')
                        ->orWhereNull($field);
                });
            case 'null':
                return $query->$null_verb($field);
            case 'notnull':
                return $query->$not_null_verb($field);
            default:
                if ($operator == 'in') {
                    return $query->$in_verb($field, explode(',', $value));
                }
                if ($operator == 'notin') {
                    return $query->$not_in_verb($field, explode(',', $value));
                }

                return $query->$verb($field, $operator, $value);
        }
    }

    /**
     * Replaces * wildcards with %
     *
     * @param string $value
     * @return string
     */
    private function replaceWildcards(string $value)
    {
        return str_replace('*', '%', $value);
    }

    /**
     * Translates operators to SQL
     *
     * @param string $filter
     * @return string
     */
    private function getFilterOperator(string $filter)
    {
        $operator = str_replace('notlike', 'not like', $filter);
        $operator = str_replace('ge', '>=', $operator);
        $operator = str_replace('gt', '>', $operator);
        $operator = str_replace('le', '<=', $operator);
        $operator = str_replace('lt', '<', $operator);
        $operator = str_replace('ne', '!=', $operator);
        $operator = str_replace('eq', '=', $operator);

        return $operator;
    }

    /**
     * Searches for b64(some based 64 encoded string)
     *
     * If found, returns the decoded content
     * If not returns the original value
     *
     * @param mixed $value
     * @return bool|string
     */
    private function base64decodeIfNecessary(mixed $value)
    {
        preg_match("/\{\{b64\((.*)\)\}\}/", $value, $matches);
        if ($matches) {
            return base64_decode($matches[1]);
        } else {
            return $value;
        }
    }
}
