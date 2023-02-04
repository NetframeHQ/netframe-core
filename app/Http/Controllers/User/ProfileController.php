<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Repository\SearchRepository2;
use App\Profile;
use App\User;
use App\Media;
use App\TEvent;
use App\NewsFeed;
use App\Subscription;
use App\Like;
use App\Friends;
use App\Tag;
use App\UsersReference;
use App\Notif;
use App\Events\NewAction;
use App\Events\AddUserSkill;
use App\Events\InterestAction;
use Carbon\Carbon;
use App\Instance;

class ProfileController extends BaseController
{
    private $searchRepository;

    public function __construct(SearchRepository2 $searchRepository)
    {
        $this->middleware('checkAuth');

        parent::__construct();

        $this->searchRepository = $searchRepository;
    }

    public function timeline()
    {
        $dataUser = User::findOrFail(auth()->guard('web')->user()->id);
        $instance = Instance::find(session('instanceId'));
        $canPostOnTimeline =  ($instance->getParameter('disable_post_on_timeline') == 1) ? false : true;

        // set user main feed preference
        $dataUser->setParameter('timelinePref', 'timeline');

        if ($dataUser != null) {
            $data['profile'] = $data['dataUser'] = $dataUser;
            $data['page'] = new \stdClass();
            // type profile
            $data['page']->type = "user";

            $data['newsfeed'] = NewsFeed::getByProfileMorph($dataUser->id, 'user', $dataUser, null, null, true);
            $data['unitPost'] = false;
            $data['rights'] = $this->Acl->getRights('user', $dataUser->id);
            //$data['dataUser'] = $dataUser;

            $spokenLanguages = $dataUser->getSpokenLanguages();
            $data['spokenLanguages'] = (count($spokenLanguages) > 0) ? $dataUser->getSpokenLanguages() : null;

            $data['linkedit'] = $data['linkedit'] = url()->route('account.account');
            $data['followed'] = Subscription::checkSubscribe($dataUser->id, 'User');
            $data['followersCount'] = $dataUser->subscriptions()->count();
            $data['followsCount'] = $dataUser->subscriptionsList()->count();
            if (auth()->guard('web')->check()) {
                $data['liked'] = Like::isLiked(['liked_id'=>$dataUser->id, 'liked_type'=>'User']);
                $data['friends'] = Friends::relation($dataUser->id);
            } else {
                $data['liked'] = false;
                $data['friends'] = '';
            }

            //prepare right column
            //$data['playlistsuser'] = $dataUser->playlistsuser->take(5);
            $data['newProfiles'] = User::lastValidated(8);

            /*
            $medias = New \Media();
            $data['newMedias'] = $medias->lastNetframeMedias(4);
            */

            $events = new TEvent();
            $data['newEvents'] = $events->nextOrLast(2);
            $data['lastNews'] = NewsFeed::lastNews('News', 2);
            $data['lastActions'] = NewsFeed::lastNews('NetframeAction', 2);
            $data['calendarView'] = 'timeline';
            $data['canPostOnTimeline'] = $canPostOnTimeline;

            if (session()->has('justCreated')) {
                session()->flash('justCreated');
            }

            return view('page.timeline', $data);
        }
    }

    /**
     * reload newsfeed for infinite scroll
     */
    public function infiniteTimeline($last_time)
    {
        $dataUser = User::findOrFail(auth()->guard('web')->user()->id);

        if (!$dataUser->instances->contains(session('instanceId'))) {
            return response(view('errors.403'), 403);
        }

        $newsfeed = NewsFeed::getByProfileMorph($dataUser->id, 'user', $dataUser, $last_time, null, true);

        $data['unitPost'] = false;
        $data['newsfeed'] = $newsfeed;
        $data['rights'] = $this->Acl->getRights('user', $dataUser->id);

        //return view('page.post-container', $data)->render();
        $view = view('page.post-container', $data)->render();
        return response()->json(['view' => $view]);
    }

