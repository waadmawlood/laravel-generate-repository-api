<?php

namespace Waad\Repository\DTO;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\DataTransferObject\DataTransferObject;

class PaginationDto extends DataTransferObject implements Responsable
{
    public LengthAwarePaginator $collection;
    public $data;

    public function __construct($collection, $dto)
    {
        $this->collection = $collection;
        $this->data = $this->fromAny($this->collection->getCollection()->toArray(), $dto);
    }

    /**
     * fromAny
     * @param Collection|Model|array $array
     * @param mixed $dto
     * @return Collection
     */
    private function fromAny(Collection|Model|array $array, mixed $dto)
    {
        $result = collect();
        if($array instanceof Collection)
            $array = $array->toArray();

        foreach ($array as $arr)
            $result->push(new $dto ($arr));

        return $result;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function toResponse($request)
    {
        $this->collection->setCollection(collect($this->data->toArray()));

        return $this->collection;
    }
}
