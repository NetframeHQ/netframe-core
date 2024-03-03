<?php

namespace App;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Observers\Searchable;
use Illuminate\Support\Facades\Mail;
use App\Mail\BoardingDemand;
use App\Events\UserLogguedEvent;
use App\Helpers\SessionHelper;

class User extends Authenticatable //implements UserInterface, RemindableInterface
{

    use CacheQueryBuilder;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    protected $type = 'user';

    protected $fillable = ['check_rights'];

    protected $appends = [
        'image',
        'url',
        'initials',
        'initialsToColor',
        'initialsToColorRgb'
    ];

    protected $visible = [
        'id',
        'firstname',
        'name',
        'image',
        'url',
        'initials',
        'initialsToColor',
        'initialsToColorRgb',
    ];

    public function isInstanceAdmin()
    {
        if (session()->has('instanceId')) {
            $instance = $this->instances()->where('id', '=', session('instanceId'))->first();
        }
        return (!session()->has('instanceId') || !isset($instance->pivot) || $instance->pivot->roles_id <= 2);
    }

    public static function getTableName()
    {
        return with(new static())->getTable();
    }

    public function getImageAttribute()
    {
        if ($this->profileImage != null) {
            return url()->route('media_download', array('id' => $this->profileImage->id, 'thumb' => 1));
        }
        return null;
    }

    public function getUrlAttribute()
    {
        return $this->getUrl();
    }

    public function getInitialsAttribute()
    {
        return $this->initials();
    }

    public function getInitialsToColorAttribute()
    {
        return $this->initialsToColor();
    }

    public function getInitialsToColorRgbAttribute()
    {
        return $this->initialsToColorRgb();
    }

    public function toReducedArray()
    {
        $training = ($this->training!=null) ? $this->training : "";
        $description = ($this->description!=null) ? $this->description : "";
        $array = [
            "id" => $this->id,
            "firstname" => $this->firstname,
            "name" => $this->name,
            "fullname" => $this->firstname.' '.$this->name,
            "email" => $this->email,
            "description" => $description,
            "training" => $training,
            "confidentiality" => $this->confidentiality,
            "slug" => $this->slug,
            "active" => $this->active,
            "pin" => [
                "location" => [
                    "lon" => floatval($this->longitude),
                    "lat" => floatval($this->latitude),
                ]
            ],
            "profile_media_id" => $this->profile_media_id,
            "created_at" => $this->created_at->format(\DateTimeInterface::ISO8601),
            "url" => parse_url($this->getUrl())['path'],
        ];
        $array['instances'] = array_map(function ($instance) {
            return ["id" => $instance['id']];
        }, $this->instances()->getResults()->toArray()??[]);

        $array['tags'] = array_map(function ($tag) {
            return ['id' => $tag['id'], 'name' => $tag['name']];
        }, (((session('instanceId')==null) ? $this->referencesForCommand : $this->references)->toArray()??[]));
        return $array;
    }

