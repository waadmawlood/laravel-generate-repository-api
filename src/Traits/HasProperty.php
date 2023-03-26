<?php

namespace Waad\Repository\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasProperty
{
    /**
     * get Properties Of Model
     * @param mixed $property
     * @return mixed
     */
    public function getPropertiesOfModel($property)
    {
        $getReflactionModel = new \ReflectionClass($this->model);
        $getProperties = $getReflactionModel->getProperty($property);
        $getProperties->setAccessible(true);

        return $getProperties->getValue(new $this->model());
    }

    public function checkNotArray(&$values)
    {
        if($values instanceof Collection || $values instanceof Model){
            $values = $values->toArray();
        }
    }
}
