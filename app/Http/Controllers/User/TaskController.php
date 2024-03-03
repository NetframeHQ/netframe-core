<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;
use App\Repository\SearchRepository2;
use App\Repository\NotificationsRepository;
use App\User;
use App\Tag;
use App\Template;
use App\TaskTable;
use App\TaskRow;
use App\Instance;
use App\Application;
use App\Workflow;
use App\WorkflowDetailsAction;
use App\Notif;
use App\Comment;
use \DB;
use App\Events\NewPost;
use Validator;
use Illuminate\Support\Str;

class TaskController extends BaseController
{
    public function __construct(SearchRepository2 $searchRepository)
    {
        parent::__construct();

        $this->searchRepository = $searchRepository;
    }

    public function home()
    {
        $instance = Instance::find(session('instanceId'));
        $appCollab = Application::where('slug', '=', 'tasks')->first();
        if (! $instance->apps->contains($appCollab->id)) {
            return response(view('errors.403'), 403);
        }

        $user = auth()->guard('web')->user();
        $tasks = $user->tasks;

       // find all project of user or of its profiles
        $entities = $user->house()->whereHas('tasks')->get();
        $communities = $user->community()->whereHas('tasks')->get();
        $projects = $user->project()->whereHas('tasks')->get();

        $tasks = [];

        foreach ($entities as $entity) {
            foreach ($entity->tasks as $task) {
                $tasks[$task->id] = [
                    'name' => $task->name,
                    'profileName' => $entity->getNameDisplay(),
                    'profileType' => 'house',
                    'task' => $task,
                    'role' => $entity->pivot->roles_id,
                ];
            }
        }
        foreach ($communities as $community) {
            foreach ($community->tasks as $task) {
                $tasks[$task->id] = [
                    'name' => $task->name,
                    'profileName' => $community->getNameDisplay(),
                    'profileType' => 'community',
                    'task' => $task,
                    'role' => $community->pivot->roles_id,
                ];
            }
        }
        foreach ($projects as $project) {
            foreach ($project->tasks as $task) {
                $tasks[$task->id] = [
                    'name' => $task->name,
                    'profileName' => $project->getNameDisplay(),
                    'profileType' => 'project',
                    'task' => $task,
                    'role' => $project->pivot->roles_id,
                ];
            }
        }

        foreach ($user->tasks as $task) {
            $tasks[$task->id] = [
                'name' => $task->name,
                'profileName' => $user->getNameDisplay(),
                'profileType' => 'user',
                'task' => $task,
                'role' => 1,
            ];
        }

        sort($tasks);

        $hasValidation = WorkflowDetailsAction::join(
            'workflows',
            'workflow_details_actions.workflows_id',
            '=',
            'workflows.id'
        )
            ->where('workflows.users_id', $user->id)
            ->where('workflows.type', 'validate_file')
            ->where('workflow_details_actions.action_validate', false)
            ->first() != null;

        $data = [];
        $data['tasks'] = $tasks;
        $data['hasValidation'] = $hasValidation;
        return view('task.home', $data);
    }

    public function project($projectId)
    {
        $instance = Instance::find(session('instanceId'));
        $appCollab = Application::where('slug', '=', 'tasks')->first();
        if (! $instance->apps->contains($appCollab->id)) {
            return response(view('errors.403'), 403);
        }

        $user = auth()->guard('web')->user();
        $project = TaskTable::find($projectId);
        if ($project) {
            $instance = Instance::find(session('instanceId'));

            // add check rights
            $author = $project->author;
            if (!$this->Acl->getRights(get_class($author), $author->id, 5)) {
                return response(view('errors.403'), 403);
            }

            $data['profile'] = $author;
            $cols = $project->template->getCols();
            $tasks = $project
                ->tasks()
                ->where('parent', null)
                ->where('archived', false);
                //->orderBy('created_at');
                //->get();
            if (request()->has('order-by')) {
                $order = request()->get('order-by');
                $type = request()->has('type') ? 'desc' : 'asc';
                switch ($order) {
                    case 'task':
                        $tasks = $tasks->orderBy('name', $type);
                        break;
                    case 'user':
                        $tasks = $tasks->join('workflows', 'workflows.id', '=', 'tables_rows.workflows_id')
                                ->join('users', 'users.id', '=', 'workflows.users_id')
                                ->orderBy(\DB::raw("concat(users.firstname,' ',users.name)"), $type)
                                ->select('tables_rows.*');
                        break;
                    case 'deadline':
                        $tasks = $tasks->orderBy('deadline', $type);
                        break;
                    case 'status':
                        $tasks = $tasks->join('workflows', 'workflows.id', '=', 'tables_rows.workflows_id')
                                ->orderBy('finished', $type);
                        break;

                    default:
                        $tasks = $tasks->orderBy('created_at');
                        break;
                }
            }
            $tasks = $tasks->get();
            $archives = $project->tasks()->where('parent', null)->where('archived', true)->orderBy('created_at')->get();
            $data['project'] = $project;
            $data['tasks'] = $tasks;
            $data['archives'] = $archives;
            $data['cols'] = $cols;

            $nbCols = 3; // default colsnumber
            $nbCols += count($cols);
            if ($project->template->linked) {
                $nbCols += 3;
            }

            $data['nbCols'] = $nbCols;

            return view('task.list', $data);
        } else {
            return view('errors.404');
        }
    }