    public static function mapping()
    {
        $index = self::first()->getSearchIndex();
        $type = self::first()->getSearchType();
        return [
            'index' => $index,
            'body' => [
                'mappings' => [
                    $type => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'firstname' => ['type' => 'text'],
                            'name' => ['type' => 'text'],
                            'email' => ['type' => 'text'],
                            'description' => ['type' => 'text'],
                            'training' => ['type' => 'text'],
                            'slug' => ['type' => 'text'],
                            'profile_media_id' => ['type' => 'long'],
                            'active' => ['type' => 'long'],
                            'created_at' => ['type' => 'text', 'fielddata' => true],
                            'pin' => [
                                'properties' => [
                                    'location' => ['type' => 'geo_point']
                                ]
                            ],
                            'instances' => [
                                'properties' => [
                                    'id' => ['type' => 'long']
                                ]
                            ],
                            'tags' => [
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'name' => ['type' => 'text'],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getSearchIndex()
    {
        return env('SEARCH_INDEX_PREFIX', '') . $this->getTable();
    }

    public function getSearchType()
    {
        return $this->getTable();
    }

    public function toSearchArray()
    {

        return $this->toReducedArray();
    }

    public function instances()
    {
        return $this->belongsToMany('App\Instance', 'users_has_instances', 'users_id', 'instances_id')
            ->withPivot(['access_granted', 'roles_id'])
            ->withTimestamps();
    }


    public function usersGroups()
    {
        return $this->belongsToMany('App\UsersGroup', 'users_groups_has_users', 'users_id', 'users_groups_id');
    }

    public function virtualUsers()
    {
        return $this->hasMany('App\VirtualUser', 'users_id', 'id')
            ->orderBy('lastname', 'asc')
            ->orderBy('firstname', 'asc');
    }

    /*
     * return friends, membership and subscriptions of user
     */
    public function relations()
    {
        $friendist = $this->friendsList(0, 'forsearch');

        $subscriptions = [
            'channel' => [],
            'community' => [],
            'project' => [],
            'house' => [],
            'user' => [],
            'event' => [],
        ];
        foreach ($this->subscriptionsList as $subscription) {
            $subscriptions[strtolower(class_basename($subscription->profile))][] = $subscription->profile->id;
        }

        $memberShip = [
            'channel' => [],
            'community' => [],
            'project' => [],
            'house' => [],
        ];
        foreach (config('netframe.join_relations') as $profileJoin) {
            $relations = $this->{$profileJoin.'Invite'};
            if ($relations != null) {
                foreach ($relations as $relation) {
                    $memberShip[strtolower(class_basename($relation))][$relation->id] = $relation->pivot->status;
                }
            }
        }

        return [
            'friends' => $friendist,
            'subscriptions' => $subscriptions,
            'membership' => $memberShip,
        ];
    }

    /*
     * return array rights profile for session
     */
    public function storeInstanceProfile($instance)
    {
        //implement profile creation roles
        $userRole = auth()->guard('web')->user()->getInstanceRole();
        $profilesAuth = json_decode($instance->getParameter('profile_profile'), true);

        $profilesRights = [];
        if (!$this->visitor) {
            $profilesRights['userCanCreate'] = $profilesAuth[$userRole];

            if (session('instanceMonoProfile')) {
                //communityBy
                $profilesRights['communityBy'] = [
                    'user' => $profilesAuth[$userRole]['community'],
                    'community' => $profilesAuth['community']['community'],
                ];
                //channelBy
                $profilesRights['channelBy'] = [
                    'user' => $profilesAuth[$userRole]['channel'],
                    'community' => $profilesAuth['community']['channel'],
                ];
            } else {
                //houseBy
                $profilesRights['houseBy'] = [
                    'user' => $profilesAuth[$userRole]['house'],
                    'house' => $profilesAuth['house']['house'],
                    'community' => $profilesAuth['community']['house'],
                    'project' => $profilesAuth['project']['house'],
                ];
                //communityBy
                $profilesRights['communityBy'] = [
                    'user' => $profilesAuth[$userRole]['community'],
                    'house' => $profilesAuth['house']['community'],
                    'community' => $profilesAuth['community']['community'],
                    'project' => $profilesAuth['project']['community'],
                ];
                //projectBy
                $profilesRights['projectBy'] = [
                    'user' => $profilesAuth[$userRole]['project'],
                    'house' => $profilesAuth['house']['project'],
                    'community' => $profilesAuth['community']['project'],
                    'project' => $profilesAuth['project']['project'],
                ];
                //channelBy
                $profilesRights['channelBy'] = [
                    'user' => $profilesAuth[$userRole]['channel'],
                    'house' => $profilesAuth['house']['channel'],
                    'community' => $profilesAuth['community']['channel'],
                    'project' => $profilesAuth['project']['channel'],
                ];
            }
        }
        return $profilesRights;
    }

    /*
     * return user role (participant, administrator = owner, manager)
     */
    public function getInstanceRoleId()
    {
        $userInstance = $this->instances()->where('id', '=', session('instanceId'))->first();
        return $userInstance->pivot->roles_id;
    }

    /*
     * return user role (participant, administrator = owner, manager)
     */
    public function getInstanceRole()
    {
        $userInstance = $this->instances()->where('id', '=', session('instanceId'))->first();
        switch ($userInstance->pivot->roles_id) {
            case 1:
            case 2:
                return 'administrator';
                break;
            case 3:
                return 'manager';
                break;
            case 5:
                return 'user';
                break;
        }
        return null;
    }

    /*
     * return instance id, by user parameter, session or first instance
     */
    public function getInstanceId()
    {
        if (isset($this->current_instance_id) && $this->instances->contains($this->current_instance_id)) {
            $instance_id = $this->current_instance_id;
        } elseif (session()->exists('instanceId') && $this->instances->contains(session('instanceId'))) {
            $instance_id = session('instanceId');
        } else {
            $instance_id = $this->instances()->first()->id;
        }
        return $instance_id;
    }

    /*
     * relations with channels
     */

    /**
     * morph relation with news of profile
     */
    public function channelsProfile()
    {
        return $this->morphMany('App\Channel', 'profile')
            ->where('personnal', '=', 0)
            ->where('instances_id', '=', session('instanceId'));
    }

    public function allChannels()
    {
        return $this->belongsToMany('App\Channel', 'channels_has_users', 'users_id', 'channels_id')
        ->where('instances_id', '=', session('instanceId'))
        ->wherePivot('status', '=', '1')
        ->withPivot('status', 'roles_id')
        ->withTimestamps()
        ->with(['newsfeed' => function ($nf) {
            $nf->wherePivot('users_id', '=', auth()->guard('web')->user()->id)
            ->wherePivot('read', '=', 0);
        }]);
    }

    public function channels()
    {
        return $this->belongsToMany('App\Channel', 'channels_has_users', 'users_id', 'channels_id')
            ->where('personnal', '=', 0)
            ->where('instances_id', '=', session('instanceId'))
            ->wherePivot('status', '=', '1')
            ->withPivot('status', 'roles_id')
            ->withTimestamps()
            ->with(['newsfeed' => function ($nf) {
                $nf->wherePivot('users_id', '=', auth()->guard('web')->user()->id)
                    ->wherePivot('read', '=', 0);
            }]);
    }

    public function channelInvite()
    {
        return $this->belongsToMany('App\Channel', 'channels_has_users', 'users_id', 'channels_id')
            ->where('instances_id', '=', session('instanceId'))
            ->where('personnal', '=', 0)
            ->withPivot('status', 'roles_id')
            ->withTimestamps();
    }

    public function directMessagesChans()
    {
        return $this->belongsToMany('App\Channel', 'channels_has_users', 'users_id', 'channels_id')
            ->leftJoin('channels_has_users as chu', 'chu.channels_id', '=', 'channels_has_users.channels_id')
            ->leftJoin('users as otheruser', 'otheruser.id', '=', 'chu.users_id')
            ->where('chu.users_id', '!=', auth()->guard('web')->user()->id)
            ->where('otheruser.active', '=', 1)
            ->where('personnal', '=', 1)
            ->where('instances_id', '=', session('instanceId'))
            ->wherePivot('status', '=', '1')
            ->withPivot('status', 'roles_id')
            ->groupBy('channels.id')
            ->withTimestamps()
            ->with(['newsfeed' => function ($nf) {
                $nf->wherePivot('users_id', '=', auth()->guard('web')->user()->id)
                ->wherePivot('read', '=', 0);
            }]);
    }

    public function directMessagesChansActive()
    {
        return $this->directMessagesChans()
            ->leftJoin('channels_has_news_feeds', 'channels_has_news_feeds.channels_id', '=', 'channels.id')
            ->whereNotNull('channels_has_news_feeds.channels_id');
    }

    /**
     * morph relation with newsfeed of profile
     */
    public function posts()
    {
        return $this->morphMany('App\NewsFeed', 'author')
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * morph relation with newsfeed of profile
     */
    public function truePosts()
    {
        return $this->morphMany('App\NewsFeed', 'true_author')
            ->where('instances_id', '=', session('instanceId'));
    }


    /**
     * morph relation with news of profile
     */
    public function news()
    {
        return $this->morphMany('App\News', 'author')
        ->where('news.instances_id', '=', session('instanceId'));
    }

    /**
     * morph relation with news of profile
     */
    public function events()
    {
        return $this->morphMany('App\TEvent', 'author')
        ->where('events.instances_id', '=', session('instanceId'));
    }


    /**
     * morph relation to likes when user is liker
     */
    public function liking()
    {
        return $this->morphMany('App\Like', 'liker')
        ->where('instances_id', '=', session('instanceId'));
    }


    /**
     * morph relation to likes when user is liked
     */
    public function liked()
    {
        return $this->morphMany('App\Like', 'liked')
        ->where('instances_id', '=', session('instanceId'));
    }


    /**
     * morph relation with shares
     */
    public function shares()
    {
        return $this->morphMany('App\Share', 'author')
        ->where('shares.instances_id', '=', session('instanceId'));
    }

    /**
     * morph relation with offers of profile
     */
    public function offers()
    {
        return $this->morphMany('App\Offer', 'author')
            ->where('offers.instances_id', '=', session('instanceId'));
    }


    /**
     * morph relation to playlist
     */
    public function playlists()
    {
        return $this->morphMany('App\Playlist', 'author')
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * users playlists via users_id in playlists --> all playlists created by user
     */
    public function playlistsuser()
    {
        return $this->hasMany('App\Playlist', 'users_id', 'id')
            ->where('instances_id', '=', session('instanceId'));
    }


    /**
     * morph relation when profile appear in playlist item
     */
    public function playlisted()
    {
        return $this->morphMany('App\PlaylistItem', 'profile')
            ->where('instances_id', '=', session('instanceId'));
    }


    /**
     * morph relation when user comment post
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'author')
            ->where('comments.instances_id', '=', session('instanceId'));
    }


    /**
     * morph relation when user is followed
     */
    public function subscriptions()
    {
        return $this->morphMany('App\Subscription', 'profile')
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * morph relation when user make actions
     */
    public function actions()
    {
        return $this->morphMany('App\NetframeAction', 'author')
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * morph relation with messages group sended
     */
    public function sentMessageGroups()
    {
        return $this->morphMany('App\MessageGroup', 'sender')
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * morph relation with messages group received
     */
    public function receivedMessageGroups()
    {
        return $this->morphMany('App\MessageGroup', 'receiver')
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * morph relation with messages sended
     */
    public function sentMessages()
    {
        return $this->morphMany('App\Message', 'sender')
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * morph relation with messages received
     */
    public function receivedMessages()
    {
        return $this->morphMany('App\Message', 'receiver')
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * check buzz of profile always return false for a user
     */
    public function isBuzz()
    {
        return false;
    }

    /**
     * get friends of user
     */
    public function friendsList($toTake = 0, $parameter = null)
    {
        //return $this->hasMany('Friends', 'users_id', 'id');
        $friendsRelations = Friends::where('blacklist', '=', 0)
            ->where('instances_id', '=', session('instanceId'))
            ->where(function ($for) use ($parameter) {
                if ($parameter == null) {
                    $for->where('status', '=', 1);
                }
            })
            ->where(function ($whereUser) {
                $whereUser->orWhere('friends_id', '=', $this->id)
                    ->orWhere('users_id', '=', $this->id);
            })
            ->orderBy('updated_at', 'DESC')
            ->get();

        $tabFriends = array();
        foreach ($friendsRelations as $friend) {
            $friendId = ($friend->users_id == $this->id) ? $friend->friends_id : $friend->users_id;
            $objFriend = User::findOrFail($friendId);
            if ($objFriend != null && $objFriend->active == 1) {
                switch ($parameter) {
                    case 'forsearch':
                        $tabFriends[$objFriend->id] = $friend ;
                        break;
                    default:
                        $tabFriends[] = $objFriend ;
                        break;
                }
            }
            if ($toTake != 0 && count($tabFriends) == $toTake) {
                break;
            }
        }
        return $tabFriends;
    }

    /**
     * Get Report Abuse
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reportAbuse()
    {
        return $this->belongsToMany('App\ReportAbuse', 'users_has_report_abuses', 'users_id', 'report_abuses_id')
            ->where('instances_id', '=', session('instanceId'))
            ->withTimestamps();
    }

    /**
     * get interest of user
     */
    public function interests()
    {
        return $this->hasMany('App\Interest', 'users_id', 'id')
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * relations with documents of user
     */
    public function documents()
    {
        return $this->hasMany('App\UsersDocument', 'users_id', 'id')
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * relations with parameters of user
     */
    public function parameters()
    {
        return $this->hasMany('App\UserParameter', 'users_id', 'id')
            ->where('instances_id', '=', session('instanceId'));
    }

    /*
     * relation with devices
     */
    public function devices()
    {
        return $this->hasMany('App\UserMobileDevice', 'users_id', 'id')
            ->where('instances_id', '=', session('instanceId'));
    }

    /*
     * relation with fcm token trow devices
     */
    public function tokensFcm()
    {
        return $this->hasManyThrough(
            'App\DeviceFcmToken',
            'App\UserMobileDevice',
            'users_id',
            'device_uuid',
            'id',
            'duuid'
        );
    }

    public static function lastValidated($limit = 4)
    {
        //whereNotNull('profile_media_id')
        return User::whereHas('instances', function ($wI) {
                $wI->where('id', '=', session('instanceId'));
        })
            ->where('active', '=', 1)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * get parameters of user
     */
    public function getParameter($parameterName, $returnObj = null)
    {
        $parameter = $this->parameters
            ->where('parameter_name', '=', $parameterName)
            ->where('instances_id', '=', $this->getInstanceId())
            ->first();
        if ($parameter != null) {
            if ($returnObj != null) {
                return $parameter;
            } else {
                return $parameter->parameter_value;
            }
        } else {
            return null;
        }
    }

    public function setParameter($parameterName, $value)
    {
        $existsParameter = $this->getParameter($parameterName, true);
        if ($existsParameter != null) {
            $existsParameter->parameter_value = $value;
            $existsParameter->save();
        } else {
            $parameter = new UserParameter();
            $parameter->users_id = $this->id;
            $parameter->instances_id = $this->getInstanceId();
            $parameter->parameter_name = $parameterName;
            $parameter->parameter_value = $value;
            $parameter->save();
        }
    }

    public function deleteParameter($parameterName)
    {
        $existsParameter = $this->getParameter($parameterName, true);
        if ($existsParameter != null) {
            \Log::info($existsParameter->id);
            $existsParameter->delete();
        }
    }

    /**
     * get subscriptions of user
     */
    public function subscriptionsList()
    {
        return $this->hasMany('App\Subscription', 'users_id', 'id')
            ->where('instances_id', '=', session('instanceId'));
    }

    public function project()
    {
        return $this->belongsToMany('App\Project', 'projects_has_users', 'users_id', 'projects_id')
            ->where('instances_id', '=', session('instanceId'))
            ->withPivot('roles_id', 'status')
            ->wherePivot('status', '=', 1);
    }

    public function projectInvite()
    {
        return $this->belongsToMany('App\Project', 'projects_has_users', 'users_id', 'projects_id')
            ->where('instances_id', '=', session('instanceId'))
            ->withPivot('roles_id', 'status');
    }


    public function houses()
    {
        return $this->belongsToMany('App\House', 'houses_has_users', 'users_id', 'houses_id')
            ->where('instances_id', '=', session('instanceId'))
            ->withPivot('status', 'roles_id')
            ->wherePivot('status', '=', 1)
            ->withTimestamps();
    }

    public function house()
    {
        return $this->belongsToMany('App\House', 'houses_has_users', 'users_id', 'houses_id')
            ->where('instances_id', '=', session('instanceId'))
            ->withPivot('status', 'roles_id')
            ->wherePivot('status', '=', 1)
            ->withTimestamps();
    }

    public function houseInvite()
    {
        return $this->belongsToMany('App\House', 'houses_has_users', 'users_id', 'houses_id')
        ->where('instances_id', '=', session('instanceId'))
        ->withPivot('status', 'roles_id')
        ->withTimestamps();
    }

    public function community()
    {
        return $this->belongsToMany('App\Community', 'community_has_users', 'users_id', 'community_id')
            ->where('instances_id', '=', session('instanceId'))
            ->withPivot('status', 'roles_id')
            ->wherePivot('status', '=', 1)
            ->withTimestamps();
    }

    public function communityInvite()
    {
        return $this->belongsToMany('App\Community', 'community_has_users', 'users_id', 'community_id')
        ->where('instances_id', '=', session('instanceId'))
        ->withPivot('status', 'roles_id')
        ->withTimestamps();
    }

    public function country()
    {
        return $this->belongsTo('App\Country', 'pays', 'iso')->where('lang', '=', Lang::locale());
    }

    public function nationalityCountry()
    {
        return $this->belongsTo('App\Country', 'nationality', 'iso')->where('lang', '=', Lang::locale());
    }


    public function media()
    {
        return $this->belongsToMany('App\Media', 'users_has_medias', 'users_id', 'medias_id')
            ->where('instances_id', '=', session('instanceId'));
    }


    public function medias()
    {
        return $this->belongsToMany('App\Media', 'users_has_medias', 'users_id', 'medias_id')
            ->where('medias.instances_id', '=', session('instanceId'))
            ->withPivot(['profile_image'])
            ->where('under_workflow', '=', 0);
    }

    public function mediasFolders()
    {
        return $this->morphMany('App\MediasFolder', 'profile')
            ->where('instances_id', '=', session('instanceId'))
            ->orderBy('name');
    }

    public function getDefaultFolder($folderName)
    {
        $mediaFolderObject = $this->mediasFolders()->where('name', '=', $folderName)->first();
        return ($mediaFolderObject != null) ? $mediaFolderObject->id : null;
    }

    public function personalFolders()
    {
        return $this->hasMany('App\MediasFolder', 'personnal_user_folder', 'id')
            ->where('instances_id', '=', session('instanceId'))
            ->where('personnal_folder', true)
            ->orderBy('name');
    }

    public function allPersonalFolders()
    {
        return $this->hasMany('App\MediasFolder', 'personnal_user_folder', 'id')
            ->where('instances_id', '=', session('instanceId'))
            ->where('personnal_folder', true)
            ->orderBy('name');
    }

    public function allMedias()
    {
        return $this->hasMany('App\Media', 'users_id', 'id')
            ->where('instances_id', '=', session('instanceId'));
    }

    public function hasEncodedMedias()
    {
        foreach ($this->allMedias as $media) {
            if ($media->encoded == 1) {
                return true;
            }
        }

        return false;
    }

    public function lastMedias()
    {
        return $this->hasMany('App\Media', 'users_id', 'id')
            ->where('instances_id', '=', session('instanceId'))
            ->orderBy('created_at', 'desc');
    }


    /*
     * public function news()
     * {
     * return $this->hasMany('News', 'users_id');
     * }
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }


    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }


    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->remember_token;
    }


    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }


    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }


    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }


    /**
     * Get the firstname for the user.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstname;
    }


    /**
     * Get the name for the user.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /*
     *
     * get user initials to build avatar
     *
     */
    public function initials()
    {
        return strtoupper(substr($this->firstname, 0, 1) . substr($this->name, 0, 1));
    }

    /*
     *
     * generate html color from initials
     *
     */
    public function initialsToColor()
    {
        $code = dechex(crc32($this->initials()));
        $code = substr($code, 0, 6);
        return '#' . $code;
    }

    public function initialsToColorRgb()
    {
        list($r, $g, $b) = sscanf($this->initialsToColor(), "#%02x%02x%02x");
        return $r .',' . $g .',' . $b;
    }


    public function getNameDisplay()
    {
        return $this->firstname . ' ' . $this->name;
    }


    /**
     * @TODO à compléter voir supprimer si inutile
     */
    public static function getUri($slug, $fullName)
    {
        $fullName = explode('-', $fullName);

        $firstname = $fullName[0];
        $name = $fullName[1];

        return static::where('slug', '=', $slug)->where('firstname', '=', $firstname)
            ->where('name', '=', $name)
            ->first();
    }


    public function getUrl()
    {
        return url()->route(
            'user.wall',
            [
                'slug' => $this->slug,
                'fullname' => str_slug($this->firstname.' '.$this->name)
            ]
        );
    }


    public function getSpokenLanguages()
    {
        $spoken_languages = \App\RefLang::select('name')->whereIn('iso_639_2', explode(',', $this->spoken_languages))
            ->where('lang', '=', \Lang::locale())
            ->where('iso_639_2', '!=', '')
            ->get();
        return $spoken_languages;
    }


    public function images()
    {
        return $this->medias()
            ->where('instances_id', '=', session('instanceId'))
            ->where('type', '=', Media::TYPE_IMAGE)
            ->orderBy('updated_at', 'DESC');
    }

    public function mosaicImage()
    {
        return $this->profileImage;
    }

    public function profileImage()
    {
        return $this->hasOne('App\Media', 'id', 'profile_media_id');
    }

    public function profileImageThumbUrl()
    {
        if ($this->profileImage != null) {
            return url()->route(
                'media_download',
                [
                    'id' => $this->profileImage->id,
                    'thumb' => 1
                ]
            );
        } else {
            return '';
        }
    }

    public function coverImage()
    {
        return $this->hasOne('App\Media', 'id', 'cover_media_id');
    }

    public function profileImageSrc()
    {
        if ($this->profileImage != null) {
            return url()->route('media_download', array('id' => $this->profileImage->id, 'thumb' => 1));
        } else {
            return url()->route('netframe.svgicon', ['name' => 'user']);
        }
    }

    public function updateLastConnect()
    {
        $this->last_connexion = date('Y-m-d H:i:s');
        $this->save();
    }


    public function follow($typeForeign, $idForeign)
    {
        $idForeign = intval($idForeign);
        $tableName = Subscription::getTableName();
        $query = \DB::table($tableName)->select('id')
            ->where('instances_id', '=', session('instanceId'))
            ->where('profile_id', '=', $idForeign)
            ->where('profile_type', '=', $typeForeign)
            ->where('users_id', '=', intval($this->id));

        $result = $query->count();

        return $result;
    }

    public function followConfidentiality($typeForeign, $idForeign)
    {
        $idForeign = intval($idForeign);
        $tableName = Subscription::getTableName();
        $query = \DB::table($tableName)->select('confidentiality')
            ->where('instances_id', '=', session('instanceId'))
            ->where('profile_id', '=', $idForeign)
            ->where('profile_type', '=', $typeForeign)
            ->where('users_id', '=', intval($this->id))
            ->get();

        if (count($query) > 0) {
            $result = $query[0]->confidentiality;
            return true;
            return $result;
        } else {
            return false;
        }
    }


    public function isOnline()
    {
        $lastAction = $this->last_connexion;
        if (null !== $lastAction) {
            $datetime1 = new \DateTime(($lastAction));
            $datetime2 = new \DateTime((date("Y-m-d H:i:s")));
            $interval = $datetime1->diff($datetime2);
            $online = ($interval->days * 86400 + $interval->h * 3600 + $interval->i * 60 + $interval->s < 300)
                ? true
                : false;
            return $online;
        }
        return false;
    }


    /**
     * return followerList.
     */
    public function followers()
    {
        $tableNameUser = User::getTableName();
        $tableNameSubs = Subscription::getTableName();

        $result = User::select(
            $tableNameUser . '.id',
            $tableNameUser . '.firstname',
            $tableNameUser . '.name',
            $tableNameUser . '.profile_media_id',
            $tableNameUser . '.slug'
        )->leftjoin(
            $tableNameSubs,
            $tableNameUser . '.id',
            '=',
            $tableNameSubs . '.users_id'
        )
            ->where($tableNameSubs . '.instances_id', '=', session('instanceId'))
            ->where($tableNameSubs . '.profile_id', '=', $this->id)
            ->where($tableNameSubs . '.profile_type', '=', 'App\User')
            ->where($tableNameUser.'.active', '=', 1)
            ->get();

        return $result;
    }


    /**
     * return array items associated with medias of instant playlist of user.
     */
    public static function instantPlaylistItems()
    {
        if (auth()->guard('web')->check()) {
            $listItems = Playlist::where('playlists.instances_id', '=', session('instanceId'))
                ->where('playlists.author_id', '=', auth()->guard('web')->user()->id)
                ->where('playlists.author_type', '=', 'User')
                ->where('playlists.instant_playlist', '=', 1)
                ->join('playlists_items', 'playlists_items.playlists_id', '=', 'playlists.id')
                ->get(array('playlists_items.medias_id'));

            $tabItems = array();
            foreach ($listItems as $item) {
                $tabItems[$item->medias_id] = 1;
            }
            return $tabItems;
        }
    }

    /**
     * return array items associated with profiles of instant playlist of user.
     */
    public static function instantPlaylistProfiles()
    {
        if (auth()->guard('web')->check()) {
            $listItems = \App\Playlist::where('playlists.instances_id', '=', session('instanceId'))
                ->where('playlists.author_id', '=', auth()->guard('web')->user()->id)
                ->where('playlists.author_type', '=', 'App\\User')
                ->where('playlists.instant_playlist', '=', 1)
                ->join('playlists_items', 'playlists_items.playlists_id', '=', 'playlists.id')
                ->get(array('profile_id', 'profile_type'));

                $tabItems = array();
            foreach ($listItems as $item) {
                $tabItems[$item->profile_type][$item->profile_id] = 1;
            }
                return $tabItems;
        }
    }

    public function hasProfiles()
    {
        $profilesList = config('netframe.list_profile');
        foreach ($profilesList as $profile) {
            if ($this->$profile->count() > 0) {
                return true;
            }
        }
        return false;
    }

    public function allProfiles()
    {
        $user = $this;

        return array_reduce(
            config('netframe.list_profile'),
            function ($profiles, $profileType) use ($user) {
                return array_merge($profiles, $user->$profileType->all());
            },
            []
        );
    }

    public function isFollowedByCurrentUser()
    {
        if (auth()->guard('web')->check()) {
            return Subscription::checkSubscribe($this->id, get_class($this));
        } else {
            return false;
        }
    }

    public function postedUserReferences($userId, $status)
    {
        return $this
            ->hasMany('App\UsersReference', 'users_id_create', 'id')
            ->where('users_id', '=', $userId)
            ->where('status', '=', $status)
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * relation with user participation o nevents
     */
    public function participateEvent()
    {
        return $this->belongsToMany('App\TEvent', 'events_has_friends', 'users_id', 'events_id')
            ->where('instances_id', '=', session('instanceId'))
            ->withPivot('status');
    }

    public function references()
    {
        return $this->belongsToMany('App\Tag', 'users_references', 'users_id', 'tags_id')
            ->where('users_references.instances_id', '=', session('instanceId'))
            ->withPivot(['like', 'users_id', 'status']);
    }

    public function userReferences()
    {
        return $this->hasMany('App\UsersReference', 'users_id', 'id')
            ->where('users_references.instances_id', '=', session('instanceId'))
            ->with('reference');
    }

    public function userValidReferences()
    {
        return $this->hasMany('App\UsersReference', 'users_id', 'id')
            ->where('instances_id', '=', session('instanceId'))
            ->where('status', '=', '1')
            ->with('reference');
    }

    public function userNotifications()
    {
        return $this->hasMany('App\UserNotification', 'users_id', 'id')
            ->where('instances_id', '=', session('instanceId'));
    }

    /**
     * int $userId
     * return sidebar object for user
     */
    public function sideBarUser()
    {
        $data = array();
        $user = $this;
        //$data['dataUser'] = $this;
        $data['profileSidebar'] = $this;
        $data['zoomMapBox'] = config('location.zoom-map-sidebar');

        //get last 5 notifications


        $data['notifications'] = array();
        $data['interests'] = array();
        if (auth()->guard('web')->check() && $this->id == auth()->guard('web')->user()->id) {
            $notificationsRepository = new \NotificationsRepository();
            $data['notifications'] = $notificationsRepository->findWaiting([0,5], false);

            $searchRepo = new \SearchRepository2;
            $targetsProfiles = \SearchRepository2::$searchProfiles;
            $searchParameters = $searchRepo->initializeConfig('search_mosaic', $targetsProfiles, false, 1, 8);
            $results = $searchRepo->search($searchParameters, $targetsProfiles);
            $data['interests'] = $results[0];
        }

        $data['sidebarProfiles'] = array();

        $data['playlistsuser'] = $user->playlistsuser->take(3);

        //subscriptions
        $subscriptions = $this->subscriptionsList->take(4);
        $subscribeProfiles = [];
        foreach ($subscriptions as $suscribe) {
            $subscribeProfiles[] = $suscribe->profile;
        }

        $data['sidebarProfiles'][] = [ 'type'=>'subscriptions', 'profiles' => $subscribeProfiles ];
        $data['sidebarProfiles'][] = [ 'type'=>'friends', 'profiles' => $this->friendsList(4) ];
        $data['sidebarProfiles'][] = [ 'type'=>'followers', 'profiles' => $this->followers()->take(4) ];
        $data['sidebarPages'][] = [ 'type'=>'project', 'profiles' => $this->project->take(2) ];
        $data['sidebarPages'][] = [ 'type'=>'community', 'profiles' => $this->community->take(4) ];
        $data['sidebarPages'][] = [ 'type'=>'house', 'profiles' => $this->houses->take(4) ];

        $data['displayUserPages'] = false;
        foreach ($data['sidebarPages'] as $pageType) {
            if (count($pageType['profiles']) > 0) {
                $data['displayUserPages'] = true;
            }
        }

        return $data;
    }

    //return netframe action linking subscriptions of user
    public function networkActivity($limitPost = 5)
    {
        $userId = $this->id;
        return App\NewsFeed::select('news_feeds.*')
                ->leftJoin('subscriptions as sub', function ($joinS) {
                    $joinS->on('sub.profile_type', '=', 'news_feeds.author_type')
                        ->on('sub.profile_id', '=', 'news_feeds.author_id');
                })

                ->leftJoin('playlists_items as playI', function ($playI) {
                    $playI->on('playI.profile_type', '=', 'news_feeds.author_type')
                        ->on('playI.profile_id', '=', 'news_feeds.author_id');
                })
                ->leftJoin('comments as com', function ($joinC) {
                    $joinC->on('com.post_type', '=', 'news_feeds.post_type')
                        ->on('com.post_id', '=', 'news_feeds.post_id');
                })
                ->leftJoin('events_has_friends as ehf', function ($joinE) {
                    $joinE->on('ehf.events_id', '=', 'news_feeds.post_id')
                    ->where('news_feeds.post_type', '=', 'TEvent');
                })
                /*
                ->where(function($where) use ($date_time_last) {
                    if($date_time_last !== null){
                        $where->where('news_feeds.updated_at', '<', $date_time_last);
                    }
                })
                */
                ->where(function ($where) use ($userId) {
                    $where->orWhere(function ($whereS) use ($userId) {
                        $whereS->where('sub.users_id', '=', $userId)
                            ->where('news_feeds.confidentiality', '>=', \DB::raw('sub.confidentiality'));
                    })
                    ->orWhere(function ($whereP) use ($userId) {
                        $whereP->where('playI.users_id', '=', $userId);
                    })
                    ->orWhere(function ($whereC) use ($userId) {
                        $whereC->where('com.author_id', '=', $userId)
                            ->where('com.author_type', '=', 'User');
                    })
                    ->orWhere(function ($whereE) use ($userId) {
                        $whereE->where('ehf.users_id', '=', $userId);
                    });
                })
                ->where('news_feeds.post_type', '=', 'NetframeAction')
                ->where('news_feeds.active', '=', '1')
                ->groupBy('news_feeds.post_type')
                ->groupBy('news_feeds.post_id')
                ->orderBy('news_feeds.updated_at', 'desc')
                ->take($limitPost)
                ->with(['post', 'author'])
                ->get();
    }

    /**
     * return user list (email, firstname, name) with active account and email (no bounce)
     */
    public static function getActiveMails()
    {
        return self::select(['email', 'firstname', 'name'])
            ->where('email', 'like', '%@%')
            ->where('active', '=', 1)
            ->get();
    }

    public function getMediaSize()
    {
        $mediaSize = round($this->getParameter('medias_size') / 1024 / 1024 / 1024, 0);

        return $mediaSize;
    }

    public function autoSubscribe(Instance $instance)
    {
        // get houses auto subscribe
        $houses = $instance->houses()->where('auto_subscribe', '=', 1)->get();
        $arrayNewSubscribe = [];
        foreach ($houses as $house) {
            $arrayNewSubscribe[] = [
                'users_id' => $this->id,
                'instances_id' => $instance->id,
                'profile_id' => $house->id,
                'profile_type' => get_class($house),
                'level' => 1,
                'confidentiality' => 1,
                'created_at' => \Carbon\Carbon::now()->toDateString(),
                'updated_at' => \Carbon\Carbon::now()->toDateString(),
            ];
        }
        Subscription::insert($arrayNewSubscribe);

        // get projects auto subscribe
        $projects = $instance->projects()->where('auto_subscribe', '=', 1)->get();
        $arrayNewSubscribe = [];
        foreach ($projects as $project) {
            $arrayNewSubscribe[] = [
                'users_id' => $this->id,
                'instances_id' => $instance->id,
                'profile_id' => $project->id,
                'profile_type' => get_class($project),
                'level' => 1,
                'confidentiality' => 1,
                'created_at' => \Carbon\Carbon::now()->toDateString(),
                'updated_at' => \Carbon\Carbon::now()->toDateString(),
            ];
        }
        Subscription::insert($arrayNewSubscribe);

        // get communities auto subscribe
        $communities = $instance->communities()->where('auto_subscribe', '=', 1)->get();
        $arrayNewSubscribe = [];
        foreach ($communities as $community) {
            $arrayNewSubscribe[] = [
                'users_id' => $this->id,
                'instances_id' => $instance->id,
                'profile_id' => $community->id,
                'profile_type' => get_class($community),
                'level' => 1,
                'confidentiality' => 1,
                'created_at' => \Carbon\Carbon::now()->toDateString(),
                'updated_at' => \Carbon\Carbon::now()->toDateString(),
            ];
        }
        Subscription::insert($arrayNewSubscribe);

        // channels autosubscribe
        $channels = $instance->channels()->where('auto_subscribe', '=', 1)->get();
        $arrayNewSubscribe = [];
        foreach ($channels as $channel) {
            $this->channels()->attach($channel->id, ['roles_id' => '4', 'status' => 1]);
        }
    }


    public function autoMember(Instance $instance)
    {
        // get houses auto subscribe
        $houses = $instance->houses()->where('auto_member', '!=', 0)->get();
        foreach ($houses as $house) {
            $house->users()->attach($this->id, ['roles_id' => $house->auto_member]);
        }

        // get projects auto subscribe
        $projects = $instance->projects()->where('auto_member', '!=', 0)->get();
        foreach ($projects as $project) {
            $project->users()->attach($this->id, ['roles_id' => $project->auto_member]);
        }

        // get communities auto subscribe
        $communities = $instance->communities()->where('auto_member', '!=', 0)->get();
        foreach ($communities as $community) {
            $community->users()->attach($this->id, ['roles_id' => $community->auto_member]);
        }
    }


    public function referencesForCommand()
    {
        return $this->belongsToMany('App\Tag', 'users_references', 'users_id', 'tags_id')
            ->withPivot(['like', 'users_id', 'status']);
    }

    public function drives()
    {
        return $this->hasMany('App\Drive', 'users_id', 'id');
    }

    public function calendars()
    {
        $param = $this->getParameter("calendars_api");
        if (isset($param)) {
            $param = json_decode($param, true);
        } else {
            $param = [];
        }
        $calendars = array();
        foreach ($param as $v) {
            $calendars[] = new CalendarApi($v);
        }
        return $calendars;
    }

    public function deleteCalendar($email)
    {
        $param = $this->getParameter("calendars_api");
        $calendars = json_decode($param, true);
        for ($i=0; $i < count($calendars); $i++) {
            if ($calendars[$i]['email']==$email) {
                unset($calendars[$i]);
            }
        }
        $new = array();
        for ($i=0; $i < count($calendars); $i++) {
            if (isset($calendars[$i])) {
                $new[] = $calendars[$i];
            }
        }
        $this->setParameter("calendars_api", json_encode($new));
    }

    public function customFields()
    {
        $instance = Instance::find(session('instanceId'));
        $fields = json_decode($instance->getParameter('custom_user_fields'), true) ?? [];
        $return = [];
        foreach ($fields as $key => $value) {
            $return[] = ['name' => $value['name'], 'value' => $this->getParameter('custom_user_field_'.$key) ?? ''];
        }
        return $return;
    }

    public function validCustomFields()
    {
        $instance = Instance::find(session('instanceId'));
        $fields = json_decode($instance->getParameter('custom_user_fields'), true) ?? [];
        $return = [];
        foreach ($fields as $key => $value) {
            if ($this->getParameter('custom_user_field_'.$key) != '') {
                $return[] = ['name' => $value['name'], 'value' => $this->getParameter('custom_user_field_'.$key) ?? ''];
            }
        }
        return $return;
    }

    public function canJoinStage($channelId)
    {
        return ($this->allChannels()->where('channels.id', '=', $channelId)->first() != null) ? true : false;
    }

    public function projects()
    {
        return $this->morphMany('App\TaskTable', 'author')
            ->where('instances_id', '=', session('instanceId'));
    }

    public function tasks()
    {
        return $this->morphMany('App\TaskTable', 'author')
        ->where('instances_id', '=', session('instanceId'));
    }

    public function colabDocs()
    {
        return $this->hasMany('App\ColabDoc', 'users_id', 'id');
    }

    /*
     * add auto member, auto subscribe, default folders, default notifi schema
     */
    public function createDefaults(Instance $instance)
    {
        $this->autoSubscribe($instance);
        $this->autoMember($instance);

        // create user mail notification planning
        $mailPlanning = config('users.defaultMailPlanning');
        foreach ($mailPlanning as $dayMail) {
            $userNotification = new UserNotification();
            $userNotification->users_id = $this->id;
            $userNotification->instances_id = $instance->id;
            $userNotification->device = 'mail';
            $userNotification->frequency = $dayMail;
            $userNotification->save();
        }

        // generate default folders
        MediasFolder::generateDefault($this, $instance->id, $this);
    }

    public function finalizeBoarding(User $user, Instance $instance)
    {
        session([
            'instanceId' => $instance->id,
            //'instance' => $instance
        ]);
        session(['withoutInstance' => false]);

        //attach user to session
        $role = (session()->has('boarding.main-user')) ? 1 : 5;
        $user->instances()->attach($instance->id, ['roles_id' => $role]);

        //get boarding informations
        if (session()->has('boarding.boarding')) {
            $boarding = Boarding::find(session('boarding.boarding'));
        }

        if ($role == 1) {
            Mail::to($user->email)->send(new BoardingDemand($boarding, $instance));
            $instance->newUser();
        }

        // prepare redirect
        if (session()->has('boarding.main-user')) {
            $redirect = "adminBoarder";
        } else {
            $redirect = "userBoarder";
        }

        //remove boarding informations
        if (session()->has('boarding')) {
            if (session()->has('boarding.boarding')) {
                $boarding->delete();
            }
            session()->forget('boarding');
        }

        // check if email is register in boarding
        $testBoardingEmail = Boarding::where('email', '=', $user->email)->first();
        if ($testBoardingEmail != null) {
            $testBoardingEmail->delete();
        }

        // Logged in user
        auth()->guard('web')->loginUsingId($user->id, true);

        // create defaults
        $this->createDefaults($instance);

        // Initialize and Storage data in profiles session
        $profile = User::find($user->id);
        $profile->save();
        $this->storeProfileSession($profile, "user");

        session(['justCreated' => true]);

        // index user
        event(new \App\Events\UserUpdatedEvent($user));


        if ($redirect == "adminBoarder") {
            session(['inCreation' => true]);
            return redirect()->to($instance->getUrl().'/welcome/subscription');
        } else {
            return redirect()->route('boarding.confirm.creation', ['type' => 'user']);
        }
    }

    /**
     * Initialize session profiles and storage data in object array
     *
     * @param (object) $profile
     * @param (string) $model give type profile in string for calling model
     */
    public function storeProfileSession($profile, $model)
    {
        //check instances
        if (!session()->has('instanceId')) {
            $instance = $profile->instances->first();
        } elseif ($profile->instances->contains(session('instanceId'))) {
            $instance = $profile->instances()->where('id', '=', session('instanceId'))->first();
        } else {
            // logout user and redirect to login page
            SessionHelper::destroyProfile();
            auth()->guard('web')->logout();
            session()->flush();
            return redirect()->to('/');
        }
        session([
            'instanceId' => $instance->id,
            //'instance' => $instance,
            'instanceRole' => $profile->getInstanceRole(),
            'instanceRoleId' => $instance->pivot->roles_id,
        ]);

        session(['visitor' => $profile->visitor]);

        \Lang::setLocale($profile->lang);

        // get and store profiles creation authorizations
        $profileAuth = $profile->storeInstanceProfile($instance);
        session(['profileAuth' => $profileAuth]);

        // Initialize session array navigation for profile user
        SessionHelper::initProfile();

        // log user loggued
        event(new UserLogguedEvent($profile, $instance));

        //get array of list profile
        session([
            "allProfiles" => Netframe::getProfiles(auth()->guard('web')->user()->id),
            "allFeeds" => auth()
                ->guard('web')
                ->user()
                ->channels()
                ->where('active', '=', 1)
                ->orderBy('name')
                ->pluck('name', 'id'),
            "acl" => Netframe::getAcl(auth()->guard('web')->user()->id),
        ]);
    }

    public function workflows()
    {
        return $this->hasMany('App\Workflow', 'users_id', 'id')
            ->where('instances_id', '=', session('instanceId'));
    }

    public function stats()
    {
        return $this->morphMany('App\Stat', 'entity')
            ->where('instances_id', '=', session('instanceId'));
    }
}
