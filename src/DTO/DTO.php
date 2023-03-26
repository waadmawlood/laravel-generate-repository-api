<?php

namespace Waad\Repository\DTO;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\DataTransferObject\DataTransferObject;

class DTO extends DataTransferObject
{
    private static LengthAwarePaginator|Collection|Model|array|null $collection;
    private static $data;

    /**
     * pagination
     * @param LengthAwarePaginator $collection
     * @param mixed $dto
     * @return LengthAwarePaginator|null
     */
    public static function pagination($collection, $dto)
    {
        if (blank($collection))
            return null;

        static::$collection = $collection;
        static::$data = static::fromAny(static::$collection->getCollection()->toArray(), $dto);
        static::$collection->setCollection(static::$data);

        return static::$collection;
    }

    /**
     * Summary of list
     * @param Collection|Model|array|null $collection
     * @param mixed $dto
     * @return Collection|Model|array|null
     */
    public static function list(Collection|Model|array|null $collection, $dto)
    {
        if (blank($collection))
            return null;

        return static::fromAny($collection, $dto);
    }

    /**
     * fromAny
     * @param Collection|Model|array $array
     * @param mixed $dto
     * @return Collection
     */
    private static function fromAny(Collection|Model|array $array, mixed $dto)
    {
        $result = collect();
        if ($array instanceof Collection)
            $array = $array->toArray();

        if ($array instanceof Model)
            $array = $array->toArray();

        foreach ($array as $arr)
            $result->push(new $dto($arr));

        return $result;
    }
}

