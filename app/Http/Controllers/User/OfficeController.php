<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Response;
use App\Media;
use App\MediasFolder;
use App\User;
use App\View;
use Illuminate\Support\Facades\Http;
use App\Instance;
use App\Profile;

class OfficeController extends BaseController
{

    public function home()
    {
        $instance = Instance::find(session('instanceId'));
        if (!$instance->hasApplication('office')) {
            return view('errors.403');
        }

        $user = auth()->guard('web')->user();
        $medias = $user->medias()
            ->where('type', Media::TYPE_DOCUMENT)
            ->get()
            ->all();
        foreach ($medias as $key => $media) {
            if (! in_array("." . pathinfo($media->file_path, PATHINFO_EXTENSION), config('office.DOC_SERV_EDITED'))) {
                unset($medias[$key]);
            }
        }
        // dd($medias);
        return view('office.home', [
            'user' => $user,
            'medias' => $medias
        ]);
    }

    public function create($documentType, $profileType, $profileId, $mediasFolder = null)
    {
        $instance = Instance::find(session('instanceId'));
        $profileType = Profile::gather($profileType);
        $profile = $profileType::find($profileId);
        $mediasFolder = $mediasFolder ? MediasFolder::find($mediasFolder) : null;

        // check user rights on profile
        if ($profile == null ||
            (!$this->Acl->getRights(ucfirst($profileType), $profileId, 4) &&
                ($mediasFolder != null && $mediasFolder->personnal_folder == 1
                && $mediasFolder->personnal_user_folder != auth('web')->user()->id)
            )
            ) {
                return response(view('errors.403'), 403);
        }

        if (!$instance->hasApplication('office')) {
            return view('errors.403');
        }

        if (request()->isMethod('POST')) {
            $filename = request()->get('name');
            $filename = preg_replace("/[^A-Za-z0-9 ]/", '', $filename);
            $file_name = sha1($filename . microtime());

            switch ($documentType) {
                case 'spreadsheet':
                    $extension = ".xlsx";
                    $mime = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
                    copy(
                        storage_path('models/new-excel.xlsx'),
                        storage_path('uploads/documents/' . $file_name . $extension)
                    );
                    break;
                case 'presentation':
                    $extension = ".pptx";
                    $mime = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
                    copy(
                        storage_path('models/new-powerpoint.pptx'),
                        storage_path('uploads/documents/' . $file_name . $extension)
                    );
                    break;

                default:
                    $extension = ".docx";
                    $mime = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                    copy(
                        storage_path('models/new-word.docx'),
                        storage_path('uploads/documents/' . $file_name . $extension)
                    );
            }

            $user = auth()->guard('web')->user();
            //$profile = $mediasFolder ? $mediasFolder->profile : $user;

            $document = new Media();
            $document->users_id = $user->id;
            $document->instances_id = session('instanceId');
            $document->file_name = $file_name . $extension;
            $document->name = $filename . $extension;
            $document->file_path = storage_path("uploads/documents/" . $document->file_name);
            $document->date = date("Y-m-d");
            $document->type = Media::TYPE_DOCUMENT;
            $document->mime_type = $mime;
            $document->platform = 'local';
            $document->save();

            $profile
                ->medias()
                ->attach(
                    $document->id,
                    [
                        'medias_folders_id' => $mediasFolder ? $mediasFolder->id : null,
                    ]
                )
            ;

            // create initial doc View
            $v = new View();
            $v->post_id = $document->id;
            $v->post_type = get_class($document);
            $v->users_id = $document->users_id;
            $v->type = View::TYPE_CREATE;
            $v->created_at = $document->created_at;
            $v->save();

            return response()->json([
                'redirect' => route('office.document', ['documentId' => $document->id]),
                'target' => '_blank',
                'reload' => true,
                'closeModal' => true,
            ]);
        }

        return view('office.create', [
            'documentType' => $documentType
        ]);
    }

    public function document($documentId)
    {
        $instance = Instance::find(session('instanceId'));
        if (!$instance->hasApplication('office')) {
            return view('errors.403');
        }

        $media = Media::findOrFail($documentId);

        // check rights
        $mediaAuthor = $media->author->first();
        if (!$this->Acl->getRights(get_class($mediaAuthor), $mediaAuthor->id, 4)) {
                return response(view('errors.403'), 403);
        }

        $user = auth()->guard('web')->user();
        $extension = strtolower(pathinfo($media->file_path, PATHINFO_EXTENSION));

        // if(!$media || !in_array('.'.$extension, config('office.DOC_SERV_EDITED'))){
        if (! $media || ! $media->isDocument()) {
            return view('errors.403');
        }

        return view('office.document', [
            'document' => $media,
            'user' => $user,
            'extension' => $extension
        ]);
    }

    /*
     * used by api only office
     */
    public function download($mediaId)
    {
        $media = Media::find($mediaId);

        if (!$media->instance->hasApplication('office')) {
            return view('errors.403');
        }

        if ($media) {
            $user = User::where('slug', request()->get('s'))->first();
            $v = new View();
            $v->post_id = $media->id;
            $v->post_type = get_class($media);
            $v->users_id = $user->id;
            $v->type = View::TYPE_OPEN;
            $v->save();
            $headers = [
                'Content-Type' => $media->mime_type,
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
                'Cache-Control' =>  ['post-check=0, pre-check=0', false],
                'Pragma' => 'no-cache',
            ];
            return response()->download($media->file_path, $media->name, $headers);
            /*$response = new Response();

            $response->headers->set('Content-Disposition', sprintf('inline; filename="%s"', $media->name));
            return $response;*/
        }
    }

    public function save($mediaId)
    {
        $media = Media::find($mediaId);

        if (!$media->instance->hasApplication('office')) {
            return view('errors.403');
        }

        if ($media) {
            if (request()->isMethod('POST')) {
                $status = request()->get('status');
                if ($status == 2) {
                    $downloadUri = request()->get("url");
                    if (! isset($downloadUri)) {
                        return response()->json([
                            'error' => 'Bad Request',
                            'saved' => false
                        ], 400);
                    }
                    $arrContextOptions = [
                        "ssl" => [
                            "verify_peer" => false,
                            "verify_peer_name" => false
                        ]
                    ];
                    if (($new_data = file_get_contents(
                        $downloadUri,
                        false,
                        stream_context_create($arrContextOptions)
                    )) === false) {
                        return response()->json([
                            'error' => 'Bad Request',
                            'saved' => false
                        ], 400);
                    } else {
                        file_put_contents($media->file_path, $new_data, LOCK_EX);
                        $actions = request()->get('actions');
                        $user = User::find($actions[0]['userid']);
                        $media->updated_at = date('Y-m-d H:i:s', strtotime(request()->get('lastsave')));
                        $media->encoded = 0;
                        $media->update();
                        $lastView = View::where('type', View::TYPE_EDIT)
                            ->where('post_id', $media->id)
                            ->where('post_type', get_class($media))
                            ->first();
                        if (!$lastView || $lastView->users_id != $user->id) {
                            $v = new View();
                            $v->post_id = $media->id;
                            $v->post_type = get_class($media);
                            $v->users_id = $user->id;
                            $v->type = View::TYPE_EDIT;
                            $v->save();
                        } else {
                            $lastView->touch();
                        }
                        if ($media->under_workflow) {
                            \Log::info('ONLY OFFICE DOC UPDATED : ' . $media->id);
                            // notify all workflow user of doc update
                        }

                        return response()->json([
                            'c' => 'saved',
                            'saved' => true
                        ]);
                    }
                }
                return response()->json([
                    'error' => 0
                ]);
            }
        }
    }
}
