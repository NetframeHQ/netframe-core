<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Netframe\Media\MediaManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use App\NewsFeed;
use App\Profile;
use App\Media;
use App\MediasArchive;
use App\Like;
use App\Notif;
use App\Events\UploadMedia;
use App\MediasFolder;
use App\Drive;
use App\Instance;
use App\News;
use App\Events\NewPost;
use App\Workflow;
use App\Community;
use App\House;
use App\Project;
use App\View;

class MediaController extends BaseController
{
    private $mediaManager;

    public function __construct(MediaManagerInterface $mediaManager)
    {
        $this->middleware('checkAuth', ['except' => 'computeSize']);
        parent::__construct();
        $this->mediaManager = $mediaManager;
    }

    /**
     * recompute size of medias of user and instance after upload and encode video or audio
     * @param int $mediaId id of the last encoded media
     */
    public function computeSize($mediaId)
    {
        $media = Media::findOrFail($mediaId);
        $user = $media->owner;

        //load event
        event(new UploadMedia($user, $media->instance->id));
    }

    public function quotaReach()
    {
        //get instance quota or user quota and send to view
        if (session('reachInstanceQuota')) {
            $overQuota = 'instance';
            $offerQuota = config('billing.offer.'.session('instanceOffer').'.globalQuota');
        }
        if (session('reachUserQuota')) {
            $overQuota = 'user';
            $offerQuota = config('billing.offer.'.session('instanceOffer').'.userQuota');
        }

        //check if user is instance admin
        $instance = auth('web')->user()->instances()->where('id', '=', session('instanceId'))->first();
        $role = $instance->pivot->roles_id;

        $data = [];
        $data['overQuota'] = $overQuota;
        $data['GBquota'] = $offerQuota;
        $data['role'] = $role;

        return view('media.over-quota', $data);
    }

    /*
     *
     * xplorer view switcher between grid and list
     *
     */
    public function switchView()
    {
        $viewMode = request()->get('viewSlug');
        auth()->guard('web')->user()->setParameter('xplorerView', $viewMode);
        session(['userXplorerView' => $viewMode]);

        return response()->json([
            'viewSlug' => $viewMode,
        ]);
    }


    /**
     * display general plorer for user with all his profiles containing documents
     */

    public function general()
    {
        // retreive user display preference
        $userXplorerView = auth()->guard('web')->user()->getParameter('xplorerView');
        if ($userXplorerView == null) {
            $userXplorerView = 'list';
            auth()->guard('web')->user()->setParameter('xplorerView', 'list');
        }
        session(['userXplorerView' => $userXplorerView]);

        $user = auth()->guard('web')->user();

        $channels = $user->channels()->where('active', '=', 1)->whereHas('medias')->get();
        $entities = $user->house()->where('active', '=', 1)->whereHas('medias')->get();
        $communities = $user->community()->where('active', '=', 1)->whereHas('medias')->get();
        $projects = $user->project()->where('active', '=', 1)->whereHas('medias')->get();

        $entitiesPublicFolders = House::where('active', '=', 1)->whereHas('publicMediasFolders')->get();
        $communitiesPublicFolders = Community::where('active', '=', 1)->whereHas('publicMediasFolders')->get();
        $projectsPublicFolders = Project::where('active', '=', 1)->whereHas('publicMediasFolders')->get();

        // compute profiles in folders
        $folders = [];
        foreach ($channels as $channel) {
            $folders['channel'.$channel->id] = [
                'name' => $channel->getNameDisplay(),
                'type' => 'channel',
                'profile' => $channel,
            ];
        }
        foreach ($entities as $entity) {
            $folders['entity'.$entity->id] = [
                'name' => $entity->getNameDisplay(),
                'type' => 'house',
                'profile' => $entity,
            ];
        }
        foreach ($communities as $community) {
            $folders['community'.$community->id] = [
                'name' => $community->getNameDisplay(),
                'type' => 'community',
                'profile' => $community,
            ];
        }
        foreach ($projects as $project) {
            $folders['project'.$project->id] = [
                'name' => $project->getNameDisplay(),
                'type' => 'project',
                'profile' => $project,
            ];
        }
        foreach ($entitiesPublicFolders as $entity) {
            $folders['entity'.$entity->id] = [
                'name' => $entity->getNameDisplay(),
                'type' => 'house',
                'profile' => $entity,
            ];
        }
        foreach ($communitiesPublicFolders as $community) {
            $folders['community'.$community->id] = [
                'name' => $community->getNameDisplay(),
                'type' => 'community',
                'profile' => $community,
            ];
        }
        foreach ($projectsPublicFolders as $project) {
            $folders['project'.$project->id] = [
                'name' => $project->getNameDisplay(),
                'type' => 'project',
                'profile' => $project,
            ];
        }

        sort($folders);

        $personnalFolder = [
            'name' => trans('netframe.myDocuments'),
            'type' => 'user',
            'profile' => $user,
        ];

        array_unshift($folders, $personnalFolder);

        $data = [];
        $data['folders'] = $folders;
        $data['profile'] = $user;

        return view('media.general', $data);
    }