    public function users()
    {
        /*
        $searchTerms = htmlentities(request()->get('q'));
        $instance = Instance::find(session('instanceId'));
        $users = $instance->users()->distinct()
            ->select(DB::raw('users.id, concat(firstname," ",users.name) as text, profile_media_id'))
            ->join('users_has_medias','users.profile_media_id','=', 'users_has_medias.medias_id')
            ->join('medias','medias.id','=', 'users_has_medias.medias_id')
            ->where(DB::raw('concat(firstname," ",users.name)'), 'like', request()->get('q').'%')
            ->orWhere(DB::raw('concat(users.name," ",firstname)'), 'like', request()->get('q').'%')
            ->limit(5);
        $results = $users->get(array('id', 'text', 'profile_media_id'))->toArray();
        $results = array_map(function($e){
            // unset($e['profile_media_id']);
            unset($e['pivot']);
            $e['image'] = url()->route('media_download', array('id' => $e['profile_media_id'])).'?thumb=1';
            $e['text'] = ucwords($e['text']);
            return $e;
        }, $results);
        // dd($results);
        $data['results'] = $results;
        return response()->json($data);
        */
        $query = request()->get('query');
        $targetsProfiles = ['user' => 1];

        $this->searchRepository->route = 'search_results';
        $this->searchRepository->targetsProfiles = $targetsProfiles;
        $this->searchRepository->toggleFilter = false;
        $this->searchRepository->byInterests = 0;
        $this->searchRepository->newProfile = 0;

        $searchParameters = $this->searchRepository->initializeConfig('search_results', $targetsProfiles, false, 0);
        $results = $this->searchRepository->search($searchParameters, $targetsProfiles);

        $returnResult = [];
        foreach ($results[0] as $user) {
            $returnResult[] = [
                'id' => $user->id,
                'name' => $user->getNameDisplay(),
                'profileImage' => ($user->profileImage != null) ? $user->profileImage->id : null,
                'online' => ($user->isOnline()) ? 'status-online' : 'status-offline',
                'initials' => $user->initials(),
                'initialsToColor' => $user->initialsToColor(),
            ];
        }

        return response()->json(['users' => $returnResult]);
    }

