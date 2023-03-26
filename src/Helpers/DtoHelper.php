<?php

namespace Waad\Repository\Helpers;

class DtoHelper
{

    /**
     * return Class Dto string
     *
     * @param string $name
     * @param array|string $attributes
     * @return bool|string|null
     */
    public static function getClassDto(string $name, array|string|null $attributes = null)
    {
        if (blank($attributes))
            return null;

        $nameDto = ucfirst($name) . 'Dto';
        $attributes = is_string($attributes) ? json_decode($attributes, true) : $attributes;

        $POSTFIELDS = [
            "namespace" => "App\\DTO\\" . ucfirst($name),
            "name" => $nameDto,
            "typed" => true,
            "v3" => true,
            "nested" => false,
            "flexible" => true,
            "source" => $attributes,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://json2dto-max7gzuovq-uc.a.run.app',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($POSTFIELDS),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}
