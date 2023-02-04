<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Repository\SearchRepository2;
use App\ColabDoc;
use App\User;
use App\Media;
use App\MediasFolder;
use Validator;
use App\Events\CollabStep;
use App\Events\CollabTelepointer;
use App\Events\UploadMedia;
use Netframe\Media\MediaManagerInterface;
use App\Instance;
use App\Application;

class ColabController extends BaseController
{

    private $mediaManager;

    private static $EDITOR_MEDIAS = "__editor_medias";

    public function __construct(SearchRepository2 $searchRepository, MediaManagerInterface $mediaManager)
    {
        parent::__construct();

        $this->searchRepository = $searchRepository;
        $this->mediaManager = $mediaManager;
    }

    public function home($path = null)
    {
        $instance = Instance::find(session('instanceId'));
        $appCollab = Application::where('slug', '=', 'collab')->first();
        if (! $instance->apps->contains($appCollab->id)) {
            return response(view('errors.403'), 403);
        }

        $data = [
            'path' => $path
        ];
        return view('colab.app', $data);
    }

    public function create()
    {
        $instance = Instance::find(session('instanceId'));
        $appCollab = Application::where('slug', '=', 'collab')->first();
        if (! $instance->apps->contains($appCollab->id)) {
            return response(view('errors.403'), 403);
        }

        return view('colab.create');
    }

    public function docs($slug, $docId = null)
    {
        $instance = Instance::find(session('instanceId'));
        $appCollab = Application::where('slug', '=', 'collab')->first();

        $user = User::where('slug', $slug)->first();
        if ($docId) {
            $doc = ColabDoc::find($docId);
            return response()->json([
                'id' => $doc->id,
                'name' => $doc->name,
                'last_update' => $doc->updated_at,
                'last_update_txt' => \App\Helpers\DateHelper::feedDate($doc->updated_at),
            ]);
        }

        $docs = $user->colabDocs();
        $shared = ColabDoc::where('users', 'like', '%"' . $user->id . '"%');

        if (request()->has('order_by')) {
            switch (request()->get('order_by')) {
                case 'name':
                    $docs = $docs->orderBy('name', 'asc');
                    $shared = $shared->orderBy('name', 'asc');
                    break;
                case 'edit':
                    $docs = $docs->orderBy('updated_at', 'desc');
                    $shared = $shared->orderBy('updated_at', 'desc');
                    break;
                default:
                    $docs = $docs->orderBy('updated_at', 'desc');
                    $shared = $shared->orderBy('updated_at', 'desc');
                    break;
            }
        }

        $docs = $docs->get();
        $shared = $shared->get();
        $docs = array_map(function ($d) {
            return [
                'name' => $d['name'],
                'id' => $d['id'],
                'last_update' =>  $d['updated_at'],
                'last_update_txt' => \App\Helpers\DateHelper::feedDate($d['updated_at']),
            ];
        }, $docs->toArray());

        $shared = array_map(function ($d) {
            return [
                'name' => $d['name'],
                'id' => $d['id'],
                'last_update' =>  $d['updated_at'],
                'last_update_txt' => \App\Helpers\DateHelper::feedDate($d['updated_at']),
            ];
        }, $shared->toArray());
        return response()->json([
            'docs' => $docs,
            'shared' => $shared
        ]);
    }