    public function addProject($projectId = null)
    {
        $user = auth()->guard('web')->user();
        $dataUser = User::find($user->id);
        $data['profile'] = $dataUser;
        $project = null;
        if (isset($projectId)) {
            $project = TaskTable::find($projectId);
            if (isset($project)) {
                $project->cols = json_decode($project->cols, true);
                $data['project'] = $project;
            }


            // check rights on owner profile
            $taskProfile = $project->author()->first();
            if (!$this->Acl->getRights(get_class($taskProfile), $taskProfile->id, 3)) {
                return response(view('errors.403'), 403);
            }
        }
        $instance = Instance::find(session('instanceId'));
        $templates = Template::where('instances_id', session('instanceId'))
            ->orWhere('instances_id', null)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
        $data['templates'] = [0=>''] + $templates;

        if (request()->isMethod('POST')) {
            // dd(request()->all());
            if (request()->has('premodel') && request()->get('premodel') != 0) {
                $validator = Validator::make(request()->all(), [
                    'project_name' => 'required',
                ]);
            } else {
                $validator = Validator::make(request()->all(), [
                    'project_name' => 'required',
                    'templates' => 'required|exists:tables_templates,id',
                ]);
            }
            if ($validator->fails()) {
                return redirect()->route('task.addProject')
                                 ->withErrors($validator)
                                 ->withInput();
            } else {
                $count = TaskTable::where('instances_id', session('instanceId'))
                    ->where('name', request()->get('project_name'))
                    ->first();
                if ($count && $count->id!=$projectId) {
                    return redirect()->route('task.addProject')
                                     ->withErrors(['project_name'=>['msg'=>trans('task.already')]])
                                     ->withInput();
                } else {
                    if (request()->has('premodel') && request()->get('premodel') != 0) {
                        // hack for gilead
                        // specify template for template of workflow
                        $template = Template::find(13);
                    } else {
                        $template = Template::find(request()->get('templates'));
                    }
                    if ($projectId) {
                        // dd($project);
                        $project = TaskTable::find($projectId);
                        // $cols = request()->get('cols');
                        // foreach ($cols as $key => $value) {
                        //     $cols[$key] = $value??"";
                        // }
                        $project->name = request()->get('project_name');
                        $project->author_id = request()->get('id_foreign');
                        $project->author_type = "App\\".ucfirst(request()->get('type_foreign'));
                        $project->tables_templates_id = $template->id;
                        $project->cols = "";
                        $project->has_medias = $template->has_medias;
                        $project->update();
                        /*$vals = json_decode($template->cols,true);
                        foreach ($vals as $key => $value) {
                            $type = $value["type"];
                            if(in_array($key, array_keys($cols))){
                                $items = $cols[$key];
                                if($type=="file"){}
                                elseif($type=="tag") {
                                    \App\Helpers\TagsHelper::attachPostedTags($items, $project);
                                }elseif ($type=="user") {}
                            }
                        }*/
                        // $tab->true_author_id = request()->get('id_foreign_as');
                        // $tab->true_author_type = "App\\".ucfirst(request()->get('type_foreign_as'));
                        // $tab->confidentiality = 1;
                    } else {
                        $tab = new TaskTable();
                        $tab->name = request()->get('project_name');
                        $tab->users_id = $user->id;
                        $tab->instances_id = $instance->id;
                        $tab->author_id = request()->get('id_foreign');
                        $tab->author_type = "App\\".ucfirst(request()->get('type_foreign'));
                        $tab->tables_templates_id = $template->id;
                        $tab->has_medias = $template->has_medias;
                        /*$cols = request()->get('cols');
                        $vals = json_decode($template->cols,true);*/

                        $tab->cols = "";
                        $tab->save();
                        $tab->update();
                        $tab->true_author_id = request()->get('id_foreign_as');
                        $tab->true_author_type = "App\\".ucfirst(request()->get('type_foreign_as'));
                        $tab->confidentiality = 1;
                        event(new NewPost("TaskTable", $tab, null, null, null));


                        if (request()->has('premodel') && request()->get('premodel') != 0) {
                            // create tasks and sub tasks for custom preinputed model
                            $this->speGilead($tab);
                        }
                    }
                    // dd($tab);
                    return redirect()->route('task.home');
                }
            }
        }

        return view('task.add-project', $data);
    }

    private function speGilead($tab)
    {
        $modelGilead = [
            0 => [
                'taskName' => 'Administratif & Finance',
                'subTasks' => [
                    'Brief de l\'assistante & identification informations/documents nécessaires',
                    'etude & lancement si besoin : MMJR / CVO / CC/ Due Diligence / DMOS',
                    'rédaction du contrat (fiche de validation à faire si parrainage/partenariat)',
                    'Faire la création de fournisseur sur Oracle dès réception de la Due Diligence "approved"',
                    'envoi du draft de contrat (et de la FV le cas échéant) à la chargée de projet pour validation',
                    'Faire le PO sur Oracle',
                    'envoi du contrat au service Legal pour validation juridique'
                        . ' (+ FV le cas échéant) avec la chargée de mission en copie',
                    'enregistrement des mails et documents adminsitratifs et drafts sur le serveur',
                    'après validation juridique, envoi du contrat au prestataire/'
                        . ' partenaire pour relecture et signature (chargée de mission en copie)',
                    'enregistrement du mail de validation juridique et du contrat validé par le Legal sur le serveur'
                        . ' (compléter le tableau de suivi des contrats)',
                    'envoi au prestataire/ partenaire le n° PO avec les consignes de facturation',
                    'compléter le tableau de suivi des contrats et archiver sur le serveur :'
                        . ' le contrat signé et scanné,'
                        . ' la fiche de validation signée par le Legal,'
                        . ' le devis signé et le PO',
                ],
            ],
            1 => [
                'taskName' => 'Suivi des prestations',
                'subTasks' => [
                    'suivre la facturation de l\'acompte (le cas échéant)',
                    'échéance 1 - récupérer la contrepartie correspondante'
                        . ' (enregister sur le serveur et compléter le tableau de suivi des contrats)'
                        . ' et vérifier si receipt du PO à faire',
                    'échéance 2 - récupérer la contrepartie correspondante'
                        . ' (enregister sur le serveur et compléter le tableau de suivi des contrats)'
                        . ' et vérifier si receipt du PO à faire',
                    'échéance 3 - récupérer la contrepartie correspondante'
                        . ' (enregister sur le serveur et compléter le tableau de suivi des contrats)'
                        . ' et vérifier si receipt du PO à faire',
                ],
            ],
            2 => [
                'taskName' => 'Clôture',
                'subTasks' => [
                    'En amont de la date d\'échéance du contrat,'
                        . ' veiller à la bonne livraison de l\'ensemble des contreparties attendues au contrat',
                    'Vérifier la facturation du solde',
                    'archivage du contrat et des contreparties',
                    'compléter le tableau de suivi des contrats',
                ],
            ],
        ];

        foreach ($modelGilead as $task) {
            $wf = new Workflow();
            $wf->users_id = auth()->guard('web')->user()->id;
            $wf->instances_id = session('instanceId');
            $wf->save();
            $wfId = $wf->id;

            $tr = new TaskRow();
            $tr->users_id = auth('web')->user()->id;
            $tr->deadline = date('Y-m-d');
            $tr->name = $task['taskName'];
            $tr->tables_tasks_id = $tab->id;
            $tr->parent = null;
            $tr->workflows_id = $wfId;
            $tr->save();

            foreach ($task['subTasks'] as $subTask) {
                $wf = new Workflow();
                $wf->users_id = auth()->guard('web')->user()->id;
                $wf->instances_id = session('instanceId');
                $wf->save();
                $wfId = $wf->id;

                $subtr = new TaskRow();
                $subtr->users_id = auth('web')->user()->id;
                $subtr->deadline = date('Y-m-d');
                $subtr->name = $subTask;
                $subtr->tables_tasks_id = $tab->id;
                $subtr->parent = $tr->id;
                $subtr->workflows_id = $wfId;
                $subtr->save();
            }
        }
    }

