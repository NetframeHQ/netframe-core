<?php

namespace Netframe\Media;

use App\Http\Controllers\BaseController;
use Netframe\Media\MediaManagerInterface;
use Netframe\Media\Import\UnsupportedUrlException;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Netframe\Media\Upload\UnsupportedFileTypeException;
use Symfony\Component\HttpFoundation\Response;
use App\Media;
use App\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Mail;
use App\Profile;
use App\Events\UploadMedia;
use App\Helpers\AclHelper;
use App\Mail\MediaError;
use App\MediasFolder;
use App\MediasArchive;

/**
 * Controller for upload related things.
 */
class MediaController extends BaseController
{
    private $mediaManager;

    public function __construct(MediaManagerInterface $mediaManager)
    {
        $this->middleware('checkAuth');
        $this->mediaManager = $mediaManager;
        $this->Acl = new AclHelper();
    }

    /**
     * Imports a media.
     */
    public function import()
    {
        $url = request()->get('url');
        $profile = request()->has('profile') ? json_decode(request()->get('profile'), true) : null;
        $profileMedia = request()->get('profileMedia');
        $postMediaModal = request()->get('postMediaModal');
        $response = new JsonResponse();
        $user = auth()->user();
        $media = new Media();
        $confidentiality = (int) request()->get('confidentiality', 1);
        $media->instances_id = session('instanceId');
        $media->confidentiality = $confidentiality;
        $media->users_id = $user->id;
        $media->linked = ($profileMedia == 0 && $postMediaModal == 0) ? 0 : 1;

        $validator = validator()->make(
            array('url' => $url),
            array('url' => 'required|url')
        );
        if ($validator->fails()) {
            $response->setData(array('errors' => $validator->errors()->all()));
            $response->setStatusCode(400);

            return $response;
        }

        try {
            $this->mediaManager->import($url, $media);

            $responseRow['id'] = $media->id;
            $responseRow['file_name'] = $media->file_name;
            $responseRow['mediaPlatform'] = $media->platform;
            $responseRow['thumb_path'] = $media->thumb_path;

            return new JsonResponse(array('import' => $responseRow, 'conf' => $confidentiality));
        } catch (UnsupportedUrlException $e) {
            $response->setData(array('errors' => array($e->getMessage())));
            $response->setStatusCode(400);

            return $response;
        }

        $response->setStatusCode(204);

        return $response;
    }

