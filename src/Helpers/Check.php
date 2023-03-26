<?php

namespace Waad\Repository\Helpers;
use Illuminate\Support\Arr;

class Check
{
    public static function trueOrFalse($condistion_true, $condistion_false, $condistion_else = true)
    {
        if($condistion_true){
            return true;
        }elseif($condistion_false){
            return false;
        }else{
            return $condistion_else;
        }
    }

    /**
     * isDesc
     *
     * @param string $value
     * @return bool
     */
    public static function isDesc(string $value)
    {
        if(filled($value)){
            if(strlen($value) >= 2){
                if($value[0] == '-'){
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * check Allowed Properties
     *
     * @param array $allowed
     * @param array|string $keys
     * @param string $classname
     * @param string $property
     * @throws \Exception
     * @return void
     */
    public static function checkAllowedProperties(array $allowed, array|string $keys, string $classname, string $property)
    {
        $keys = Arr::wrap($keys);

        foreach($keys as $key){
            if (!in_array($key, $allowed, true) || blank($key)){
                $propertiesException = implode(', ', $allowed);
                throw new \Exception("the `{$key}` not allowed in $classname model property $property `{$propertiesException}`");
            }
        }
    }
}