    public function detailsProject($projectId)
    {
        $user = auth()->guard('web')->user();
        $project = $user->projects()->find($projectId);
        if (!$project) {
            return view('errors.404');
        }
        $data['project'] = $project;
        $data['profile'] = $user;
        $data['template_cols'] = json_decode($project->template->cols, true);
        $data['cols'] = json_decode($project->cols, true);
        return view('task.details', $data);
    }

    public function deleteTemplate($template)
    {
        $template = Template::find($template);
        if ($template) {
            $table = TaskTable::where('tables_templates_id', $template->id)->first();
            if (!$table) {
                $id = $template->id;
                $template->delete();
                return response()->json(['deleted'=>true,'id'=>$id]);
            }
        }
        return response()->json(['deleted'=>false]);
    }

    public function delete()
    {
        $data = request()->get('postData');
        $type = $data['type'];
        $id = $data['id'];
        $obj = null;

        if ($type=='project') {
            $obj = TaskTable::find($id);
            // check rights on owner profile
            if ($obj) {
                $taskProfile = $obj->author()->first();
                if (!$this->Acl->getRights(get_class($taskProfile), $taskProfile->id, 2)) {
                    return response(view('errors.403'), 403);
                }

                $obj->delete();
            }
            /*if($obj){

                $tasks = $obj->tasks();
                $wfs = [];
                foreach ($tasks as $task) {
                    $wfs[] = $task->workflow;
                }



                foreach ($wfs as $wf) {
                    if($wf)
                        $wf->delete();
                }

            }*/
        } elseif ($type=='task') {
            $obj = TaskRow::find($id);

            $mainTask = $obj->project;
            // check rights on owner profile
            $taskProfile = $mainTask->author()->first();
            if (!$this->Acl->getRights(get_class($taskProfile), $taskProfile->id, 2)) {
                return response(view('errors.403'), 403);
            }
            /*
            $children = TaskRow::where('parent', $id)->get();
            $wf = $obj->workflow;
            foreach ($children as $child) {
                $wf = $child->workflow;
                $child->delete();
                if($wf)
                    $wf->delete();
            }
            */
            $obj->delete();
            /*
            if($wf)
                $wf->delete();
            */
        }

        $dataJson = [
            'delete' => true,
            'targetId' => '#task-' . $id,
        ];
        return response()->json($dataJson);
    }

    public function archive()
    {
        $data = request()->get('postData');
        $id = $data['id'];

        $task = TaskRow::find($id);

        $mainTask = $task->project;
        // check rights on owner profile
        $taskProfile = $mainTask->author()->first();
        if (!$this->Acl->getRights(get_class($taskProfile), $taskProfile->id, 2)) {
            return response(view('errors.403'), 403);
        }

        $task->archived = true;
        $task->update();

        return response()->json("ok");
    }

