<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Repository\SearchRepository2;
use App\User;
use App\Profile;
use App\Netframe;
use App\Notif;
use App\Subscription;
use App\Events\NewAction;
use App\Events\AddProfile;
use App\Events\InterestAction;
use App\Events\SubscribeToProfile;
use App\Events\PostNotif;
use App\Role;
use App\Helpers\Lib\Acl;
use App\Instance;

class JoinController extends BaseController
{
    private $searchRepository;

    public function __construct(SearchRepository2 $searchRepository)
    {
        $this->middleware('checkAuth');

        parent::__construct();

        $this->searchRepository = $searchRepository;
    }

    public function getFormJoinAsk($profile_id, $profile_type, $users_id)
    {
        $data = array();

        $joinProfile = Profile::gather($profile_type);
        $profile = $joinProfile::findOrFail($profile_id);

        //test if user is already join
        $alreadyJoin = false;
        $inviteAnswer = false;
        if ($profile->users->contains(auth()->guard('web')->user()->id)) {
            $checkUser = $profile->users()->where('id', '=', auth()->guard('web')->user()->id)->first();
            if ($checkUser->pivot->status != 0) {
                $alreadyJoin = true;
            } else {
                $this->inviteOk($profile);
                $inviteAnswer = true;
                return redirect()->to($profile->getUrl());
            }
        }

        if ($profile->instances_id != session('instanceId') || $alreadyJoin) {
            return response(view('errors.403'), 403);
        }

        //if public profile, direct join and confirm message in modal
        //if($profile->confidentiality == 1 || $profile->free_join == 1){
        if ($profile->free_join == 1) {
            $profile->users()->attach(auth()->guard('web')->user()->id, ['roles_id' => 4, 'status' => 1]);

            $profile->attachToDefaultChannel(auth()->guard('web')->user()->id);
            $profile->createPersonnalUserFolder(auth()->guard('web')->user()->id);

            // implement user rights
            session([
                "acl" => Netframe::getAcl(auth()->guard('web')->user()->id),
                "allProfiles" => Netframe::getProfiles(auth()->guard('web')->user()->id)
            ]);

            //insert notification for profile owners, add netframe action
            $notifArray = [];
            $notifJson = [
                'profile_type'      => get_class($profile),
                'profile_id'        => $profile->id,
            ];

            $profileUsers = $profile->users()->where('id', '!=', auth()->guard('web')->user()->id)->get();
            foreach ($profileUsers as $profileUser) {
                $notifArray = [
                    'instances_id'   => session('instanceId'),
                    'author_id'      => $profileUser->id,
                    'author_type'    => "App\\".ucfirst('user'),
                    'type'           => 'has_join_'.$profile->getType(),
                    'user_from'      => auth()->guard('web')->user()->id,
                    'parameter'      => json_encode($notifJson),
                    'read'           => 0,
                    'created_at'     => new \DateTime(),
                    'updated_at'     => new \DateTime()
                ];
                event(new PostNotif($notifArray));
            }

            $this->subscribeInterest($profile, auth()->guard('web')->user()->id);

            return redirect()->to($profile->getUrl());
        } else {
            $data['profile_id'] = $profile_id;
            $data['profile_type'] = $profile_type;
            $data['users_id'] = $users_id;

            return view('netframe.form-join', $data);
        }
    }