    /**
     * Shows the list of medias of the given user.
     *
     * @return \Illuminate\View\View
     */
    public function showList($profileType, $profileId, $idFolder = null, $driveFolder = null)
    {
        $data = [];

        // retreive user display preference
        $userXplorerView = auth()->guard('web')->user()->getParameter('xplorerView');
        if ($userXplorerView == null) {
            $userXplorerView = 'list';
            auth()->guard('web')->user()->setParameter('xplorerView', 'list');
        }
        session(['userXplorerView' => $userXplorerView]);

        $profileObject = Profile::gather($profileType);
        $profile = $profileObject::find($profileId);
        // test instance
        if (($profileType != 'user' && $profile->instances_id != session('instanceId'))
            || ($profileType == 'user' && !$profile->instances->contains(session('instanceId')))) {
            return response(view('errors.403'), 403);
        }

        // test if folder in public directory
        $publicFolder = false;
        if ($profileType != 'channel'
            && $profileType != 'user'
            && $profile->whereHas('publicMediasFolders')->get() != null
            && $idFolder != null) {
            $folder = MediasFolder::find($idFolder);
            $publicFolder = $folder ? $folder->isInPublic(): false;
        }

        // test if personnal folder of a profile
        if ($idFolder != null && !$this->Acl->getRights(get_class($profile), $profile->id, 2)) {
            $folder = MediasFolder::find($idFolder);
            if ($folder->personnal_folder && $folder->personnal_user_folder != auth('web')->user()->id) {
                return response(view('errors.403'), 403);
            }
        }

        // test rights on profile
        if (!$this->Acl->getRights(get_class($profile), $profile->id, 5)
            && $profile->confidentiality == 0
            && (
                !in_array(class_basename($profile), ['User', 'Channel'])
                && (
                    $profile->whereHas('publicMediasFolders')->get() == null
                    || ($profile->whereHas('publicMediasFolders')->get() != null
                        && $idFolder != null
                        && $publicFolder == false
                    )
                )
            )
        ) {
            return response(view('errors.403'), 403);
        }

        // get user rights on profile and role
        $rights = $this->Acl->getRights($profileType, $profileId, 5);
        if ($rights <= 2) {
            $role = 'user';
        } elseif ($rights <= 5) {
            $role = 'group';
        } else {
            $role = 'other';
        }

        $channelFolders = false;
        $publicFolders = null;

        if ($profileType != 'channel' && $idFolder != 'channels') {
            $mediasFolders = $profile->mediasFolders()->where('medias_folders_id', '=', $idFolder)->get();
            // dd($idFolder);
            if (!$idFolder) {
                if ($this->Acl->getRights(get_class($profile), $profile->id, 2)) {
                    $personalFolders = $profile->allPersonalFolders()->where('personnal_folder', '=', true)->get();
                } else {
                    $personalFolders = $profile->personalFolders()->where('medias_folders_id', '=', $idFolder)->get();
                }
                $mediasFolders = $personalFolders->merge($mediasFolders);
            }
            // create list for public folder when no access rights
            if (!$this->Acl->getRights(get_class($profile), $profile->id, 5) && $profile->confidentiality == 0) {
                if ($publicFolder) {
                    $publicFolders = $mediasFolders;
                } else {
                    $mediasFolders = $publicFolders = $profile
                        ->publicMediasFolders()
                        ->where('medias_folders_id', '=', $idFolder)
                        ->get();
                }
            }
            if ($idFolder == null) {
                foreach ($profile->channels as $channel) {
                    if ($channel->medias()->count() != 0) {
                        $channelFolders = true;
                    }
                }
            }
            $medias = $profile
                ->medias()
                ->wherePivot('medias_folders_id', '=', $idFolder)
                ->orderBy('name')
                ->with(['archives'])
                ->get();
        } elseif ($idFolder != 'channels') {
            $mediasFolders = [];
            $publicFolders = [];
            $medias = $profile->medias()->orderBy('name')->get();
        } else {
            $mediasFolders = [];
            $publicFolders = [];
            $medias = [];
        }

        if ($profileType == 'channel') {
            $joined = $profile->users()->where('users_id', '=', auth()->guard('web')->user()->id)->first();
            if ($joined != null) {
                $joined = $joined->pivot->status;
            }
            $data['joined'] = $joined;
        }

        if (request()->isMethod('POST')) {
            $folder = new MediasFolder();
            $folder->name = request()->get('name');
            $folder->medias_folders_id = (request()->get('idFolder') == 0) ? null : request()->get('idFolder');
            $folder->profile_id = $profile->id;
            $folder->profile_type = get_class($profile);
            $folder->users_id = auth()->guard('web')->user()->id;
            $folder->instances_id = session('instanceId');
            $folder->save();
            $drive = Drive::find(session('chooseDriveFolder'));
            $drive->mediasFolders()->associate($folder);
            $drive->path = (request()->get('id') == '0') ? null : request()->get('id');
            $drive->save();
            session()->forget('chooseDriveFolder');
            return redirect()->route('medias_explorer', ['profileType' => $profileType, 'profileId' => $profileId]);
        }


        if ($idFolder != null && $idFolder != 'channels') {
            $folder = MediasFolder::find($idFolder);

            if ($folder->personnal_folder == 1 && ((class_basename($profile) == 'User'
                && $folder->personnal_user_folder == auth('web')->user()->id) || class_basename($profile) != 'User')) {
                $refFolder = $folder;
            } else {
                $refFolder = $profile->mediasFolders()->where('id', '=', $idFolder)->first();
            }
            if (isset($folder)) {
                $drive = $folder->drive;
                if (isset($drive)) {
                    if (isset($driveFolder)) {
                        $content = $drive->getFiles($driveFolder);
                    } else {
                        $content = $drive->getFiles();
                    }
                    $data['driveFolders'] = isset($content['folders']) ? $content['folders'] : [];
                    $data['driveFiles'] = isset($content['files']) ?$content['files'] : [];
                    $data['parentsTree'] = []; //$drive->getParentsTree($driveFolder);
                }
            }
        } elseif ($idFolder != 'channels') {
            $refFolder = null;
        } else {
            $refFolder = null;
            foreach ($profile->channels as $channel) {
                if ($channel->medias()->count() != 0) {
                    $mediasFolders[] = $channel;
                }
            }
        }

        // test if in public folder
        if (!$this->Acl->getRights(get_class($profile), $profile->id, 5)
            && $profile->confidentiality == 0 && $publicFolder == false) {
            $medias = [];
        }

        // $data = [];
        $data['rights'] = $rights;
        $data['role'] = $role;
        $data['profileType'] = $profileType;
        $data['profileId'] = $profileId;
        $data['profile'] = $profile;
        $data['folders'] = $mediasFolders;
        $data['publicFolders'] = $publicFolders;
        $data['channelFolders'] = $channelFolders;
        $data['medias'] = $medias;
        $data['idFolder'] = $idFolder;
        $data['driveFolder'] = $driveFolder;
        $data['refFolder'] = $refFolder;
        $data['confidentiality'] = (\App\Http\Controllers\BaseController::hasViewProfile($profile))
            ? 1
            : $profile->confidentiality;

        // $data['drives'] = array();
        // foreach ($user->drives as $drive) {
        //     $data['drives'][] = $drive;
        // }

        if (session()->has('chooseDriveFolder')) {
            $data['drive'] = Drive::find(session('chooseDriveFolder'))->getFiles();
        }
        return view('media.list', $data);
    }

    public function delete()
    {
        $mediaType = request()->get('mediaType');
        $mediaId = request()->get('mediaId');
        // \Log::info(request());

        if ($mediaType == 'folder') {
            $folder = MediasFolder::find($mediaId);

            // check rights
            if (!$this->Acl->getRights(get_class($folder->profile), $folder->profile->id, 4)
                || $folder->default_folder == 1) {
                return response(view('errors.403'), 403);
            }

            if ($folder->default_folder) {
                return response(view('errors.403'), 403);
            }

            if (isset($folder->drive)) {
                $folder->drive->delete();
            }

            $folder->delete();

            event(new UploadMedia(auth()->guard('web')->user(), session('instanceId')));

            $data = [];
            $data['delete'] = true;
            $data['targetId'] = '#folder-'.$mediaId;
            return response()->json($data);
        } elseif ($mediaType == 'media') {
            $media = Media::find($mediaId);
            if ($media) {
                if (!$this->Acl->getRights(get_class($media->author()->first()), $media->author()->first()->id, 4)
                    || $media->read_only == 1) {
                    return response(view('errors.403'), 403);
                }

                // if(request()->get('drive')!=null){
                //     $drive = Drive::find(request()->get('drive'))->first();
                //     $$drive->deleteMedia($mediaId);
                // }
                $media->delete();

                event(new UploadMedia(auth()->guard('web')->user(), session('instanceId')));

                $data = [];
                $data['delete'] = true;
                $data['targetId'] = '#file-'.$mediaId;
                return response()->json($data);
            } else {
                return response(view('errors.403'), 403);
            }
        } elseif ($mediaType == 'driveFolder') {
            $idFolder = $mediaId['folder'];
            $mediaId = $mediaId['drive'];
            $folder = MediasFolder::find($idFolder);

            // check rights
            if (!$this->Acl->getRights(get_class($folder->profile), $folder->profile->id, 4)) {
                return response(view('errors.403'), 403);
            }
            $drive = $folder->drive;
            if (isset($drive)) {
                $drive->deleteFolder($mediaId);
            }

            $data = [];
            $data['delete'] = true;
            $data['targetId'] = '#folder-'.$mediaId;
            return response()->json($data);
        } elseif ($mediaType == 'driveFile') {
            $idFolder = $mediaId['folder'];
            $mediaId = $mediaId['drive'];
            $folder = MediasFolder::find($idFolder);

            // check rights
            if (!$this->Acl->getRights(get_class($folder->profile), $folder->profile->id, 4)) {
                return response(view('errors.403'), 403);
            }
            $drive = $folder->drive;
            if (isset($drive)) {
                $drive->deleteMedia($mediaId);
            }

            $data = [];
            $data['delete'] = true;
            $data['targetId'] = '#file-'.$mediaId;
            return response()->json($data);
        } else {
            return response(view('errors.403'), 403);
        }
    }

