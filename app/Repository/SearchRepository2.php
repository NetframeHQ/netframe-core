<?php

namespace App\Repository;

use App\Helpers\SessionHelper;
use Illuminate\Http\Request;
use App\Buzz;
use App\User;
use App\House;
use App\Community;
use App\Project;
use App\Instance;

class SearchRepository2
{
    public static $searchProfiles = array(
        'house' => 1,
        'project' => 1,
        'community' => 1,
        'user' => 1,
        'channel' => 1,
    );

    public static $searchProfilesMosaic = array(
        'house' => 1,
        'project' => 1,
        'community' => 1,
        'user' => 1,
        'channel' => 0
    );

    public $route;
    public $targetsProfiles;
    public $toggleFilter = false;
    public $byInterests = 0;
    public $newProfile = 0;
    public $inviteProfile = null;
    public $random = null;
    public $search_limit = 20;
    public $geosearch = true;

    private $geolgo = '';
    private $geolgoTrue = '';
    private $geolgoWhere = '';
    private $geolgoTrueWhere = '';

    const SEARCH_HOUSES = 'house';
    const SEARCH_PROJECTS = 'project';
    const SEARCH_COMMUNITIES = 'community';
    const SEARCH_USERS = 'user';
    const SEARCH_CHANNELS = 'channel';
    const SEARCH_ALL = 'all';

    public function __construct()
    {
        $location = SessionHelper::getLocation();
        $lat = $location->lat;
        $lng = $location->lon;
        $this->setGeolgo($location->lat, $location->lon);
    }

    public function initializeConfig()
    {
        if ($this->search_limit == null) {
            $this->search_limit = config('netframe.search_limit');
        }

        $query = (request()->has('query')) ? request()->get('query') : '';
        $hashtag = (request()->has('$hashtag')) ? request()->get('$hashtag') : '';
        $profile = request()->get('profile', SearchRepository2::SEARCH_ALL);
        $subject = (request()->has('subject')) ? request()->get('subject') : '';
        $category = (request()->has('category')) ? request()->get('category') : '';
        $placeSearch = (request()->has('placeSearch')) ? request()->get('placeSearch') : '';
        $buzz = (request()->has('buzz')) ? request()->get('buzz') : 0;
        $distance = (request()->has('distance')) ? request()->get('distance') : 35000;

        //$byInterests = 0;
        if ($this->byInterests == 1) { // if(request()->has('byInterests') && request()->get('byInterests') == 1){
            if (auth()->guard('web')->check()) {
                $interests = auth()->guard('web')->user()->interests;
            } else {
                $interests = array();
            }
            //$category = App::make('User\InterestController')->convertToIdArray($interests);
            $category = User\InterestController::convertToIdArray($interests);
            $this->byInterests = 1;
        }

        if (request()->has('latitude') && request()->has('longitude')) {
            $lat = request()->get('latitude');
            $lng = request()->get('longitude');
        } else {
            $location = SessionHelper::getLocation();
            $lat = $location->lat;
            $lng = $location->lon;
        }

        if (request()->has('profile')) {
            $this->targetsProfiles = array();
            $testFilters = array();
            foreach (request()->get('profile') as $key => $targetProfile) {
                $this->targetsProfiles[$targetProfile] = 1;
                $testFilters[] = $targetProfile;
            }
        }

        $loadedProfiles = array();
        foreach ($this->targetsProfiles as $profileFilter => $value) {
            if (request()->has($profileFilter.'Loaded')) {
                $loadedProfiles[$profileFilter] = request()->get($profileFilter.'Loaded');
            }
        }

        $currentPage = request()->get('page');
        $nextPage = $currentPage+1;

        if (request()->isMethod('POST') && $this->route != 'search_mosaic') {
            $limits = [request()->get('currentPage') * $this->search_limit, $this->search_limit];
        } else {
            $limits = [0, $this->search_limit];
        }

        //get max buzz date
        $buzzDate = Buzz::select(\DB::raw('max(date(`created_at`)) as buzzDate'))->first();

        return array(
            'buzz' => $buzz,
            'todayBuzz' => ($buzzDate->buzzData != null) ? $buzzDate->buzzDate : date('Y-m-d'),
            'byInterests' => $this->byInterests,
            'category' => $category,
            'currentPage' => 1,
            'distance' => $distance,
            'hashtag' => $hashtag,
            'latitude' => $lat,
            'limits' => $limits,
            'loadedProfiles' => $loadedProfiles,
            'longitude' => $lng,
            'newProfile' => $this->newProfile,
            'placeSearch' => $placeSearch,
            'profile' => $profile,
            'query' => $query,
            'subject' => $subject,
            'route' => $this->route,
            'search_min_res' => $this->search_limit,
            'targetsProfiles' => $this->targetsProfiles,
            'toggleFilter' => $this->toggleFilter,
            'geosearch' => $this->geosearch,
            'inviteProfile' => $this->inviteProfile,
            'random' => $this->random,
        );
    }


