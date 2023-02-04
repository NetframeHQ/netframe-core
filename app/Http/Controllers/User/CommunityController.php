<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Repository\SearchRepository2;
use App\Helpers\Lib\Acl;
use App\Community;
use App\MessageMail;
use App\Netframe;
use App\Events\NewAction;
use App\Events\ChangeConfidentiality;
use App\Events\InterestAction;
use App\Events\SubscribeToProfile;
use App\MediasFolder;
use App\Role;
use App\Events\AutoMember;
use App\Instance;
use App\Media;

class CommunityController extends BaseController
{

    public function __construct(SearchRepository2 $searchRepository)
    {
        $this->middleware('checkAuth');
        parent::__construct();

        $this->searchRepository = $searchRepository;
    }

    public function edit($id = null)
    {
        if (!is_null($id) && (!Acl::getRights('community', $id) || Acl::getRights('community', $id) > 2 )) {
            return redirect()->route('home');
        } elseif (is_null($id) && !session('profileAuth.userCanCreate.community')) {
            return response(view('errors.403'), 403);
        }

        $instance = Instance::find(session('instanceId'));

        $community = Community::findOrNew($id);

        //$View = view('community.post')->with('community', $community);

        if (request()->isMethod('POST')) {
            $validationsRules = config('validation.community/edit');

            if (request()->has('auto_member') && request()->get('auto_member') == 1) {
                $validationsRules['auto_member_role'] = 'required|not_in:0';
            }

            $validator = validator(request()->all(), $validationsRules);

            $profileConfidentiality = (request()->has('confidentiality') && request()->get('confidentiality') == 1 )
                ? 0
                : 1;

            $profileFreeJoin = (request()->has('free_join') && request()->get('free_join') == 0 ) ? 0 : 1;

            if ($validator->fails()) {
                $community->formTags = \App\Helpers\TagsHelper::getFromForm(request()->get('tags'));
                $community->formTagsSelecteds = request()->get('tags');
                $community->free_join = $profileFreeJoin;
                $community->id_foreign = request()->get('id_foreign');
                $community->type_foreign = request()->get('type_foreign');
                $community->confidentiality = $profileConfidentiality;
                $community->name = $community->name;

                if (request()->ajax()) {
                    $view = view(
                        'welcome.modals.modal-create-group',
                        ['community' => $community]
                    )->withErrors($validator)->render();

                    return response()->json([
                        'view' => $view
                    ]);
                } else {
                    $roles = new Role();
                    $data = [
                        'community' => $community,
                        'roles' => $roles->getSelectList(),
                        'tasks' => $instance->taskTemplates()->pluck('name', 'id')->toArray(),
                    ];

                    return view('community.form.form-edit', $data)->withErrors($validator);
                }
            } else {
                if (is_null($id)) {
                    $community->slug = uniqid();
                    $community->active = 1;
                } else { //edit mode, check old confidentiality
                    if ($community->confidentiality != request()->get('confidentiality')) {
                        event(new ChangeConfidentiality($community, request()->get('confidentiality')));
                    }
                    $oldTags = $community->tags;
                }

                $community->name = request()->get('name');
                $community->description = htmlentities(request()->get('description'));
                $community->users_id = auth()->guard('web')->user()->id;
                $community->instances_id = session('instanceId');
                $community->owner_id = request()->get('id_foreign');
                $community->owner_type = "App\\".studly_case(request()->get('type_foreign'));

                $personnalFolder = request()->get('with_personnal_folder') ?? false;
                $community->with_personnal_folder = $personnalFolder;

                // automember
                if (request()->has('auto_member') && request()->get('auto_member') == 1) {
                    $community->auto_member = request()->get('auto_member_role');
                    $community->auto_subscribe = 1;
                    $loadAutomember = true;
                } else {
                    $community->auto_member = 0;
                    $community->auto_subscribe = 0;
                    $loadAutomember = false;
                }

                // Geolocation
                if (request()->get('latitude') != null && request()->get('longitude') != null) {
                    $community->latitude = request()->get('latitude');
                    $community->longitude = request()->get('longitude');
                    $community->location = \App\Helpers\LocationHelper::getLocation(
                        $community->latitude,
                        $community->longitude
                    );
                }
                $community->confidentiality = $profileConfidentiality;
                $community->free_join = $profileFreeJoin;
                $community->save();

                // Renvoie du message de succes après enregistrement
                if ($id == null) {
                    \App\Helpers\ActionMessageHelper::success(trans('form.createSuccess'));

                    $community->users()->attach(auth()->guard('web')->user()->id, array('roles_id' => 1,'status' => 1));

                    //add netframe action
                    event(new NewAction(
                        'new_profile',
                        $community->id,
                        'community',
                        auth()->guard('web')->user()->id,
                        'user'
                    ));

                    // subscribe owner to profile
                    event(new SubscribeToProfile(auth()->guard('web')->user()->id, $community->id, 'Community'));

                    // generate default folders
                    MediasFolder::generateDefault($community, session('instanceId'), auth()->guard('web')->user());
                } else {
                    \App\Helpers\ActionMessageHelper::success(trans('form.editSuccess'));
                }

                // Save the tags
                \App\Helpers\TagsHelper::attachPostedTags(request()->get('tags'), $community);

                //insert interest
                if (is_null($id)) {
                    $tags = $community->tags;
                    // hard fix for forcing elastic index
                    $community->save();

                    // proccess profile image on profile creation
                    if (request()->get('profileMediaId') != null) {
                        $profileMediaId = request()->get('profileMediaId');
                        // detach from user and attach to profile
                        $media = Media::find($profileMediaId);
                        if ($media != null) {
                            auth()->user()->medias()->detach($media->id);
                            $mediaFolder = $community->getDefaultFolder('__profile_medias');
                            $community->medias()->attach($media->id, [
                                'medias_folders_id' => $mediaFolder,
                                'profile_image' => 1,
                            ]);
                            $community->profile_media_id = $media->id;
                            $community->save();
                        }
                    }

                    // proccess cover image on profile creation
                    if (request()->get('coverMediaId') != null) {
                        $coverMediaId = request()->get('coverMediaId');
                        // detach from user and attach to profile
                        $media = Media::find($coverMediaId);
                        if ($media != null) {
                            auth()->user()->medias()->detach($media->id);
                            $mediaFolder = $community->getDefaultFolder('__profile_medias');
                            $community->medias()->attach($media->id, [
                                'medias_folders_id' => $mediaFolder,
                                'cover_image' => 1,
                            ]);
                            $community->cover_media_id = $media->id;
                            $community->save();
                        }
                    }
                } else {
                    $community->load('tags');
                    $tags = \App\Helpers\TagsHelper::compareTags($oldTags, $community->tags);
                }

                if ($community->tags != null) {
                    event(new InterestAction(auth()->guard('web')->user(), $community->tags, 'profile.create'));
                }

                /*
                 * create default users folders
                 */
                if ($personnalFolder) {
                    $community->createPersonnalFolders();
                }

                // manage default channel
                if (request()->has('default_channel')
                    && request()->get('default_channel') == 1
                    && $community->has_defaultChannel()->first() == null) {
                    // create channel
                    $community->createDefaultChannel();
                }

                // manage default task project
                if (request()->has('default_tasks')
                    && request()->get('default_tasks') == 1
                    && $community->has_defaultTasks()->first() == null) {
                    // create channel
                    $community->createDefaultTasks(request()->get('default_tasks_template'));
                }

                session([
                    "allProfiles" => Netframe::getProfiles(auth()->guard('web')->user()->id),
                    "acl" => Netframe::getAcl(auth()->guard('web')->user()->id)
                ]);

                if ($loadAutomember) {
                    event(new AutoMember($community, request()->get('auto_member_role')));
                }

                if (request()->ajax()) {
                    $subViewMenu = view('community.partials.menu-line', ['profile' => $community])->render();

                    return response()->json([
                        'targetId' => '#sidebar-wrapper #community-list',
                        'viewContent' => $subViewMenu,
                        'newContent' => true,
                        'success' => true,
                        'modalReplaceUrl' => url()->route('welcome.modal.invite.users'),
                    ]);
                } else {
                    return redirect()->route('community.edit', ['id' => $community->id]);
                }
            }
        } elseif (is_null($id)) {
            $community->confidentiality = 0;
        }

        $community->free_join = 1;

        session()->flash('profileDisplay', 'community');
        session()->flash('profileDisplayId', $community->id);

        $roles = new Role();
        $data = [
            'community' => $community,
            'roles' => $roles->getSelectList(),
            'tasks' => $instance->taskTemplates()->pluck('name', 'id')->toArray(),
        ];

        return view('community.form.form-edit', $data);
    }