    public function addTemplate($projectId = null)
    {

        $instance = Instance::find(session('instanceId'));

        if (request()->isMethod('POST')) {
            $data = [];
            $validator = validator(request()->all(), array("name"=>"required"));
            if ($validator->fails()) {
                $data['errors'] = $validator->messages();
                return response()->json($data);
            } else {
                $data['closeModal'] = true;
                $names = request()->get('names');
                $types = request()->get('types');
                $required = request()->get('required');
                $array = array();
                $hasMedias = 0;

                for ($i=0; $i < count($names); $i++) {
                    if (isset($names[$i])) {
                        $slug = Str::slug($names[$i], "-");
                        $array[$slug] = array('name'=>$names[$i],'type'=>$types[$i]);
                        $array[$slug]['required'] = isset($required[$i]);
                        if ($types[$i] == 'file') {
                            $hasMedias = 1;
                        }
                    }
                }
                $user = auth()->guard('web')->user();
                $template = new Template();
                $linked = request()->get('switch');
                if (!$linked) {
                    $template->linked = false;
                }
                $template->name = request()->get('name');
                $template->instances_id = $instance->id;
                $template->cols = json_encode($array);
                $template->language = $user->lang;
                $template->has_medias = $hasMedias;
                $template->save();

                // add new template selector in json return
                $templates = Template::where('instances_id', session('instanceId'))
                    ->orWhere('instances_id', null)
                    ->orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray();

                $project = ($projectId != null) ? TaskTable::find($projectId) : null;
                $dataTemplate = [
                    'templates' => [0=>''] + $templates,
                    'project' => $project,
                ];
                $data['replaceContent'] = true;
                $data['viewContent'] = view('task.partials.template-selector', $dataTemplate)->render();
                $data['targetId'] = 'select[name="templates"]';

                return response()->json($data);
            }
        }

        return view('task.add-template');
    }

    public function editTemplates()
    {
        $templates = Template::where('instances_id', session('instanceId'))->get();
        $user = auth()->guard('web')->user();
        return view('task.edit-templates', ['templates'=>$templates, 'profile'=>$user]);
    }

    public function editTemplate($template)
    {

        $instance = Instance::find(session('instanceId'));
        $tt = Template::find($template);

        if (request()->isMethod('POST')) {
            $data = [];
            $validator = validator(request()->all(), array("name"=>"required"));
            if ($validator->fails()) {
                $data['errors'] = $validator->messages();
                return response()->json($data);
            } else {
                $data['closeModal'] = true;
                $names = request()->get('names');
                $types = request()->get('types');
                $hasMedias = 0;
                $array = array();
                if ($names != null) {
                    for ($i=0; $i < count($names); $i++) {
                        if (isset($names[$i])) {
                            $slug = Str::slug($names[$i], "-");
                            $array[$slug] = array('name'=>$names[$i],'type'=>$types[$i]);
                            if ($types[$i] == 'file') {
                                $hasMedias = 1;
                            }
                        }
                    }
                }
                $user = auth()->guard('web')->user();
                $template = Template::find($template);

                $oldHasMedias = $template->has_medias;

                $template->name = request()->get('name');
                $template->instances_id = $instance->id;
                $template->cols = json_encode($array);
                $template->language = $user->lang;
                $template->has_medias = $hasMedias;
                $template->save();

                // test if media col has change to update project medias cols
                if ($oldHasMedias != $hasMedias) {
                    foreach ($template->projects as $project) {
                        $project->has_medias = $hasMedias;
                        $project->save();
                    }
                }

                return response()->json($data);
            }
        }
        $edit = request()->get('edit');
        if (!isset($edit)) {
            $edit = true;
        } else {
            $edit = false;
        }
        $data['template'] = $tt;
        $data['edit'] = $edit;
        return view('task.edit-template', $data);
    }

    public function getCols()
    {

        $instance = Instance::find(session('instanceId'));
        $postData = request()->get('postData');
        $template = Template::find($postData['id']);
        $project = null;
        $return = array();
        if ($template) {
            $data = [];
            $data['cols'] = json_decode($template->cols, true);
            if (isset($postData['project'])) {
                $project = TaskTable::find($postData['project']);
                $cols = json_decode($project->cols, true);
            }
            foreach ($data['cols'] as $key => $value) {
                if ($value['type']=='tag') {
                    $return['tag'] = true;
                    if (isset($postData['project'])) {
                        foreach ($cols[$key] as $tag) {
                            if (is_numeric($tag)) {
                                $data['pcols'][$key][] = Tag::find($tag);
                            } else {
                                $data['pcols'][$key][] = Tag::where('name', $tag)->first();
                            }
                        }
                    }
                }
                if ($value['type']=='user') {
                    $return['user'] = true;
                    if (isset($postData['project'])) {
                        foreach ($cols[$key] as $userId) {
                            $data['pcols'][$key][] = User::find($userId);
                        }
                    }
                }
            }
            $return['body'] = view('task.cols', $data)->render();
            if ($project) {
                $return['cols'] = $data['pcols'];
            }
        }
        return response()->json($return);
    }

