<?php
namespace App\Http\Controllers\Workflow;

use App\Http\Controllers\BaseController;
use App\WorkflowAction;
use App\User;
use App\Profile;
use App\WorkflowDetailsAction;
use App\Workflow;
use App\Notif;
use App\Events\PostNotif;
use App\Http\Controllers\User\MediaController;

class WorkflowController extends BaseController
{
    public function listActions($objectType)
    {
        $actions = WorkflowAction::where('object_type', '=', $objectType)
            ->whereActive('1')
            ->orderBy('display_order')
            ->pluck('action_type', 'action_type')
            ->toArray();

        // replace action slug with translation
        \App::setLocale(auth('web')->user()->lang);
        foreach ($actions as $key => $slug) {
            $actions[$key] = trans('workflow.actions.'.$slug);
        }

        $dataView = [];
        $dataView['actions'] = $actions;
        $dataView['fieldSlug'] = rand();

        $view = view('workflow.partials.actions', $dataView)->render();

        return response()->json([
            'view' => $view,
        ]);
    }

    public function chooseAction()
    {
        \App::setLocale(auth('web')->user()->lang);
        $actionType = request()->get('actionType');
        $actionSlug = request()->get('actionSlug');
        $profileId = request()->get('profileId');
        $profileType = request()->get('profileType');

        $action = WorkflowAction::where('action_type', '=', $actionType)->first();
        if ($action != null) {
            // bluid sub view for action
            $dataView = [];
            $dataView['actionSlug'] = $actionSlug;
            $dataView['fieldClass'] = 'wf_'.$actionSlug;
            if ($action->action_type == 'destination_folder') {
                $dataView['profileId'] = $profileId;
                $dataView['profileType'] = $profileType;

                $currentProfileModel = Profile::gather($profileType);
                $currentProfile = $currentProfileModel::find($profileId);
                if ($currentProfile == null
                    || !$this->Acl->getRights(get_class($currentProfile), $currentProfile->id, 4)) {
                    return response(view('errors.403'), 403);
                }

                $rootFolders = $currentProfile->mediasFolders()->whereNull('medias_folders_id')->get();
                $folders = [];
                $folders[0] = trans('xplorer.defaultFolders.__root_folder');
                foreach ($rootFolders as $folder) {
                    $level = 0;
                    if ($folder->default_folder == 0) {
                        $folders[$folder->id] = $folder->name;
                    } else {
                        $folders[$folder->id] = trans('xplorer.defaultFolders.'.$folder->name);
                    }

                    $folders = array_replace_recursive(
                        $folders,
                        $folder->formatFolderTree($level, null)
                    );
                }
                $dataView['folders'] = $folders;
                $dataView['NetframeProfiles'] = session('allProfiles');
            }

            $view = view('workflow.actions.'.$action->action_view, $dataView)->render();

            return response()->json([
                'view' => $view,
                'field_name' => $action->action_type.'_'.$actionSlug,
                'action_type' => $action->action_type,
                'target_element' => '.wf_'.$actionSlug,
                'is_final' => $action->is_final_action
            ]);
        } else {
            return response()->json([
                'code'      =>  401,
                'message'   =>  'no action referenced with id '.$actionType
            ], 401);
        }
    }

    public function searchUsers()
    {
        $searchTerms = htmlentities(request()->get('q'));
        $results = User::select('id', 'name', 'firstname')
            ->whereHas('instances', function ($qI) {
                $qI->where('id', '=', session('instanceId'));
            })
            ->where('active', '=', '1')
            ->where(function ($w) use ($searchTerms) {
                $w->orWhere('name', 'like', '%'.$searchTerms.'%')
                  ->orWhere('firstname', 'like', '%'. $searchTerms.'%');
            })
            ->get(array('id', 'name', 'firstname'));

        $tabRes = [];
        $searchInRes = false;
        foreach ($results as $result) {
            $objUser = new \stdClass();
            $objUser->id = $result->id;
            $objUser->text = $result->firstname.' '.$result->name;

            if ($result->text == $searchTerms) {
                $searchInRes = true;
            }
            $tabRes[] = $objUser;
        }

        /*
        if($searchInRes == false){
            $objTag = new \stdClass();
            $objTag->id = $searchTerms;
            $objTag->text = $searchTerms;
            $tabRes[] = $objTag;
        }
        */

        if ($results->count() != 0) {
            $data['results'] = $tabRes;
            return response()->json($data);
        } else {
            $data['results'][0] = new \stdClass();
            $data['results'][0]->id = $searchTerms;
            $data['results'][0]->text = $searchTerms;
            return response()->json($data);
        }
    }

    public function manageAnswer()
    {
        $wfActionId = request()->get('actionId');
        $notifId = request()->get('notifId');

        $action = WorkflowDetailsAction::find($wfActionId);
        $workflow = $action->workflow;

        if ($action != null && $action->action_validate == 0 && $action->users_id == auth()->guard('web')->user()->id) {
            switch ($action->actions->action_type) {
                case 'group_valid':
                case 'user_valid':
                    $validateStatus = request()->get('validateStatus');
                    $fileId = request()->get('fileId');

                    $action->action_validate = 1;

                    if ($validateStatus == 'accept') {
                        $action_result = [
                            'result' => 'accept',
                        ];
                        $notifParameter = [
                            'workflow_type' => 'answerValidateFileAccept',
                            'file_id' => $fileId
                        ];
                    } elseif ($validateStatus == 'decline-send') {
                        $action_result = [
                            'result' => 'decline',
                            'comment' => json_encode(request()->get('reason'))
                        ];
                        $notifParameter = [
                            'workflow_type' => 'answerValidateFileDecline',
                            'reason' => request()->get('reason'),
                            'file_id' => $fileId
                        ];
                    }

                    $action->action_result = json_encode($action_result);

                    // make notification for workflow owner
                    $notification = [
                        'instances_id' => $action->instances_id,
                        'author_id' => $workflow->users_id,
                        'author_type' => 'App\\User',
                        'type' => 'workflow',
                        'user_from' => $action->users_id,
                        'parameter' => json_encode($notifParameter),
                    ];
                    event(new PostNotif($notification));

                    break;
            }
            $action->save();
            $workflow->proccedAction($action->id);

            // delete notif
            $notif = Notif::find($notifId);
            $notif->delete();

            $resultAnswer = [
                'result' => 'success',
                'notifId' => $notifId
            ];

            return response()->json($resultAnswer);
        } else {
            return response(view('errors.403'), 403);
        }
    }

    public function delete($id)
    {
        $wf = auth()->user()->workflows()->where('id', '=', $id)->first();
        if ($wf == null) {
            return response(view('errors.403'), 403);
        } else {
            $wf->delete();

            return response()->json([
                'delete' => true,
                'targetId' => '#wf-' . $id,
            ]);
        }
    }
}