    public function removeJoin()
    {

        if (request()->ajax()) {
            $notify     = new Notif();

            $dataJson   = array();
            $data       = array();

            $profile_id = request()->get('profile_id');
            $profile_type = request()->get('profile_type');
            $users_id = request()->get('users_id');

            if ($users_id != auth()->guard('web')->user()->id) {
                return response(view('errors.403'), 403);
            }

            $user = User::find($users_id);

            $joinProfile = Profile::gather($profile_type);
            $profile = $joinProfile::find($profile_id);

            //ask user to recheck rights on new action
            event(new AddProfile($users_id));

            //detach user
            $profile->users()->detach($users_id);

            if ($profile->confidentiality == 0) {
                //delete subscription
                $user
                    ->subscriptionsList()
                    ->where('profile_type', '=', get_class($profile))
                    ->where('profile_id', '=', $profile->id)
                    ->delete();
            } else {
                //update subscription
                $user
                    ->subscriptionsList()
                    ->where('profile_type', '=', get_class($profile))
                    ->where('profile_id', '=', $profile->id)
                    ->update(['confidentiality' => 1]);
            }

            // delete join notification if exists
            $deleteNotifications = Notif::where('user_from', '=', $users_id)
                ->where('type', '=', 'join' . class_basename($profile))
                ->where('parameter', 'like', '%profile_id":"'.$profile->id.'%')
                ->get();

            foreach ($deleteNotifications as $notifications) {
                $notifications->delete();
            }

            $dataJson['viewContent'] = \HTML::joinProfileBtn(
                $profile->id,
                $profile->getType(),
                auth()->guard('web')->user()->id,
                null,
                $profile->free_join,
                $profile->users()->count()
            );
            if ($profile_type == 'channel') {
                $dataJson['removeElement'] = 'channels feeds #'.$profile_type.'-'.$profile_id;
            }

            return response()->json($dataJson);
        }
    }

    public function postJoinAsk()
    {
        $data = array();

        if (request()->ajax()) {
            $dataJson = array();

            $notify = new Notif();

            $users_id       = request()->get('users_id');
            $profile_id     = request()->get('profile_id');
            $profile_type   = request()->get('profile_type');
            $comment        = htmlentities(request()->get('content'));
            /*
            $guest_id       = request()->get('guest_id');
            $guest_type     = request()->get('guest_type');
            */

            $validator = validator(request()->all(), config('validation.page/joinPost'));

            if ($validator->fails()) {
                $data['errors']     = $validator->messages();
                $data['inputOld']   = request()->all();
                return response()->json(array(
                    'view' => view('netframe.form-join', $data)->render(),
                ));
            } else {
                $joinProfile = Profile::gather($profile_type);
                $profile = $joinProfile::findOrFail($profile_id);

                //test if user is already join
                if ($profile->users->contains(auth()->guard('web')->user()->id)
                    || $profile->instances_id != session('instanceId')) {
                    return response(view('errors.403'), 403);
                }

                $type = ucfirst($joinProfile->getType());

                $profile->users()->attach(auth()->guard('web')->user()->id, ['roles_id' => 4, 'status' => 2]);

                $toBeNotified = array();
                $joinAuthors = $profile->users()->wherePivot('status', '<=', '2')->get();
                foreach ($joinAuthors as $joinAuthor) {
                    if ($joinAuthor->pivot->roles_id == 1 || $joinAuthor->pivot->roles_id == 2) {
                        if (!in_array($joinAuthor->pivot->users_id, $toBeNotified)) {
                            $toBeNotified[] = $joinAuthor->pivot->users_id;
                            $parameter = [
                                'profile_id'    => $profile_id,
                                /*
                                'guest_id'      => $guest_id,
                                'guest_type'    => $guest_type,
                                */
                                'comment'       => $comment
                            ];
                            //$notifJoinProfile = $notify->insertAuthor(
                            //    $joinAuthor->pivot->users_id,
                            //    $users_id,
                            //    $parameter,
                            //    'join'. $type
                            //);
                            $notifArray = [
                                'instances_id'   => session('instanceId'),
                                'author_id'      => $joinAuthor->pivot->users_id,
                                'author_type'    => "App\\".ucfirst('user'),
                                'type'           => 'join'. $type,
                                'user_from'      => $users_id,
                                'parameter'      => json_encode($parameter),
                                'read'           => 0,
                                'created_at'     => new \DateTime(),
                                'updated_at'     => new \DateTime()
                            ];
                            event(new PostNotif($notifArray));
                        }
                    }
                }

                $data = [
                    'profile_id' => $profile_id,
                    'profile_type' => $profile_type,
                    'users_id' => auth()->guard('web')->user()->id
                ];

                $dataJson['closeModal'] = true;
                $joined = $profile->users()->where('users_id', '=', auth()->guard('web')->user()->id)->first();
                if ($joined != null) {
                    $joined = $joined->pivot->status;
                }
                $dataJson['viewContent'] = \HTML::joinProfileBtn(
                    $profile->id,
                    $profile->getType(),
                    auth()->guard('web')->user()->id,
                    $joined,
                    $profile->confidentiality,
                    1,
                    $profile->users()->count()
                );
                $dataJson['joinNotify'] = true;
                $dataJson['joinProfile'] = $profile->getType().'-'.$profile->id;

                return response()->json($dataJson);
            }
        }
    }