    public function getGeolgo()
    {
        return $this->geolgo;
    }

    public function setGeolgo($lat, $lng)
    {
        $this->geolgo = '( ( 25000 - ( 3959 * acos( cos( radians(' . $lat
            . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') )
            + sin( radians(' . $lat . ') ) * sin( radians( latitude ) ) ) ) ) / 10000 ) as distance';
        $this->geolgoTrue = ' ( 3959 * acos( cos( radians(' . $lat
            . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') )
            + sin( radians(' . $lat . ') ) * sin( radians( latitude ) ) ) ) as truedistance';
        $this->geolgoTrueWhere = ' ( 3959 * acos( cos( radians(' . $lat
            . ') ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(' . $lng . ') )
            + sin( radians(' . $lat . ') ) * sin( radians( latitude ) ) ) )';
    }

    /**
     * Perform the search.
     *
     * @param string $query : The search query
     * @param int $subject : ref_subject_id
     * @param int $category : ref_category_id
     * @param array $targets : Profiles arra of unwanted search profiles
     * @param array $limits : [0] : startLimit, [1] maxitems to return
     * @return array
     */
    //public function search($query, $subject, $category, $targetsProfiles, $limit = null)
    public function search($searchParameters, $targetsProfiles)
    {
        $results = array();
        $targetsProfilesCompare = self::$searchProfiles;

        $this->setGeolgo($searchParameters['latitude'], $searchParameters['longitude']);

        $querySearch = addslashes($searchParameters['query']);
        $querySearch = str_replace('@', ' ', $querySearch);
        if ($targetsProfilesCompare != $searchParameters['targetsProfiles']) {
            foreach ($searchParameters['targetsProfiles'] as $profile => $value) {
                if ($value == 1) {
                    $functionName = "find".studly_case($profile);
                    $results = array_merge(
                        $results,
                        $this->$functionName(
                            $querySearch,
                            $searchParameters['subject'],
                            $searchParameters['category'],
                            $searchParameters
                        )
                    );
                }
            }
        } else {
            foreach ($targetsProfilesCompare as $profile => $value) {
                if ($value == 1) {
                    $functionName = "find".studly_case($profile);
                    $results = array_merge(
                        $results,
                        $this->$functionName(
                            $querySearch,
                            $searchParameters['subject'],
                            $searchParameters['category'],
                            $searchParameters
                        )
                    );
                }
            }
        }

        if ($searchParameters['inviteProfile'] == null) {
            if ($searchParameters['random'] != null) {
                shuffle($results);
            } else {
                rsort($results);
            }
        }

        //delete first col of array
        $return = array_map(function ($line) {
            return $line[1];
        }, $results);

        /*
        if(is_array($searchParameters['limits'])){
            $return = array_slice($return, $searchParameters['limits'][0], $searchParameters['limits'][1]);
        }
        */

        return array($return, $searchParameters['targetsProfiles']);
    }

    /**
     * find posts by hashtag
     * @param string $hashtag
     */
    private function findPosts($hashtag)
    {
        if ($hashtag != '') {
            $against = str_replace(' ', '*', '*'.$hashtag.'*');
            $compareRank = '>';
        } else {
            $against = '';
            $compareRank = '>=';
        }

        //load query in posts, events, offers, join with comments content
    }

    /**
     *
     * @param unknown $query
     * @param unknown $subject
     * @param unknown $category
     * @return multitype:|multitype:StdClass
     */
    private function findUser($query, $subject, $category, $searchParameters)
    {
        $instance = Instance::find(session('instanceId'));
        if ($subject != '' || $category != '') {
            return array();
        }

        if ($query != '') {
            $against = str_replace(' ', '*', '*'.$query.'*');
            $compareRank = '>';
        } else {
            $against = '';
            $compareRank = '>=';
        }

        if ($searchParameters['inviteProfile'] != null) {
            $baseSearch = new User();
            $orderBy = 'users.name';
        } else {
            $baseSearch = $instance->users();
            $orderBy = 'rank';
        }

        $users = $baseSearch->where('active', '=', 1)
            ->whereHas('instances', function ($wI) {
                $wI->where('id', '=', session('instanceId'));
            })
            ->select(array(
                \DB::raw("users.*"),
                \DB::raw("(4*(MATCH(tags.name) AGAINST('" . $against
                    . "' IN BOOLEAN MODE)) + 3*(MATCH(firstname) AGAINST('" . $against
                    . "' IN BOOLEAN MODE)) + 3*(MATCH(users.name) AGAINST('" . $against
                    . "' IN BOOLEAN MODE)) + (MATCH(description) AGAINST('" . $against
                    . "' IN BOOLEAN MODE))) AS `rank`")
            ))
            ->leftJoin('users_references as ur', 'ur.users_id', '=', 'users.id')
            ->leftJoin('tags', 'tags.id', '=', 'ur.tags_id')
            ->where(function ($whereAgainst) use ($against) {
                if ($against != '') {
                    $whereAgainst->orWhereRaw("MATCH(firstname) AGAINST('".$against."' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH(users.name) AGAINST('".$against."' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH(tags.name) AGAINST('".$against."' IN BOOLEAN MODE)");
                }
            })
            ->where(function ($whereExclude) use ($searchParameters) {
                if (isset($searchParameters['loadedProfiles']['user'])) {
                    $listProfiles = explode(',', $searchParameters['loadedProfiles']['user']);
                    $whereExclude->whereNotIn('users.id', $listProfiles);
                }
            })

            ->where(function ($whereNew) use ($searchParameters) {
                if ($searchParameters['newProfile'] == 1) {
                    $date = new Carbon\Carbon;
                    $date->subDays(7);
                    $whereNew->where('users.created_at', '>=', $date->toDateTimeString());
                }
            })

            ->where(function ($whereJoined) use ($searchParameters) {
                // if search from profile community invite, check if user isn't joined to profile
                if ($searchParameters['inviteProfile'] != null && $searchParameters['inviteProfile'] != 'instance') {
                    $profile = $searchParameters['inviteProfile']->getType();
                    $whereJoined->whereDoesntHave($profile.'Invite', function ($wP) use ($searchParameters) {
                        $wP->where('id', '=', $searchParameters['inviteProfile']->id);
                    });
                }
            })

            //->having('truedistance', '<=', $searchParameters['distance'])
            ->groupBy('users.id')
            ->having('rank', $compareRank, '0')
            ->take($searchParameters['search_min_res'])
            ->orderBy($orderBy)
            ->get();
        $results = array();

        foreach ($users as $user) {
            $results[] = array(
                $user->rank *2,
                $user,
            );
        }

        return $results;
    }

    /**
     * Finds houses.
     *
     * @param string $query
     *
     * @return array
     */
    private function findHouse($query, $subject, $category, $searchParameters)
    {
        $instance = Instance::find(session('instanceId'));

        if ($query != '') {
            $against = str_replace(' ', '*', '*'.$query.'*');
            $compareRank = '>';
        } else {
            $against = '';
            $compareRank = '>=';
        }

        $houses = $instance->houses()->select(array(
                \DB::raw("houses.*"),
                \DB::raw("(4*(MATCH(tags.name) AGAINST('" . $against
                    . "' IN BOOLEAN MODE)) + 4*(MATCH(houses.name) AGAINST('" . $against
                    . "' IN BOOLEAN MODE)) + 2*(MATCH(description) AGAINST('" . $against
                    . "' IN BOOLEAN MODE))) AS `rank`")
            ))
            ->addSelect(\DB::raw($this->geolgo))
            //->addSelect( \DB::raw($this->geolgoTrue) )
            ->leftJoin('taggables', function ($joinTag) {
                $joinTag->on('taggables.taggable_id', '=', 'houses.id')
                        ->where('taggables.taggable_type', '=', 'App\\House');
            })
            ->leftJoin('tags', 'tags.id', '=', 'taggables.tag_id')
            ->where('houses.instances_id', '=', session('instanceId'))
            ->where(function ($whereAgainst) use ($against) {
                if ($against != '') {
                    $whereAgainst->orWhereRaw("MATCH(houses.name) AGAINST('".$against."' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH(description) AGAINST('".$against."' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH(tags.name) AGAINST('".$against."' IN BOOLEAN MODE)");
                }
            })

            ->where('active', '=', 1)
            ->where(function ($whereExclude) use ($searchParameters) {
                if (isset($searchParameters['loadedProfiles']['house'])) {
                    $listProfiles = explode(',', $searchParameters['loadedProfiles']['house']);
                    $whereExclude->whereNotIn('houses.id', $listProfiles);
                }
            })

            //buzz filter
            ->leftJoin('buzz', function ($joinB) use ($searchParameters) {
                $joinB->on('buzz.profile_id', '=', 'houses.id')
                ->where('buzz.profile_type', '=', 'House')
                ->where('buzz.created_at', '>=', $searchParameters['todayBuzz']);
            })
            ->where(function ($whereB) use ($searchParameters) {
                if ($searchParameters['buzz'] != null) {
                    $buzzColumn = config('netframe.buzz_columns.'.$searchParameters['buzz']);
                    $buzzScore = \Buzz::topBuzz($buzzColumn);
                    $whereB->where($buzzColumn, '>=', $buzzScore)
                    ->where($buzzColumn, '!=', 0);
                }
            })

            ->where(function ($whereNew) use ($searchParameters) {
                if ($searchParameters['newProfile'] == 1) {
                    $date = new Carbon\Carbon;
                    $date->subDays(7);
                    $whereNew->where('houses.created_at', '>=', $date->toDateTimeString());
                }
            })
            ->where(function ($wLoc) use ($searchParameters) {
                $wLoc->orWhere(\DB::raw($this->geolgoTrueWhere), '<=', $searchParameters['distance'])
                ->orWhereNull(\DB::raw($this->geolgoTrueWhere));
            })

            // Filter by category
            //->leftJoin('ref_categories AS cat', 'cat.reference_category_id', '=', 'houses.ref_categories_id')

            //->having('truedistance', '<=', $searchParameters['distance'])
            ->having('rank', $compareRank, '0')
            ->groupBy('houses.id')
            ->orderBy('rank')
            ->take($searchParameters['search_min_res'])
            ->get();

        $results = array();

        foreach ($houses as $house) {
            $results[] = array(
                ($house->rank + 1) * $house->distance ,
                $house,
            );
        }

        return $results;
    }

    /**
     * Finds projects.
     *
     * @param string $query
     *
     * @return array
     */
    private function findProject($query, $subject, $category, $searchParameters)
    {
        $instance = Instance::find(session('instanceId'));

        if ($query != '') {
            $against = str_replace(' ', '*', '*'.$query.'*');
            $compareRank = '>';
        } else {
            $against = '';
            $compareRank = '>=';
        }

        $projects = $instance->projects()->select(array(
                    \DB::raw("projects.*"),
                    \DB::raw("(4*(MATCH(tags.name) AGAINST('" . $against
                        . "' IN BOOLEAN MODE)) + 3*(MATCH(title) AGAINST('" . $against
                        . "' IN BOOLEAN MODE)) + (MATCH(description) AGAINST('" . $against
                        . "' IN BOOLEAN MODE))) AS `rank`")
            ))
           ->addSelect(\DB::raw($this->geolgo))
           //->addSelect( \DB::raw($this->geolgoTrue) )
            ->leftJoin('taggables', function ($joinTag) {
                $joinTag->on('taggables.taggable_id', '=', 'projects.id')
                        ->where('taggables.taggable_type', '=', 'App\\Project');
            })
            ->leftJoin('tags', 'tags.id', '=', 'taggables.tag_id')
            ->where('projects.instances_id', '=', session('instanceId'))
            ->where(function ($whereAgainst) use ($against) {
                if ($against != '') {
                    $whereAgainst->orWhereRaw("MATCH(title) AGAINST('".$against."' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH(description) AGAINST('".$against."' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH(tags.name) AGAINST('".$against."' IN BOOLEAN MODE)");
                }
            })

            ->where('active', '=', 1)
            ->where(function ($whereExclude) use ($searchParameters) {
                if (isset($searchParameters['loadedProfiles']['project'])) {
                    $listProfiles = explode(',', $searchParameters['loadedProfiles']['project']);
                    $whereExclude->whereNotIn('projects.id', $listProfiles);
                }
            })

            //buzz filter
            ->leftJoin('buzz', function ($joinB) use ($searchParameters) {
                $joinB->on('buzz.profile_id', '=', 'projects.id')
                ->where('buzz.profile_type', '=', 'App\\Project')
                ->where('buzz.created_at', '>=', $searchParameters['todayBuzz']);
            })
            ->where(function ($whereB) use ($searchParameters) {
                if ($searchParameters['buzz'] != null) {
                    $buzzColumn = config('netframe.buzz_columns.'.$searchParameters['buzz']);
                    $buzzScore = \Buzz::topBuzz($buzzColumn);
                    $whereB->where($buzzColumn, '>=', $buzzScore)
                    ->where($buzzColumn, '!=', 0);
                }
            })
            ->where(function ($whereNew) use ($searchParameters) {
                if ($searchParameters['newProfile'] == 1) {
                    $date = new Carbon\Carbon;
                    $date->subDays(7);
                    $whereNew->where('projects.created_at', '>=', $date->toDateTimeString());
                }
            })
            ->where(function ($wLoc) use ($searchParameters) {
                $wLoc->orWhere(\DB::raw($this->geolgoTrueWhere), '<=', $searchParameters['distance'])
                     ->orWhereNull(\DB::raw($this->geolgoTrueWhere));
            })

            // Filter by category
            //->leftJoin('ref_categories AS cat', 'cat.reference_category_id', '=', 'projects.ref_categories_id')

            //->having('truedistance', '<=', $searchParameters['distance'])
            ->having('rank', $compareRank, '0')
            ->groupBy('projects.id')
            ->orderBy('rank')
            ->take($searchParameters['search_min_res'])
            ->get();

        $results = array();

        foreach ($projects as $project) {
            $results[] = array(
                ($project->rank + 1) * $project->distance ,
                $project,
            );
        }

        return $results;
    }

    /**
     * Finds communities.
     *
     * @param string $query
     *
     * @return array
     */
    private function findCommunity($query, $subject, $category, $searchParameters)
    {
        $instance = Instance::find(session('instanceId'));

        if ($query != '') {
            $against = str_replace(' ', '*', '*'.$query.'*');
            $compareRank = '>';
        } else {
            $against = '';
            $compareRank = '>=';
        }

        $communities = $instance->communities()->select(array(
                \DB::raw("community.*"),
                \DB::raw("(4*(MATCH(tags.name) AGAINST('" . $against
                    . "' IN BOOLEAN MODE)) + 3*(MATCH(community.name) AGAINST('" . $against
                    . "' IN BOOLEAN MODE)) + (MATCH(description) AGAINST('" . $against
                    . "' IN BOOLEAN MODE))) AS `rank`")
            ))
            ->addSelect(\DB::raw($this->geolgo))
            //->addSelect( \DB::raw($this->geolgoTrue) )
            ->leftJoin('taggables', function ($joinTag) {
                $joinTag->on('taggables.taggable_id', '=', 'community.id')
                        ->where('taggables.taggable_type', '=', 'App\\Community');
            })
            ->leftJoin('tags', 'tags.id', '=', 'taggables.tag_id')
            ->where('community.instances_id', '=', session('instanceId'))
            ->where(function ($whereAgainst) use ($against) {
                if ($against != '') {
                    $whereAgainst->orWhereRaw("MATCH(community.name) AGAINST('".$against."' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH(description) AGAINST('".$against."' IN BOOLEAN MODE)")
                        ->orWhereRaw("MATCH(tags.name) AGAINST('".$against."' IN BOOLEAN MODE)");
                }
            })

            ->where('active', '=', 1)
            ->where(function ($whereExclude) use ($searchParameters) {
                if (isset($searchParameters['loadedProfiles']['community'])) {
                    $listProfiles = explode(',', $searchParameters['loadedProfiles']['community']);
                    $whereExclude->whereNotIn('community.id', $listProfiles);
                }
            })

            //buzz filter
            ->leftJoin('buzz', function ($joinB) use ($searchParameters) {
                $joinB->on('buzz.profile_id', '=', 'community.id')
                ->where('buzz.profile_type', '=', 'Communtiy')
                ->where('buzz.created_at', '>=', $searchParameters['todayBuzz']);
            })
            ->where(function ($whereB) use ($searchParameters) {
                if ($searchParameters['buzz'] != null) {
                    $buzzColumn = config('netframe.buzz_columns.'.$searchParameters['buzz']);
                    $buzzScore = \Buzz::topBuzz($buzzColumn);
                    $whereB->where($buzzColumn, '>=', $buzzScore)
                    ->where($buzzColumn, '!=', 0);
                }
            })

            ->where(function ($whereNew) use ($searchParameters) {
                if ($searchParameters['newProfile'] == 1) {
                    $date = new Carbon\Carbon;
                    $date->subDays(7);
                    $whereNew->where('community.created_at', '>=', $date->toDateTimeString());
                }
            })
            ->where(function ($wLoc) use ($searchParameters) {
                $wLoc->orWhere(\DB::raw($this->geolgoTrueWhere), '<=', $searchParameters['distance'])
                ->orWhereNull(\DB::raw($this->geolgoTrueWhere));
            })

            // Filter by category
            //->leftJoin('ref_categories AS cat', 'cat.reference_category_id', '=', 'community.ref_categories_id')

            //->having('truedistance', '<=', $searchParameters['distance'])
            ->having('rank', $compareRank, '0')
            ->groupBy('community.id')
            ->orderBy('rank')
            ->take($searchParameters['search_min_res'])
            ->get();

        $results = array();


        foreach ($communities as $community) {
            $results[] = array(
                ($community->rank + 1) * $community->distance ,
                $community,
            );
        }

        return $results;
    }

    /**
     * Finds communities.
     *
     * @param string $query
     *
     * @return array
     */
    private function findChannel($query, $subject, $category, $searchParameters)
    {
        $instance = Instance::find(session('instanceId'));

        if ($query != '') {
            $against = str_replace(' ', '*', '*'.$query.'*');
            $compareRank = '>';
        } else {
            $against = '';
            $compareRank = '>=';
        }

        $channels = $instance->channels()->select(array(
            \DB::raw("channels.*"),
            \DB::raw("(4*(MATCH(tags.name) AGAINST('" . $against
                . "' IN BOOLEAN MODE)) + 3*(MATCH(channels.name) AGAINST('" . $against
                . "' IN BOOLEAN MODE)) + (MATCH(description) AGAINST('" . $against
                . "' IN BOOLEAN MODE))) AS `rank`")
        ))
        ->leftJoin('taggables', function ($joinTag) {
            $joinTag->on('taggables.taggable_id', '=', 'channels.id')
            ->where('taggables.taggable_type', '=', 'App\\Channel');
        })
        ->leftJoin('tags', 'tags.id', '=', 'taggables.tag_id')
        ->where('channels.personnal', '=', 0)
        ->where('channels.instances_id', '=', session('instanceId'))
        ->where(function ($whereAgainst) use ($against) {
            if ($against != '') {
                $whereAgainst->orWhereRaw("MATCH(channels.name) AGAINST('".$against."' IN BOOLEAN MODE)")
                ->orWhereRaw("MATCH(description) AGAINST('".$against."' IN BOOLEAN MODE)")
                ->orWhereRaw("MATCH(tags.name) AGAINST('".$against."' IN BOOLEAN MODE)");
            }
        })

        //->where('active', '=', 1)
        ->where(function ($whereExclude) use ($searchParameters) {
            if (isset($searchParameters['loadedProfiles']['community'])) {
                $listProfiles = explode(',', $searchParameters['loadedProfiles']['channel']);
                $whereExclude->whereNotIn('channels.id', $listProfiles);
            }
        })

        ->where(function ($whereNew) use ($searchParameters) {
            if ($searchParameters['newProfile'] == 1) {
                $date = new Carbon\Carbon;
                $date->subDays(7);
                $whereNew->where('channels.created_at', '>=', $date->toDateTimeString());
            }
        })

        // Filter by category
            //->leftJoin('ref_categories AS cat', 'cat.reference_category_id', '=', 'community.ref_categories_id')

            ->having('rank', $compareRank, '0')
            ->groupBy('channels.id')
            ->orderBy('rank')
            ->take($searchParameters['search_min_res'])
            ->get();

            $results = array();


        foreach ($channels as $channel) {
            $results[] = array(
                $channel->rank + 1 ,
                $channel,
            );
        }

            return $results;
    }
}
