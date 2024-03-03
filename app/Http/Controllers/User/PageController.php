<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Repository\NotificationsRepository;
use App\Helpers\SessionHelper;
use App\NewsFeed;
use App\Subscription;
use App\Like;
use App\User;
use App\Profile;
use App\Project;
use App\House;
use App\Community;
use App\Comment;
use App\TEvent;
use App\Events\InterestAction;
use App\Instance;
use App\Friends;
use Carbon\Carbon;
use App\Helpers\StatsHelper;

class PageController extends BaseController
{


    public function __construct()
    {
        $this->middleware('checkAuth');
        parent::__construct();
    }


    public function location()
    {
        $instance = Instance::find(session('instanceId'));
        $activeMap = $instance->apps()->where('slug', '=', 'map')->first();

        if (!$activeMap || !auth()->guard('web')->user()->gdpr_agrement) {
            return response(view('errors.403'), 403);
        }
        $data = [];
        $location = SessionHelper::getLocation();

        session([
            'lat' => $location->lat,
            'lng' => $location->lon,
        ]);

        $data['location'] = $location;
        $filters = config('location.markers');
        $data['filterTypes'] = $filters;
        $data['targetsProfiles'] = $filters;
        $data['zoomMapBox'] = config('location.zoom-map-home');

        return view('location.map', $data);
    }

