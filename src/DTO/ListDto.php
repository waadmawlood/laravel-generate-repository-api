<?php

namespace Waad\Repository\DTO;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Responsable;
use Spatie\DataTransferObject\DataTransferObject;

class ListDto extends DataTransferObject implements Responsable
{
    public Collection|Model|array $collection;

    public function __construct($collection, $dto)
    {
        $this->collection = $this->fromAny($collection, $dto);
    }

    /**
     * fromAny
     *
     * @param Collection|Model|array $array
     * @param mixed $dto
     * @return Collection
     */
    private function fromAny(Collection|Model|array $array, mixed $dto)
    {
        $result = collect();
        if($array instanceof Collection)
            $array = $array->toArray();

        if($array instanceof Model)
            $array = $array->toArray();

        foreach ($array as $arr)
            $result->push(new $dto ($arr));

        return $result;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return Collection|array
     */
    public function toResponse($request)
    {
        return $this->collection;
    }
}