    public function socialToolbar($mediaId, $take = 'resume')
    {
        $limit = 5;
        $media = Media::find($mediaId);
        $nbComments = $media->comments()->count();
        $nbComHide = $nbComments - 5;

        $author = $media->author()->first();

        if ($media->instances_id != session('instanceId')
            || ($author->confidentiality == 0 && !BaseController::hasViewProfile($author))) {
            return response(view('errors.403'), 403);
        }

        $data = array();
        $data['media'] = $media;
        $data['post'] = $media;

        if ($take == 'resume') {
            $skip = ($nbComments > $limit) ? $nbComments-$limit : 0;
            $comments = $media->lastComments;
            $linkMore = ($skip > 0) ? true : false;
            $data['comments'] = $comments;
            $data['linkMoreComments'] = $linkMore;
            $data['viewType'] = 'full';
        } elseif ($take == 'all') {
            $comments = $media->comments()->take($limit)->get();
            $data['comments'] = $comments;
            $data['linkMoreComments'] = false;
            $data['viewType'] = 'partial';
        }

        if (auth()->guard('web')->check()) {
            $data['liked'] = Like::isLiked(['liked_id' => $media->id, 'liked_type' => 'Netframe\Media\Model\Media']);
        } else {
            $data['liked'] = false;
        }

        $data['media'] = $media;
        $data['author'] = $media->owner;
        $data['profile'] = $author;
        $data['nbComments'] = $nbComments;
        $data['removeMoreComments'] = ( $nbComHide >= 1) ? false : true;

        $view = view('media.social', $data)->render();

        $dataJson = [];
        if ($nbComHide >= 1) {
            $dataJson['removeMoreComments'] = '';
        } else {
            $dataJson['removeMoreComments'] = 'd-none';
        }
        $dataJson['nbComHide'] = $nbComHide;
        $dataJson['nbComments'] = $nbComments;
        $dataJson['view'] = $view;
        return response()->json($dataJson);
    }