    public function wall($slug, $fullname, $idNewsFeed = null)
    {
        // get slug and find user by slug
        $dataUser = User::where('slug', "=", $slug)->get()->first();

        if (!$dataUser->instances->contains(session('instanceId'))) {
            return response(view('errors.403'), 403);
        }

        $data = array();

        if ($dataUser->active == 0) {
            return view('page.inactive-user');
        }

        if (session()->has('bootstrapTour') && session()->has('justCreated')) {
            $btTour = session('bootstrapTour');
            session()->forget('bootstrapTour');
            session()->flash('bootstrapTour', $btTour);

            $justCreated = session('justCreated');
            session()->forget('justCreated');
            session()->flash('justCreated', true);
        }

        $data['profile'] = $dataUser;
        $data['page'] = new \stdClass();
        // type profile
        $data['page']->type = "user";

        $data['newsfeed'] = NewsFeed::getByProfileMorph($dataUser->id, 'user', $dataUser, null, $idNewsFeed);
        if ($idNewsFeed !== null) {
            $data['unitPost'] = true;
        } else {
            $data['unitPost'] = false;
        }

        //$data['displayCardUser'] = true;
        $data['profilePicture'] = $dataUser->profileImage;
        $data['rights'] = $this->Acl->getRights('user', $dataUser->id);
        $data['dataUser'] = $dataUser;

        $spokenLanguages = $dataUser->getSpokenLanguages();
        $data['spokenLanguages'] = (count($spokenLanguages) > 0) ? $dataUser->getSpokenLanguages() : null;

        $data['linkedit'] = $data['linkedit'] = url()->route('account.account');
        $data['followed'] = Subscription::checkSubscribe($dataUser->id, 'App\\User');
        $data['followersCount'] = $dataUser->subscriptions()->count();
        $data['followsCount'] = $dataUser->subscriptionsList()->count();
        if (auth()->guard('web')->check()) {
            $data['liked'] = Like::isLiked(['liked_id'=>$dataUser->id, 'liked_type'=>'App\\User']);
            $data['friends'] = Friends::relation($dataUser->id);
        } else {
            $data['liked'] = false;
            $data['friends'] = '';
        }

        return view('page.feed-user', $data);
    }

    /*
    public function sideBarUser($userId)
    {
        $data = array();
        $user = User::find($userId);
        $data['dataUser'] = $user;
        $data['profileSidebar'] = $user;
        $data['zoomMapBox'] = config('location.zoom-map-sidebar');
        $data['sidebarProfiles'] = array();

        $data['playlistsuser'] = $user->playlistsuser->take(3);

        //subscriptions
        $subscriptions = $user->subscriptionsList->take(4);
        $subscribeProfiles = [];
        foreach($subscriptions AS $suscribe){
            if($suscribe->profile->active == 1){
                $subscribeProfiles[] = $suscribe->profile;
            }
        }

        $data['sidebarProfiles'][] = [ 'type'=>'subscriptions', 'profiles' => $subscribeProfiles ];
        $data['sidebarProfiles'][] = [ 'type'=>'friends', 'profiles' => $user->friendsList(4) ];
        $data['sidebarProfiles'][] = [ 'type'=>'followers', 'profiles' => $user->followers()->take(4) ];
        $data['sidebarPages'][] = [ 'type'=>'house', 'profiles' => $user->houses->where('active', '=', 1)->take(4) ];
        $data['sidebarPages'][] = [
            'type'=>'community',
            'profiles' => $user->community->where('active', '=', 1)->take(4)
        ];
        $data['sidebarPages'][] = [ 'type'=>'project', 'profiles' => $user->project->where('active', '=', 1)->take(4) ];
        $data['sidebarPages'][] = [
            'type'=>'channel',
            'profiles' => $user->channels->where('active', '=', 1)->take(4)
        ];
        $data['profile'] = $user;
        $data['rights'] = $this->Acl->getRights('user', $user->id);

        //get references liked by showing user
        $userLikedReferences = UsersReference::select('users_references.id')
            ->where('users_references.instances_id', '=', session('instanceId'))
            ->join('likes as l', 'l.liked_id', '=', 'users_references.id')
            ->where('l.liked_type', '=', 'App\\UsersReference')
            ->where('l.users_id', '=', auth()->guard('web')->user()->id)
            ->where('users_references.users_id', '=', $userId)
            ->pluck('users_references.id')->toArray();
        $data['userLikedReferences'] = $userLikedReferences;

        $data['displayUserPages'] = true;
        foreach($data['sidebarPages'] as $pageType){
            if(count($pageType['profiles']) > 0){
                $data['displayUserPages'] = true;
            }
        }

        return $data;
    }
    */