    /*
     * global list of user communitites
     */

    public function manage()
    {
        $user = auth('web')->user();
        $communities = $user->community()->orderBy('name')->get();

        $data = [
            'profileType' => 'community',
            'profiles' => $communities,
        ];

        return view('profiles.list', $data);
    }

    /**
     * get message feed of a specific community
     * @param int $idCommunity
     */
    public function inbox($idCommunity, $full = 0)
    {
        $community = Community::findOrFail($idCommunity);
        $groupMail = $community->receivedMessageGroups[0];

        $data = array();
        $nbDisplayed = config('messages.nbDisplayed');

        $feedInit = MessageMail::where('messages_mail_group_id', '=', $groupMail->id)
        ->orderBy('updated_at', 'asc');

        if ($full == 0) {
            $totalMessages = $feedInit->count();
            $skip = ($totalMessages > $nbDisplayed) ? $totalMessages - $nbDisplayed : 0;
            $feed = $feedInit->skip($skip)->take($nbDisplayed)->get();
            $data['totalMessages'] = $totalMessages;
            $data['nbDisplayed'] = $nbDisplayed;
        } else {
            $feed = $feedInit->get();
        }

        $data['idForeignTo'] = $idCommunity;
        $data['typeForeignTo'] = 'community';
        $data['idForeignFrom'] = auth()->guard('web')->user()->id;
        $data['typeForeignFrom'] = 'user';
        $data['community'] = $community;
        $data['feed'] = $feed;
        $data['feedId'] = $groupMail->id;
        $data['type'] = $groupMail->type;
        $data['overrideType'] = 2;
        $data['types'] = config('messages.types');

        session()->flash('profileDisplay', 'community');
        session()->flash('profileDisplayId', $community->id);

        return view('community.page.inbox', $data);
    }

