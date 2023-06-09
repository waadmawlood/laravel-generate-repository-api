<?php

namespace Waad\Repository\Repositories;

use App\Http\Requests\Pagination;
use App\Http\Requests\Unlimit;
use Illuminate\Http\Request;
use Waad\Repository\Helpers\Check;
use Waad\Repository\Interfaces\BaseInterface;
use Waad\Repository\Traits\FiltersApi;
use Waad\Repository\Traits\HasProperty;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Waad\Repository\Traits\Responsable;
use Waad\Repository\Traits\SetWhereCondition;

abstract class BaseRepository implements BaseInterface
{
    use HasProperty;
    use FiltersApi;
    use SetWhereCondition;
    use Responsable;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var QueryBuilder
     */
    public $result;


    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * indexObject
     *
     * @param Request|Pagination|Unlimit $request
     * @param array|null $where
     * @param string|null $trash
     * @param bool|null $QueryBilderEnable
     * @return EloquentBuilder|QueryBuilder|SpatieQueryBuilder|SortsQuery|mixed
     */
    public function indexObject(Request|Pagination|Unlimit $request, array|null $where = null, string|null $trash = null, bool|null $QueryBilderEnable = true)
    {
        $classname = class_basename($this->model);

        $this->result = SpatieQueryBuilder::for($this->model);
        if($QueryBilderEnable){
            $this->result = $this->result->allowedIncludes($this->getPropertiesOfModel('includeable'))
                ->allowedFilters($this->getPropertiesOfModel('filterable'));
        }

        if ($request->has('find') && filled($request->find)) {
            Check::checkAllowedProperties($this->getPropertiesOfModel('filterable'), array_keys($request->find), $classname, 'filterable');
            $this->result = $this->filterApiRequest(request(), $this->result);
        }

        if ($request->has('search') && filled($request->search)) {
            $strict = ! $request->strict;
            $this->result = $this->result->search($request->search, null, true, $strict);
        }

        if ($request->filled('select')) {
            $attributes = explode(',', str_replace(' ', '', $request->select));
            $this->result = $this->result->select($attributes);
        }

        if ($request->filled('except')) {
            $attributes = explode(',', str_replace(' ', '', $request->except));
            $this->result = $this->result->except($attributes);
        }

        if (filled($where)) {
            $where = Arr::wrap($request->where);
            foreach ($where as $condition) {
                if (!is_array($condition))
                    continue;

                $this->setWhere($this->result, $condition);
            }
        }

        if ($trash) {
            if ($trash == 'all') {
                $this->result = $this->result->withTrashed();
            } elseif ($trash == 'trashed') {
                $this->result = $this->result->onlyTrashed();
            }
        }

        if ($request->has('sort') && filled($request->sort)) {
            $orders = explode(',', $request->sort);
            foreach($orders as $order){
                $order = trim($order);
                $isDesc = Check::isDesc($order);
                $key = $isDesc ? substr($order, 1) :  $order;
                $direction = $isDesc ? 'DESC' : 'ASC';

                Check::checkAllowedProperties($this->getPropertiesOfModel('sortable'), $key, $classname, 'sortable');

                $this->result = $this->result->orderBy($key, $direction);
            }
        }

        return $this->result;
    }

    /**
     * showObject
     *
     * @param Model|int|string $object
     * @param bool|null $trash
     * @param bool|null $enableQueryBuilder
     * @return Collection|array|null
     */
    public function showObject(Model|int|string $object, bool|null $trash = false, bool|null $enableQueryBuilder = true)
    {
        if (blank($object))
            return null;

        $result = SpatieQueryBuilder::for($this->model);
        if($enableQueryBuilder){
            $result = $result->allowedIncludes($this->getPropertiesOfModel('includeable'));
        }

        if($trash){
            $result = $result->withTrashed();
        }

        if(is_int($object) || is_string($object)){
            return $result->find($object);
        }

        $primaryKey =  $object->getKeyName();

        if (request()->filled('select')) {
            $attributes = explode(',', str_replace(' ', '', request()->get('select')));
            $result = $result->select($attributes);
        }

        if (request()->filled('except')) {
            $attributes = explode(',', str_replace(' ', '', request()->get('except')));
            $result = $result->except($attributes);
        }

        return $result->find($object->$primaryKey);
    }


    /**
     * storeObject
     *
     * @param array|Model|Collection $data
     * @param bool|null $is_object
     * @return Model|int
     */
    public function storeObject(array|Model|Collection $data, bool|null $is_object = true)
    {
        $this->checkNotArray($data);

        return $is_object ?
            $this->model->fill($data)->create($data) :
            $this->model->fill($data)->insertGetId($data);
    }

    /**
     * updateObject
     *
     * @param array|Model|Collection $values
     * @param Model|int|string $object
     * @param bool|null $getObject
     * @return Model|bool|null
     */
    public function updateObject(array|Model|Collection $data, Model|int|string $object, bool|null $getObject = false)
    {
        if (!$object)
            return null;

        $this->checkNotArray($data);

        if(is_int($object) || is_string($object)){
            $updated = $this->model->where($this->model->getKeyName(), $object)->fill($data)->update($data);
        }else{
            $updated = $object ? $object->fill($data)->update($data) : null;
        }

        if (!$updated)
            return null;

        if ($getObject)
            $updated = $this->showObject($object, trash: false, enableQueryBuilder: false);

        return $updated;
    }

    /**
     * deleteObject
     *
     * @param Model|int|string $object
     * @return bool|null
     */
    public function deleteObject(Model|int|string $object)
    {
        if (!$object)
            return null;

        if(is_int($object) || is_string($object)){
            return $this->model->where($this->model->getKeyName(), $object)->forceDelete();
        }

        return $object->forceDelete();
    }

    /**
     * destroyObject
     *
     * @param Model|int|string $object
     * @return bool|null
     */
    public function destroyObject(Model|int|string $object)
    {
        if (!$object)
            return null;

        if(is_int($object) || is_string($object)){
            return $this->model->where($this->model->getKeyName(), $object)->delete();
        }

        return $object->delete();
    }

    /**
     * restoreObject
     *
     * @param Model|int|string $object
     * @return bool|null
     */
    public function restoreObject(Model|int|string $object)
    {
        if (!$object)
            return null;

        if(is_int($object) || is_string($object)){
            return $this->model->where($this->model->getKeyName(), $object)->restore();
        }

        return $object->restore();
    }
}