    /**
     * return collection to show in modal sidebar widget
     * @param int $userId
     * @param string $typeContent :: method name to retrieve collection
     */
    public function sideBarContent($userId, $typeContent)
    {
        $user = User::find($userId);

        if (!$user->instances->contains(session('instanceId'))) {
            return response(view('errors.403'), 403);
        }

        switch ($typeContent) {
            case "around": //carto
                break;

            case "suggest": // mosaic by interests
                break;

            case "playlists":
                $objects = $user->playlistsuser;
                $view = "playlist";
                $varName = "playlists";
                break;

            case "subscriptions":
                $subscriptions = $user->subscriptionsList;
                $objects = [];
                foreach ($subscriptions as $suscribe) {
                    if ($suscribe->profile->active == 1) {
                        $objects[] = $suscribe->profile;
                    }
                }
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "followers":
                $objects = $user->followers();
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "friends":
                $objects = $user->friendsList();
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "project":
                $objects = $user->project->where('active', '=', 1);
                $view = "profile-project";
                $varName = "projects";
                break;

            case "channel":
                $objects = $user->channels->where('active', '=', 1);
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "community":
                $objects = $user->community->where('active', '=', 1);
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "house":
                $objects = $user->houses->where('active', '=', 1);
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "discover":
                $targetsProfiles = SearchRepository2::$searchProfiles;

                $this->searchRepository->route = 'search_mosaic';
                $this->searchRepository->targetsProfiles = $targetsProfiles;
                $this->searchRepository->toggleFilter = false;
                $this->searchRepository->byInterests = 1;
                $this->searchRepository->search_limit = 40;

                $searchParameters = $this
                    ->searchRepository
                    ->initializeConfig('search_mosaic', $targetsProfiles, false, 1, 40);
                $results = $this->searchRepository->search($searchParameters, $targetsProfiles);
                $objects = $results[0];
                $view = "profile-mosaic";
                $varName = "profiles";
                break;

            case "newProfiles":
                $view = "profile-mosaic";
                $varName = "profiles";

                $objects = User::lastValidated(40);

                break;

            case "newMedias":
                $medias = new Media();
                $objects = $medias->lastNetframeMedias();
                $view = "profile-medias";
                $varName = "medias";
                break;

            case "toutNetframe":
                //prepare right column
                $medias = new Media();

                $events = new TEvent();
                $objects = [
                    'newProfiles' => User::lastValidated(),
                    'newMedias' => $medias->lastNetframeMedias(4),
                    'newEvents' => $events->nextOrLast(4),
                    'lastNews' => NewsFeed::lastNews('News', 4),
                    'lastActions' => NewsFeed::lastNews('NetframeAction', 4),
                ];
                $varName = "toutNetframe";
                $view = "tout-netframe";
                break;
        }

        $data = array();
        $data['subview'] = $view;
        $data['collection'] = $objects;
        $data['varName'] = $varName;
        $data['profileType'] = $typeContent;
        return view('page.sidebar.modal-sidebar', $data);
    }

    /**
     * Choose an image as profile image.
     *
     * @param string $profileType
     * @param integer $profileId
     * @param integer $mediaId
     *
     * @return Response
     */

    public function chooseImage($profileType, $profileId, $mediaId)
    {
        //return $this->doChooseImage($profileType, $profileId, $mediaId, 1);
    }

    /**
     * Choose an image as profile image.
     *
     * @param string $profileType
     * @param integer $profileId
     * @param integer $mediaId
     * @param integer $value
     *
     * @return Response
     */
    private function doChooseImage($profileType, $profileId, $mediaId, $value)
    {
        switch ($profileType) {
            case Profile::TYPE_USER:
                $profile = User::findOrFail($profileId);

                \DB::table('users_has_medias')->where(array(
                    'users_id' => $profileId,
                    'medias_id' => $mediaId
                ))->update(array(
                    'profile_image' => $value
                ));
                break;

            case Profile::TYPE_COMMUNITY:
                $profile = Community::findOrFail($profileId);

                \DB::table('community_has_medias')->where(array(
                    'community_id' => $profileId,
                    'medias_id' => $mediaId
                ))->update(array(
                    'profile_image' => $value
                ));
                break;

            case Profile::TYPE_HOUSE:
                $profile = House::findOrFail($profileId);

                \DB::table('houses_has_medias')->where(array(
                    'houses_id' => $profileId,
                    'medias_id' => $mediaId
                ))->update(array(
                    'profile_image' => $value
                ));
                break;

            case Profile::TYPE_PROJECT:
                $profile = Project::findOrFail($profileId);

                \DB::table('projects_has_medias')->where(array(
                    'projects_id' => $profileId,
                    'medias_id' => $mediaId
                ))->update(array(
                    'profile_image' => $value
                ));
                break;

            default:
                return new response('', 400);
        }

        $profile->profile_media_id = $mediaId;
        $profile->save();

        //return img src
        return url()->route('media_download', array('id' => $mediaId,'thumb'=>1));
    }