    public function add($slug, $id = null)
    {
        $instance = Instance::find(session('instanceId'));
        $appCollab = Application::where('slug', '=', 'collab')->first();

        $user = User::where('slug', $slug)->first();
        \App::setLocale($user->lang);
        $doc = null;

        if ($id) {
            $doc = ColabDoc::find($id);
        }

        if (request()->isMethod('POST')) {
            $validator = Validator::make(request()->all(), [
                'doc_name' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->messages()
                ]);
            } else {
                $id = request()->get('id');
                $document = '{"doc":{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text",'
                    . '"text":" "}]}]}'
                    . ',"version":1,"steps":[]}';
                $notifs = [];
                if (! $id) {
                    $doc = new ColabDoc();
                    $doc->users_id = $user->id;
                    $doc->instances_id = $user->instances()->first()->id;
                    $doc->content = json_encode(json_decode($document, true));
                    $doc->name = request()->get('doc_name');
                    $doc->users = json_encode(request()->get('doc_users'));
                    $doc->save();
                    $notifs = request()->get('doc_users') ?? [];
                } else {
                    $doc = ColabDoc::find($id);
                    $doc->name = request()->get('doc_name');
                    $oldUsers = json_decode($doc->users, true);
                    $doc->users = json_encode(request()->get('doc_users'));
                    $doc->update();
                    $notifs = array_diff(request()->get('doc_users') ?? [], $oldUsers ?? []) ?? [];
                }
                foreach ($notifs as $aUser) {
                    $notif = new \App\Notif();
                    $notif->instances_id = $user->instances()->first()->id;
                    $notif->author_id = $aUser;
                    $notif->author_type = "App\\User";
                    $notif->type = "add_collab";
                    $notif->user_from = $user->id;
                    $notif->read = false;
                    $notif->parameter = json_encode(array(
                        'collab_doc' => $doc->id
                    ));
                    $notif->save();
                }
                return response()->json([
                    'closeModal' => true,
                    'reload' => true
                ]);
            }
        }
        $return = [];
        $return['mode'] = 'add';
        if ($id) {
            $return['id'] = $id;
            $return['name'] = $doc->name;
            $return['mode'] = 'edit';
            $users = json_decode($doc->users, true) ?? [];
            foreach ($users as $userId) {
                $user = User::find($userId);
                $return['users'][] = [
                    'id' => $user->id,
                    'text' => $user->getNameDisplay(),
                    'image' => ($user->profileImage != null) ? $user->profileImage->getUrl() : "",
                    'online' => ($user->isOnline()) ? 'status-online' : 'status-offline',
                    'initialsToColor' => $user->initialsToColorRgb(),
                    'initials' => $user->initials(),
                ];
            }
        }
        return view('colab.add', $return);
    }

    public function delete($slug)
    {
        $instance = Instance::find(session('instanceId'));
        $appCollab = Application::where('slug', '=', 'collab')->first();

        $user = User::where('slug', $slug)->first();
        $data = request()->get('postData');
        $id = $data['id'];

        $obj = ColabDoc::find($id);

        if ($obj) {
            $obj->delete();
        }

        return response()->json("ok");
    }

    public function getUsers()
    {
        $query = request()->get('q');
        $targetsProfiles = [
            'user' => 1
        ];

        $this->searchRepository->route = 'search_results';
        $this->searchRepository->targetsProfiles = $targetsProfiles;
        $this->searchRepository->toggleFilter = false;
        $this->searchRepository->byInterests = 0;
        $this->searchRepository->newProfile = 0;

        $searchParameters = $this->searchRepository->initializeConfig('search_results', $targetsProfiles, false, 0);
        $results = $this->searchRepository->search($searchParameters, $targetsProfiles);

        $returnResult = [];
        foreach ($results[0] as $user) {
            if ($user->id != auth()->guard('web')->user()->id) {
                $returnResult[] = [
                    'id' => $user->id,
                    'text' => $user->getNameDisplay(),
                    'image' => ($user->profileImage != null) ? $user->profileImage->getUrl() : "",
                    'online' => ($user->isOnline()) ? 'status-online' : 'status-offline',
                    'initialsToColor' => $user->initialsToColorRgb(),
                    'initials' => $user->initials()
                ];
            }
        }

        return response()->json([
            'results' => $returnResult
        ]);
    }

    public function push($slug)
    {
        $user = User::where('slug', $slug)->first();

        if (request()->isMethod('POST')) {
            $document = ColabDoc::find(request()->get('docId'));
            if ($document) {
                $doc = json_decode($document->content, true);
                $data = request()->get('data');
                if ($data) {
                    $content = $data;
                    ;
                    unset($content['version']);
                    $doc['doc'] = $content;
                    $document->content = json_encode($doc);
                    $document->update();
                }
            }
        }
    }

    public function document($slug, $documentId)
    {
        $user = User::where('slug', $slug)->first();
        $doc = ColabDoc::find($documentId);
        $steps = [];
        if ($doc) {
            $content = json_decode($doc->content, true);
            $version = $content['version'];
            if ($content && array_key_exists('steps', $content)) {
                $steps = $content['steps'] ?? [];
                unset($content['steps']);
            }
            // unset($content['version']);
            $content = json_encode($content);
        }
        if ($doc && ! $doc->content) {
            $document = '{"doc":{"type":"doc","content":[{"type":"paragraph","content":[{"type":"text",'
                . '"text":"This is a collaborative test document. Start editing to make it more interesting!"}]}]},'
                . '"version":1,"steps":[]}';
            $content = $document;

            $doc->content = json_encode(json_decode($document, true));
            $doc->save();
        }
        if (request()->isMethod('POST')) {
            $doc->content = json_encode(request()->get('content'));
            $doc->update();
            return response()->json([
                'ok' => true
            ]);
        } else {
            return response()->json(json_decode($content, true));
        }
    }

    public function steps($slug, $documentId)
    {
        $user = User::where('slug', $slug)->first();
        $document = ColabDoc::find($documentId);

        $steps = request()->get('steps');
        $remoteVersion = request()->get('version');
        $doc = json_decode($document->content, true);
        $version = $doc['version'];
        if ($remoteVersion < 0 || $remoteVersion > $version) {
            return response()->json([
                'title' => 'Invalid version ' . $remoteVersion
            ], 400);
        }

        if (request()->isMethod('POST')) {
            if ($remoteVersion != $version) {
                return response()->json([
                    'title' => 'Version not current'
                ], 409);
            }

            $version += count($steps);

            $steps = array_map(function ($step) use ($user) {
                $step["userId"] = $user->id;
                return $step;
            }, $steps);

            $MAX = 10;
            $doc['steps'] = array_merge($doc['steps'], $steps);

            $length = count($doc['steps']);
            if ($length > $MAX) {
                $doc['steps'] = array_slice($doc['steps'], $length - $MAX);
            }

            $startIndex = $length - ($version - $remoteVersion);
            $doc['version'] = $version;
            $document->content = json_encode($doc);
            $document->update();
            broadcast(new CollabStep($documentId, [
                'steps' => $steps,
                'version' => $version
            ]))->toOthers();
            return response()->json([
                'steps' => $steps,
                'version' => $version
            ]);
        }
        $steps = $doc['steps'] ?? [];

        $len = count($doc['steps']);
        $version = $doc['version'];
        $startIndex = $len - ($version - $remoteVersion);

        if ($startIndex < 0) {
            return response()->json([
                'title' => 'History no longer available'
            ], 410);
        }

        $steps = array_slice($steps, $startIndex);

        return response()->json([
            'steps' => $steps,
            'version' => $version
        ]);
    }

    public function telepointer($slug, $documentId)
    {
        $user = User::where('slug', $slug)->first();
        $document = ColabDoc::find($documentId);
        $user = User::find(request()->get('sessionId'));
        $content = array_merge(request()->all(), [
            'name' => $user->getNameDisplay(),
            'email' => $user->email,
            'avatar' => $user->profileImage->getUrl(),
            'initialsToColor' => $user->initialsToColorRgb(),
            'initials' => $user->initials(),
        ]);
        broadcast(new CollabTelepointer($documentId, $content))->toOthers();
        return response()->json(request()->all());
    }

    public function users($userId)
    {
        $user = User::find($userId);
        return response()->json([
            'userId' => $userId,
            'name' => $user->getNameDisplay(),
            'email' => $user->email,
            'avatar' => $user->profileImage->getUrl(),
            'initialsToColor' => $user->initialsToColorRgb(),
            'initials' => $user->initials(),
        ]);
    }

    public function subscribe()
    {
        // \Log::info(request()->get('channels'));
    }

    private function checkVersion($remote, $local)
    {
        return $remote < 0 || $remote > $local;
    }

    public function upload()
    {
        $user = auth()->guard('web')->user();
        $folder = MediasFolder::where('name', self::$EDITOR_MEDIAS)->where('profile_type', get_class($user))
            ->where('profile_id', $user->id)
            ->where('users_id', $user->id)
            ->where('instances_id', session('instanceId'))
            ->first();
        if (! $folder) {
            $folder = new MediasFolder();
            $folder->name = self::$EDITOR_MEDIAS;
            $folder->default_folder = true;
            $folder->profile_id = $user->id;
            $folder->profile_type = get_class($user);
            $folder->users_id = $user->id;
            $folder->instances_id = session('instanceId');
            $folder->save();
        }

        $media = new Media();

        $file = request()->file('file');

        $media->users_id = $user->id;
        $media->instances_id = session('instanceId');
        $media->file_name = $file->getClientOriginalName();
        $media->file_path = '';
        $media->feed_path = '';
        $media->thumb_path = '';
        try {
            $this->mediaManager->upload($file, $media);
        } catch (UnsupportedFileTypeException $e) {
        }

        \DB::table('users_has_medias')->insert(array(
            'users_id' => $user->id,
            'medias_id' => $media->id,
            'medias_folders_id' => $folder->id
        ));
        event(new UploadMedia($user, session('instanceId')));
        return response()->json([
            'path' => $media->getUrl()
        ]);
    }
}