    public function joinAnswer($action)
    {
        $dataJson = array();
        // formate string to json and decode json to array
        $data = request()->get('postData');

        $notif = new Notif();

        if (request()->ajax()) {
            $dataJson = array();
            $deleteJoinNotif = true;

            // formate string to json and decode json to array
            $data = request()->get('postData');
            $profile_type   = $data['type_profile'];
            $profile_id     = $data['profile_id'];
            $users_id       = $data['users_id'];
            $user_from      = $data['friend_id'];

            //initialize guest user and profile
            $guestUser = User::findOrFail($user_from);
            $profileType = Profile::gather($profile_type);
            $profile = $profileType->find($profile_id);
            $type = class_basename($profileType);

            if (!$this->Acl->getRights($profile_type, $profile_id)
                || !$guestUser->instances->contains($profile->instances_id)) {
                return response(view('errors.403'), 403);
            }

            if ($action == "deleteInvite") {
                $deleteJoinNotif = false;
                $UserExists = $profile->users()->detach($user_from);

                // delete join notification if exists
                $deleteNotifications = Notif::where('author_id', '=', $guestUser->id)
                ->where('author_type', '=', get_class($guestUser))
                ->where('type', '=', 'invite' . class_basename($profile))
                ->where('parameter', 'like', '%profile_id":"'.$profile->id.'%')
                ->get();

                foreach ($deleteNotifications as $notifications) {
                    $notifications->delete();
                }
            } elseif ($action == "resend") {
                $deleteNotifications = Notif::where('author_id', '=', $guestUser->id)
                ->where('author_type', '=', get_class($guestUser))
                ->where('type', '=', 'invite' . class_basename($profile))
                ->where('parameter', 'like', '%profile_id":"'.$profile->id.'%')
                ->get();

                foreach ($deleteNotifications as $notifications) {
                    $notifications->delete();
                }
                $notifJson = [
                    "role" => request()->get('role'),
                    "profile_type" => get_class($profile),
                    "profile_id" => $profile_id,
                ];

                $notifArray = [
                    'instances_id'   => $profile->instances_id,
                    'author_id'      => $guestUser->id,
                    'author_type'    => get_class($guestUser),
                    'type'           => 'invite'.class_basename($profile),
                    'user_from'      => auth()->guard('web')->user()->id,
                    'parameter'      => json_encode($notifJson),
                    'read'           => 0,
                ];

                event(new PostNotif($notifArray));
            } elseif ($action == "refuse") {
                $UserExists = $profile->users()->detach($user_from);

                // check detach from default channel
                $profile->removeUser($user_from);

                //ask user to recheck rights on new action
                event(new AddProfile($user_from));

                // delete user subscription
                if ($profile->confidentiality == 0) {
                    $guestUser
                        ->subscriptionsList()
                        ->where('profile_type', '=', get_class($profile))
                        ->where('profile_id', '=', $profile->id)
                        ->delete();
                }
            } elseif ($action == "blacklist") {
                $profile->users()->updateExistingPivot($user_from, ['status' => 3]);

                //ask user to recheck rights on new action
                event(new AddProfile($user_from));

                // delete user subscription
                if ($profile->confidentiality == 0) {
                    $guestUser
                        ->subscriptionsList()
                        ->where('profile_type', '=', get_class($profile))
                        ->where('profile_id', '=', $profile->id)
                        ->delete();
                }
            } elseif ($action == "release") {
                $profile->users()->detach($user_from);
            } elseif (in_array($action, [
                'toOwner',
                'toAdministrator',
                'toModerator',
                'toContributor',
                'toParticipant'
            ])) {
                $roleName = substr($action, 2, strlen($action));
                $role = Role::where('name', 'like', $roleName)->first();
                $profile->users()->updateExistingPivot($user_from, ['status' => 1, 'roles_id' => $role->id]);

                if (class_basename($profile) != 'Channel') {
                    $profile->attachToDefaultChannel($user_from);
                    $profile->createPersonnalUserFolder($user_from);
                }

                //ask user to recheck rights on new action
                event(new AddProfile($user_from));

                //send notification to user
                $test = Notif::where('parameter', 'like', '%profile_type":"%'.class_basename($profile).'%')
                    ->where('parameter', 'like', '%profile_id":'.$profile->id.'%')
                    ->where('type', '=', 'memberUpdate')
                    ->where('author_id', '=', $user_from)
                    ->where('author_type', '=', 'App\\User')
                    ->delete();

                $notifArray = array(
                    'instances_id'   => session('instanceId'),
                    'author_id'      => $user_from,
                    'author_type'    => 'App\\'.ucfirst('user'),
                    'type'           => 'memberUpdate',
                    'user_from'      => auth()->guard('web')->user()->id,
                    'parameter'      => json_encode([
                        'roles_id' => $role->id,
                        'profile_type' => get_class($profile),
                        'profile_id' => $profile->id
                    ]),
                );
                event(new PostNotif($notifArray));


                //return user view in ajax
                $dataJson = [];
                $user = $profile->users()->wherePivot('users_id', $user_from)->first();
                $dataJson['viewContent'] = view(
                    'join.member-card',
                    ['member' => $user, 'profile' => $profile]
                )->render();
                return response()->json($dataJson);
            } else {
                $role = Role::where('name', 'like', $action)->first();

                $UserExists = $profile->users()->where(['users_id'  => $user_from])->where('status', '=', '2')->first();
                $typeNotif = $profileType->getType();
                $notifConfig = config('notification.type');

                if ($UserExists) {
                    $parameter = [
                        'profile_id' => $profile_id,
                        'type'       => $notifConfig[$typeNotif]['the_Profile'],
                    ];

                    $profile->users()->updateExistingPivot($user_from, ['status' => 1, 'roles_id' => $role->id]);

                    // send notification to all other members
                    $notifArray = [];
                    $notifJson = [
                        'profile_type'      => get_class($profile),
                        'profile_id'        => $profile->id,
                    ];

                    $otherMembers = $profile
                        ->users()
                        ->whereNotIn('id', [$user_from, auth()->guard('web')->user()->id])
                        ->get();
                    foreach ($otherMembers as $profileUser) {
                        $notifArray = [
                            'instances_id'   => session('instanceId'),
                            'author_id'      => $profileUser->id,
                            'author_type'    => get_class($profileUser),
                            'type'           => 'has_join_'.$profile->getType(),
                            'user_from'      => $user_from,
                            'parameter'      => json_encode($notifJson),
                            'read'           => 0,
                            'created_at'     => new \DateTime(),
                            'updated_at'     => new \DateTime()
                        ];
                        event(new PostNotif($notifArray));
                    }

                    /*
                    if(!empty($notifArray)){
                        Notif::insert($notifArray);
                    }
                    */

                    $this->subscribeInterest($profile, $user_from);

                    // notify guest
                    // $joinProfileOk = $notif->insertAuthor($user_from, $users_id, $parameter, 'join' . $type . 'Ok');
                    $notifArray = [
                        'instances_id'   => session('instanceId'),
                        'author_id'      => $user_from,
                        'author_type'    => 'App\\User',
                        'type'           => 'join' . $type . 'Ok',
                        'user_from'      => $users_id,
                        'parameter'      => json_encode($parameter),
                        'read'           => 0,
                    ];
                    event(new PostNotif($notifArray));

                    //ask user to recheck rights on new action
                    event(new AddProfile($user_from));

                    // add netframe action
                    event(new NewAction('join'.ucfirst($type), $profile_id, ucfirst($type), $user_from, 'user'));
                }
            }

            if ($deleteJoinNotif) {
                //delete ask notifications
                $deleteNotifications = $notif->where('user_from', '=', $user_from)
                    ->where('type', '=', 'join'. $type)
                    ->where('parameter', 'like', '%profile_id":"'.$profile->id.'%')
                    ->get();

                foreach ($deleteNotifications as $notifications) {
                    $notifications->delete();
                }
            }

            return response()->json($dataJson);
        }
    }