    /**
     * input : post from ajax form var newReference
     */
    public function addReference()
    {
        $user = User::find(request()->get('userId'));

        if (!$user->instances->contains(session('instanceId'))) {
            return response(view('errors.403'), 403);
        }

        $data = array();
        $data['rights'] = $rights = $this->Acl->getRights($user->getType(), $user->id);

        if (request()->has('newReference') && $user->userReferences->count() < 15 && ( ( $rights && $rights < 3 )  ||
                (!$rights && auth()->guard('web')->user()->postedUserReferences($user->id, 0)->count() < 3) )) {
            $newReference = request()->get('newReference');
            if (is_numeric($newReference) && Tag::find($newReference) != null) {
                $tag = Tag::find($newReference);
            } else {
                $tag = new Tag();
                $tag->instances_id = session('instanceId');
                $tag->name = $newReference;
                $tag->lang = \Lang::locale();
                $tag->users_id = auth()->guard('web')->user()->id;
                $tag->save();
            }

            //attach to existing reference if relation not exists
            $testUserRef = UsersReference::where('tags_id', '=', $tag->id)
                ->where('instances_id', '=', session('instanceId'))
                ->where('users_id', '=', $user->id)
                ->count();
            if ($testUserRef == 0) {
                $status = (auth()->guard('web')->user()->id != $user->id) ? 0 : 1;
                $user->references()->attach($tag->id, [
                    'users_id_create' => auth()->guard('web')->user()->id,
                    'instances_id' => session('instanceId'),
                    'users_id' => $user->id,
                    'status' => $status,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $newReference = $user->userReferences()->where('tags_id', '=', $tag->id)->first();

                if ($status == 1) { //reference added by user himself
                    event(new NewAction('userNewreference', $newReference->id, 'usersReference', $user->id, 'user'));
                    event(new InterestAction(auth()->guard('web')->user(), $tag, 'reference.create'));
                    event(new \App\Events\UserUpdatedEvent(auth()->guard('web')->user()));
                }
            } else {
                //if reference already exists for user
                return response()->json(array(
                    'exists' => true,
                ));
            }

            //limit to 15 references number
            if ($rights && $rights < 3) {
                //if added by himself
                $data['reference'] = UsersReference::where('tags_id', '=', $tag->id)->first();
                return response()->json(array(
                    'view' => view('user.references.unit-reference', $data)->render(),
                    'totalReferences' => $user->userReferences->count(),
                    'postedByOther' => 0,
                ));
            } else {
                //add by other
                $data['referenceName'] = $tag->name;

                //push notification for angel owner
                $reference = UsersReference::where('tags_id', '=', $tag->id)->first();
                event(new AddUserSkill($user, $reference));

                return response()->json(array(
                    'view' => view('user.references.waiting-valid', $data)->render(),
                    'totalReferences' => $user->userReferences->count(),
                    'postedByOther' => auth()->guard('web')->user()->postedUserReferences($user->id, 0)->count(),
                ));
            }
        } else {
            return response(view('errors.403'), 403);
        }
    }

    /**
     *
     * @param int $id
     */
    public function deleteReference($id)
    {
        $reference = UsersReference::findOrFail($id);
        $user = $reference->user;
        $rights = $this->Acl->getRights($user->getType(), $user->id);
        if ($rights && $rights < 3) {
            //delete netframe action, news_feed TAction post
            if ($reference->status == 1) {
                $tAction = $reference->actions->first();
                if ($tAction != null) {
                    $tAction->post()->delete();
                }
                $reference->actions()->delete();
            }

            $reference->delete();
            $reference->liked()->delete();

            //check if notification exists on this reference and delete it
            $notifs = Notif::where('type', '=', 'userNewReferenceByUser')
                ->where('author_type', '=', 'App\\User')
                ->where('author_id', '=', auth()->guard('web')->user()->id)
                ->where('parameter', 'like', '%referenceId":'.$id.'%')
                ->get();
            if (count($notifs) > 0) {
                foreach ($notifs as $notif) {
                    $notif->delete();
                }
            }

            return response()->json(array(
                'delete' => true,
                'targetId' => '#userReference-'.$id,
            ));
        } else {
            return response(view('errors.403'), 403);
        }
    }

    /**
     *
     * @param int $id
     */
    public function validReference($id)
    {
        $reference = UsersReference::findOrFail($id);
        $user = $reference->user;
        $rights = $this->Acl->getRights($user->getType(), $user->id);
        if ($rights && $rights < 3) {
            $reference->status = 1;
            $reference->updated_at = date('Y-m-d H:i:s');
            $reference->save();

            event(new NewAction('userNewreference', $reference->id, 'usersReference', $user->id, 'user'));

            //for reference owner
            event(new InterestAction($user, $reference->reference, 'reference.create'));

            event(new \App\Events\UserUpdatedEvent(auth()->guard('web')->user()));

            //for reference creator
            if ($reference->userCreate->id != $user->id) {
                event(new InterestAction($reference->userCreate, $reference->reference, 'reference.participate'));
            }

            $data['rights'] = $rights;
            $data['reference'] = $reference;

            return response()->json([
                'viewReplace' => view('user.references.unit-reference', $data)->render(),
                'targetId' => '#userReference-'.$id,
            ]);
        } else {
            return response(view('errors.403'), 403);
        }
    }
}
