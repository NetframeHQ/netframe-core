<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Boris\Boris;
use Torann\GeoIP\GeoIP;
use App\User;
use App\Community;
use App\House;
use App\Project;
use App\TEvent;
use App\Buzz;
use \Carbon\Carbon;

/**
 * Model Class for Generic Query Eloquent
 *
 *
 *
 */
class Netframe extends Model
{

    /**
     * Get All profile belonging to user
     *
     * @param int $userId
     * @return object
     */
    public static function getProfiles($userId)
    {
        $profiles = [];
        $user = User::find(auth()->guard('web')->user()->id);

        $profiles['user'][$user->id] = [
            'id'    => $user->id,
            'name'  => $user->getNameDisplay(),
            'slug'  => $user->slug,
            'url'   => $user->getUrl(),
            'role'  => 1,
            'profile' => serialize($user)
        ];

        $communities = $user->community()->where('active', '=', 1)->orderBy('name')->withPivot('roles_id')->get();
        foreach ($communities as $community) {
            $profiles['community'][$community->id] = [
                'id'    => $community->id,
                'name'  => $community->name,
                'slug'  => $community->name,
                'url'   => $community->getUrl(),
                'role'  => $community->pivot->roles_id,
                'profile' => serialize($community)
            ];
        }

        $houses = $user->houses()->where('active', '=', 1)->orderBy('name')->withPivot('roles_id')->get();
        foreach ($houses as $house) {
            $profiles['house'][$house->id] = [
                'id'    => $house->id,
                'name'  => $house->name,
                'slug'  => $house->name,
                'url'   => $house->getUrl(),
                'role'  => $house->pivot->roles_id,
                'profile' => serialize($house)
                ];
        }

        //profils_has_project --> add role id + role participant + pour acl type_profil = user
        $projects = $user->project()->where('active', '=', 1)->orderBy('title')->withPivot('roles_id')->get();
        foreach ($projects as $project) {
            $profiles['project'][$project->id] = [
                'id'    => $project->id,
                'name'  => $project->title,
                'slug'  => $project->name,
                'url'   => $project->getUrl(),
                'role'  => $project->pivot->roles_id,
                'profile' => serialize($project)
                ];
        }

        return $profiles;
    }


    public static function getAcl($userId)
    {
        $arrayAcl = array();

        //community_has_user --> add role id + role participant
        $communities = User::find($userId)->community()->get();
        foreach ($communities as $community) {
            $arrayAcl['community'][$community->pivot->community_id] = $community->pivot->roles_id;
        }

        //houses_has_user --> add role id + role participant
        $houses = User::find($userId)->house()->get();
        foreach ($houses as $house) {
            $arrayAcl['house'][$house->pivot->houses_id] = $house->pivot->roles_id;
        }

        //channels_has_users
        $channels = User::find($userId)->allChannels()->get();
        foreach ($channels as $channel) {
            $arrayAcl['channel'][$channel->pivot->channels_id] = $channel->pivot->roles_id;
        }

        //projects_has_user --> add role id + role participant
        $projects = User::find($userId)->project()->get();
        foreach ($projects as $project) {
                $arrayAcl['project'][$project->pivot->projects_id] = $project->pivot->roles_id;
        }
        $arrayAcl['user'][$userId] = 1;

        return $arrayAcl;
    }