    public function searchUsers()
    {
        $profile_type = request()->get('profile_type');
        $profile_id = request()->get('profile_id');

        if (!$this->Acl->getRights($profile_type, $profile_id)) {
            return response(view('errors.403'), 403);
        }

        $joinProfile = Profile::gather(strtolower($profile_type));
        $profile = $joinProfile::findOrFail($profile_id);

        $query = request()->get('query');
        $loadFilters = (request()->has('loadFilters')) ? request()->get('loadFilters') : true ;
        $hashtag = (request()->has('$hashtag')) ? request()->get('$hashtag') : '';
        $placeSearch = (request()->has('placeSearch')) ? request()->get('placeSearch') : '';
        $targetsProfiles = ['user' => 1];
        $byInterests = (request()->has('byInterests') && request()->get('byInterests') == 1) ? 1 : 0;

        $this->searchRepository->route = 'search_results';
        $this->searchRepository->targetsProfiles = $targetsProfiles;
        $this->searchRepository->toggleFilter = false;
        $this->searchRepository->byInterests = $byInterests;
        $this->searchRepository->newProfile = 0;
        $this->searchRepository->inviteProfile = $profile;

        $searchParameters = $this
            ->searchRepository
            ->initializeConfig('search_results', $targetsProfiles, false, $byInterests);
        $results = $this->searchRepository->search($searchParameters, $targetsProfiles);

        //load view
        $data = [];
        $data['profile'] = $profile;
        $data['profileCommunity'] = $results[0];

        $dataJson = [];
        $dataJson['viewContent'] = view('join.search-results', $data)->render();

        return response()->json($dataJson);
    }

