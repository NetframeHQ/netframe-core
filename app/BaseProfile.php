<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Buzz;
use App\Profile;
use App\User;
use App\Playlist;
use App\Subscription;
use App\Events\InterestAction;
use App\Events\NewPost;

abstract class BaseProfile extends Model
{
    protected $profile = 'UNDEFINED';
    public $rolesLangKey = 'members.roles.';

    abstract public function medias();

    public function getType()
    {
        return $this->type;
    }

    public function getInstanceRelation()
    {
        return $this->instanceRelation;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function owner()
    {
        return $this->morphTo();
    }

    public function houses()
    {
        return $this->morphMany('App\House', 'owner')->where('active', '=', 1);
    }

    public function projects()
    {
        return $this->morphMany('App\Project', 'owner')->where('active', '=', 1);
    }

    public function communities()
    {
        return $this->morphMany('App\Community', 'owner')->where('active', '=', 1);
    }

    public function author()
    {
        return $this->belongsTo(get_class($this), 'id', 'id');
    }

    /*
     * create default users folders
     */
    public function createPersonnalFolders($userId = null)
    {
        if ($userId == null) {
            foreach ($this->users as $user) {
                if ($this->allPersonalFolders()->where('personnal_user_folder', $user->id)->first() == null) {
                    $folder = new MediasFolder();
                    $folder->personnal_user_folder = $user->id;
                    $folder->personnal_folder = true;
                    $folder->users_id = $user->id;
                    $folder->instances_id = session('instanceId');
                    $folder->profile_id = $this->id;
                    $folder->profile_type = get_class($this);
                    $folder->save();
                }
            }
        } else {
            $folder = new MediasFolder();
            $folder->personnal_user_folder = $userId;
            $folder->personnal_folder = true;
            $folder->users_id = $userId;
            $folder->instances_id = session('instanceId');
            $folder->profile_id = $this->id;
            $folder->profile_type = get_class($this);
            $folder->save();
        }
    }

    /**
     * morph relation with news of profile
     */
    public function channels()
    {
        return $this->morphMany('App\Channel', 'profile')
            ->where('instances_id', '=', session('instanceId'));
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function has_defaultChannel()
    {
        return $this->channels()->where('default_channel', '=', 1);
    }


    /*
     * create default channel associated to profile
     */
    public function createDefaultChannel()
    {
        // create channel
        $channel = new Channel();
        $channel->instances_id = session('instanceId');
        $channel->users_id = auth()->guard('web')->user()->id;
        $channel->profile_id = $this->id;
        $channel->profile_type = get_class($this);
        $channel->name = $this->getNameDisplay();
        $channel->description = $this->description;
        $channel->confidentiality = $this->confidentiality;
        $channel->default_channel = 1;
        $channel->save();

        // Save the tags
        \App\Helpers\TagsHelper::attachPostedTags($this->tagsList(), $channel);

        if ($channel->tags != null) {
            event(new InterestAction(auth()->guard('web')->user(), $this->tagsList(), 'profile.create'));
        }

        auth()->guard('web')->user()->channels()->attach($channel->id, ['roles_id' => 1, 'status' => 1]);

        // attach all profile users to channel
        foreach ($this->users as $profileUser) {
            if ($profileUser->id != auth()->guard('web')->user()->id) {
                $profileUser->channels()->attach($channel->id, ['roles_id' => 1, 'status' => 4]);
            }
        }
    }

    /*
     * create default task list associated to profile
     */
    public function createDefaultTasks($taskTemplateId)
    {
        // get task template
        $template = Template::find($taskTemplateId);

        // create tasklist
        $taskList = new TaskTable();
        $taskList->name = $this->getNameDisplay();
        $taskList->users_id = auth('web')->user()->id;
        $taskList->instances_id = session('instanceId');
        $taskList->author_id = $this->id;
        $taskList->author_type = get_class($this);
        $taskList->tables_templates_id = $template->id;
        $taskList->has_medias = $template->has_medias;
        $taskList->cols = "";
        $taskList->default_task = 1;
        $taskList->save();

        // fields for NewPost event
        $taskList->true_author_id = $this->id;
        $taskList->true_author_type = get_class($this);
        $taskList->confidentiality = 1;

        event(new NewPost("TaskTable", $taskList, null, null, null));
    }

    /**
     * morph relation with newsfeed of profile
     */
    public function posts()
    {
        return $this->morphMany('App\NewsFeed', 'author');
    }

    /**
     * morph relation with newsfeed of profile
     */
    public function truePosts()
    {
        return $this->morphMany('App\NewsFeed', 'true_author');
    }

    /**
     * morph relation with news of profile
     */
    public function news()
    {
        return $this->morphMany('App\News', 'author');
    }

    /**
     * morph relation with netframes actions of profile
     */
    public function netframeActions()
    {
        return $this->morphMany('App\NetframeAction', 'author');
    }

    /**
     * morph relation to likes when profile is liker
     */
    public function liking()
    {
        return $this->morphMany('App\Like', 'liker');
    }

    /**
     * morph relation to likes when profile is liked
     */
    public function liked()
    {
        return $this->morphMany('App\Like', 'liked');
    }

    /**
     * morph relation with shares
     */
    public function shares()
    {
        return $this->morphMany('App\Share', 'author');
    }

    /**
     * morph relation with shares
     */
    public function post()
    {
        return $this->morphMany('App\Share', 'post');
    }

    /**
     * morph relation to events
     */
    public function events()
    {
        return $this->morphMany('App\TEvent', 'author');
    }

    /**
     * morph relation with offers of profile
     */
    public function offers()
    {
        return $this->morphMany('App\Offer', 'author');
    }

    /**
     * morph relation with buzz of profile
     */
    public function buzz()
    {
        return $this->morphOne('App\Buzz', 'profile')
            ->whereRaw('created_at = (select max(created_at) from buzz)');
    }

    /**
     * morph relation with tags
     */
    public function tags()
    {
        return $this->morphToMany('App\Tag', 'taggable');
    }

    public function tagsList($onlyIds = false)
    {
        $tagsTab = [];
        foreach ($this->tags as $tag) {
            if ($onlyIds) {
                $tagsTab[] =$tag->id;
            } else {
                $tagsTab[$tag->id] = $tag->name;
            }
        }
        return $tagsTab;
    }

    /**
     * morph relation to events
     */
    public function nextEvent()
    {
        return $this->morphMany('App\TEvent', 'author')
            ->where('date', '>=', Date('Y-m-d'))
            ->where('time', '>=', Date('H:i:s'))
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc');
    }

    /**
     * morph relation to playlist
     */
    public function playlists()
    {
        return $this->morphMany('App\Playlist', 'author');
    }

    /**
     * morph relation when profile appear in playlist item
     */
    public function playlisted()
    {
        return $this->morphMany('App\PlaylistItem', 'profile');
    }

    /**
     * morph relation when profile comment post
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'author');
    }

    /**
     * morph relation with news comments
     */
    public function profileComments()
    {
        return $this->morphMany('App\Comment', 'post');
    }

    /**
     * morph relation when profile is followed
     */
    public function subscriptions()
    {
        return $this->morphMany('App\Subscription', 'profile');
    }

    /**
     * morph relation when profile make actions
     */
    public function actions()
    {
        return $this->morphMany('App\NetframeAction', 'author');
    }

    /**
     * morph relation whith projects
     */
    /*
    public function projects()
    {
        return $this->morphToMany('App\Project', 'profils_has_project');
    }
    */

    /**
     * morph relation with messages group sended
     */
    public function sentMessageGroups()
    {
        return $this->morphMany('App\MessageGroup', 'sender');
    }

    /**
     * morph relation with messages group received
     */
    public function receivedMessageGroups()
    {
        return $this->morphMany('App\MessageGroup', 'receiver');
    }

    /**
     * morph relation with messages sended
     */
    public function sentMessages()
    {
        return $this->morphMany('App\Message', 'sender');
    }

    /**
     * morph relation with messages received
     */
    public function receivedMessages()
    {
        return $this->morphMany('App\Message', 'receiver');
    }

    /**
     * check buzz of profile
     */
    public function isBuzz()
    {
        if ($this->buzz != null) {
            $dayBuzz = Buzz::topBuzz('day_score');
            $weekBuzz = Buzz::topBuzz('week_score');

            if (($this->buzz->day_score >= $dayBuzz && $dayBuzz > 0)
                || ($this->buzz->week_score > $weekBuzz && $weekBuzz > 0)) {
                return true;
            }
        }
        return false;
    }

    public function getUrl()
    {
        switch ($this->type) {
            case Profile::TYPE_HOUSE:
                return url()->route('page.house', array($this->id, str_slug($this->name)));
                break;

            case Profile::TYPE_COMMUNITY:
                return url()->route('page.community', array($this->id, str_slug($this->name)));
                break;

            case Profile::TYPE_PROJECT:
                return url()->route('page.project', array($this->id, str_slug($this->title)));
                break;

            case Profile::TYPE_USER:
                $userFullName = str_slug($this->firstname).'-'.str_slug($this->name);
                return url()->route('profile.user', array($this->slug, $userFullName));
                break;
        }
    }

    public function getNameDisplay()
    {
        return $this->getName();
    }

    public function getName()
    {
        switch ($this->type) {
            case Profile::TYPE_HOUSE:
            case Profile::TYPE_COMMUNITY:
                return $this->name;
                break;

            case Profile::TYPE_PROJECT:
                return $this->title;
                break;
        }
    }

    public function instances()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'users_id')
            ->whereHas('instances', function ($wI) {
                $wI->where('id', '=', session('instanceId'));
            });
    }