    public static function getFluxMapbox()
    {
        $oneWeekAgo = new Carbon;
        $oneWeekAgo->subDays(7);

        $distance = 200;
        $location = \App\Helpers\SessionHelper::getLocation();
        $lat = $location->lat;
        $lng = $location->lon;

        if (request()->has('centerLat') && request()->has('centerLng') && request()->has('distance')) {
            $distance = request()->get('distance');
            $lat = request()->get('centerLat');
            $lng = request()->get('centerLng');
        }

        //get all playlisted user items
        $userPlaylistItems = PlaylistItem::getAllUserItems();

        // Array Builder for json format
        $array = array();

        $formule = "( 3959 * acos( cos( radians($lat) ) *
                    cos( radians( latitude ) ) * cos( radians( longitude ) - radians($lng) )
                    + sin( radians($lat) )
                    * sin( radians( latitude ) ) ) ) as distance";

        // initialize vars
        $query = null;
        $listProfiles = config('netframe.geoip_profiles');
        $arrayQuery = array();

        $postFilters = config('netframe.geoip_filters');

        $now = false;

        if (request()->has('query') && request()->get('query') == 1) {
            if (request()->get('filters') !='') {
                $postFilters = explode(',', request()->get('filters'));
            }
            $subjectsSearch = request()->get('subjects');
            $categoriesSearch = request()->get('categories');
        } elseif (request()->has('query') && request()->get('query') == 'now') {
            //modify listprofileTab to search only events
            foreach ($listProfiles as $model => $active) {
                if ($model != 'events') {
                    $listProfiles[$model][0] = 0;
                }
                $now = true;
            }
        }

        $newProfile = (request()->has('newProfile') && request()->get('newProfile') == 1) ? 1 : 0;

        //check top buzz
        $buzzConfig = config('netframe.buzz_columns');
        if (request()->has('buzz') && request()->get('buzz') != null) {
            $buzzColumn = $buzzConfig[request()->get('buzz')];
            /*
            $buzzQuery = Buzz::select($buzzColumn)
                ->orderBy($buzzColumn, 'desc')
                ->take(10)
                ->get();
            $buzzScore = $buzzQuery[count($buzzQuery)-1][$buzzColumn];
            */
            $buzzScore = Buzz::topBuzz($buzzColumn);
        } else {
            $buzzColumn = 'day_score';
            $buzzScore = Buzz::topBuzz();
        }

        // Sotrage in $arrayQuery result query profiles & events
        foreach ($listProfiles as $model => $active) {
            if ($active[0] == 1 && in_array($model, $postFilters)) {
                $query = \DB::table($model)->select($model.'.*');
                if ($model != 'users') {
                    $query->where($model.'.instances_id', '=', session('instanceId'));
                } else {
                    $query->leftJoin('users_has_instances as uhi', 'uhi.users_id', '=', 'users.id')
                        ->where('uhi.instances_id', '=', session('instanceId'));
                }
                $query->addSelect(\DB::raw($formule));
                if (request()->has('buzz')  && request()->get('buzz') != null) {
                    $query->addSelect('buzz.day_score', 'buzz.week_score', 'buzz.month_score', 'buzz.year_score')
                        ->leftjoin('buzz', function ($joinBuzz) use ($model, $active) {
                            $joinBuzz->on('profile_id', '=', $model.'.id')
                            ->where('profile_type', '=', $active[2])
                            ->where('buzz.instances_id', '=', session('instanceId'))
                            ->where('buzz.created_at', '=', \DB::raw('select max(created_at) from buzz'));
                        })
                        ->where($buzzColumn, '>=', $buzzScore)
                        ->where($buzzColumn, '!=', 0);
                }
                if ($model == 'events') {
                    $query->where(\DB::raw('CONCAT(date, " ", time)'), '>=', $oneWeekAgo);

                    if ($now) {
                        $query->where(\DB::raw('CONCAT(date, " ", time)'), '>=', date('Y-m-d H:i:s'));

                        $to24hours = strftime("%Y-%m-%d %H:%M:%S", mktime(
                            date("H"),
                            date("i"),
                            date("s"),
                            date("m"),
                            date("d") + 2,
                            date("Y")
                        ));

                        $query->where(\DB::raw('CONCAT(date, " ", time)'), '<=', $to24hours);
                    }
                } else {
                    $query->where('active', '=', 1);
                }

                if ($newProfile == 1) {
                    $date = new Carbon;
                    $date->subDays(7);
                    $query->where($model.'.created_at', '>=', $date->toDateTimeString());
                }

                $query->orderBy('distance');
                $query->having('distance', '<', $distance);
                $query->take(200);
                $arrayQuery[$model] = $query->get();
            }
        }

        // Draft array and init
        $array["type"] = "FeatureCollection";
        $array["crs"] = array(
            "type" => "name",
            "properties" => array(
                "name" => "urn:ogc:def:crs:OGC:1.3:CRS84"
            )
        );

        // get parameter for markers profiles
        $markerConf = config('location.markers');
        // Counter increment
        $cpt = 0;

        // Build array for draft json response
        foreach ($arrayQuery as $profile => $arrayResult) {
            foreach ($arrayResult as $key => $row) {
                $profileImageSrc = !empty($row->profile_media_id)
                    ? url()->route('media_download', array('id' => $row->profile_media_id,'thumb'=>1))
                    : '';
                $array["features"][$cpt] = array(
                    "type" => "Feature",
                    "properties" => array(
                        "image" => $profileImageSrc,
                        "markerName" => $profile,
                        "marker-color" => $markerConf[$profile]["color"],
                        "markerColor" => $markerConf[$profile]["color"],
                        "marker-symbol" => $markerConf[$profile]["symbol"],
                        "markerSymbol" => $markerConf[$profile]["symbol"],
                        //"profil" => $profile,
                        "profil" => trans('netframe.'.$profile),
                        "id" => $row->id
                    ),
                    "geometry" => array(
                        "type" => "Point",
                        "coordinates" => array(
                            floatval($row->longitude),
                            floatval($row->latitude),

                        )
                    )
                );

                if ($profile == "users") {
                    $user = User::find($row->id);
                    $array["features"][$cpt]["properties"]["profileId"] = $row->id;
                    $array["features"][$cpt]["properties"]["profileType"] = 'user';
                    $array["features"][$cpt]["properties"]["uniqueId"] = 'user-'.$row->id;
                    if ($user != null
                        && $user->buzz != null
                        && $user->buzz->$buzzColumn >= $buzzScore
                        && $buzzScore > 0) {
                        $buzzProfile = $user->buzz->$buzzColumn;
                    } else {
                        $buzzProfile = 0;
                    }

                    $array["features"][$cpt]["properties"]["buzz"] = $buzzProfile;
                }

                if ($profile == "projects") {
                    $project = Project::find($row->id);
                    $array["features"][$cpt]["properties"]["profileId"] = $row->id;
                    $array["features"][$cpt]["properties"]["profileType"] = 'project';
                    $array["features"][$cpt]["properties"]["uniqueId"] = 'project-'.$row->id;
                    if ($project->buzz != null && $project->buzz->$buzzColumn >= $buzzScore && $buzzScore > 0) {
                        $buzzProfile = $project->buzz->$buzzColumn;
                    } else {
                        $buzzProfile = 0;
                    }
                    $array["features"][$cpt]["properties"]["buzz"] = $buzzProfile;
                }

                if ($profile == "houses") {
                    $house = House::find($row->id);
                    $array["features"][$cpt]["properties"]["profileId"] = $row->id;
                    $array["features"][$cpt]["properties"]["profileType"] = 'house';
                    $array["features"][$cpt]["properties"]["uniqueId"] = 'houses-'.$row->id;
                    if ($house->buzz != null && $house->buzz->$buzzColumn >= $buzzScore && $buzzScore > 0) {
                        $buzzProfile = $house->buzz->$buzzColumn;
                    } else {
                        $buzzProfile = 0;
                    }
                    $array["features"][$cpt]["properties"]["buzz"] = $buzzProfile;
                }

                if ($profile == "community") {
                    $community = Community::find($row->id);
                    $array["features"][$cpt]["properties"]["profileId"] = $row->id;
                    $array["features"][$cpt]["properties"]["profileType"] = 'community';
                    $array["features"][$cpt]["properties"]["uniqueId"] = 'community-'.$row->id;
                    if ($community->buzz != null && $community->buzz->$buzzColumn >= $buzzScore && $buzzScore > 0) {
                        $buzzProfile = $community->buzz->$buzzColumn;
                    } else {
                        $buzzProfile = 0;
                    }
                    $array["features"][$cpt]["properties"]["buzz"] = $buzzProfile;
                }

                if ($profile == "events") {
                    //get event owner
                    $event = TEvent::find($row->id);
                    $author = $event->author;
                    $array["features"][$cpt]["properties"]["profileId"] = $row->id;
                    $array["features"][$cpt]["properties"]["profileType"] = 'event';
                    $array["features"][$cpt]["properties"]["uniqueId"] = 'event-'.$row->id;
                    $array["features"][$cpt]["properties"]["buzz"] = 0;
                }

                //check if item is playlisted by user
                $array["features"][$cpt]["properties"]["canBookmark"] = ($userPlaylistItems !== false) ? 1 : 0;
                $array["features"][$cpt]["properties"]["isBookmarked"] = isset(
                    $userPlaylistItems[$listProfiles[$profile][1]][$row->id]
                ) ? 1 : 0;
                $array["features"][$cpt]["properties"]["bookmarkProfile"] = $listProfiles[$profile][1];
                // Increment
                $cpt++;
            }
        }

        return $array;
    }
}