    /**
     * Upload medias
     */
    public function upload()
    {
        if (session('reachInstanceQuota') || session('reachUserQuota')) {
            die();
        }

        $updateMode = false;
        if (request()->has('specificField') && request()->get('specificField') != null) {
            $fieldFile = request()->get('specificField');
            $files[0] = request()->file($fieldFile);
        } else {
            $fieldFile = 'files';
            $files = request()->file($fieldFile);
            if (request()->has('mediaId') && request()->get('mediaId') != '') {
                $updateMode = true;
            }
        }

        $confidentiality = (int) request()->get('confidentiality', 1);
        $profile = request()->has('profile') ? json_decode(request()->get('profile'), true) : null;
        $profileMedia = (request()->has('profileMedia')) ? request()->get('profileMedia') : 0;
        $profileCover = (request()->has('profileCover')) ? request()->get('profileCover') : 0;
        $fromXplorer = request()->get('fromXplorer');
        $idFolder = request()->get('idFolder');
        $postMedia = request()->get('postMedia');
        $responseRows = array();
        $user = auth()->guard('web')->user();

        $folder = (request()->get('idFolder') != 0) ? MediasFolder::find(request()->get('idFolder')) : null;
        if ($profile == null ||
            (!$this->Acl->getRights(ucfirst($profile['type']), $profile['id'], 4) &&
                ($folder != null && $folder->personnal_folder == 1
                    && $folder->personnal_user_folder != auth('web')->user()->id)
                )
            ) {
            return response(view('errors.403'), 403);
        }

        /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $integratedMiedas = [];

        foreach ($files as $file) {
            if ($updateMode) {
                // make historic of preview media
                $originalMedia = Media::find(request()->get('mediaId'))->toArray();
                $originalMedia['medias_id'] = $originalMedia['id'];
                unset($originalMedia['id']);
                unset($originalMedia['keep_files']);
                unset($originalMedia['url']);
                unset($originalMedia['thumb']);
                MediasArchive::insert($originalMedia);

                $media = Media::find(request()->get('mediaId'));
                $viewType = View::TYPE_REPLACE;
            } else {
                $media = new Media();
                $viewType = View::TYPE_CREATE;
            }

            $media->confidentiality = $confidentiality;
            $media->users_id = $user->id;
            $media->instances_id = session('instanceId');
            $media->linked = ($postMedia == 1) ? 0 : 1;
            $media->file_name = '';
            $media->file_path = '';
            $media->feed_path = '';
            $media->thumb_path = '';

            $responseRow = array(
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            );

            $integratedMieda = [];
            $integratedMieda['error'] = false;

            try {
                $this->mediaManager->upload($file, $media);

                $v = new View();
                $v->post_id = $media->id;
                $v->post_type = get_class($media);
                $v->users_id = $user->id;
                $v->type = $viewType;
                $v->save();
            } catch (UnsupportedFileTypeException $e) {
                $responseRow['error'] = \Lang::get('media::messages.error_unsupported_file_type');
                //$responseRow['clientMime'] = $file->getClientMimeType();
                //$responseRow['mime'] = $file->getMimeType();
                $integratedMieda['error'] = true;
                \Log::error(
                    \Lang::get('media::messages.error_unsupported_file_type') . ' : ' . $file->getClientOriginalName()
                );

                $dataError = [];
                $dataError['clientMime'] = $file->getClientMimeType();
                $dataError['mime'] = $file->getMimeType();
                $dataError['file'] = $file->getClientOriginalName();

                //send mail to admin
                $configMail = config('admin.emailAdmin');
                Mail::to($configMail['email'], $configMail['name'])->send(new MediaError($dataError));
            }

            if (!$updateMode
                && ($profileMedia == 1 || $profileCover == 1 || $fromXplorer == 1)
                && null !== $profile && !$integratedMieda['error']) {
                $this->attachMediaToProfile($media, $profile, $profileMedia, $profileCover, $idFolder);
            }

            $integratedMieda['media'] = $media;
            $responseRow['id'] = $media->id;
            $responseRow['file_path'] = $media->file_path;
            $responseRow['profileMedia'] = $profileMedia;
            $responseRow['profileCover'] = $profileCover;
            $responseRow['mediaId'] = $media->id;
            $responseRow['type'] = $media->type;
            $responseRow['mediaPlatform'] = $media->platform;
            $responseRow['mediaUrl'] = url()->route('media_download', array('id' => $media->id,'thumb'=>1));
            $responseRow['feed_width'] = $media->feed_width;
            $responseRow['feed_height'] = $media->feed_height;
            $responseRow['mediaFullUrl'] = url()->route('media_download', array('id' => $media->id));
            if ($fromXplorer == 1) {
                $responseRow['fromXplorer'] = true;
                $responseRow['replaceFile'] = request()->get('replace');
                $responseRow['originalId'] = request()->get('originalId');
            }
            $responseRows[] = $responseRow;
            $integratedMiedas[] = $integratedMieda;

            // load event to compute total medias size
            event(new UploadMedia($user, session('instanceId')));
        }

        return new JsonResponse([
            'files' => $responseRows,
            'redirect' => request()->get('httpReferer'),
            'fromXplorer' => $fromXplorer,
        ]);
    }