    public function addTask($projectId)
    {

        $project = TaskTable::find($projectId);

        $author = $project->author;
        if (!$this->Acl->getRights(get_class($author), $author->id, 4)) {
            return response(view('errors.403'), 403);
        }

        $tasks = TaskRow::select(DB::raw('cast(id as char(19)) as idx'), 'name')
            ->where('tables_tasks_id', $projectId)
            ->where('parent', null)
            ->pluck('name', 'idx');

        if (request()->isMethod('POST')) {
            $validator = Validator::make(request()->all(), [
                'task_name' => 'required',
                'task_user' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                $data['errors'] = $validator->messages();
                return response()->json($data);
            } elseif ($project) {
                $wfId = null;
                if ($project->template->linked) {
                    $wf = new Workflow();
                    $wf->users_id = request()->get('task_user');
                    $wf->instances_id = session('instanceId');
                    // $wf->type = null;
                    $wf->save();
                    $wfId = $wf->id;
                }
                $tr = new TaskRow();
                $tr->users_id = auth('web')->user()->id;
                $tr->deadline = request()->get('deadline');
                $tr->name = request()->get('task_name');
                $tr->workflows_id = $wfId;
                $tr->tables_tasks_id = $projectId;
                $tr->parent = request()->get('parent');
                $tr->save();
                if ($project->template->linked) {
                    $notif = new Notif();
                    $notif->instances_id = session('instanceId');
                    $notif->author_id = $wf->users_id;
                    $notif->author_type = "App\\User";
                    $notif->type = "assign_task";
                    $notif->user_from = auth()->guard('web')->user()->id;
                    $notif->read = false;
                    $notif->parameter = json_encode(array('task_id'=>$tr->id));
                    $notif->save();
                }
                $data['closeModal'] = true;
                return response()->json($data);
            }
        }

        return view('task.task', ['tasks'=>$tasks]);
    }

    public function addTaskCol()
    {
        $data = request()->get('postData');
        $id = $data['id'];
        $custom = false;

        $project = TaskTable::find($data['project']);
        $author = $project->author;
        if (!$this->Acl->getRights(get_class($author), $author->id, 4)) {
            return response(view('errors.403'), 403);
        }

        if (in_array("custom", $data) && $data['custom']=="yes") {
            $custom = true;
        }
        if ($id == 0) {
            $wfId = null;
            $project = TaskTable::find($data['project']);
            if ($project->template->linked) {
                if (!request()->has('workflowId')) {
                    $wf = new Workflow();
                    $wf->users_id = auth()->guard('web')->user()->id;
                    $wf->instances_id = session('instanceId');
                    $wf->save();
                    $wfId = $wf->id;
                } else {
                    $wfId = request()->get('workflowId');
                    $wf = Workflow::find($wfId);
                }
            }

            if (request()->get('existingTaskRow') != 0) {
                $tr = TaskRow::find(request()->get('existingTaskRow'));

                $inserted = false;
                $updated = true;
            } else {
                $tr = new TaskRow();
                // $tr->users_id = auth('web')->user()->id;
                $tr->deadline = date('Y-m-d');
                $tr->name = "";

                $inserted = true;
                $updated = false;
            }

            $tr->workflows_id = $wfId;
            $tr->tables_tasks_id = $data['project'];
            $tr->parent = null;

            if (request()->has('workflowId')) {
                $mediaColName = $project->template->getMediaCol();
                if ($mediaColName != null) {
                    $fileId = request()->get('fileId');
                    $colContent = [
                        $mediaColName => $fileId
                    ];
                    if (request()->get('existingTaskRow') == 0) {
                        $tr->name = trans('workflow.types.'.$wf->type);
                    }
                    $tr->cols = json_encode($colContent);
                }
            }
            $tr->save();
            if ($project->template->linked) {
                $notif = new Notif();
                $notif->instances_id = session('instanceId');
                $notif->author_id = $wf->users_id;
                $notif->author_type = "App\\User";
                $notif->type = "assign_task";
                $notif->user_from = auth()->guard('web')->user()->id;
                $notif->read = false;
                $notif->parameter = json_encode(array('task_id'=>$tr->id));
                $notif->save();
            }

            $cols = $project->template->getCols();
            $nbCols = 3; // default colsnumber
            $nbCols += count($cols);
            if ($project->template->linked) {
                $nbCols += 3;
            }

            $return = [
                'inserted'=> $inserted,
                'updated'=> $updated,
                'id'=> $tr->id,
                'deadline'=>date_format(date_create($tr->deadline), 'Y-m-d'),
                'body' => view(
                    'task.row',
                    [
                        'task' => $tr,
                        'project' => $project,
                        'template' => $project->template,
                        'nbCols' => $nbCols,
                        'cols' => $cols,
                        'nbDirectTasks' => $project->directTasks()->count(),
                    ]
                )->render()
            ];
            // $return['value'] = date_format(date_create($tr->deadline),'Y-m-d');
            return response()->json($return);
        } else {
            $field = $data['field'];
            $value = $data['value'];
            $task = TaskRow::find($id);
            if ($task) {
                if (!$custom && ($field == "name" || $field == "deadline")) {
                    $task->$field = $value;
                    $task->update();
                    if (\DateTime::createFromFormat('Y-m-d', $value) !== false) {
                        $return_value = date_format(date_create($value), 'd/m/Y');
                    } else {
                        $return_value = $value;
                    }
                    return response()->json(['inserted'=> true, 'value'=> $return_value]);
                } elseif (!$custom && ($field == "finished" || $field == "users_id")) {
                    $wf = $task->workflow;
                    if ($field=="users_id") {
                        $notif = Notif::where('parameter', json_encode(['task_id'=>$task->id]))->first();
                        if ($notif) {
                            $notif->author_id = $value;
                            $notif->read = false;
                            $notif->update();
                        }
                    }
                    $wf->$field = $value;
                    $wf->update();
                    $return = ['inserted'=> true, 'id'=>$value, 'text'=>$wf->user->getNameDisplay()];
                    $return['image'] = $wf->user->profileImage ?? "";
                    return response()->json($return);
                } else {
                    $task->setCol($field, $value);
                    return response()->json(['inserted'=> true]);
                }
            }
        }
        return response()->json(['inserted'=> false]);
    }

    public function editTask($task)
    {
        $user = auth()->guard('web')->user();
        $instance = Instance::find(session('instanceId'));
        $task = TaskRow::find($task);

        $tasks = TaskRow::select(DB::raw('cast(id as char(19)) as idx'), 'name')
            ->where('tables_tasks_id', $task->tables_tasks_id)
            ->where('parent', null)
            ->pluck('name', 'idx');

        // check rights on owner profile
        $project = $task->project;
        $taskProfile = $project->author()->first();
        if (!$this->Acl->getRights(get_class($taskProfile), $taskProfile->id, 3)
            && $task->users_id != auth('web')->user()->id) {
            return response(view('errors.403'), 403);
        }

        $cols = $task->project->template->getCols();
        if (request()->isMethod('POST')) {
            $validator = Validator::make(request()->all(), [
                'task_name' => 'required',
                'task_user' => 'required|exists:users,id',
            ]);
            if ($validator->fails()) {
                $data['errors'] = $validator->messages();
                return response()->json($data);
            } elseif ($task) {
                $wf = $task->workflow;
                $wf->users_id = request()->get('task_user');
                /*
                if(request()->get('state')==1)
                    $wf->finished = true;
                elseif(request()->get('state')==0)
                    $wf->finished = false;*/
                if (request()->get('state')) {
                    $wf->finished = request()->get('state');
                }
                $wf->update();
                $task->deadline = request()->get('deadline');
                $task->name = request()->get('task_name');
                $task->parent = request()->get('parent');
                $temp = request()->get('cols');
                $task->cols = json_encode($temp);
                $task->update();

                $data['closeModal'] = true;
                $data['reloadPage'] = true;
                return response()->json($data);
            }
        }

        return view('task.edit-task', ['tasks'=>$tasks,'task'=>$task, 'cols'=>$cols]);
    }

    public function duplicateTask()
    {
        $data = request()->get('postData');
        $id = $data['id'];
        $task = TaskRow::find($id);
        $project = $task->project;

        // check rights on owner profile
        $taskProfile = $project->author()->first();
        if (!$this->Acl->getRights(get_class($taskProfile), $taskProfile->id, 3)
            && $task->users_id != auth('web')->user()->id) {
            return response(view('errors.403'), 403);
        }

        $template = $project->template;
        if ($task) {
            $wf = $task->workflow;
            $newWf = null;
            if ($wf) {
                $newWf = $wf->replicate();
                $newWf->save();
            }
            $new = $task->replicate();
            if ($wf) {
                $new->workflows_id = $newWf->id;
            }
            $new->save();

            $cols = $project->template->getCols();
            $nbCols = 3; // default colsnumber
            $nbCols += count($cols);
            if ($project->template->linked) {
                $nbCols += 3;
            }

            return response()->json([
                'inserted' => true,
                'body' => view(
                    'task.row',
                    [
                        'task' => $new,
                        'project' => $project,
                        'template' => $project->template,
                        'nbCols' => $nbCols,
                        'cols' => $cols,
                        'nbDirectTasks' => $project->directTasks()->count(),
                    ]
                )->render()
            ]);
        }
        return response()->json(['inserted'=>false]);
    }

    public function linkTask($projectId, $taskId)
    {
        $tasks = TaskRow::where('tables_tasks_id', $projectId)
            ->where('id', '<>', $taskId)
            ->where('parent', null)
            ->pluck('name', 'id');

        if (request()->isMethod('POST')) {
            $parent = request()->get('parent');
            $task = TaskRow::find($parent);
            $t = TaskRow::find($taskId);

            $project = $t->project;
            $taskProfile = $project->author()->first();
            if ($task->tables_tasks_id != $t->tables_tasks_id
                || (!$this->Acl->getRights(get_class($taskProfile), $taskProfile->id, 3)
                    && $taskProfile->users_id != auth('web')->user()->id
                )
            ) {
                return response(view('errors.403'), 403);
            }

            if ($task) {
                if ($t) {
                    $t->parent = $parent;
                    $t->update();
                }
                \DB::table('tables_rows')->where('parent', $taskId)->update(['parent'=>$parent]);
                return response()->json(['closeModal'=>true, 'reloadPage' => true]);
            }
        }
        return view('task.link', ['tasks'=>$tasks]);
    }

    public function sub($projectId)
    {
        $tasks = TaskRow::where('tables_tasks_id', $projectId)->where('parent', null)->pluck('name', 'id');

        $project = TaskTable::find($projectId);
        $author = $project->author;
        if (!$this->Acl->getRights(get_class($author), $author->id, 4)) {
            return response(view('errors.403'), 403);
        }

        if (request()->isMethod('POST')) {
            $parent = request()->get('parent');

            $wfId = null;
            if ($project->template->linked) {
                $wf = new Workflow();
                $wf->users_id = auth()->guard('web')->user()->id;
                $wf->instances_id = session('instanceId');
                $wf->save();
                $wfId = $wf->id;
            }
            $tr = new TaskRow();
            $tr->users_id = auth('web')->user()->id;
            $tr->deadline = date('Y-m-d');
            $tr->name = "";
            $tr->workflows_id = $wfId;
            $tr->tables_tasks_id = $projectId;
            $tr->parent = $parent;
            $tr->save();
            if ($project->template->linked) {
                $notif = new Notif();
                $notif->instances_id = session('instanceId');
                $notif->author_id = $wf->users_id;
                $notif->author_type = "App\\User";
                $notif->type = "assign_task";
                $notif->user_from = auth()->guard('web')->user()->id;
                $notif->read = false;
                $notif->parameter = json_encode(array('task_id'=>$tr->id));
                $notif->save();
            }
            return response()->json(['closeModal'=>true, 'reloadPage' => true]);
        }
        return view('task.sub', ['tasks'=>$tasks]);
    }

    public function comment($taskId)
    {
        $task = TaskRow::find($taskId);
        $user = auth()->guard('web')->user();
        if ($task) {
            $comments = $task->comments;
            if (request()->isMethod('POST')) {
                $validator = Validator::make(request()->all(), [
                    'comment' => 'required',
                ]);
                if ($validator->fails()) {
                    $data['errors'] = $validator->messages();
                    return response()->json($data);
                } else {
                    $comment = new Comment();
                    $comment->instances_id = session('instanceId');
                    $comment->content = request()->get('comment');
                    $comment->author_id = $user->id;
                    $comment->author_type = "App\\User";
                    $comment->users_id = $user->id;
                    $comment->post_id = $taskId;
                    $comment->post_type = "App\\TaskRow";
                    $comment->save();
                    return response()->json(['closeModal'=>true]);
                }
            }
            return view('task.comment', ['task'=>$task, 'comments'=>$comments]);
        }
        return view('errors.404');
    }

    public function validation()
    {
        $user = auth()->guard('web')->user();
        $actions = \DB::table('workflow_details_actions')
            ->select(\DB::raw('workflow_details_actions.*, workflows.wf_datas, workflow_actions.action_type'))
            ->join('workflows', 'workflows.id', '=', 'workflow_details_actions.workflows_id')
            ->join('workflow_actions', 'workflow_actions.id', '=', 'workflow_details_actions.workflow_actions_id')
            ->where('workflows.users_id', $user->id)
            ->where('workflows.type', 'validate_file')
            ->get();
        $actions = \App\WorkflowDetailsAction::hydrate($actions->toArray());

        $workflows = $user->workflows()
            ->where('type', '=', 'validate_file')
            ->orderBy('created_at', 'desc')
            ->with(['detailsActions', 'detailsActions.actions'])
            ->get();

        $data = [
            'actions' => $actions,
            'workflows' => $workflows,
        ];

        return view('task.validation', $data);
    }

    public function revive()
    {
        $user = auth()->guard('web')->user();
        if (request()->isMethod('POST')) {
            $det = WorkflowDetailsAction::findOrFail(request()->get('action'));
            $action = $det->actions;
            $wf = json_decode($det->workflow->wf_datas, true);
            $mediaId = $wf['mediasIds'][0];
            $array = [
                "workflow_type" => $action->notif_slug,
                "file_id" => $mediaId,
                "workflow_action_id" => $det->id
            ];
            $notif = Notif::where("user_from", $user->id)
                ->where('type', 'workflow')
                ->where('instances_id', session('instanceId'))
                ->where('parameter', json_encode($array))
                ->first();
            $notif->read=0;
            $notif->update();
            // return response()->json($det);
        }
    }
}
