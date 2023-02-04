<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Repository\SearchRepository2;
use \App\Helpers\Lib\Acl;
use App\House;
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

class HouseController extends BaseController
{

    public function __construct(SearchRepository2 $searchRepository)
    {
        $this->middleware('checkAuth');
        parent::__construct();

        $this->searchRepository = $searchRepository;
    }

    /**
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\$this
     */
    public function edit($id = null)
    {
        if (!is_null($id) && (!Acl::getRights('house', $id) || Acl::getRights('house', $id) > 2 )) {
            return redirect()->route('home');
        } elseif (is_null($id) && !session('profileAuth.userCanCreate.house')) {
            return response(view('errors.403'), 403);
        }

        $instance = Instance::find(session('instanceId'));

        $house = House::findOrNew($id);

        // Store instance view with object profile in variable
        //$View = view('house.post')->with('house', $house);

        if (request()->isMethod('POST')) {
            // Trim every input get
            //request()->merge( array_map('trim', request()->all()) );

            $validationsRules = config('validation.house/edit');

            if (request()->has('auto_member') && request()->get('auto_member') == 1) {
                $validationsRules['auto_member_role'] = 'required|not_in:0';
            }

            $validator = validator(request()->all(), $validationsRules);

            $profileConfidentiality = (request()->has('confidentiality') && request()->get('confidentiality') == 1 )
                ? 0
                : 1;

            $profileFreeJoin = (request()->has('free_join') && request()->get('free_join') == 0 ) ? 0 : 1;

            if ($validator->fails()) {
                $house->formTags = \App\Helpers\TagsHelper::getFromForm(request()->get('tags'));
                $house->formTagsSelecteds = request()->get('tags');
                $house->free_join = $profileFreeJoin;
                $house->id_foreign = request()->get('id_foreign');
                $house->type_foreign = request()->get('type_foreign');
                $house->confidentiality = $profileConfidentiality;

                $roles = new Role();
                $data = [
                    'house' => $house,
                    'roles' => $roles->getSelectList(),
                    'tasks' => $instance->taskTemplates()->pluck('name', 'id')->toArray(),
                ];

                return view('house.form.form-edit', $data)->withErrors($validator);
            } else {
                if (is_null($id)) {
                    $house->slug = uniqid();
                    $house->active = 1;
                } else {
                    if ($house->confidentiality != request()->get('confidentiality')) {
                        event(new ChangeConfidentiality($house, request()->get('confidentiality')));
                    }
                    $oldTags = $house->tags;
                }

                // Enregistrement base de donnée
                $house->name = request()->get('name');
                $house->description = htmlentities(request()->get('description'));
                $house->users_id = auth()->guard('web')->user()->id;
                $house->instances_id = session('instanceId');
                $house->owner_id = request()->get('id_foreign');
                $house->owner_type = "App\\".studly_case(request()->get('type_foreign'));

                $personnalFolder = request()->get('with_personnal_folder') ?? false;
                $house->with_personnal_folder = $personnalFolder;

                // automember
                if (request()->has('auto_member') && request()->get('auto_member') == 1) {
                    $house->auto_member = request()->get('auto_member_role');
                    $house->auto_subscribe = 1;
                    $loadAutomember = true;
                } else {
                    $house->auto_member = 0;
                    $house->auto_subscribe = 0;
                    $loadAutomember = false;
                }

                // Geolocation
                if (request()->get('latitude') != null && request()->get('longitude') != null) {
                    $house->latitude = request()->get('latitude');
                    $house->longitude = request()->get('longitude');
                    $house->location = \App\Helpers\LocationHelper::getLocation($house->latitude, $house->longitude);
                }
                $house->confidentiality = $profileConfidentiality;
                $house->free_join = $profileFreeJoin;
                $house->save();

                // Renvoie du message de succes après enregistrement
                if ($id == null) {
                    \App\Helpers\ActionMessageHelper::success(trans('form.createSuccess'));
                    $house->users()->attach(auth()->guard('web')->user()->id, array('roles_id' => 1, 'status'=>1));

                    //add netframe action
                    event(new NewAction('new_profile', $house->id, 'house', auth()->guard('web')->user()->id, 'user'));

                    // subscribe owner to profile
                    event(new SubscribeToProfile(auth()->guard('web')->user()->id, $house->id, 'House'));

                    // generate default folders
                    MediasFolder::generateDefault($house, session('instanceId'), auth()->guard('web')->user());
                } else {
                    \App\Helpers\ActionMessageHelper::success(trans('form.editSuccess'));
                }

                // Save the tags
                \App\Helpers\TagsHelper::attachPostedTags(request()->get('tags'), $house);

                //insert interest
                if (is_null($id)) {
                    $tags = $house->tags;
                    // hard fix for forcing elastic index
                    $house->save();

                    // proccess profile image on profile creation
                    if (request()->get('profileMediaId') != null) {
                        $profileMediaId = request()->get('profileMediaId');
                        // detach from user and attach to profile
                        $media = Media::find($profileMediaId);
                        if ($media != null) {
                            auth()->user()->medias()->detach($media->id);
                            $mediaFolder = $house->getDefaultFolder('__profile_medias');
                            $house->medias()->attach($media->id, [
                                'medias_folders_id' => $mediaFolder,
                                'profile_image' => 1,
                            ]);
                            $house->profile_media_id = $media->id;
                            $house->save();
                        }
                    }

                    // proccess cover image on profile creation
                    if (request()->get('coverMediaId') != null) {
                        $coverMediaId = request()->get('coverMediaId');
                        // detach from user and attach to profile
                        $media = Media::find($coverMediaId);
                        if ($media != null) {
                            auth()->user()->medias()->detach($media->id);
                            $mediaFolder = $house->getDefaultFolder('__profile_medias');
                            $house->medias()->attach($media->id, [
                                'medias_folders_id' => $mediaFolder,
                                'cover_image' => 1,
                            ]);
                            $house->cover_media_id = $media->id;
                            $house->save();
                        }
                    }
                } else {
                    $house->load('tags');
                    $tags = \App\Helpers\TagsHelper::compareTags($oldTags, $house->tags);
                }

                if ($house->tags != null) {
                    event(new InterestAction(auth()->guard('web')->user(), $house->tags, 'profile.create'));
                }

                /*
                 * create default users folders
                 */
                if ($personnalFolder) {
                    $house->createPersonnalFolders();
                }

                // manage default channel
                if (request()->has('default_channel')
                    && request()->get('default_channel') == 1
                    && $house->has_defaultChannel()->first() == null) {
                    // create channel
                    $house->createDefaultChannel();
                }

                // manage default task project
                if (request()->has('default_tasks')
                    && request()->get('default_tasks') == 1
                    && $house->has_defaultTasks()->first() == null) {
                    // create channel
                    $house->createDefaultTasks(request()->get('default_tasks_template'));
                }

                session([
                    "allProfiles" => Netframe::getProfiles(auth()->guard('web')->user()->id),
                    "acl" => Netframe::getAcl(auth()->guard('web')->user()->id)
                    ]);

                if ($loadAutomember) {
                    event(new AutoMember($house, request()->get('auto_member_role')));
                }

                return redirect()->route('house.edit', ['id' => $house->id]);
                //return redirect()->route('page.house', array('id' => $house->id, 'name' => str_slug($house->name)));
            }
        } elseif (is_null($id)) {
            $house->confidentiality = 1;
            $house->free_join = 1;
        }


        session()->flash('profileDisplay', 'house');
        session()->flash('profileDisplayId', $house->id);

        $roles = new Role();
        $data = [
            'house' => $house,
            'roles' => $roles->getSelectList(),
            'tasks' => $instance->taskTemplates()->pluck('name', 'id')->toArray(),
        ];

        return view('house.form.form-edit', $data);
    }