    /**
     *
     * @param int $mediaId
     * @param string $take (resume, all)
     */
    public function mediaComments($mediaId, $take = 'resume')
    {
        $limit = 5;
        $media = Media::find($mediaId);

        if ($media->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        $data = array();
        $data['media'] = $media;
        $data['post'] = $media;

        $nbComments = $media->comments()->count();
        if ($take == 'resume') {
            $skip = ($nbComments > $limit) ? $nbComments-$limit : 0;
            $comments = $media->comments()->take($limit)->skip($skip)->get();
            $linkMore = ($skip > 0) ? true : false;
            $data['comments'] = $comments;
            $data['linkMoreComments'] = $linkMore;
            $data['viewType'] = 'full';
            return view('page.media-comments', $data);
        } elseif ($take == 'all') {
            $comments = $media->comments()->take($nbComments-$limit)->get();
            $data['comments'] = $comments;
            $data['linkMoreComments'] = false;
            $data['viewType'] = 'partial';
            return response()->json(array(
                'view' => view('page.media-comments', $data)->render(),
            ));
        }
    }

    public function show($fileName, $mediaId)
    {
        $dataMedia = [];
        $media = Media::find($mediaId);

        if ($media->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        $author = $media->author()->first();
        //var_dump($author->getUrl());

        $dataMedia['liked'] = Like::isLiked(['liked_id' => $media->id, 'liked_type' => 'App\Media']);
        $dataMedia['media'] = $media;
        $dataMedia['fullSize'] = true;

        $modalView = view('media.modal', $dataMedia)->render();
        //session()->flash('autoFireModal', $modalView);
        session()->flash('autoFireMediaModalView', 'media.modal');
        session()->flash('autoFireMediaModalViewData', $dataMedia);
        unset($dataMedia);

        //redirect to author page with
        if ($author->getType() != 'user') {
            return app('User\PageController')->{$author->getType()}($author->id, $author->slug);
        } else {
            return app('User\ProfileController')->wall($author->slug, $author->slug);
        }
    }

    public function editFolder($profileType, $profileId, $idFolder = null, $driveFolder = null)
    {
        $profileModel = Profile::gather($profileType);
        $profile = $profileModel::find($profileId);

        /*
         @TODO
         * add folder rights control
         */

        if ($profile == null || !$this->Acl->getRights(get_class($profile), $profile->id, 3)) {
            return response(view('errors.403'), 403);
        }
        $isDrive = false;

        if (request()->isMethod('POST')) {
            $validator = validator(request()->all(), config('validation.xplorer.addFolder'));
            if ($validator->fails()) {
                $data = [];
                $data['profileType'] = $profileType;
                $data['profileId'] = $profileId;
                $data['errors'] = $validator->messages();
                $data['inputOld'] = request()->all();

                return response()->json([
                    'view' => view('media.xplorer.edit-folder', $data)->render(),
                ]);
            }

            if (request()->has('id')) {
                $folder = MediasFolder::find(request()->get('id'));

                // check rights
                $profile = $folder->profile;
                if ($profile == null || !$this->Acl->getRights(get_class($profile), $profile->id, 4)) {
                    return response(view('errors.403'), 403);
                }
            } else {
                $folder = new MediasFolder();
            }

            if (request()->get('id') != request()->get('idFolder')
                && (!isset($folder->drive) || !isset($driveFolder))) {
            // create or update folder

                $folder->name = request()->get('name');
                $folder->medias_folders_id = (request()->get('idFolder') == 0) ? null : request()->get('idFolder');
                $folder->profile_id = $profile->id;
                $folder->profile_type = get_class($profile);
                $folder->users_id = auth()->guard('web')->user()->id;
                $folder->instances_id = session('instanceId');
                $folder->public_folder = (request()->has('publicFolder')) ? request()->get('publicFolder') : 0;
                $folder->save();
            } else {
                $isDrive = true;
                $drive = $folder->drive;

                if (request()->get('parent')!=null) {
                    $folder = $drive->addFolder(request()->get('name'), request()->get('parent'));
                } else {
                    $folder = $drive->addFolder(request()->get('name'));
                }
            }
            // dd($folder);
            $rights = $this->Acl->getRights($profileType, $profileId, 4);

            $dataJson = [];
            $dataJson['profileType'] = $profileType;
            $dataJson['profileId'] = $profileId;
            $dataJson['folder'] = $folder;
            $dataJson['driveFolder'] = $driveFolder;
            $dataJson['rights'] = $rights;
            $dataJson['idFolder'] = $idFolder;
            $dataJson['profile'] = $profile;
            if (isset($idFolder)) {
                $dataJson['idFolder'] = $idFolder;
            } else {
                $dataJson['idFolder'] = request()->get('id');
            }

            $data = [];
            $data['view'] = '';
            $data['newContent'] = true;
            $data['idFolder'] = $dataJson['idFolder'];
            if ($isDrive) {
                $data['viewContent'] = [
                    'type' => 'insert',
                    'view' => view('media.xplorer.drive.folder', $dataJson)->render()
                ];
            } else {
                $data['viewContent'] = [
                    'type' => 'insert',
                    'view' => view('media.xplorer.folder', $dataJson)->render()
                ];
            }
            $data['folderName'] = $folder->name;
            $data['closeModal'] = true;

            $profile->touch();

            return response()->json($data);
        } else {
            $data = [];

            $data['folder'] = null;

            if ($idFolder != null) {
                $folder = MediasFolder::find($idFolder);
                \Log::info($idFolder);
                //check rights
                $profile = $folder->profile;
                if ($profile == null || !$this->Acl->getRights(get_class($profile), $profile->id, 4)) {
                    return response(view('errors.403'), 403);
                } else {
                    if (isset($driveFolder)) {
                        $drive = $folder->drive;
                        $folder = $drive->getFolder($driveFolder);
                    }
                    $data['folder'] = $folder;
                }
            }

            $data['driveFolder'] = isset($driveFolder) ? $driveFolder : null;
            $data['idFolder'] = $idFolder;

            $data['profileType'] = $profileType;
            $data['profileId'] = $profileId;

            if (request()->has('parent')) {
                $data['parentFolder'] = request()->get('parent');
            }

            return view('media.xplorer.edit-folder', $data);
        }
    }

    public function importFolder($profileType, $profileId, $idFolder = null)
    {
        $data = array();
        $drive = new \App\GoogleDrive();
        $onedrive = new \App\OneDrive();
        $dropbox = new \App\DropBox();
        $box = new \App\Box();
        $data = [
            'google_drive_url' => $drive->getAuthUrl(),
            'onedrive_url' => $onedrive->getAuthUrl(),
            'dropbox_url' => $dropbox->getAuthUrl(),
            'box_url' => $box->getAuthUrl(),
        ];

        // store origin page in session
        session(['landingDrivePage' => url()->previous()]);

        return view('media.xplorer.import-folder', $data);
    }

    public function driveAuthorize($drive)
    {
        $user = auth()->guard('web')->user();

        // $folder = new MediasFolder();
        // $folder->name = uniqid();
        // $folder->medias_folders_id = null;
        // $folder->users_id = $user->id;
        // $folder->instances_id = session('instanceId');
        // $folder->save();
        $client = new Drive();
        if ($drive=='google') {
            $client->type = Drive::GOOGLE;
        } elseif ($drive=='onedrive') {
            $client->type = Drive::ONEDRIVE;
        } elseif ($drive=='dropbox') {
            $client->type = Drive::DROPBOX;
        } elseif ($drive=='box') {
            $client->type = Drive::BOX;
        }
        $client->code = request()->get('code');
        $client->auth(false);
        // $client->mediasFolder()->associate($folder);
        $client->medias_folders_users_id = $user->id;
        $client->medias_folders_instances_id = session('instanceId');
        $client->save();
        session(['chooseDriveFolder' => $client->id]);
        //return redirect()->to('medias/show/user/1');
        return response(
            '<script>window.opener.location.replace(\'' . session('landingDrivePage')
            . '\', \'_self\');window.self.close()</script>'
        );
    }

    public function drive($driveId, $folder = null)
    {
        $drive = Drive::find($driveId);
        $data = $drive->getFiles($folder);
        $data['driveId'] = $driveId;
        return view('media.drive', $data);
    }

    public function addFile($profileType, $profileId, $idFolder = 0, $driveFolder = null)
    {
        $profileModel = Profile::gather($profileType);
        $profile = $profileModel::find($profileId);

        $forceWorkflow = (request()->has('forceWorkflow') && request()->get('forceWorkflow') == 1) ? true : false;
        $attachToExistingTask = (request()->has('taskId') && !empty(request()->get('taskId'))) ?
            request()->get('taskId') :
            0;

        /*
         @TODO
         * add folder rights control
         */
        $folder = ($idFolder != 0) ? MediasFolder::find($idFolder) : null;

        if ($profile == null ||
            (!$this->Acl->getRights(get_class($profile), $profile->id, 4)
                && ($folder != null
                    && $folder->personnal_folder == 1
                    && $folder->personnal_user_folder != auth('web')->user()->id
                )
            )
        ) {
            return response(view('errors.403'), 403);
        }

        if (request()->isMethod('POST')) {
            // iterate each file get new name and tags
            $fileIds = request()->get('file-id');
            $mediaViews = [];

            $validationRules = [];
            $tags = [];

            if (empty($fileIds)) {
                return response()->json(['closeModal' => true]);
            }

            $ref = explode('/', request()->get('httpReferer'));
            $driveFolder = $ref[count($ref)-1];
            $folder = MediasFolder::find($idFolder);

            $rights = $this->Acl->getRights($profileType, $profileId, 4);

            if (isset($folder->drive) || $driveFolder != $idFolder && 1 == 0) {
                // add file in connected drive
                $drive = $folder->drive;
                $file = new Media();
                foreach ($fileIds as $fileId) {
                    $media = Media::find($fileId);
                    if (isset($media)) {
                        if (request()->get('parent')!=null) {
                            $file = $drive->addFile($media->name, $media->file_path, request()->get('parent'));
                        } else {
                            $file = $drive->addFile($media->name, $media->file_path);
                        }
                        $media->delete();
                    }
                }
                $fileName = request()->get('filename-'.$fileId);
                $dataView = [];
                $dataView['media'] = $file;
                $dataView['profileType'] = $profileType;
                $dataView['profileId'] = $profileId;
                $dataView['rights'] = $rights;
                $dataView['idFolder'] = $idFolder;
                $mediaViews[$fileName] = view('media.xplorer.drive.file', $dataView)->render();
            } else {
                foreach ($fileIds as $fileId) {
                    $fileRules = ["filename-".$fileId => "required"];
                    $validationRules = array_merge($validationRules, $fileRules);

                    $tags[$fileId] = [
                        'formTags' => \App\Helpers\TagsHelper::getFromForm(request()->get('tags-'.$fileId)),
                        'formTagsSelecteds' => request()->get('tags-'.$fileId),
                    ];
                }

                $validator = validator(request()->all(), $validationRules);
                if ($validator->fails()) {
                    $data = [];
                    $data['profileType'] = $profileType;
                    $data['profileId'] = $profileId;
                    $data['idFolder'] = $idFolder;
                    $data['confidentiality'] = $profile->confidentiality;
                    $data['errors'] = $validator->messages();
                    $data['inputOld'] = request()->all();
                    $data['tags'] = $tags;
                    return response()->json([
                        'view' => view(
                            'media.xplorer.add-file',
                            $data
                        )->render(),
                        'error' => true,
                    ]);
                }

                // check if files are under workflow
                $filesWorkflow = (request()->has('makeWorkflow') && request()->get('makeWorkflow') == 1) ? 1 : 0;

                $workflowsIds = [];

                foreach ($fileIds as $fileId) {
                    $replaceExisting = request()->get('replace-'.$fileId);
                    $originalFileId = request()->get('originalId-'.$fileId);

                    $media = Media::find($fileId);
                    $fileName = request()->get('filename-'.$fileId);
                    $fileTags = request()->get('tags-'.$fileId);
                    $metaAuthor = request()->get('author-'.$fileId);
                    $description = request()->get('description-'.$fileId);

                    if ($replaceExisting === 'true') {
                        // move old file in archives and link to new file
                        $originalMedia = Media::find($originalFileId)->toArray();
                        $originalMedia['medias_id'] = $originalMedia['id'];
                        unset($originalMedia['id']);
                        unset($originalMedia['keep_files']);
                        unset($originalMedia['url']);
                        unset($originalMedia['thumb']);
                        MediasArchive::insert($originalMedia);

                        // update original media object
                        $mediaToDelete = $media;  // place new uploaded media in tmp var
                        $media = Media::find($originalFileId);

                        $media->file_name = $mediaToDelete->file_name;
                        $media->file_path = $mediaToDelete->file_path;
                        $media->feed_path = $mediaToDelete->feed_path;
                        $media->thumb_path = $mediaToDelete->thumb_path;
                        $media->feed_width = $mediaToDelete->feed_width;
                        $media->feed_height = $mediaToDelete->feed_height;
                        $media->file_size = $mediaToDelete->file_size;
                        $media->linked = $mediaToDelete->linked;
                        $media->like = $mediaToDelete->like;
                        $media->share = $mediaToDelete->share;

                        $v = new View();
                        $v->post_id = $media->id;
                        $v->post_type = get_class($media);
                        $v->users_id = auth()->guard('web')->user()->id;
                        $v->type = View::TYPE_REPLACE;
                        $v->save();

                        // remove new record of media without deleting attache file on drive
                        $mediaToDelete->keep_files = 1;
                        $mediaToDelete->save();
                        $mediaToDelete->delete();
                    } elseif ($originalFileId != 'null') {
                        // rename file name with number
                        $fileName = $this->fileNumbering($media, $idFolder, $profile);
                        $v = new View();
                        $v->post_id = $media->id;
                        $v->post_type = get_class($media);
                        $v->users_id = auth()->guard('web')->user()->id;
                        $v->type = View::TYPE_REPLACE;
                        $v->save();
                    }

                    $media->name = $fileName;
                    $media->meta_author = $metaAuthor;
                    $media->description = $description;
                    $media->under_workflow = ($filesWorkflow) ? 1 : 0;
                    $media->save();

                    if ($filesWorkflow) {
                        // add workflow processing
                        $workflow = new Workflow();
                        $wfFields = $workflow->mergePostedFields();
                        $wfDatas = [
                            'mediasIds' => [$fileId],
                        ];
                        $workflow->makeNew('validate_file', $wfFields, $wfDatas);

                        $workflowsIds[] = [
                            'wfId' => $workflow->id,
                            'fileId' => $media->id
                        ];
                    } else {
                        $dataView = [];
                        $dataView['media'] = $profile->medias()->where('id', '=', $media->id)->first();
                        $dataView['profileType'] = $profileType;
                        $dataView['profileId'] = $profileId;
                        $dataView['rights'] = $rights;
                        $mediaViews[$fileName] = [
                            'type' => ($replaceExisting === 'true') ? 'replace' : 'insert',
                            'view' => view(
                                'media.xplorer.file',
                                $dataView
                            )->render(),
                        ];
                    }

                    \App\Helpers\TagsHelper::attachPostedTags($fileTags, $media);
                }

                // if not under workflow and not file replacement and not in personnal folder, publish post
                if ($replaceExisting === 'false'
                    && !$filesWorkflow
                    && (($media->folder(true) != null
                        && $media->folder(true)->personnal_folder == 0)
                        || $media->folder(true) == null)
                    ) {
                    // prepare medias ids for post
                    $post = new News();
                    $post->users_id = auth()->guard('web')->user()->id;
                    $post->instances_id = session('instanceId');
                    $post->author_id = $profile->id;
                    $post->author_type = get_class($profile);
                    $post->content = '';
                    $post->confidentiality = $profile->confidentiality;
                    $post->save();

                    foreach ($fileIds as $mediaId) {
                        $post->medias()->attach($mediaId);
                    }
                    // link media state
                    Media::whereIn('id', $fileIds)->update(['linked' => 1]);

                    // newsfeed

                    $post->author_id = $profile->id;
                    $post->author_type = get_class($profile);
                    $post->true_author_id = $profile->id;
                    $post->true_author_type = get_class($profile);
                    event(new NewPost("news", $post, null, $fileIds, [], true));
                }
            }


            $data = [];
            $data['view'] = '';
            $data['closeModal'] = true;

            if (request()->has('fromTasks')) {
                $data['withWorkflow'] = $filesWorkflow;
                $data['workflows'] = $workflowsIds;
                $data['files'] = $fileIds;
                $data['attachExistingTask'] = $attachToExistingTask;
            } else {
                $data['newContent'] = true;
                $data['viewContent'] = $mediaViews;
            }

            $profile->touch();

            return response()->json($data);
        } else {
            $data = [];
            $data['profileType'] = $profileType;
            $data['profileId'] = $profileId;
            $data['idFolder'] = $idFolder;
            $data['driveFolder'] = $driveFolder;
            $data['confidentiality'] = ($profile instanceof \App\User) ? 0 : $profile->confidentiality;
            $data['forceWorkflow'] = $forceWorkflow;
            $data['attachToExistingTask'] = $attachToExistingTask;

            return view('media.xplorer.add-file', $data);
        }
    }

    private function fileNumbering($media, $idFolder, $profile)
    {
        // get occurency in folder
        // and check slug unicity
        if ($idFolder != 0) {
            $folder = MediasFolder::find($idFolder);
            $slugCount = count(
                $folder
                    ->medias()
                    ->whereRaw("name REGEXP '^{$media->getBaseName()} \\\(([0-9]*)\\\)\\\.{$media->getExtension()}?$'")
                    ->get()
            );
        } else {
            $slugCount = count(
                $profile
                    ->medias()
                    ->where('medias_folders_id', '=', null)
                    ->whereRaw("name REGEXP '^{$media->getBaseName()} \\\(([0-9]*)\\\)\\\.{$media->getExtension()}?$'")
                    ->get()
            );
        }

        $slugCount += 1;
        $mediaSlug = $media->getBaseName().' ('.$slugCount.')'.'.'.$media->getExtension();
        return $mediaSlug;
    }

    public function editFile($idFile = null)
    {
        if (request()->isMethod('POST')) {
            $media = Media::findOrFail(request()->get('idFile'));

            $mediaProfile = $media->author()->first();
            if ($mediaProfile == null
                || !$this->Acl->getRights(get_class($mediaProfile), $mediaProfile->id, 4)
                || $media->read_only == 1) {
                return response(view('errors.403'), 403);
            }

            $fileTags = request()->get('tags');

            $media->name = request()->get('filename');
            $media->meta_author = request()->get('author');
            $media->description = request()->get('description');
            $media->save();

            \App\Helpers\TagsHelper::attachPostedTags($fileTags, $media);

            $rights = $this->Acl->getRights(get_class($mediaProfile), $mediaProfile->id, 4);

            $dataJson = [];
            $dataJson['media'] = $mediaProfile->medias()->where('id', '=', $media->id)->first();
            $dataJson['rights'] = $rights;
            $dataJson['profileType'] = $mediaProfile->getType();
            $dataJson['profileId'] = $mediaProfile->id;

            $data = [];
            $data['view'] = '';
            $data['replaceContent'] = true;
            $data['targetId'] = '#file-'.$media->id;
            $data['viewContent'] = view(
                'media.xplorer.file',
                $dataJson
            )->render();
            $data['closeModal'] = true;

            $mediaProfile->touch();

            return response()->json($data);
        } else {
            $media = Media::findOrFail($idFile);

            // test if media is locked
            if ($media->read_only == 1) {
                return view('media.xplorer.modal-locked');
            }

            $mediaProfile = $media->author()->first();
            if ($mediaProfile == null || !$this->Acl->getRights(get_class($mediaProfile), $mediaProfile->id, 4)) {
                return response(view('errors.403'), 403);
            }

            $rights = $this->Acl->getRights(get_class($mediaProfile), $mediaProfile->id, 4);

            $data = [];
            $data['media'] = $media;
            $data['rights'] = $rights;

            return view('media.xplorer.edit-file', $data);
        }
    }

    public function loadFolders()
    {
        $profileModel = Profile::gather(request()->get('profileType'));
        $profile = $profileModel::find(request()->get('profileId'));
        $elementType = $profileModel::find(request()->get('movedElementType'));
        $elementId = $profileModel::find(request()->get('movedElementId'));

        if ($profile == null || !$this->Acl->getRights(get_class($profile), $profile->id, 4)) {
            return response(view('errors.403'), 403);
        }

        $rootFolders = $profile->mediasFolders()->whereNull('medias_folders_id')->get();
        $folders = [];
        $folders[0] = trans('xplorer.defaultFolders.__root_folder');
        $moveFolderId = ($elementType == 'folder') ? $elementId : null;
        foreach ($rootFolders as $folder) {
            $level = 0;
            if ($elementType == 'folder' && $elementId == $folder->id) {
                continue;
            }

            $folders[$folder->id] = $folder->getNameDisplay();
            $folders= array_replace_recursive($folders, $folder->formatFolderTree($level, $moveFolderId));
        }

        $foldersJson = [];
        foreach ($folders as $id => $name) {
            $foldersJson[] = [
                'value' => $id,
                'text' => $name
            ];
        }

        $data = [];
        $data['options'] = $foldersJson;
        return response()->json($data);
    }

    public function moveElement($profileType = null, $profileId = null, $elementType = null, $elementId = null)
    {
        if (request()->isMethod('POST')) {
            // target profile
            $profileType = request()->get('type_foreign');
            $profileId = request()->get('id_foreign');

            // moved element
            $elementType = request()->get('movedElementType');
            $elementId = request()->get('movedElementId');
        }

        if ($elementType == 'media') {
            $movedElement = Media::find($elementId);
            $movedProfile = $movedElement->author()->first();

            if ($movedProfile == null || !$this->Acl->getRights(get_class($movedProfile), $movedProfile->id, 4)) {
                return response(view('errors.403'), 403);
            }
        } elseif ($elementType == 'folder') {
            $movedElement = MediasFolder::find($elementId);
            $movedProfile = $movedElement->profile;
            if ($movedProfile == null
                || !$this->Acl->getRights(get_class($movedProfile), $movedProfile->id, 4)
                || $movedElement->default_folder == 1) {
                return response(view('errors.403'), 403);
            }
        } else {
            return response(view('errors.403'), 403);
        }

        if (request()->has('target')) {
            // check rights on target profile
            $changeProfile = false;
            if ($movedProfile->getType() != $profileType || $movedProfile->id != $profileId) {
                $currentProfileModel = Profile::gather($profileType);
                $currentProfile = $currentProfileModel::find($profileId);
                if ($currentProfile == null
                    || !$this->Acl->getRights(get_class($currentProfile), $currentProfile->id, 4)) {
                    return response(view('errors.403'), 403);
                }
                $changeProfile = true;
            }

            if ($movedElement->default_folder == 1) {
                return response(view('errors.403'), 403);
            }

            // move element
            if ($elementType == 'media') {
                if ($changeProfile) {
                    // detach from old profile and attach to new profile
                    $movedProfile->medias()->detach($movedElement->id);
                    $currentProfile->medias()->attach($movedElement->id, [
                        'medias_folders_id' => (request()->get('target') == 0) ? null : request()->get('target'),
                    ]);
                } else {
                    $media = $movedProfile->medias()->where('id', '=', $movedElement->id)->first();
                    $media->pivot->medias_folders_id = (request()->get('target') == 0)
                        ? null
                        : request()->get('target');
                    $media->pivot->save();
                }

                $targetDom = '#file-'.$movedElement->id;
            } elseif ($elementType == 'folder') {
                // check if new folder is not the same and not child of itself
                $childsAndMe = $movedElement->getChildrenTree(true, true, false, true);
                if (!in_array(request()->get('target'), $childsAndMe)) {
                    $movedElement->medias_folders_id = (request()->get('target') == 0)
                        ? null
                        : request()->get('target');

                    if ($changeProfile) {
                        // folder media treatment
                        $medias = $movedElement->medias;

                        foreach ($medias as $media) {
                            $movedProfile->medias()->detach($media->id);

                            $currentProfile->medias()->attach($media->id, [
                                'medias_folders_id' => $movedElement->id,
                            ]);
                        }

                        // change owner profile
                        $movedElement->profile_type = get_class($currentProfile);
                        $movedElement->profile_id = $currentProfile->id;

                        // change profile for all childs
                        $this->changeChildsOwner($movedElement, $movedProfile, $currentProfile);
                    }
                    $movedElement->save();
                } else {
                    return response(view('errors.403'), 403);
                }
                $targetDom = '#folder-'.$movedElement->id;
            }

            // return json
            $data = [];
            $data['success'] = true;
            $data['movedElement'] = $targetDom;
            $data['closeModal'] = true;
            return response()->json($data);
        } else {
            // test if media is locked
            if ($movedElement->read_only == 1) {
                return view('media.xplorer.modal-locked');
            }

            // return list
            $currentProfileModel = Profile::gather($profileType);
            $currentProfile = $currentProfileModel::find($profileId);
            if ($currentProfile == null || !$this->Acl->getRights(get_class($currentProfile), $currentProfile->id, 4)) {
                return response(view('errors.403'), 403);
            }

            $rootFolders = $currentProfile->mediasFolders()->whereNull('medias_folders_id')->get();
            $folders = [];
            $folders[0] = trans('xplorer.defaultFolders.__root_folder');
            $moveFolderId = ($elementType == 'folder') ? $elementId : null;
            foreach ($rootFolders as $folder) {
                $level = 0;
                if ($elementType == 'folder' && $elementId == $folder->id) {
                    continue;
                }
                if ($folder->default_folder == 0) {
                    $folders[$folder->id] = $folder->name;
                } else {
                    $folders[$folder->id] = trans('xplorer.defaultFolders.'.$folder->name);
                }
                $folders= array_replace_recursive($folders, $folder->formatFolderTree($level, $moveFolderId));
            }

            $data = [];
            $data['folders'] = $folders;
            $data['movedElementType'] = $elementType;
            $data['movedElementId'] = $elementId;
            $data['profileType'] = $profileType;
            $data['profileId'] = $profileId;
            $data['typeAction'] = 'move';

            return view('media.xplorer.move-element', $data);
        }
    }

    public function copyElement($profileType = null, $profileId = null, $elementType = null, $elementId = null)
    {
        if (request()->isMethod('POST')) {
            // target profile
            $profileType = request()->get('type_foreign');
            $profileId = request()->get('id_foreign');

            // moved element
            $elementType = request()->get('movedElementType');
            $elementId = request()->get('movedElementId');
        }

        if ($elementType == 'media') {
            $copiedElement = Media::find($elementId);
            $copiedProfile = $copiedElement->author()->first();

            if ($copiedProfile == null || !$this->Acl->getRights(get_class($copiedProfile), $copiedProfile->id, 4)) {
                return response(view('errors.403'), 403);
            }
        } elseif ($elementType == 'folder') {
            $copiedElement = MediasFolder::find($elementId);
            $copiedProfile = $copiedElement->profile;
            if ($copiedProfile == null
                || !$this->Acl->getRights(get_class($copiedProfile), $copiedProfile->id, 4)
                || $copiedElement->default_folder == 1) {
                return response(view('errors.403'), 403);
            }
        } else {
            return response(view('errors.403'), 403);
        }

        if (request()->has('target')) {
            // check rights on target profile
            $changeProfile = false;
            if ($copiedProfile->getType() != $profileType || $copiedProfile->id != $profileId) {
                $currentProfileModel = Profile::gather($profileType);
                $currentProfile = $currentProfileModel::find($profileId);
                if ($currentProfile == null
                    || !$this->Acl->getRights(get_class($currentProfile), $currentProfile->id, 4)) {
                    return response(view('errors.403'), 403);
                }
                $changeProfile = true;
            }

            $data = [];
            // copy element
            if ($elementType == 'media') {
                // duplicate media
                $newMedia = $copiedElement->duplicateFiles();
                $newFolder = (request()->get('target') == 0) ? null : request()->get('target');

                if ($changeProfile) {
                    // attach media to new profile
                    $currentProfile->medias()->attach($newMedia->id, [
                        'medias_folders_id' => $newFolder,
                    ]);
                } else {
                    $copiedProfile->medias()->attach($newMedia->id, [
                        'medias_folders_id' => $newFolder,
                    ]);

                    //.test if copy in same folder
                    if ($copiedProfile->pivot->medias_folders_id == $newFolder) {
                        // return media view
                        $dataView = [];
                        $dataView['media'] = $copiedProfile->medias()->where('id', '=', $newMedia->id)->first();
                        $dataView['profileType'] = $profileType;
                        $dataView['profileId'] = $profileId;
                        $dataView['rights'] = $this->Acl->getRights($profileType, $profileId, 4);
                        $mediaView = view('media.xplorer.file', $dataView)->render();
                        $data['viewContent'] = [
                            'elementType' => 'file',
                            'name' => $newMedia->name,
                            'view' => $mediaView
                        ];
                    }
                }
            } elseif ($elementType == 'folder') {
                $newParentFolder = (request()->get('target') == 0) ? null : request()->get('target');

                if ($changeProfile) {
                    $targetProfile = $currentProfile;
                    $returnView = false;
                } else {
                    $targetProfile = $copiedProfile;
                    $returnView = true;
                }

                $newFolder = $copiedElement->duplicate($targetProfile, $newParentFolder);

                if ($returnView) {
                    if ($copiedElement->medias_folders_id == $newParentFolder) {
                        $rights = $this->Acl->getRights($profileType, $profileId, 4);
                        $dataJson = [];
                        $dataJson['profileType'] = $profileType;
                        $dataJson['profileId'] = $profileId;
                        $dataJson['folder'] = $newFolder;
                        $dataJson['rights'] = $rights;
                        $data['viewContent'] = [
                            'elementType' => 'folder',
                            'name' => $newFolder->name,
                            'view' => view('media.xplorer.folder', $dataJson)->render(),
                        ];
                    }
                }
            }

            event(new UploadMedia(auth()->guard('web')->user(), session('instanceId')));

            // return json

            $data['success'] = true;
            $data['autoFireModal'] = view(
                'media.xplorer.success',
                ['successMessage' => trans('xplorer.copyElement.success' . ucfirst($elementType))]
            )->render();
            $data['returnMessage'] = true;
            return response()->json($data);
        } else {
            // return list
            $currentProfileModel = Profile::gather($profileType);
            $currentProfile = $currentProfileModel::find($profileId);
            if ($currentProfile == null || !$this->Acl->getRights(get_class($currentProfile), $currentProfile->id, 4)) {
                return response(view('errors.403'), 403);
            }

            $rootFolders = $currentProfile->mediasFolders()->whereNull('medias_folders_id')->get();
            $folders = [];
            $folders[0] = trans('xplorer.defaultFolders.__root_folder');
            $moveFolderId = ($elementType == 'folder') ? $elementId : null;
            foreach ($rootFolders as $folder) {
                $level = 0;
                if ($elementType == 'folder' && $elementId == $folder->id) {
                    continue;
                }
                if ($folder->default_folder == 0) {
                    $folders[$folder->id] = $folder->name;
                } else {
                    $folders[$folder->id] = trans('xplorer.defaultFolders.'.$folder->name);
                }
                $folders= array_replace_recursive($folders, $folder->formatFolderTree($level, $moveFolderId));
            }

            $data = [];
            $data['folders'] = $folders;
            $data['movedElementType'] = $elementType;
            $data['movedElementId'] = $elementId;
            $data['profileType'] = $profileType;
            $data['profileId'] = $profileId;
            $data['typeAction'] = 'copy';

            return view('media.xplorer.move-element', $data);
        }
    }

    private function changeChildsOwner($folder, $oldProfile, $newProfile)
    {
        $childs = $folder->getChildrenTree(false, false, false, true);
        foreach ($childs as $child) {
            // child media treatment
            $medias = $child->medias;
            foreach ($medias as $media) {
                $oldProfile->medias()->detach($media->id);
                $newProfile->medias()->attach($media->id, [
                    'medias_folders_id' => $child->id,
                ]);
            }

            $child->profile_type = $folder->profile_type;
            $child->profile_id = $folder->profile_id;
            $child->save();
        }
    }

    public function dragElement()
    {
        // get moved element and check rights
        if (request()->get('movedElementType') == 'media') {
            $movedElement = Media::find(request()->get('movedElementId'));
            $movedProfile = $movedElement->author()->first();
            if ($movedProfile == null
                || !$this->Acl->getRights(get_class($movedProfile), $movedProfile->id, 4)
                || $movedElement->read_only == 1) {
                return response(view('errors.403'), 403);
            }
        } elseif (request()->get('movedElementType') == 'folder') {
            $movedElement = MediasFolder::find(request()->get('movedElementId'));
            $movedProfile = $movedElement->profile;
            if ($movedProfile == null
                || !$this->Acl->getRights(get_class($movedProfile), $movedProfile->id, 4)
                || $movedElement->default_folder == 1) {
                return response(view('errors.403'), 403);
            }
        } else {
            return response(view('errors.403'), 403);
        }

        // get target folder and check rights
        $targetFolder = MediasFolder::find(request()->get('targetId'));
        $profile = $targetFolder->profile;
        if ($profile == null || !$this->Acl->getRights(get_class($profile), $profile->id, 4)) {
            return response(view('errors.403'), 403);
        }

        // check if both elements are on same profile
        if ($movedProfile->getType() == $profile->getType() && $movedProfile->id == $profile->id) {
            // change moved element pivot
            if (class_basename($movedElement) == 'MediasFolder') {
                $movedElement->medias_folders_id = $targetFolder->id;
                $movedElement->save();
            } else {
                $movedProfile->pivot->medias_folders_id = $targetFolder->id;
                $movedProfile->pivot->save();
            }

            return response()->json(['success' => true]);
        } else {
            return response(view('errors.403'), 403);
        }
    }

    public function starMedia()
    {
        $media = Media::find(request()->get('mediaId'));
        $mediaProfile = $media->author()->first();
        if ($mediaProfile == null || !$this->Acl->getRights(get_class($mediaProfile), $mediaProfile->id, 4)) {
            return response(view('errors.403'), 403);
        }

        $media = $mediaProfile->medias()->where('id', '=', $media->id)->first();
        $media->pivot->favorite = ($media->pivot->favorite == 1) ? 0 : 1;
        $media->pivot->save();

        $data = [];
        $data['success'] = true;
        $data['state'] = ($media->pivot->favorite == 1) ? 'on' : 'off';
        return response()->json($data);
    }

    public function modalMedia()
    {
        $nbCommentsDisplay = request()->get('nbCommentsDisplay');
        $mediaId = request()->get('mediaId');
        $mediaSelected = Media::find($mediaId);
        if (request()->has('newsFeedId')) {
            $newsFeedId = request()->get('newsFeedId');
            $newsFeeD = NewsFeed::find($newsFeedId);
            if ($newsFeeD->post_type == "App\\Share") {
                if (class_basename($newsFeeD->post->post) != 'Media') {
                    $allMedias = $newsFeeD->post->post->medias()->where('active', '=', 1)->get();
                } else {
                    $allMedias = [];
                    $allMedias[] = $newsFeeD->post->post;
                }
            } else {
                $allMedias = $newsFeeD->post->medias()->where('active', '=', 1)->get();
            }
            $author = $newsFeeD->post->author;
            $media = null;

            if ($newsFeeD->instances_id != session('instanceId')
                || ($author->confidentiality == 0 && !BaseController::hasViewProfile($author))) {
                return response(view('errors.403'), 403);
            }
        } elseif (request()->has('directoryMode') && request()->get('directoryMode') != 'false') {
            // get profile and folder var
            $profileType = request()->get('profileType');
            $profileId = request()->get('profileId');
            $folderId = request()->get('folderId');

            if ($folderId != '') {
                // get all medias of folder
                $folder = MediasFolder::find($folderId);
                $allMedias = $folder->medias;
            } else {
                // get all media of profile root directory
                $profileObject = Profile::gather($profileType);
                $profile = $profileObject::find($profileId);
                if ($profileType != 'channel') {
                    $allMedias = $profile->medias()->where('medias_folders_id', '=', null)->orderBy('name')->get();
                } else {
                    $allMedias = $profile->medias()->orderBy('name')->get();
                }
            }

            // get all medias of directory
            $media = Media::find($mediaId);
            if ($media) {
                $author = $media->author()->first();

                // check if in public folder
                $publicFolder = false;
                if ($author->pivot->medias_folders_id != null) {
                    $folder = MediasFolder::find($author->pivot->medias_folders_id);
                    $publicFolder = $folder->isInPublic();
                }

                if (($media->instances_id != session('instanceId')
                    || ($author->confidentiality == 0 && !BaseController::hasViewProfile($author))) && !$publicFolder) {
                    return response(view('errors.403'), 403);
                }
            } else {
                return response(view('errors.404'), 404);
            }
        } else {
            $allMedias = [];
            $media = Media::find($mediaId);
            $author = $media->author()->first();
            $allMedias[] = $media;

            if ($media->instances_id != session('instanceId')
                || ($author->confidentiality == 0 && !BaseController::hasViewProfile($author))) {
                return response(view('errors.403'), 403);
            }
        }

        if ($mediaSelected->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        if (!empty($allMedias)) {
            $media = null;
            $arr = [];
            $currentPos = 0;
            foreach ($allMedias as $oneMedia) {
                if ($mediaId == $oneMedia->id) {
                    $media = $oneMedia;
                }
                if ($media == null) {
                    $currentPos++;
                }
            }
        } else {
            $currentPos = 1;
        }

        $data = [];
        $data['nbCommentsDisplay'] = $nbCommentsDisplay;
        $data['mediaId'] = $media->id;
        $data['allMedias'] = $allMedias;
        $data['media'] = $media;
        $data['media']['author'] = $media->author()->first();
        $data['currentPos'] = $currentPos;

        //            $view = view('media.social', $media)->render();
        //            $data['view'] = $view;

        return response()->json($data);
    }

    public function showMoreComments()
    {
        //        $nbCommentsDisplay = request()->get('nbCommentsDisplay');
        $dateComment = request()->get('dateComment');
        $mediaId = request()->get('mediaId');
        $media = Media::find($mediaId);
        //        $fifthComment = $media->lastComments->first()->created_at;

        $nbComments = count($media->comments()->get());
        $nbComHide = count($media->comments()->where('created_at', '<', $dateComment)->get()) - 10;

        $skip = $nbComHide;

        $skip = ($skip < 0) ? 0 : $skip;
        $comments = $media
            ->comments()
            ->where('created_at', '<', $dateComment)
            ->orderBy('created_at')
            ->skip($skip)
            ->take(10)
            ->get();

        $data = [];
        $data['comments'] = $comments;
        $data['post'] = $media;

        $view = view('page.more-comments', $data)->render();

        $dataJson = [];
        $dataJson['mediaId'] = $mediaId;
        $dataJson['dateComment'] = $dateComment;
        $dataJson['view'] = $view;
        $dataJson['skip'] = $skip;
        $dataJson['nbComHide'] = $nbComHide;
        $dataJson['nbComments'] = $nbComments;
        $dataJson['comments'] = $comments;
        $dataJson['removeMoreComments'] = ($nbComHide <= 0 ? 'd-none' : '');

        return response()->json($dataJson);
    }

    public function testFileFolder()
    {
        $profileInfos = json_decode(request()->get('profile'));

        $profileModel = Profile::gather($profileInfos->type);
        $profile = $profileModel::find($profileInfos->id);

        $folder = (request()->get('idFolder') != 0) ? MediasFolder::find(request()->get('idFolder')) : null;
        if ($profile == null ||
            (!$this->Acl->getRights(get_class($profile), $profile->id, 4) &&
                ($folder != null
                    && $folder->personnal_folder == 1
                    && $folder->personnal_user_folder != auth('web')->user()->id )
                )
            ) {
            return response(view('errors.403'), 403);
        }

        $filename = request()->get('filename');
        $folderId = request()->get('idFolder');



        if ($folderId == 0) {
            $media = $profile->medias()->wherePivot('medias_folders_id', null)->where('name', '=', $filename)->first();
        } else {
            $folder = $profile->mediasFolders()->where('id', '=', $folderId)->first();
            if ($folder != null) {
                $media = $folder->medias()->where('name', '=', $filename)->first();
            } else {
                $media = null;
            }
        }
        if ($media == null) {
            return response()->json([
                'response' => false,
            ]);
        } else {
            return response()->json([
                'response' => true,
                'originalId' => $media->id
            ]);
        }
    }

    public function viewArchives($mediaId)
    {
        $media = Media::find($mediaId);

        $mediaAuthor = $media->author->first();
        if (isset($mediaAuthor->pivot->profile_image) && $mediaAuthor->pivot->profile_image != 1 &&
            (!$this->Acl->getRights(get_class($mediaAuthor), $mediaAuthor->id, 5) && $mediaAuthor->confidentiality == 0)
            ) {
            return response(view('errors.403'), 403);
        }

        // check access rights

        $archives = $media->archives;

        $data = [];
        $data['media'] = $media;
        $data['archives'] = $archives;

        return view('media.xplorer.modal-archives', $data);
    }

    public function downloadArchive($id)
    {
        $media = MediasArchive::where('id', '=', $id)
            ->where('instances_id', '=', session('instanceId'))
            ->firstOrFail();
        $thumb = (bool) request()->get('thumb', false);
        $feed = (bool) request()->get('feed', false);

        $rootMedia = $media->media;

        if ($rootMedia->active == 1) {
            //check media privacy and profile rights
            $mediaAuthor = $rootMedia->author->first();
            if (isset($mediaAuthor->pivot->profile_image) && $mediaAuthor->pivot->profile_image != 1 &&
                (!$this->Acl->getRights(get_class($mediaAuthor), $mediaAuthor->id, 5)
                    && $mediaAuthor->confidentiality == 0)
                ) {
                return response(view('errors.403'), 403);
            }

            if ($thumb && $media->thumb_path) {
                $path = $media->thumb_path;
                if ($media->platform != 'local') {
                    $media->mime_type = 'image/jpeg';
                    $media->file_name = $media->file_name.'.jpg';
                }
            } elseif ($feed && $media->feed_path) {
                $path = $media->feed_path;
            } else {
                $path = $media->file_path;
            }

            $response = new Response();
            $response->headers->set('X-Sendfile', $path);
            $response->headers->set('Content-Type', $media->mime_type);
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Cache-Control', 'post-check=0, pre-check=0', false);
            $response->headers->set('Pragma', 'no-cache');

            if ($media->type == Media::TYPE_DOCUMENT && !$feed) {
                $response->headers->set('Content-Disposition', sprintf('inline; filename="%s"', $media->name));
            } else {
                $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $media->name));
            }

            return $response;
        } else {
            return null;
        }
    }

    public function modifyLock()
    {
        $mediaId = request()->get('mediaId');
        $newState = request()->get('newState');

        $media = Media::findOrFail($mediaId);

        // check if user is owner or admin on profile media
        $mediaProfile = $media->author()->first();
        if ($mediaProfile == null || !$this->Acl->getRights(get_class($mediaProfile), $mediaProfile->id, 1)) {
            return response(view('errors.403'), 403);
        }

        $media->read_only = ($newState == 'lock') ? 1 : 0;
        $media->save();

        $rights = $this->Acl->getRights(get_class($mediaProfile), $mediaProfile->id, 4);

        $dataJson = [];
        $dataJson['media'] = $mediaProfile->medias()->where('id', '=', $media->id)->first();
        $dataJson['rights'] = $rights;
        $dataJson['profileType'] = $mediaProfile->getType();
        $dataJson['profileId'] = $mediaProfile->id;

        $data = [];
        $data['view'] = '';
        $data['replaceContent'] = true;
        $data['targetId'] = '#file-'.$media->id;
        $data['viewContent'] = view('media.xplorer.file', $dataJson)->render();
        $data['closeModal'] = true;

        // $mediaProfile->touch();

        return response()->json($data);
    }

    public function details($id)
    {
        // @TODO : implement security check
        $media = Media::find($id);
        $views = View::where('post_type', get_class($media))
            ->where('post_id', $media->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response(view('media.details', ['media' => $media, 'views' => $views]));
    }

    public function actionsMenu()
    {
        $mediaId = request()->get('mediaId');
        $media = Media::where('id', '=', $mediaId)
            ->where('instances_id', '=', session('instanceId'))
            ->firstOrFail();

        if ($media->active == 1) {
            //check media privacy and profile rights
            $mediaAuthor = $media->author->first();

            // check if in public folder
            $publicFolder = false;
            if (class_basename($mediaAuthor) != 'User' && $mediaAuthor->pivot->medias_folders_id != null) {
                $folder = MediasFolder::find($mediaAuthor->pivot->medias_folders_id);
                $publicFolder = $folder->isInPublic();
            }

            if ((isset($mediaAuthor->pivot->profile_image) && $mediaAuthor->pivot->profile_image != 1 &&
                (!$this->Acl->getRights(get_class($mediaAuthor), $mediaAuthor->id, 5)
                    && $mediaAuthor->confidentiality == 0)
                && !$publicFolder) ||
                (!$this->Acl->getRights(get_class($mediaAuthor), $mediaAuthor->id, 2)
                    && $media->folder(true) != null
                    && $media->folder(true)->personnal_folder == 1
                    && $media->folder(true)->personnal_user_folder != auth('web')->user()->id)
                ) {
                    return response(view('errors.403'), 403);
            }
        }

        return response()->json([
            'actionMenu' => view('media.partials.menu-actions', [
                'media' => $media,
                'rights' => \App\Http\Controllers\BaseController::hasRightsProfile($media->folder(), 5),
                'profileType' => $media->mainProfile()->getType(),
                'profileId' => $media->mainProfile()->id
            ])->render(),
        ]);
    }
}
