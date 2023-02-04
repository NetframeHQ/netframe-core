<?php

namespace App\Helpers;

use App\Instance;

/**
 *
 *
 * Manage Session user & other for netframe
 *
 */
class SessionHelper
{


    /**
     * Initialize session array for profiles when instance login
     *
     * @return boolean
     */
    public static function initProfile()
    {
        $profiles = config('users.session.profiles');

        if (session()->has('profiles')) {
            return true;
        } else {
            // build array session "profiles"
            session(['profiles' => $profiles]);
            return false;
        }
    }


    /**
     * Set value or variable in profile type ex:
     * session(['current' => array('name'=>'my name')]);
     *
     * @param (string) $profileNav give key profile in session ex: 'default', 'current' or 'as'
     * @param (object) $model attempt data query object
     * @param (string) $objectType default NULL give type profile ex: user, house...
     */
    public static function setProfile($profileNav, $model, $objectType)
    {

        // convert array into object
        $items = (object) $model->toArray();


        $geoip = session('geoip_location'); //\GeoIP::getLocation('88.163.128.207');

        // Store array media in profile_media_id
//         if(is_object($model->profileImage))
//         {
//             $items->profile_media = (object) $model->profileImage->getAttributes();
//         }

        if ($objectType) {
            $items->profile = $objectType;
            switch ($objectType) {
                case "user":
                    $items->profileName = $model->name.' '.$model->firstname;
                    break;

                case "project":
                    $items->profileName = $model->title;
                    break;

                case "house":
                case "community":
                    $items->profileName = $model->name;
                    break;
            }
        }

        $items->latitude = $geoip['lat'];
        $items->longitude = $geoip['lon'];

        session(["profiles.{$profileNav}" => $items]);
    }


    /**
     * Return or get object from status profile (default or current or as)
     *
     * @param (string) $status
     * @return (object) data user profile
     */
    public static function profile($status = null)
    {
        if (is_null($status)) {
            $status = 'current';
        }

        return session("profiles.{$status}");
    }



    /**
     * return current object user session nav
     *
     * @return (object)
     */
    public static function getCurrent()
    {
        return static::profile('current');
    }


    /**
     * return as object user session nav
     *
     * @return (object)
     */
    public static function getAs()
    {
        return static::profile('as');
    }


    /**
     * Check if array status exist in session variable profiles
     * @example :
     * check first time if profile session exist, so after check if array type exist
     * profiles => array(
     *      'default' => [],
     *      'current' => [],
     *      'as' => []
     * );
     *
     * @param (string) $status <default> or <current> or <as>
     * @return boolean TRUE / FALSE
     */
    public static function profileIsEmpty($status)
    {
        $object = session('profiles.'.$status);

        if ($object) {
            return false;
        } else {
            return true;
        }
    }


    /**
     * Destroy session var profiles
     *
     */
    public static function destroyProfile()
    {
        session()->forget('profiles');
    }


    /**
     * Check if user profile is property of page or not
     * return true or false
     *
     * @param integer $pageId
     * @param string $pageType
     * @return boolean true / false
     */
    public static function isProperty($pageId, $pageType)
    {
        if (auth()->guard('web')->check()) {
            $profileSession = session('profiles.current');
            if (null !== $profileSession && $profileSession->id == $pageId && $profileSession->profile == $pageType) {
                return true;
            } else {
                return false;
            }
        }
    }


    /**
     *  set location by HTML 5 geoloc
     *
     * @return StdClass
     */
    public static function setLocation($lat, $lng)
    {
        $geoip = new \stdClass();
        $geoip->lat = $lat;
        $geoip->lon = $lng;

        return $geoip;
    }

    /**
     *  get object info GeoIP location
     *
     * @return StdClass
     */
    public static function getLocation($ip = null)
    {
        $instance = Instance::find(session('instanceId'));
        $defaultGeolocation = null;
        if ($instance != null) {
            $defaultGeolocation = $instance->getParameter('default_geolocation');
        }

        if ($defaultGeolocation != null) {
            $defaultGeolocation = json_decode($defaultGeolocation);
            $geoip = new \stdClass();
            $geoip->lat = $defaultGeolocation->latitude;
            $geoip->lon = $defaultGeolocation->longitude;
            if (isset($defaultGeolocation->timezone)) {
                $geoip->timezone = $defaultGeolocation->timezone;
                session(['userTimezone' => $geoip->timezone]);
            }
        } elseif (session()->has('geoip')) {
            $geoip = new \stdClass();
            $geoip->lat = session('geoip')['latitude'];
            $geoip->lon = session('geoip')['longitude'];
        } else {
            if (is_null($ip)) {
                if (getenv('HTTP_CLIENT_IP')) {
                    $ipaddress = getenv('HTTP_CLIENT_IP');
                } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
                    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
                } elseif (getenv('HTTP_X_FORWARDED')) {
                    $ipaddress = getenv('HTTP_X_FORWARDED');
                } elseif (getenv('HTTP_FORWARDED_FOR')) {
                    $ipaddress = getenv('HTTP_FORWARDED_FOR');
                } elseif (getenv('HTTP_FORWARDED')) {
                    $ipaddress = getenv('HTTP_FORWARDED');
                } elseif (getenv('REMOTE_ADDR')) {
                    $ipaddress = getenv('REMOTE_ADDR');
                } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                    $ipaddress = $_SERVER['REMOTE_ADDR'];
                } else {
                    $ipaddress = '88.163.128.207';
                }

                if (! filter_var($ipaddress, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE)) {
                    //$ipaddress = '88.163.128.207';
                    //$ipaddress = '78.247.52.39';
                }
            }

            $geoip = (object) \GeoIP::getLocation($ipaddress);
        }

        return $geoip;
    }

    public static function getIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ipaddress = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_X_FORWARDED')) {
            $ipaddress = getenv('HTTP_X_FORWARDED');
        } elseif (getenv('HTTP_FORWARDED_FOR')) {
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        } elseif (getenv('HTTP_FORWARDED')) {
            $ipaddress = getenv('HTTP_FORWARDED');
        } elseif (getenv('REMOTE_ADDR')) {
            $ipaddress = getenv('REMOTE_ADDR');
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = '88.163.128.207';
        }

        return $ipaddress;
    }
}