    /*
     * global list of user houses
     */

    public function manage()
    {
        $user = auth('web')->user();
        $houses = $user->house()->orderBy('name')->get();

        $data = [
            'profileType' => 'house',
            'profiles' => $houses,
        ];

        return view('profiles.list', $data);
    }

    /*
     * edit community of project
     * @param int $id id of the project
     * @param int $status refers to members status (config netframe.members_status)
     */
    public function editCommunity($id, $status = null)
    {
        if (!is_null($id) && (!Acl::getRights('house', $id) || Acl::getRights('house', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $house = House::findOrFail($id);

        //get project members
        $communityStatus = config('netframe.members_status');

        $houseCommunity = $house->users()->wherePivot('status', $status)->orderBy('houses_has_users.roles_id')->get();

        $data = [];
        $data['profile'] = $house;
        $data['house'] = $house;
        $data['profileCommunity'] = $houseCommunity;
        $data['communityType'] = $communityStatus[$status];

        session()->flash('profileDisplay', 'house');
        session()->flash('profileDisplayId', $house->id);

        return view('join.community', $data);
    }

    public function inviteUsers($id)
    {
        if (!is_null($id) && (!Acl::getRights('house', $id) || Acl::getRights('house', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $house = House::findOrFail($id);

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
        $this->searchRepository->inviteProfile = $house;

        $searchParameters = $this
            ->searchRepository
            ->initializeConfig('search_results', $targetsProfiles, false, $byInterests);
        $results = $this->searchRepository->search($searchParameters, $targetsProfiles);

        $data = [];
        $data['profile'] = $house;
        $data['house'] = $house;
        $data['profileCommunity'] = $results[0];
        $data['fromInvite'] = true;

        session()->flash('profileDisplay', 'house');
        session()->flash('profileDisplayId', $house->id);

        return view('join.invite', $data);
    }
}