    public function inviteAnswer($action)
    {
        $data = request()->get('postData');
        $users_id = $data['users_id'];
        $profile_type = $data['profile_type'];
        $profile_id = $data['profile_id'];

        $guestUser = User::findOrFail($users_id);
        $profileType = Profile::gather($profile_type);
        $profile = $profileType->find($profile_id);

        if ($users_id != auth()->guard('web')->user()->id
            || !$profile->users->contains($users_id)
            || $profile->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        if ($action == 'accept') {
            $profile->users()->updateExistingPivot($users_id, ['status' => 1]);

            // implement user rights
            session([
                "acl" => Netframe::getAcl(auth()->guard('web')->user()->id),
                "allProfiles" => Netframe::getProfiles(auth()->guard('web')->user()->id)
            ]);

            $this->subscribeInterest($profile, auth()->guard('web')->user()->id);
        } elseif ($action == 'deny') {
            //$profile->users()->detach($users_id);
        }

        // delete join notification if exists
        $deleteNotifications = Notif::where('author_id', '=', $guestUser->id)
            ->where('author_type', '=', get_class($guestUser))
            ->where('type', '=', 'invite' . class_basename($profile))
            ->where(function ($w) use ($profile) {
                $w->orWhere('parameter', 'like', '%profile_id":"'.$profile->id.'%')
                   ->orWhere('parameter', 'like', '%profile_id":'.$profile->id.'%');
            })
            ->get();

        foreach ($deleteNotifications as $notifications) {
            $notifications->delete();
        }

        return response()->json([]);
    }

    private function subscribeInterest($profile, $users_id)
    {
        $guestUser = User::findOrFail($users_id);

        //subscribe user to profile
        if (!Subscription::existing($profile->id, class_basename($profile), $users_id)) {
            event(new SubscribeToProfile($users_id, $profile->id, class_basename($profile)));
        }

        //insert interests on tags
        if ($profile->tags != null) {
            event(new InterestAction($guestUser, $profile->tags, 'profile.participate'));
        }
    }

    private function inviteOk($profile)
    {
        $guestUser = User::findOrFail(auth()->guard('web')->user()->id);
        $profile->users()->updateExistingPivot($guestUser->id, ['status' => 1]);

        $profile->attachToDefaultChannel(auth()->guard('web')->user()->id);
        $profile->createPersonnalUserFolder(auth()->guard('web')->user()->id);

        $this->subscribeInterest($profile, auth()->guard('web')->user()->id);

        // implement user rights
        session([
            "acl" => Netframe::getAcl(auth()->guard('web')->user()->id),
            "allProfiles" => Netframe::getProfiles(auth()->guard('web')->user()->id)
        ]);

        // delete join notification if exists
        $deleteNotifications = Notif::where('author_id', '=', $guestUser->id)
            ->where('author_type', '=', get_class($guestUser))
            ->where('type', '=', 'invite' . class_basename($profile))
            ->where('parameter', 'like', '%profile_id":"'.$profile->id.'%')
            ->get();

        foreach ($deleteNotifications as $notifications) {
            $notifications->delete();
        }
    }

    /*
     * global function to manage righs for user on instance or groups
     */
    public function changeRights()
    {
        $typeLink = request()->get('type'); // instance or profile (house, community, project, channel)
        $userId = request()->get('user');
        $newStatus = request()->get('status');
        $profileId = request()->get('id');

        if ($typeLink == 'instance' && $profileId == session('instanceId')) {
            $this->middleware('instanceManager');
            if (!auth('web')->user()->isInstanceAdmin()) {
                return response(view('errors.403'), 403);
            }
            $instance = Instance::find($profileId);
            $user = $instance->users()->where('id', '=', $userId)->first();

            if (is_numeric($newStatus) && config('rights.instance.' . $newStatus) != null) {
                    $user->instances()->updateExistingPivot(
                        $instance->id,
                        ['roles_id' => $newStatus]
                    );
            } else {
                if ($newStatus == 'enable') {
                    $user->active = 1;
                    $user->save();
                } elseif ($newStatus == 'disable') {
                    $user->active = 0;
                    $user->save();
                }
            }

            // build view return
            $view = view('join.member-card', [
                'profile' => $instance,
                'member' => $user
            ])->render();
        } else {
            // check current user rights on profile
            if (!is_null($profileId) &&
                (
                    (!Acl::getRights($typeLink, $profileId) || Acl::getRights($typeLink, $profileId) > 2 ) &&
                    !auth('web')->user()->isInstanceAdmin()
                )) {
                return response(view('errors.403'), 403);
            }

            $profileModel = Profile::gather($typeLink);
            $profile = $profileModel::find($profileId);

            if (request()->get('fromInvite') == 1) {
                $this->inviteUser($profile, $userId, $newStatus);
            } else {
                if ($newStatus == -1) { // remove
                    $profile->users()->detach($userId);
                } elseif ($newStatus == -2 && $profile->users->contains($userId)) { // ban
                    $profile->users()->updateExistingPivot($userId, [
                        'status' => 3
                    ]);
                } elseif ($newStatus == -3 && $profile->users->contains($userId)) { // ban
                    $profile->users()->updateExistingPivot($userId, [
                        'status' => 1
                    ]);
                } elseif ($profile->users->contains($userId)) {
                    $profile->users()->updateExistingPivot($userId, ['roles_id' => $newStatus]);
                } else {
                    $profile->users()->attach($userId, [
                        'roles_id' => $newStatus,
                        'status' => 1
                    ]);
                }
            }

            $user = $profile->users()->find($userId);
            if ($user == null) { //if detached
                $user = User::find($userId);
            }

            // build view return
            $profileReturn = (request()->get('from') == 'User') ? $user : $profile;
            $member = (request()->get('from') == 'User') ? $profile : $user;

            $view = view('join.member-card', [
                'profile' => $profileReturn,
                'member' => $member
            ])->render();
        }

        // event to reload user rights
        event(new AddProfile($userId));



        return response()->json([
            'view' => $view,
        ]);
    }

    private function inviteUser($profile, $userId, $role)
    {
        $defaultRights = [
            'roles_id' => $role,
            'status' => 0,
        ];

        //check user instance and if profile has user
        $user = User::find($userId);

        if ($user != null && $user->instances->contains($profile->instances_id)
            && !$profile->users->contains($userId)) {
                // attach waiting user to profile
                $profile->users()->attach($userId, $defaultRights);

                //push notification
                $notifJson = [
                    "role" => request()->get('role'),
                    "profile_type" => get_class($profile),
                    "profile_id" => $profile->id,
                ];

                $notifArray = [
                    'instances_id'   => $profile->instances_id,
                    'author_id'      => $user->id,
                    'author_type'    => get_class($user),
                    'type'           => 'invite'.class_basename($profile),
                    'user_from'      => auth()->guard('web')->user()->id,
                    'parameter'      => json_encode($notifJson),
                    'read'           => 0,
                ];

                event(new PostNotif($notifArray));
        }
    }
}
