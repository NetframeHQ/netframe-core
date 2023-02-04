<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;

class LocationController extends BaseController
{
    public function __construct()
    {
        $this->middleware('checkAuth');
    }

    public function getLocation($lat, $lng)
    {
        $apiKey = config('external-api.googleApi.key');
        $urlSc = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$lat.','.$lng.'&key='.$apiKey;
        $geocode = json_decode(file_get_contents($urlSc), true);

        if (count($geocode['results']) > 0) {
            $data = array();
            foreach ($geocode['results']['0']['address_components'] as $element) {
                $data[ implode(' ', $element['types']) ] = $element['long_name'];
            }

            if (isset($data['locality political']) || isset($data['country political'])) {
                if (isset($data['locality political'])) {
                    $location = $data['locality political'].' - '.$data['country political'];
                } else {
                    $location = $data['country political'];
                }
            } else {
                $location = '';
            }
            return $location;
        } else {
            return null;
        }
    }
}