    /**
     * Download a media.
     *
     * @param integer $id The media id
     *
     * @return Response
     *
     * @todo access check
     */
    public function download($id)
    {
        $media = Media::where('id', '=', $id)
            ->where('instances_id', '=', session('instanceId'))
            ->firstOrFail();
        $thumb = (bool) request()->get('thumb', false);
        $feed = (bool) request()->get('feed', false);

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

            if ($thumb && $media->thumb_path) {
                $path = $media->thumb_path;
            } elseif ($feed && $media->feed_path) {
                $path = $media->feed_path;
            } else {
                $path = $media->file_path;
            }

            $disposition = $media->type == Media::TYPE_DOCUMENT && !$feed ? 'inline' : 'attachment';
            $response = new Response();
            $response->headers->set('Content-Type', $media->mime_type);
            $response->headers->set('Content-Disposition', sprintf('%s; filename="%s"', $disposition, $media->name));

            // tells webserver to output the file content
            $response->headers->set(config('media.proxy_file_sending_header'), $path);

            if (!$feed && !$thumb) {
                if ($feed) {
                    $media->view();
                } else {
                    $media->view(View::TYPE_DOWNLOAD);
                }
            }

            if (!request()->has('returnJson')) {
                return $response;
            } else {
                $responseRow['id'] = $media->id;
                $responseRow['file_path'] = $media->file_path;
                $responseRow['profileMedia'] = 0;
                $responseRow['profileCover'] = 0;
                $responseRow['mediaId'] = $media->id;
                $responseRow['type'] = $media->type;
                $responseRow['mediaPlatform'] = $media->platform;
                $responseRow['mediaUrl'] = url()->route('media_download', array('id' => $media->id,'thumb'=>1));
                $responseRow['name'] = $media->name;
                $responseRow['size'] = $media->file_size;
                $responseRow['feed_width'] = $media->feed_width;
                $responseRow['feed_height'] = $media->feed_height;
                $responseRow['mediaFullUrl'] = url()->route('media_download', array('id' => $media->id));
                $responseRow['meta_author'] = $media->meta_author;
                $responseRow['description'] = $media->description;

                return new JsonResponse([
                    'files' => [$responseRow],
                    'redirect' => request()->get('httpReferer'),
                    'fromXplorer' => 0,
                ]);
            }
        } else {
            return null;
        }
    }

    /**
     *
     */
    public function getPreview($media, $size = 'thumb')
    {
        if ($media->platform === 'local') {
            switch ($media->type === \Netframe\Media\Model\Media::TYPE_VIDEO) {
                case \Netframe\Media\Model\Media::TYPE_VIDEO:
                    $path = $media->{$size.'_path'};
                    break;

                case \Netframe\Media\Model\Media::TYPE_AUDIO:
                    $path = \asset('asset/js/holder.js/'.$height.'x'.$width.'/text:Audio');
                    break;

                case \Netframe\Media\Model\Media::TYPE_IMAGE:
                    $path = $media->{$size.'_path'};
                    break;

                case \Netframe\Media\Model\Media::TYPE_ARCHIVE:
                    $path = \asset('asset/js/holder.js/'.$height.'x'.$width.'/text:Archive');
                    break;

                case \Netframe\Media\Model\Media::TYPE_DOCUMENT:
                    $path = \asset('asset/js/holder.js/'.$height.'x'.$width.'/text:Document');
                    break;

                case \Netframe\Media\Model\Media::TYPE_APPLICATION:
                    $path = \asset('asset/js/holder.js/'.$height.'x'.$width.'/text:Document');
                    break;

                case \Netframe\Media\Model\Media::TYPE_SCRIPT:
                    $path = \asset('asset/js/holder.js/'.$height.'x'.$width.'/text:Document');
                    break;

                case \Netframe\Media\Model\Media::TYPE_OTHER:
                    $path = \asset('asset/js/holder.js/'.$height.'x'.$width.'/text:Document');
                    break;

                case \Netframe\Media\Model\Media::TYPE_FONT:
                    $path = \asset('asset/js/holder.js/'.$height.'x'.$width.'/text:Document');
                    break;
            }
        } elseif ($media->platform === $importer->getPlatform()) {
        }

        return $path;
    }

    /**
     * Edits a media.
     */
    public function edit($id)
    {
        if (null === $media = Media::find($id)) {
            throw new NotFoundHttpException();
        }

        $user = auth()->guard('web')->user();

        if ($media->users_id !== $user->id) {
            throw new AccessDeniedException();
        }

        if (request()->isMethod('POST')) {
            $inputs = request()->only(array('name'));

            $validator = validator()->make(
                $inputs,
                array('name' => 'required')
            );

            if ($validator->fails()) {
                return $this->redirecToEditRoute($id)->withErrors($validator->errors());
            }

            $media->name = $inputs['name'];
            $media->save();

            return $this->redirecToEditRoute($id)
                ->with('success', Lang::get('media::messages.edit_success'));
        }

        return view('media::edit.edit', array(
            'media' => $media,
            'importers' => $this->mediaManager->getImporters(),
            'player_attributes' => (object) array(
                'width' => config('media::edit_media_page.video_player_width'),
                'height' => config('media::edit_media_page.video_player_height'),
            )
        ));
    }

    /**
     * Deletes a media.
     */
    public function delete($id)
    {
        if (null === $media = Media::find($id)) {
            throw new NotFoundHttpException();
        }

        $user = auth()->guard('web')->user();

        if ($media->users_id !== $user->id) {
            throw new AccessDeniedException();
        }


        //$media->delete($id);
        $media->active = 0;
        $media->save();

        $media->delete();

        return $this->redirectToAttachmentRoute()
            ->with('success', Lang::get('media::messages.delete_success'));
    }

    /**
     * Shows the paginated list of medias the user has access to.
     */
    public function jsonList()
    {
        $user = auth()->guard('web')->user();
        $mediaQuery = Media::getQuery()
            ->where('users_id', '=', $user->id)
            ->where('instancesId', '=', session('instanceId'));

        // Order by specific media ids first
        $orderIds = explode(',', request()->get('ids', ''));

        foreach ($orderIds as $orderId) {
            $mediaQuery->orderByRaw('id = ' . (int) $orderId . ' desc');
        }

        $mediaQuery->orderBy('created_at', 'desc');

        $paginatedMedias = $mediaQuery
            ->paginate(
                16,
                array('id', 'name', 'type', 'platform', 'file_path', 'thumb_path', 'file_name', 'mime_type','platform')
            )
            ->toArray();

        foreach ($paginatedMedias['data'] as &$paginatedMedia) {
            // Remove the file path for local files
            if ($paginatedMedia->platform === 'local') {
                $paginatedMedia->file_path = null;
            }

            $paginatedMedia->player_html = View::make('media::player.player', array(
                'media' => $paginatedMedia,
                'importers' => $this->mediaManager->getImporters(),
                'attributes' => (object) array(
                    'height' => '',
                    'width' => '100%'
                )
            ))->render();
        }

        return new JsonResponse($paginatedMedias);
    }

    /**
     * Attaches the media to the given profile after it has been saved.
     *
     * @param Media $media   The saved media
     * @param array $profile The profile informations in the form:
     *
     * $profile['type'] = Profile::TYPE_USER
     * $profile['id'] = 3
     */

    private function attachMediaToProfile(
        Media $media,
        array $profile,
        $profileMedia = 0,
        $profileCover = 0,
        $folderId = null
    ) {
        $folderId = ($folderId == 0) ? null : $folderId;

        if (isset($profile['type'])) {
            $mediaFolder = null;
            if ($profileMedia == 1 || $profileCover == 1) {
                if ($profile['id'] != 0) {
                    $profileObjectType = Profile::gather($profile['type']);
                    $profileObject = $profileObjectType::find($profile['id']);
                    $attachToProfile = true;
                } else {
                    // if upload media during profile creation proccess
                    $profileObject = Auth()->user();
                    $profile['id'] = $profileObject->id;
                    $profile['type'] = 'user';
                    $attachToProfile = false;
                }
                $mediaFolder = $profileObject->getDefaultFolder('__profile_medias');
            } else {
                $mediaFolder = $folderId;
            }

            switch ($profile['type']) {
                case Profile::TYPE_COMMUNITY:
                    \DB::table('community_has_medias')->insert(array(
                        'community_id' => $profile['id'],
                        'medias_id' => $media->id,
                        'profile_image' => $profileMedia,
                        'cover_image' => $profileCover,
                        'medias_folders_id' => $mediaFolder
                    ));
                    if ($profileMedia == 1 && $attachToProfile) {
                        \DB::table('community')->where(array(
                            'id' => $profile['id'],
                        ))->update(array(
                            'profile_media_id' => $media->id
                        ));
                    }
                    if ($profileCover == 1 && $attachToProfile) {
                        \DB::table('community')->where(array(
                            'id' => $profile['id'],
                        ))->update(array(
                            'cover_media_id' => $media->id
                        ));
                    }
                    break;

                case Profile::TYPE_HOUSE:
                    \DB::table('houses_has_medias')->insert(array(
                        'houses_id' => $profile['id'],
                        'medias_id' => $media->id,
                        'profile_image' => $profileMedia,
                        'cover_image' => $profileCover,
                        'medias_folders_id' => $mediaFolder
                    ));
                    if ($profileMedia == 1 && $attachToProfile) {
                        \DB::table('houses')->where(array(
                            'id' => $profile['id'],
                        ))->update(array(
                            'profile_media_id' => $media->id
                        ));
                    }
                    if ($profileCover == 1 && $attachToProfile) {
                        \DB::table('houses')->where(array(
                            'id' => $profile['id'],
                        ))->update(array(
                            'cover_media_id' => $media->id
                        ));
                    }
                    break;

                case Profile::TYPE_PROJECT:
                    \DB::table('projects_has_medias')->insert(array(
                        'projects_id' => $profile['id'],
                        'medias_id' => $media->id,
                        'profile_image' => $profileMedia,
                        'cover_image' => $profileCover,
                        'medias_folders_id' => $mediaFolder
                    ));
                    if ($profileMedia == 1 && $attachToProfile) {
                        \DB::table('projects')->where(array(
                            'id' => $profile['id'],
                        ))->update(array(
                            'profile_media_id' => $media->id
                        ));
                    }
                    if ($profileCover == 1 && $attachToProfile) {
                        \DB::table('projects')->where(array(
                            'id' => $profile['id'],
                        ))->update(array(
                            'cover_media_id' => $media->id
                        ));
                    }
                    break;

                case Profile::TYPE_USER:
                    \DB::table('users_has_medias')->insert(array(
                        'users_id' => $profile['id'],
                        'medias_id' => $media->id,
                        'profile_image' => $profileMedia,
                        'cover_image' => $profileCover,
                        'medias_folders_id' => $mediaFolder
                    ));
                    if ($profileMedia == 1 && $attachToProfile) {
                        \DB::table('users')->where(array(
                            'id' => $profile['id'],
                        ))->update(array(
                            'profile_media_id' => $media->id
                        ));
                        session(['user.profile_media_id' => $media->id]);
                    }
                    if ($profileCover == 1 && $attachToProfile) {
                        \DB::table('users')->where(array(
                            'id' => $profile['id'],
                        ))->update(array(
                            'cover_media_id' => $media->id
                        ));
                    }
                    break;

                // Default is attached to the user
                default:
                    \DB::table('users_has_medias')->insert(array(
                        'users_id' => $profile['id'],
                        'medias_id' => $media->id,
                        'profile_image' => 0,
                        'cover_image' => 0,
                        'medias_folders_id' => $mediaFolder
                    ));
                    if ($profileMedia == 1 && $attachToProfile) {
                        \DB::table('user')->where(array(
                            'id' => $profile['id'],
                        ))->update(array(
                            'profile_media_id' => $media->id
                        ));
                    }
                    if ($profileCover == 1 && $attachToProfile) {
                        \DB::table('users')->where(array(
                            'id' => $profile['id'],
                        ))->update(array(
                            'cover_media_id' => $media->id
                        ));
                    }
                    break;
            }
        } else {
            // Attach the media to the user if no profile is chosen
            \DB::table('users_has_medias')->insert(array(
                'users_id' => auth()->guard('web')->user()->id,
                'medias_id' => $media->id,
                'profile_image' => $profileMedia,
                'cover_image' => $profileMedia,
            ));
            if ($profileMedia == 1) {
                \DB::table('user')->where(array(
                    'id' => auth()->guard('web')->user()->id,
                ))->update(array(
                    'profile_media_id' => $media->id
                ));
            }
            if ($profileCover == 1) {
                \DB::table('users')->where(array(
                    'id' => $profile['id'],
                ))->update(array(
                    'cover_media_id' => $media->id
                ));
            }
        }
    }

    private function redirectToAttachmentRoute($errors = array())
    {
        if (is_array($errors)) {
            $errors = new MessageBag($errors);
        } elseif (is_string($errors)) {
            $errors = new MessageBag(array($errors));
        }

        return redirect()->route('media_attachment')->withErrors($errors);
    }

    private function redirecToEditRoute($id)
    {
        return redirect()->route('media_edit', array('id' => $id));
    }
}
