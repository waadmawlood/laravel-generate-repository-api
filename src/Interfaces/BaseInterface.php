<?php

namespace Waad\Repository\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder as SpatieQueryBuilder;

interface BaseInterface
{
    /**
     * indexObject
     *
     * @param Request $request
     * @param array|null $where
     * @param string|null $trash
     * @param bool|null $QueryBilderEnable
     * @return SpatieQueryBuilder
     */
    public function indexObject(Request $request, array |null $where = null, string|null $trash = null, bool|null $QueryBilderEnable = true);

    /**
     * showObject
     *
     * @param Model|int|string $object
     * @param bool|null $trash
     * @param bool|null $enableQueryBuilder
     * @return Model|null
     */
    public function showObject(Model|int|string $object, bool|null $trash = false, bool|null $enableQueryBuilder = true);

    /**
     * storeObject
     *
     * @param array $data
     * @param bool|null $is_object
     * @return Model|int
     */
    public function storeObject(array $data, bool|null $is_object = true);

    /**
     * updateObject
     *
     * @param array $values
     * @param Model|int|string $object
     * @param bool|null $is_object
     * @return Model|bool|null
     */
    public function updateObject(array $values, Model|int|string $object, bool|null $getObject = false);

    /**
     * deleteObject
     *
     * @param Model|int|string $object
     * @return bool|null
     */
    public function deleteObject(Model|int|string $object);

    /**
     * destroyObject
     *
     * @param Model|int|string $object
     * @return bool|null
     */
    public function destroyObject(Model|int|string $object);

    /**
     * restoreObject
     *
     * @param Model|int|string $object
     * @return bool|null
     */
    public function restoreObject(Model|int|string $object);

    /**
     * jsonResponce
     *
     * @param string|null $message
     * @param mixed $data
     * @param int|null $status
     * @param bool|null $success
     * @return \Illuminate\Http\JsonResponse
     */
    public function jsonResponce(string|null $message = null, mixed $data = null, int|null $status = 200, bool|null $success = true);
}