    public function usersGroup()
    {
        return $this->morphToMany('App\UsersGroup', 'groups_profiles');
    }

    public function mosaicImage()
    {
        return $this->profileImage;
    }

    public function profileImage()
    {
        return $this->hasOne('App\Media', 'id', 'profile_media_id');
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
            return url()->route('netframe.svgicon', ['name' => $this->getType()]);
        }
    }

    public function images()
    {
        return $this->medias()->where('type', '=', Media::TYPE_IMAGE)
            ->orderBy('updated_at', 'DESC');
    }

    /**
     * Gets the favorite or lastmedia.
     *
     */
    public function getFavoriteOrLastMedia()
    {
        $lastFavorite = $this->lastFavoriteMedia();

        if ($lastFavorite->count() != 0) {
            return $lastFavorite;
        }

        $lastMedias = $this->lastMedia()->merge($this->channelsMedias())->sortByDesc('updated_at');

        return $lastMedias;
    }

    public function channelsMedias()
    {
        return Media::select('medias.*')->leftJoin('channels_has_medias as chm', 'chm.medias_id', '=', 'medias.id')
            ->leftJoin('channels', 'channels.id', '=', 'chm.channels_id')
            ->where('channels.profile_type', '=', get_class($this))
            ->where('channels.profile_id', '=', $this->id)
            ->groupBy('medias.id')
            ->orderBy('medias.updated_at', 'desc')
            ->take(8)
            ->get();
    }

    public function lastFavoriteMedia()
    {
        return $this->medias()
            ->withPivot(['favorite', 'profile_image'])
            ->wherePivot('favorite', 1)
            ->orderBy('updated_at', 'DESC')
            ->get();
    }

    public function lastMedia()
    {
        return $this->medias()
            ->where('profile_image', '!=', '1')
            ->where('under_workflow', '=', '0')
            ->orderBy('updated_at', 'DESC')
            ->take(8)
            ->get();
    }

    public function hasEncodedMedias()
    {
        foreach ($this->medias as $media) {
            if ($media->encoded == 1 && $media->pivot->profile_image != 1) {
                return true;
            }
        }

        return false;
    }

    public function mediasFolders()
    {
        return $this->morphMany('App\MediasFolder', 'profile')
            ->where('instances_id', '=', session('instanceId'))
            ->where('personnal_folder', false)
            ->orderBy('name');
    }

    public function personalFolders($userId = null)
    {
        $userId = ($userId == null) ? auth()->guard('web')->user()->id : $userId;

        return $this->morphMany('App\MediasFolder', 'profile')
            ->where('instances_id', '=', session('instanceId'))
            ->where('personnal_folder', true)
            ->where('personnal_user_folder', $userId)
            ->orderBy('name');
    }

    public function allPersonalFolders()
    {
        return $this->morphMany('App\MediasFolder', 'profile')
            ->where('instances_id', '=', session('instanceId'))
            ->where('personnal_folder', true)
            ->orderBy('name');
    }

    public function publicMediasFolders()
    {
        return $this->morphMany('App\MediasFolder', 'profile')
        ->where('instances_id', '=', session('instanceId'))
        ->where('public_folder', '=', 1)
        ->orderBy('name');
    }

    public function getDefaultFolder($folderName)
    {
        $mediaFolderObject = $this->mediasFolders()->where('name', '=', $folderName)->first();
        return ($mediaFolderObject != null) ? $mediaFolderObject->id : null;
    }

    public function isInstantBookmarkedByCurrentUser()
    {
        if (auth()->guard('web')->check()) {
            $user = auth()->guard('web')->user();

            $items = Playlist::where('playlists.author_id', '=', $user->id)
                ->where('playlists.author_type', '=', 'User')
                ->where('playlists.instant_playlist', '=', 1)
                ->join('playlists_items', 'playlists_items.playlists_id', '=', 'playlists.id')
                ->where('playlists_items.profile_type', '=', get_class($this))
                ->where('playlists_items.profile_id', '=', $this->id)
                ->where('playlists_items.medias_id', '=', 0)
                ->first();
            /*
            $items = PlaylistItem::where('users_id', '=', $user->id)
                ->where('profile_type', '=', get_class($this))
                ->where('profile_id', '=', $this->id)
                ->first();
            */
            return count($items) > 0;
        } else {
            return false;
        }
    }

    public function isFollowedByCurrentUser()
    {
        if (auth()->guard('web')->check()) {
            return Subscription::checkSubscribe($this->id, get_class($this));
        } else {
            return false;
        }
    }

    public function isOnline($byUser = 0)
    {
        $lastAction = $this->user->last_connexion;
        if (null !== $lastAction) {
            $datetime1 = new DateTime(($lastAction));
            $datetime2 = new DateTime((date("Y-m-d H:i:s")));
            $interval = $datetime1->diff($datetime2);
            $online = ($interval->days*86400 + $interval->h*3600 + $interval->i*60 + $interval->s < 300) ? true : false;
            return $online;
        }
        return false;
    }

    /**
     * return followerList.
     *
     */
    public function followers()
    {
        $tableNameUser = User::getTableName();
        $tableNameSubs = Subscription::getTableName();

        $result = User::select(
            $tableNameUser.'.id',
            $tableNameUser.'.firstname',
            $tableNameUser.'.name',
            $tableNameUser.'.profile_media_id',
            $tableNameUser.'.slug'
        )
            ->leftjoin($tableNameSubs, $tableNameUser.'.id', '=', $tableNameSubs.'.users_id')
            ->where($tableNameSubs.'.instances_id', '=', session('instanceId'))
            ->where($tableNameSubs.'.profile_id', '=', $this->id)
            ->where($tableNameSubs.'.profile_type', '=', get_class($this))
            ->where($tableNameUser.'.active', '=', 1)
            ->get();

        return $result;
    }

    public function tasks()
    {
        return $this->morphMany('App\TaskTable', 'author')
            ->where('instances_id', '=', session('instanceId'));
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    public function has_defaultTasks()
    {
        return $this->tasks()->where('default_task', '=', 1);
    }

    public function attachToDefaultChannel($userId)
    {
        $profileDefaultChannel = $this->has_defaultChannel()->first();
        if ($profileDefaultChannel != null && !$profileDefaultChannel->users->contains($userId)) {
            $profileDefaultChannel->users()->attach($userId, ['roles_id' => 4, 'status' => 1]);
        }
    }

    public function createPersonnalUserFolder($userId)
    {
        // check if this user already has personnal folder in project
        if ($this->with_personnal_folder && $this->personalFolders($userId)->first() == null) {
            $this->createPersonnalFolders($userId);
        }
    }

    /*
     * action to remove user from profile
     */
    public function removeUser($userId)
    {
        // check default channel
        $profileDefaultChannel = $this->has_defaultChannel()->first();
        if ($profileDefaultChannel != null && $profileDefaultChannel->users->contains($userId)) {
            $profileDefaultChannel->users()->detach($userId);
        }
    }

    public function listRoles($roleKey = null)
    {
        if ($roleKey == null) {
            return config('rights.groups');
        } else {
            return config('rights.groups.' . $roleKey);
        }
    }

    public function stats()
    {
        return $this->morphMany('App\Stat', 'entity')
            ->where('instances_id', '=', session('instanceId'));
    }
}