    /*
     * edit community of project
     * @param int $id id of the project
     * @param int $status refers to members status (config netframe.members_status)
     */
    public function editCommunity($id, $status = null)
    {
        if (!is_null($id) && (!Acl::getRights('community', $id) || Acl::getRights('community', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $community = Community::findOrFail($id);

        //get project members
        $communityStatus = config('netframe.members_status');

        $communityCommunity = $community
            ->users()
            ->wherePivot('status', $status)
            ->orderBy('community_has_users.roles_id')
            ->get();

        $data = [];
        $data['profile'] = $community;
        $data['community'] = $community;
        $data['profileCommunity'] = $communityCommunity;
        $data['communityType'] = $communityStatus[$status];

        session()->flash('profileDisplay', 'community');
        session()->flash('profileDisplayId', $community->id);

        return view('join.community', $data);
    }

    public function inviteUsers($id)
    {
        if (!is_null($id) && (!Acl::getRights('community', $id) || Acl::getRights('community', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $community = Community::findOrFail($id);

        $query = '';
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
        $this->searchRepository->inviteProfile = $community;

        $searchParameters = $this
            ->searchRepository
            ->initializeConfig('search_results', $targetsProfiles, false, $byInterests);
        $results = $this->searchRepository->search($searchParameters, $targetsProfiles);

        $data = [];
        $data['profile'] = $community;
        $data['community'] = $community;
        $data['profileCommunity'] = $results[0];
        $data['fromInvite'] = true;

        session()->flash('profileDisplay', 'community');
        session()->flash('profileDisplayId', $community->id);

        return view('join.invite', $data);
    }
}