    /**
     * Display Page Project
     */
    public function project($id, $name, $idNewsFeed = null)
    {
        $data = array();

        $project = Project::findOrFail($id);

        if ($project->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        if (empty($project)) {
            return view('errors.404');
        } else {
            // check if profile exist and return name type profile
            $arrayProfile = config('netframe.list_profile');
            $keyProfile = array_search(request()->segment(2), $arrayProfile);
            // return name of type profile
            $profileType = $arrayProfile[$keyProfile];

            $data['profile'] = $project;
            $data['profilePicture'] = $project->profileImage;
            $data['page'] = new \stdClass();

            if ($project->active == 0) {
                $data['profile'] = $project;
                $data['rights'] = $this->Acl->getRights($profileType, $id);
                $data['linkedit'] = url()->route('project.edit', [$id]);
                return view('page.inactive-profile', $data);
            }

            // type profile
            $data['page']->type = $profileType;
            $data['newsfeed'] = \App\NewsFeed::getByProfileMorph($id, $profileType, $project, null, $idNewsFeed);
            $data['topPost'] = $project->posts()->where('pintop', '=', 1)->first();

            // si le top post == id post demandé on n'affiche pas le top post
            if ($data['topPost'] != null && $idNewsFeed != null && $idNewsFeed == $data['topPost']->id) {
                $data['topPost'] = null;
            }

            if ($idNewsFeed !== null) {
                $data['unitPost'] = true;
            } else {
                $data['unitPost'] = false;
            }
            $data['rights'] = $this->Acl->getRights($profileType, $id, 4);
            $data['comments'] = new \App\Comment();
            $data['profileComments'] = $project->profileComments()->count();

            $data['confidentiality'] = \App\Http\Controllers\BaseController::hasViewProfile($project)
                ? 1
                : $project->confidentiality;

            session()->flash('profileDisplay', 'project');
            session()->flash('profileDisplayId', $project->id);

            if (request()->has('fromAjax')) {
                $data['unitPost'] = false;
                $data['post'] = $data['newsfeed'][0];
                $ajaxReturn = [
                    'containerId' => '#' .
                        class_basename($data['post']->post) .'-'.
                        class_basename($data['post']->author) .'-'.
                        $data['post']->post_id,
                    'view' => view('page.post-content', $data)->render(),
                ];
                return response()->json($ajaxReturn);
            }

            return view('page.publish', $data);
        }
    }

    /**
     *
     * @param string $name
     * @param string $userid
     * @return \Illuminate\View\View
     */
    public function house($id, $name, $idNewsFeed = null)
    {
        $data = array();
        $house = House::findOrFail($id);

        if ($house->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        if (empty($house)) {
            return view('errors.404');
        } else {
            $arrayProfile = config('netframe.list_profile');
            $keyProfile = array_search(request()->segment(2), $arrayProfile);
            // return name of type profile
            $profileType = $arrayProfile[$keyProfile];

            // get Like profile
            $house->likeProfile = \App\Like::getProfile($house->id, $profileType);

            $data['profile'] = $house;
            $data['profilePicture'] = $house->profileImage;

            if ($house->active == 0) {
                $data['profile'] = $house;
                $data['rights'] = $this->Acl->getRights($profileType, $id);
                $data['linkedit'] = url()->route('house.edit', [$id]);
                return view('page.inactive-profile', $data);
            }

            $data['page'] = new \stdClass();

            // type profile
            $data['page']->type = $profileType;
            $data['newsfeed'] = \App\NewsFeed::getByProfileMorph($id, 'house', $house, null, $idNewsFeed);
            $data['topPost'] = $house->posts()->where('pintop', '=', 1)->first();

            // si le top post == id post demandé on n'affiche pas le top post
            if ($data['topPost'] != null && $idNewsFeed != null && $idNewsFeed == $data['topPost']->id) {
                $data['topPost'] = null;
            }

            if ($idNewsFeed !== null) {
                $data['unitPost'] = true;
            } else {
                $data['unitPost'] = false;
            }
            $data['rights'] = $this->Acl->getRights($profileType, $id, 4);
            $data['comments'] = new \App\Comment();
            $data['profileComments'] = $house->profileComments()->count();

            if (!$data['rights']) {
                // event(new InterestAction($house->ref_subjects_id, $house->ref_categories_id));
            }
            $data['confidentiality'] = \App\Http\Controllers\BaseController::hasViewProfile($house)
                ? 1
                : $house->confidentiality;

            session()->flash('profileDisplay', 'house');
            session()->flash('profileDisplayId', $house->id);

            if (request()->has('fromAjax')) {
                $data['unitPost'] = false;
                $data['post'] = $data['newsfeed'][0];
                $ajaxReturn = [
                    'containerId' => '#' .
                        class_basename($data['post']->post) .'-'.
                        class_basename($data['post']->author) .'-'.
                        $data['post']->post_id,
                    'view' => view('page.post-content', $data)->render(),
                ];
                return response()->json($ajaxReturn);
            }

            return view('page.publish', $data);
        }
    }




    /**
     *
     * @return \Illuminate\View\View|\Illuminate\View\$this
     */
    public function community($id, $name, $idNewsFeed = null)
    {
        $data = array();
        $community = Community::findOrFail($id);

        if ($community->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        if (empty($community)) {
            return view('errors.404');
        } else {
            $arrayProfile = config('netframe.list_profile');
            $keyProfile = array_search(request()->segment(2), $arrayProfile);
            // return name of type profile
            $profileType = $arrayProfile[$keyProfile];

            $data['profile'] = $community;
            $data['profilePicture'] = $community->profileImage;

            if ($community->active == 0) {
                $data['profile'] = $community;
                $data['rights'] = $this->Acl->getRights($profileType, $id);
                $data['linkedit'] = url()->route('community.edit', [$id]);
                return view('page.inactive-profile', $data);
            }

            $data['page'] = new \stdClass();

            // type profile
            $data['page']->type = $profileType;
            $data['newsfeed'] = \App\NewsFeed::getByProfileMorph($id, $profileType, $community, null, $idNewsFeed);
            $data['topPost'] = $community->posts()->where('pintop', '=', 1)->first();

            // si le top post == id post demandé on n'affiche pas le top post
            if ($data['topPost'] != null && $idNewsFeed != null && $idNewsFeed == $data['topPost']->id) {
                $data['topPost'] = null;
            }

            if ($idNewsFeed !== null) {
                $data['unitPost'] = true;
            } else {
                $data['unitPost'] = false;
            }
            $data['rights'] = $this->Acl->getRights($profileType, $id, 4);
            $data['comments'] = new \App\Comment();
            $data['profileComments'] = $community->profileComments()->count();

            if (!$data['rights']) {
                // event(new InterestAction($community->ref_subjects_id, $community->ref_categories_id));
            }

            $data['confidentiality'] = \App\Http\Controllers\BaseController::hasViewProfile($community)
                ? 1
                : $community->confidentiality;

            session()->flash('profileDisplay', 'community');
            session()->flash('profileDisplayId', $community->id);

            if (request()->has('fromAjax')) {
                $data['unitPost'] = false;
                $data['post'] = $data['newsfeed'][0];
                $ajaxReturn = [
                    'containerId' => '#' .
                        class_basename($data['post']->post) .'-'.
                        class_basename($data['post']->author) .'-'.
                        $data['post']->post_id,
                    'view' => view('page.post-content', $data)->render(),
                ];
                return response()->json($ajaxReturn);
            }

            return view('page.publish', $data);
        }
    }

    /**
     *
     * @return \Illuminate\View\View|\Illuminate\View\$this
     */
    public function identityCard($profil, $id, $prevId = 0, $nextId = 0, $prevProfile = null, $nextProfile = null)
    {

            $data = array();

            $profile = \App\Profile::gather($profil)->find($id);

        if (($profil == 'user' && !$profile->instances->contains(session('instanceId')))
            || ($profil != 'user' &&  $profile->instances_id != session('instanceId'))) {
            return response(view('errors.403'), 403);
        }

        if (empty($profile)) {
            return view('errors.404');
        } elseif (!request()->ajax()) {
            return redirect()->to($profile->getUrl(), 301);
        } elseif ($profile->active == 0) {
            $data['profile'] = $profile;
            return view('page.inactive-profile-card', $data);
        } elseif (request()->ajax()) {
                $data['profile'] = $profile;
                $data['profilePicture'] = $profile->profileImage;

                $data['page'] = new \stdClass();
                // type profile
                $data['page']->type = $profil;

                $data['rights'] = $this->Acl->getRights($profil, $id);
                //$data['linkedit'] = url()->route($profil.'.edit', [$id]);

                $data['liked'] = Like::isLiked(['liked_id'=>$id, 'liked_type'=>studly_case($profile->getType())]);
                $data['instantItems'] = User::instantPlaylistItems();
                $data['followed'] = Subscription::checkSubscribe($id, $profil);
                $data['prevId'] = $prevId;
                $data['prevProfile'] = $prevProfile;
                $data['nextId'] = $nextId;
                $data['nextProfile'] = $nextProfile;
                return view('page.identity-card', $data);
        }
    }

    public function mapCard($profile, $profileType)
    {

        $data = array();

        if (is_numeric($profile)) {
            if ($profileType != 'event') {
                $profile = Profile::gather($profileType)->find($profile);
                $event = null;
                $eventMedia = null;
            } else {
                $profile = TEvent::find($profile);
                $event = $profile;
                $eventMedia = $event->medias()->first();
            }
        }

        //if(!$profile->instances->contains(session('instanceId'))){
        if (($profileType == 'user' && !$profile->instances->contains(session('instanceId')))
            || ($profileType != 'user' && $profile->instances_id != session('instanceId'))) {
            return response(view('errors.403'), 403);
        }

        $data['profile'] = $profile;
        $data['event'] = $event;
        $data['eventMedia'] = $eventMedia;
        $data['profilePicture'] = $profile->profileImage;
        $data['page'] = new \stdClass();
        // type profile
        $data['page']->type = $profile->getType();
        $data['rights'] = false;
        $data['liked'] = Like::isLiked(['liked_id'=>$profile->id, 'liked_type'=>get_class($profile)]);
        $data['instantItems'] = User::instantPlaylistItems();
        $data['followed'] = Subscription::checkSubscribe($profile->id, get_class($profile));

        if ($profile->active == 0) {
            $view = view('page.inactive-profile-card', $data)->render();
        } elseif ($profileType == 'event') {
            $data['author'] = $profile->author;
            $data['profilePicture'] = $eventMedia;
            $view = view('page.identity-card-map-event', $data)->render();
        } else {
            $view = view('page.identity-card-map', $data)->render();
        }

        return $view;
    }

    /**
     * reload newsfeed for infinite scroll
     */
    public function infiniteNewsFeed($profile_type, $profile_id, $last_time)
    {
        $profile = \App\Profile::gather($profile_type)->find($profile_id);

        if (($profile_type == 'user' && !$profile->instances->contains(session('instanceId')))
            || ($profile_type != 'user' && $profile->instances_id != session('instanceId'))) {
            return response(view('errors.403'), 403);
        }

        $newsfeed = \App\NewsFeed::getByProfileMorph($profile_id, $profile_type, $profile, $last_time);

        $data['unitPost'] = false;
        $data['newsfeed'] = $newsfeed;
        $data['rights'] = $this->Acl->getRights($profile_type, $profile_id);
        $data['withLoader'] = true;

        $view = view('page.post-container', $data)->render();
        return response()->json(['view' => $view]);
    }

    /**
     * load news and all comments in modal
     */
    public function unitPostModal($id, $modal = 'on')
    {
        $newsfeed = NewsFeed::where('id', '=', $id)
        ->where('instances_id', '=', session('instanceId'))
            ->get();

        $data['newsfeed'] = $newsfeed;
        $data['unitPost'] = true;

        $author = $newsfeed[0]->author;
        $author_type = $author->getType();

        //check right
        if (in_array($author_type, ['user', 'house', 'project'])
            || $author->getType() == 'community'
            || $newsfeed[0]->confidentiality == 0) {
            //check view for profile
            if ($author_type == 'user') {
                // check friend relation
                $isFriend = Friends::relation($author->id);
                if ($newsfeed[0]->confidentiality == 0 && $isFriend == null) {
                    return view('page.private', $data)->render();
                }
            } else {
                /*
                $canViewPost = (BaseController::hasViewProfile($author)) ? 1 : $author->confidentiality;
                if($canViewPost == 0){
                    return view('page.private', $data)->render();
                }
                */

                //check view for post
                if ($newsfeed[0]->confidentiality == 0
                    && (!auth()->guard('web')->check()
                        || (auth()->guard('web')->check() && !BaseController::hasViewProfile($author) ))) {
                    return view('page.private', $data)->render();
                }
            }
        }

        if ($modal == 'on') {
            return view('page.post-container', $data)->render();
        } else {
            return redirect()->to($author->getUrl().'/'.$id);
        }
    }

    /**
     * load all comments in feed
     */
    public function allComments()
    {
        $newsfeed = NewsFeed::where('id', '=', request()->get('id'))
            ->where('instances_id', '=', session('instanceId'))
            ->get();

        $data['post'] = $newsfeed[0];
        if (request()->has('comment')) {
            $comment = Comment::find(request()->get('comment'));
            $data['comments'] = [];
            if ($comment) {
                $data['comments'] = $comment->replies;
            }
        } else {
            $data['comments'] = $newsfeed[0]->post->comments;
        }

        $author = $newsfeed[0]->author;
        $author_type = $author->getType();

        //check right
        if (in_array($author_type, ['user', 'house', 'project'])
            || $author->getType() == 'community'
            || $newsfeed[0]->confidentiality == 0) {
            //check view for profile
            if ($author_type == 'user') {
                // check friend relation
                $isFriend = Friends::relation($author->id);
                if ($newsfeed[0]->confidentiality == 0 && $isFriend == null) {
                    return response(view('errors.403'), 403);
                }
            } else {
                //check view for post
                if ($newsfeed[0]->confidentiality == 0
                    && (!auth()->guard('web')->check()
                        || (auth()->guard('web')->check() && !BaseController::hasViewProfile($author) ))
                ) {
                    return response(view('errors.403'), 403);
                }
            }
        }

        return response()->json([
            'view' => view('page.all-comments', $data)->render()
        ]);
    }

    /**
     * return collection to show in modal sidebar widget
     * @param string $model
     * @param int $userId
     * @param string $typeContent :: method name to retrieve collection
     */
    public function sideBarContent($model, $id, $typeContent)
    {
        $profile = \App\Profile::gather($model)->find($id);

        if (empty($profile)) {
            return view('errors.404');
        }

        if ($profile->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        $data = array();
        $data['rights'] = $this->Acl->getRights($model, $id);

        switch ($typeContent) {
            case "playlists":
                $objects = $profile->playlists;
                $view = "playlist";
                $varName = "playlists";
                break;

            case "followers":
                $objects = $profile->followers();
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "project":
                $objects = $profile->projects;
                $view = "profile-project";
                $varName = "projects";
                break;

            case "event":
                $objects = $profile;
                $view = "event";
                $varName = "profiles";
                break;

            case "participants":
                $objects = $profile->profiles;
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "bookmarks":
                $objects = $profile;
                $view = "bookmark";
                $varName = "project";
                break;

            case "activity":
                $objects = $profile->recentActivity();
                $view = "last-activity";
                $varName = "expert_action";
                break;

            case "community":
                $objects = $profile->communities;
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "house":
                $objects = $profile->houses;
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "channel":
                $objects = $profile->channels;
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "members":
                $objects = $profile->users()->wherePivot('status', '=', 1)->get();
                $view = "profile-mosaic";
                $varName = "profiles";
                break;
        }

        $data['subview'] = $view;
        $data['collection'] = $objects;
        $data['varName'] = $varName;
        $data['profileType'] = $typeContent;

        return view('page.sidebar.modal-sidebar', $data);
    }

    /**
     *
     * @param string $profileType
     * @param int $profileId
     * @param string $take (resume, all)
     */
    public function profileComments($profileType, $profileId, $take = 'resume')
    {
        $limit = 5;
        $profile = Profile::gather($profileType)->find($profileId);

        if (empty($profile)) {
            return response(view('errors.404'), 404);
        }

        $data = array();
        $data['profile'] = $profile;
        $data['post'] = $profile;

        $nbComments = $profile->profileComments()->count();
        if ($take == 'resume') {
            $skip = ($nbComments > $limit) ? $nbComments-$limit : 0;
            $comments = $profile->profileComments()->take($limit)->skip($skip)->get();
            $linkMore = ($skip > 0) ? true : false;
            $data['comments'] = $comments;
            $data['linkMoreComments'] = $linkMore;
            $data['viewType'] = 'full';
            return view('page.profile-comments', $data);
        } elseif ($take == 'all') {
            $comments = $profile->profileComments()->take($nbComments-$limit)->get();
            $data['comments'] = $comments;
            $data['linkMoreComments'] = false;
            $data['viewType'] = 'partial';
            return response()->json(array(
                'view' => view('page.profile-comments', $data)->render(),
            ));
        }
    }

    public function disableProfile($profileType, $profileId, $active = 0)
    {
        //check rights
        $rights = $this->Acl->getRights($profileType, $profileId);

        if (!$rights || $rights >= 3) {
            return response(view('errors.403'), 403);
        }

        $profileModel = Profile::gather($profileType);

        $profile = $profileModel::find($profileId);
        $profile->active = $active;
        $profile->save();

        // force recheck rights of all members
        $members = $profile->users;
        foreach ($members as $member) {
            $member->check_rights = 1;
            $member->save();
        }

        sleep(1);

        return redirect()->to($profile->getUrl());
    }

    public function stats($profileType, $profileId, $period = 7)
    {
        //check rights
        $rights = $this->Acl->getRights($profileType, $profileId);

        if (!$rights || $rights >= 3) {
            return response(view('errors.403'), 403);
        }

        $profileModel = Profile::gather($profileType);
        $profile = $profileModel::find($profileId);

        // manage period
        $endInterval = Carbon::now()
            ->format('Y-m-d');
        $startInterval = Carbon::now()
            ->subDays($period)
            ->format('Y-m-d');
        $startPreviousInterval = Carbon::now()
            ->subDays($period*2)
            ->format('Y-m-d');

        $newUsers = $profile->stats()
            ->where('day', '>=', $startInterval)
            ->where('day', '<=', $endInterval)
            ->where('stat_type', '=', 'users')
            ->sum('counter');
        $instancePreviewNewUsers = $profile->stats()
            ->where('day', '>=', $startPreviousInterval)
            ->where('day', '<=', $startInterval)
            ->where('stat_type', '=', 'users')
            ->sum('counter');

        $users = $profile->validatedUsers()->count();
        $news = $profile->posts()->count();
        $events = $profile->events()->count();
        $offers = $profile->offers()->count();
        $medias = $profile->medias()->count();

        $views = $profile->stats()
            ->where('stat_type', '=', 'post')
            ->sum('counter');
        $comments = $profile->stats()
            ->where('stat_type', '=', 'comments')
            ->sum('counter');
        $likes = $profile->stats()
            ->where('stat_type', '=', 'likes')
            ->sum('counter');

        $newNews = $profile->posts()
            ->where('post_type', '=', 'App\\News')
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newEvents = $profile->posts()
            ->where('post_type', '=', 'App\\TEvent')
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newOffers = $profile->posts()
            ->where('post_type', '=', 'App\\Offer')
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();
        $newMedias = $profile->medias()
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->count();

        $newPreviewNews = $profile->posts()
            ->where('post_type', '=', 'App\\News')
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();
        $newPreviewOffers = $profile->posts()
            ->where('post_type', '=', 'App\\Offer')
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();
        $newPreviewEvents = $profile->posts()
            ->where('post_type', '=', 'App\\TEvent')
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();
        $newPreviewMedias = $profile->medias()
            ->where('created_at', '>=', $startPreviousInterval)
            ->where('created_at', '<=', $startInterval)
            ->count();

        // add reactions
        $newViews = $profile->stats()
            ->where('day', '>=', $startInterval)
            ->where('day', '<=', $endInterval)
            ->where('stat_type', '=', 'post')
            ->sum('counter');
        $newPreviewViews = $profile->stats()
            ->where('day', '>=', $startPreviousInterval)
            ->where('day', '<=', $startInterval)
            ->where('stat_type', '=', 'post')
            ->sum('counter');
        $newComments = $profile->stats()
            ->where('day', '>=', $startInterval)
            ->where('day', '<=', $endInterval)
            ->where('stat_type', '=', 'comments')
            ->sum('counter');
        $newPreviewComments = $profile->stats()
            ->where('day', '>=', $startPreviousInterval)
            ->where('day', '<=', $startInterval)
            ->where('stat_type', '=', 'comments')
            ->sum('counter');
        $newLikes = $profile->stats()
            ->where('day', '>=', $startInterval)
            ->where('day', '<=', $endInterval)
            ->where('stat_type', '=', 'likes')
            ->sum('counter');
        $newPreviewLikes = $profile->stats()
            ->where('day', '>=', $startPreviousInterval)
            ->where('day', '<=', $startInterval)
            ->where('stat_type', '=', 'likes')
            ->sum('counter');

        // most active users
        $usersIds = $profile->users()->pluck('id')->toArray();
        $topUsersPosts = NewsFeed::select(\DB::raw('count(id)*2.5 as score, users_id'))
            ->whereIn('users_id', $usersIds)
            ->where('author_type', '=', get_class($profile))
            ->where('author_id', '=', $profile->id)
            ->where('created_at', '>=', $startInterval)
            ->where('created_at', '<=', $endInterval)
            ->groupBy('users_id')
            ->get();
        // comments per user = *2
        $topUsersCommentsNews = $profile->posts()
            ->select(\DB::raw('count(comments.id)*2 as score, comments.users_id'))
            ->leftJoin('news', 'news.id', '=', 'news_feeds.post_id')
            ->leftJoin('comments', 'comments.post_id', '=', 'news.id')
            ->whereIn('comments.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\News')
            ->where('comments.post_type', '=', 'App\\News')
            ->where('comments.created_at', '>=', $startInterval)
            ->where('comments.created_at', '<=', $endInterval)
            ->groupBy('comments.users_id')
            ->get();
        $topUsersCommentsOffers = $profile->posts()
            ->select(\DB::raw('count(comments.id)*2 as score, comments.users_id'))
            ->leftJoin('offers', 'offers.id', '=', 'news_feeds.post_id')
            ->leftJoin('comments', 'comments.post_id', '=', 'offers.id')
            ->whereIn('comments.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\Offer')
            ->where('comments.post_type', '=', 'App\\Offer')
            ->where('comments.created_at', '>=', $startInterval)
            ->where('comments.created_at', '<=', $endInterval)
            ->groupBy('comments.users_id')
            ->get();
        $topUsersCommentsEvents = $profile->posts()
            ->select(\DB::raw('count(comments.id)*2 as score, comments.users_id'))
            ->leftJoin('events', 'events.id', '=', 'news_feeds.post_id')
            ->leftJoin('comments', 'comments.post_id', '=', 'events.id')
            ->whereIn('comments.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\TEvent')
            ->where('comments.post_type', '=', 'App\\Event')
            ->where('comments.created_at', '>=', $startInterval)
            ->where('comments.created_at', '<=', $endInterval)
            ->groupBy('comments.users_id')
            ->get();
        //@TODO add media comments
        // likes per user = *0.5
        $topUsersLikesNews = $profile->posts()
            ->select(\DB::raw('count(likes.id)*0.5 as score, likes.users_id'))
            ->leftJoin('news', 'news.id', '=', 'news_feeds.post_id')
            ->leftJoin('likes', 'likes.liked_id', '=', 'news.id')
            ->whereIn('likes.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\News')
            ->where('likes.liked_type', '=', 'App\\News')
            ->where('likes.created_at', '>=', $startInterval)
            ->where('likes.created_at', '<=', $endInterval)
            ->groupBy('likes.users_id')
            ->get();
        $topUsersLikesOffers = $profile->posts()
            ->select(\DB::raw('count(likes.id)*0.5 as score, likes.users_id'))
            ->leftJoin('offers', 'offers.id', '=', 'news_feeds.post_id')
            ->leftJoin('likes', 'likes.liked_id', '=', 'offers.id')
            ->whereIn('likes.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\Offer')
            ->where('likes.liked_type', '=', 'App\\Offer')
            ->where('likes.created_at', '>=', $startInterval)
            ->where('likes.created_at', '<=', $endInterval)
            ->groupBy('likes.users_id')
            ->get();
        $topUsersLikesEvents = $profile->posts()
            ->select(\DB::raw('count(likes.id)*0.5 as score, likes.users_id'))
            ->leftJoin('events', 'events.id', '=', 'news_feeds.post_id')
            ->leftJoin('likes', 'likes.liked_id', '=', 'events.id')
            ->whereIn('likes.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\TEvent')
            ->where('likes.liked_type', '=', 'App\\Event')
            ->where('likes.created_at', '>=', $startInterval)
            ->where('likes.created_at', '<=', $endInterval)
            ->groupBy('likes.users_id')
            ->get();

        // shares per user = *1.5
        $topUsersSharesNews = $profile->posts()
            ->select(\DB::raw('count(shares.id)*0.5 as score, shares.users_id'))
            ->leftJoin('news', 'news.id', '=', 'news_feeds.post_id')
            ->leftJoin('shares', 'shares.post_id', '=', 'news.id')
            ->whereIn('shares.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\News')
            ->where('shares.post_type', '=', 'App\\News')
            ->where('shares.created_at', '>=', $startInterval)
            ->where('shares.created_at', '<=', $endInterval)
            ->groupBy('shares.users_id')
            ->get();
        $topUsersSharesOffers = $profile->posts()
            ->select(\DB::raw('count(shares.id)*0.5 as score, shares.users_id'))
            ->leftJoin('offers', 'offers.id', '=', 'news_feeds.post_id')
            ->leftJoin('shares', 'shares.post_id', '=', 'offers.id')
            ->whereIn('shares.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\Offer')
            ->where('shares.post_type', '=', 'App\\Offer')
            ->where('shares.created_at', '>=', $startInterval)
            ->where('shares.created_at', '<=', $endInterval)
            ->groupBy('shares.users_id')
            ->get();
        $topUsersSharesEvents = $profile->posts()
            ->select(\DB::raw('count(shares.id)*0.5 as score, shares.users_id'))
            ->leftJoin('events', 'events.id', '=', 'news_feeds.post_id')
            ->leftJoin('shares', 'shares.post_id', '=', 'events.id')
            ->whereIn('shares.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\TEvent')
            ->where('shares.post_type', '=', 'App\\TEvent')
            ->where('shares.created_at', '>=', $startInterval)
            ->where('shares.created_at', '<=', $endInterval)
            ->groupBy('shares.users_id')
            ->get();

        // views = *0.1
        $topUsersViewsNews = $profile->posts()
            ->select(\DB::raw('count(views.id)*0.1 as score, views.users_id'))
            ->leftJoin('news', 'news.id', '=', 'news_feeds.post_id')
            ->leftJoin('views', 'views.post_id', '=', 'news.id')
            ->whereIn('views.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\News')
            ->where('views.post_type', '=', 'App\\News')
            ->where('views.created_at', '>=', $startInterval)
            ->where('views.created_at', '<=', $endInterval)
            ->groupBy('views.users_id')
            ->get();
        $topUsersViewsOffers = $profile->posts()
            ->select(\DB::raw('count(views.id)*0.5 as score, views.users_id'))
            ->leftJoin('offers', 'offers.id', '=', 'news_feeds.post_id')
            ->leftJoin('views', 'views.post_id', '=', 'offers.id')
            ->whereIn('views.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\Offer')
            ->where('views.post_type', '=', 'App\\Offer')
            ->where('views.created_at', '>=', $startInterval)
            ->where('views.created_at', '<=', $endInterval)
            ->groupBy('views.users_id')
            ->get();
        $topUsersViewsEvents = $profile->posts()
            ->select(\DB::raw('count(shares.id)*0.5 as score, shares.users_id'))
            ->leftJoin('events', 'events.id', '=', 'news_feeds.post_id')
            ->leftJoin('shares', 'shares.post_id', '=', 'events.id')
            ->whereIn('shares.users_id', $usersIds)
            ->where('news_feeds.post_type', '=', 'App\\TEvent')
            ->where('shares.post_type', '=', 'App\\TEvent')
            ->where('shares.created_at', '>=', $startInterval)
            ->where('shares.created_at', '<=', $endInterval)
            ->groupBy('shares.users_id')
            ->get();

        $topUsersComputes = [
            $topUsersPosts,
            $topUsersCommentsNews,
            $topUsersCommentsOffers,
            $topUsersCommentsEvents,
            $topUsersLikesNews,
            $topUsersLikesOffers,
            $topUsersLikesEvents,
            $topUsersSharesNews,
            $topUsersSharesOffers,
            $topUsersSharesEvents,
            $topUsersViewsNews,
            $topUsersViewsOffers,
            $topUsersViewsEvents,
        ];

        $topUsers = StatsHelper::computeTopusers($topUsersComputes);

        // @TODO top posts

        $data = [
            'profileType' => $profileType,
            $profileType => $profile,
            'statPage' => true,
            'period' => $period,
            'startPeriod' => $startInterval,
            'endPeriod' => $endInterval,
            'startPreviousPeriod' => $startPreviousInterval,
            'users' => $users,
            'news' => $news,
            'events' => $events,
            'offers' => $offers,
            'medias' => $medias,
            'newUsers' => $newUsers,
            'newNews' => $newNews,
            'newEvents' => $newEvents,
            'newOffers' => $newOffers,
            'newMedias' => $newMedias,
            'newPreviewUsers' => $instancePreviewNewUsers,
            'newPreviewNews' => $newPreviewNews,
            'newPreviewEvents' => $newPreviewEvents,
            'newPreviewOffers' => $newPreviewOffers,
            'newPreviewMedias' => $newPreviewMedias,
            'views' => $views,
            'newViews' => $newViews,
            'newPreviewViews' => $newPreviewViews,
            'comments' => $comments,
            'newComments' => $newComments,
            'newPreviewComments' => $newPreviewComments,
            'likes' => $likes,
            'newLikes' => $newLikes,
            'newPreviewLikes' => $newPreviewLikes,
            'topUsers' => $topUsers,
            'instance' => Instance::find(session('instanceId')),
        ];

        return view('page.stats', $data);
    }
}
