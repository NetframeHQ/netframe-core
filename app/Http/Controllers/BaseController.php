<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Form;
use App\Helpers\AclHelper;
use Illuminate\Support\Facades\View;
use App\Helpers\SessionHelper;
use App\User;
use App\Netframe;
use App\Profile;
use App\Instance;

class BaseController extends Controller
{

    protected $listProfiles;
    protected $NetframeGeoIp;
    protected $listSubscribe;
    protected $Acl;

    public function __construct()
    {
        $this->Acl = new AclHelper();

        $apiKeys = config('external-api');
        view::share('googleMapsKey', $apiKeys['googleApi']['key']);
    }

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout()
    {
        if (!is_null($this->layout)) {
            $this->layout = view($this->layout);
        }
    }

    public static function hasRights($post)
    {
        //return Acl::getRights(strtolower($post->author_type), $post->author_id);
        return AclHelper::getRights(strtolower($post->author_type), $post->author_id);
    }

    public static function hasRightsProfile($profile, $rightCheck = 3)
    {
        if ($profile == null) {
            return false;
        } elseif (get_class($profile) == 'Illuminate\Database\Eloquent\Collection') {
            $maxRight = 0;
            foreach ($profile as $profil) {
                $testRights = \App\Helpers\Lib\Acl::getRights(strtolower(get_class($profil)), $profil->id, $rightCheck);
                if ($testRights > 0 && $testRights < $maxRight) {
                    $maxRight = $testRights;
                }
            }
            return $maxRight;
        } else {
            return \App\Helpers\Lib\Acl::getRights(strtolower(get_class($profile)), $profile->id, $rightCheck);
        }
    }

    public static function hasViewProfile($profile)
    {
        if ($profile->confidentiality == 0 && auth()->guard('web')->check()) {
            //if private profile and member, can view all
            if (\App\Helpers\Lib\Acl::getRights(strtolower(class_basename($profile)), $profile->id, 5)) {
                return true;
            }
        } elseif (auth()->guard('web')->check()) {
            //if member can view all
            if (\App\Helpers\Lib\Acl::getRights(strtolower(class_basename($profile)), $profile->id, 5)) {
                return true;
            }
            //if subscriber and public profile can view all
            if (auth()->guard('web')->user()->followConfidentiality(class_basename($profile), $profile->id)) {
                return true;
            }
        }

        return false;
    }
}
