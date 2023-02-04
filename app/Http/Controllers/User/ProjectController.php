<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Repository\SearchRepository2;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use \App\Helpers\Lib\Acl;
use App\Repository\NotificationsRepository;
use App\Project;
use App\MessageMail;
use App\Bookmark;
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

class ProjectController extends BaseController
{
    public function __construct(SearchRepository2 $searchRepository)
    {
        $this->middleware('checkAuth');
        parent::__construct();

        $this->searchRepository = $searchRepository;
    }

/* public function __construct()
    {
        $this->beforeFilter('csrf', array('on' => 'post'));
    }*/

    /**
     * Creates and edits a project.
     *
     * @param integer $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id = null)
    {
        if (!is_null($id) && (!Acl::getRights('project', $id) || Acl::getRights('project', $id) > 2 )) {
            return response(view('errors.403'), 403);
        } elseif (is_null($id) && !session('profileAuth.userCanCreate.project')) {
            return response(view('errors.403'), 403);
        }

        $user = auth()->guard('web')->user();

        if (null !== $id) {
            $project = Project::findOrFail($id);
            $project->updateMode = 1;

            /*
            if ($project->users_id != $user->id) {
                return response(view('errors.403'), 403);
            }
            */
        } else {
            $project = new Project();
            $project->confidentiality = 0;
            $project->free_join = 1;
        }

        // Handle form submission
        if (request()->isMethod('POST')) {
            return $this->handleFormSubmission($project);
        }

        session()->flash('profileDisplay', 'project');
        session()->flash('profileDisplayId', $project->id);

        return $this->createProjectFormView($project);
    }

    /*
     * global list of user projects
     */

    public function manage()
    {
        $user = auth('web')->user();
        $projects = $user->project()->orderBy('title')->get();

        $data = [
            'profileType' => 'project',
            'profiles' => $projects,
        ];

        return view('profiles.list', $data);
    }

    /*
     * edit bookmarks of project
     */
    public function editBookmarks($id, $status = null)
    {
        if (!is_null($id) && (!Acl::getRights('project', $id) || Acl::getRights('project', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $project = Project::findOrFail($id);

        $data = [];
        $data['project'] = $project;

        return view('project.form.bookmarks', $data);
    }

    /**
     * Handles the project form submission.
     *
     * @param \Project $project
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function handleFormSubmission(Project $project)
    {
        $user = auth()->guard('web')->user();
        $inputs = request()->all();

        $profileConfidentiality = (request()->has('confidentiality') && request()->get('confidentiality') == 1 )
            ? 0
            : 1;

        $profileFreeJoin = (request()->has('free_join') && request()->get('free_join') == 0 ) ? 0 : 1;

        $project->title = $inputs['title'];
        $project->description = $inputs['description'];

        $project->users_id = auth()->guard('web')->user()->id;
        $project->instances_id = session('instanceId');
        $project->slug = uniqid();
        if (request()->get('latitude') != null && request()->get('longitude') != null) {
            $project->latitude = request()->get('latitude');
            $project->longitude = request()->get('longitude');
            $project->location = \App\Helpers\LocationHelper::getLocation($project->latitude, $project->longitude);
        }
        $project->confidentiality = $profileConfidentiality;
        $project->free_join = $profileFreeJoin;
        $project->owner_id = $inputs['id_foreign'];
        $project->owner_type = "App\\".studly_case($inputs['type_foreign']);

        if (isset($project->updateMode)) {
            unset($project->updateMode);
        }

        $validationsRules = [
            'title' => 'required',
            'id_foreign' => 'required',
            'type_foreign' => 'required',
            'placeSearch' => 'sometimes',
        ];

        if (isset($inputs['auto_member']) && $inputs['auto_member'] == 1) {
            $validationsRules['auto_member_role'] = 'required|not_in:0';
        }

        $validator = validator($inputs, $validationsRules);

        if ($validator->fails()) {
            $project->id_foreign = $inputs['id_foreign'];
            $project->type_foreign = strtolower(str_replace('App\\', '', $inputs['type_foreign']));

            $project->formTags = \App\Helpers\TagsHelper::getFromForm(request()->get('tags'));
            $project->formTagsSelecteds = request()->get('tags');
            return $this->createProjectFormView($project)->withErrors($validator);
        }

        $personnalFolder = request()->get('with_personnal_folder') ?? false;
        $project->with_personnal_folder = $personnalFolder;

        // automember
        if (isset($inputs['auto_member']) && $inputs['auto_member'] == 1) {
            $project->auto_member = $inputs['auto_member_role'];
            $project->auto_subscribe = 1;
            $loadAutomember = true;
        } else {
            $project->auto_member = 0;
            $project->auto_subscribe = 0;
            $loadAutomember = false;
        }

        $editMode = 0;
        if (null !== $project->id) {
            $editMode = 1;
            $oldValProject = Project::find($project->id);
            $oldTags = $project->tags;
        } else {
            $project->active = 1;
        }

        $project->description = htmlentities($project->description);
        $project->save();

        if ($editMode == 0) {
            $project->users()->attach(auth()->guard('web')->user()->id, array('roles_id' => 1,'status' => 1));

            //reload netframe sessions
            session(["allProfiles",Netframe::getProfiles(auth()->guard('web')->user()->id)]);
            session(["acl",Netframe::getAcl(auth()->guard('web')->user()->id)]);

            //add netframe action
            event(new NewAction(
                'new_profile',
                $project->id,
                'project',
                $inputs['id_foreign'],
                $inputs['type_foreign']
            ));

            // subscribe owner to profile
            event(new SubscribeToProfile(auth()->guard('web')->user()->id, $project->id, 'Project'));

            // generate default folders
            MediasFolder::generateDefault($project, session('instanceId'), auth()->guard('web')->user());

            // hard fix for forcing elastic index
            $project->save();

            // proccess profile image on profile creation
            if (request()->get('profileMediaId') != null) {
                $profileMediaId = request()->get('profileMediaId');
                // detach from user and attach to profile
                $media = Media::find($profileMediaId);
                if ($media != null) {
                    auth()->user()->medias()->detach($media->id);
                    $mediaFolder = $project->getDefaultFolder('__profile_medias');
                    $project->medias()->attach($media->id, [
                        'medias_folders_id' => $mediaFolder,
                        'profile_image' => 1,
                    ]);
                    $project->profile_media_id = $media->id;
                    $project->save();
                }
            }

            // proccess cover image on profile creation
            if (request()->get('coverMediaId') != null) {
                $coverMediaId = request()->get('coverMediaId');
                // detach from user and attach to profile
                $media = Media::find($coverMediaId);
                if ($media != null) {
                    auth()->user()->medias()->detach($media->id);
                    $mediaFolder = $project->getDefaultFolder('__profile_medias');
                    $project->medias()->attach($media->id, [
                        'medias_folders_id' => $mediaFolder,
                        'cover_image' => 1,
                    ]);
                    $project->cover_media_id = $media->id;
                    $project->save();
                }
            }
        } else {
            //check old confidentiality
            if ($oldValProject->confidentiality != $profileConfidentiality) {
                event(new ChangeConfidentiality($project, $profileConfidentiality));
            }
        }

        if ($loadAutomember) {
            event(new AutoMember($project, request()->get('auto_member_role')));
        }

        // Save the tags
        \App\Helpers\TagsHelper::attachPostedTags(request()->get('tags'), $project);

        //insert interest
        if ($editMode == 0) {
            $tags = $project->tags;
        } else {
            $project->load('tags');
            $tags = \App\Helpers\TagsHelper::compareTags($oldTags, $project->tags);
        }

        if ($project->tags != null) {
            event(new InterestAction(auth()->guard('web')->user(), $project->tags, 'profile.create'));
        }

        /*
         * create default users folders
         */
        if ($personnalFolder) {
            $project->createPersonnalFolders();
        }

        // manage default channel
        if (request()->has('default_channel')
            && request()->get('default_channel') == 1
            && $project->has_defaultChannel()->first() == null) {
            // create channel
            $project->createDefaultChannel();
        }

        // manage default task project
        if (request()->has('default_tasks')
            && request()->get('default_tasks') == 1
            && $project->has_defaultTasks()->first() == null) {
            // create channel
            $project->createDefaultTasks(request()->get('default_tasks_template'));
        }

        if (null !== $project->id) {
            \App\Helpers\ActionMessageHelper::success(trans('project.project_edit_success'));
        } else {
            \App\Helpers\ActionMessageHelper::success(trans('project.project_create_success'));
        }

        session([
            "allProfiles" => Netframe::getProfiles(auth()->guard('web')->user()->id),
            "acl" => Netframe::getAcl(auth()->guard('web')->user()->id)
        ]);

        //return redirect()->route('project.edit', array('id' => $project->id));
        return redirect()->route('project.edit', ['id' => $project->id]);
    }

    private function createProjectFormView(Project $project, array $extraData = [])
    {
        $notificationsRepository = new NotificationsRepository();

        $roles = new Role();

        $instance = Instance::find(session('instanceId'));

        return view('project.form.project', array_merge([
            'user' => auth()->guard('web')->user(),
            'project' => $project,
            //'offersType' => config('netframe.offersType'),
            'latitude' => request()->get('latitude'),
            'longitude' => request()->get('longitude'),
            'roles' => $roles->getSelectList(),
            'tasks' => $instance->taskTemplates()->pluck('name', 'id')->toArray(),
        ], $extraData));
    }

    /**
     * form treatment for add and edit bookmark
     *
     * @param int $idProject
     * @param int $idBookmark
     */
    public function bookmarkForm($idProject, $idBookmark = null)
    {
        //check rights
        $project = Project::findOrFail($idProject);

        if (!Acl::getRights('project', $idProject) || Acl::getRights('project', $idProject) > 2) {
            throw new AccessDeniedException();
        }

        $bookmark = Bookmark::findOrNew($idBookmark);

        $data['project'] = $project;


        if (request()->isMethod('POST')) {
            $validationsRules = config('validation.project/bookmark');
            $validator = validator(request()->all(), $validationsRules);

            $bookmark->name = request()->get('name');
            $bookmark->url = request()->get('url');
            $bookmark->description = request()->get('description');

            if ($validator->fails()) {
                $data['bookmark'] = $bookmark;
                return response()->json([
                    'view' => view('project.form.bookmarks.form', $data)->withErrors($validator)->render()
                ]);
            } else {
                //record or update bookmark
                $bookmark->users_id = auth()->guard('web')->user()->id;
                $bookmark->instances_id = session('instanceId');
                $bookmark->projects_id = $idProject;
                $bookmark->save();

                $data['bookmark'] = $bookmark;

                //check if update or new bookmark
                if ($idBookmark == null) {
                    $typeContent = "newContent";
                    $target = "#bookmark-list";
                } else {
                    $typeContent = "replaceContent";
                    $target = "#bookmark-".$bookmark->id;
                }

                return response()->json(array(
                    'view' => view('project.form.bookmarks.form', $data)->withErrors($validator)->render(),
                    'viewContent' => view('project.form.bookmarks.item', $data)->render(),
                    $typeContent => true,
                    'targetId' => $target,
                    'closeModal' => true,
                ));
            }


            return response()->json(array(
                'view' => view('project.form.bookmarks.form', $data)->render->withErrors($validator)->render()
            ));
        } else {
            $data['bookmark'] = $bookmark;
            return view('project.form.bookmarks.form', $data);
        }
    }

    /**
     * delete bookmark
     *
     * @param int $idProject
     * @param int $idBookmark
     */
    public function bookmarkDelete($idProject, $idBookmark = null)
    {
        //check rights
        $project = Project::findOrFail($idProject);

        if (!Acl::getRights('project', $idProject) || Acl::getRights('project', $idProject) > 2) {
            throw new AccessDeniedException();
        }

        $bookmark = Bookmark::findOrFail($idBookmark);
        $bookmark->delete();

        return response()->json(array(
            'delete' => true,
            'targetId' => "#bookmark-".$idBookmark
        ));
    }

    /**
     * get message feed of a specific project
     * @param int $idProject
     */
    public function inbox($idProject, $full = 0)
    {
        $project = Project::findOrFail($idProject);
        $groupMail = $project->receivedMessageGroups[0];

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

        $data['idForeignTo'] = $idProject;
        $data['typeForeignTo'] = 'project';
        $data['idForeignFrom'] = auth()->guard('web')->user()->id;
        $data['typeForeignFrom'] = 'user';
        $data['project'] = $project;
        $data['feed'] = $feed;
        $data['feedId'] = $groupMail->id;
        $data['type'] = $groupMail->type;
        $data['overrideType'] = 2;
        $data['types'] = config('messages.types');

        session()->flash('profileDisplay', 'project');
        session()->flash('profileDisplayId', $project->id);

        return view('project.page.inbox', $data);
    }

    /*
     * edit community of project
     * @param int $id id of the project
     * @param int $status refers to members status (config netframe.members_status)
     */
    public function editCommunity($id, $status = null)
    {
        if (!is_null($id) && (!Acl::getRights('project', $id) || Acl::getRights('project', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $project = Project::findOrFail($id);

        //get project members
        $communityStatus = config('netframe.members_status');

        $projectCommunity = $project
            ->users()
            ->wherePivot('status', $status)
            ->orderBy('projects_has_users.roles_id')
            ->get();

        $data = [];
        $data['profile'] = $project;
        $data['project'] = $project;
        $data['profileCommunity'] = $projectCommunity;
        $data['communityType'] = $communityStatus[$status];

        session()->flash('profileDisplay', 'project');
        session()->flash('profileDisplayId', $project->id);

        return view('join.community', $data);
    }

    public function inviteUsers($id)
    {
        if (!is_null($id) && (!Acl::getRights('project', $id) || Acl::getRights('project', $id) > 2 )) {
            return response(view('errors.403'), 403);
        }

        $project = Project::findOrFail($id);

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
        $this->searchRepository->inviteProfile = $project;
        $searchParameters = $this
            ->searchRepository
            ->initializeConfig('search_results', $targetsProfiles, false, $byInterests);
        $results = $this->searchRepository->search($searchParameters, $targetsProfiles);

        $data = [];
        $data['profile'] = $project;
        $data['project'] = $project;
        $data['profileCommunity'] = $results[0];
        $data['fromInvite'] = true;

        session()->flash('profileDisplay', 'project');
        session()->flash('profileDisplayId', $project->id);

        return view('join.invite', $data);
    }
}
