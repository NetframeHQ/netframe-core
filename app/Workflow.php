<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Events\PostNotif;
use App\Events\NewPost;

class Workflow extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workflows';

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($workflow) {
            // notify user
            if ($workflow->users_id != auth()->user()->id) {
                if ($workflow->media() != null) {
                    $notifParameter = [
                        'file_id' => $workflow->media()->id,
                        'file_name' => $workflow->media()->name,
                    ];
                    $notifArray = [
                        'instances_id'   => session('instanceId'),
                        'author_id'      => $workflow->users_id,
                        'author_type'    => 'App\\User',
                        'type'           => 'deleteWorkflow',
                        'user_from'      => auth()->user()->id,
                        'parameter'      => json_encode($notifParameter),
                        'read'           => 0,
                        'created_at'     => new \DateTime(),
                        'updated_at'     => new \DateTime()
                    ];
                    event(new PostNotif($notifArray));
                }
            }

            // delete workflow details actions linked
            $workflow->detailsActions()->get()->each(function ($action) {
                $action->delete();
            });

            // delete tasks row linked
            $workflow->tasks()->get()->each(function ($task) {
                $task->delete();
            });

            $workflow->deleteMedia();
        });
    }

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    public function detailsActions()
    {
        return $this->hasMany('App\WorkflowDetailsAction', 'workflows_id', 'id')->orderBy('action_order');
    }

    public function actions()
    {
        return $this
            ->belongsToMany('App\WorkflowAction', 'workflow_details_actions', 'workflows_id', 'workflow_actions_id')
            ->withPivot('action_order')
            ->orderBy('workflow_details_actions.action_order')
            ->withTimestamps();
    }

    public function tasks()
    {
        return $this->belongsTo('App\TaskRow', 'id', 'workflows_id');
    }

    public function deleteMedia()
    {
        $media = $this->media();
        if ($media != null) {
            $media->under_workflow = 0;
            $media->save();
            $media->delete();
        }
    }

    public function media()
    {
        if ($this->type == 'validate_file') {
            $parameters = json_decode($this->wf_datas);
            $mediaId = $parameters->mediasIds[0];
            return Media::find($mediaId);
        }

        return null;
    }

    public function getNotifiableUsers()
    {
    }

    public function proccedAction($currentActionId = 0)
    {
        // check if group action , if group action ckeck if action complete before load next
        // input workflow  $this = workflow

        if ($currentActionId != 0) {
            $currentAction = $this->detailsActions()->where('id', '=', $currentActionId)->first();
            $actionOrder = $currentAction->action_order;
            $checkCurrentGroup = ($currentAction->group_action == 1) ? true : false;
            $checkValidate = (in_array($currentAction->actions->action_type, ['group_valid', 'user_valid']))
                ? true
                : false;
        } else {
            $actionOrder = 1;
            $checkCurrentGroup = false;
            $checkValidate = false;
            $proceedNext = true;
        }

        // check if group action complete
        if ($checkCurrentGroup) {
            $actions = $this
                ->detailsActions()
                ->where('action_validate', '=', 0)
                ->where('action_order', '=', $actionOrder)
                ->get();
            if ($actions->count() == 0) {
                $proceedNext = true;
            } else {
                $proceedNext = false;
            }
        } else {
            $proceedNext = true;
        }

        // if validate action, check results to decide proceed next
        if ($checkValidate) {
            if ($currentAction->actions->action_type == 'user_valid') {
                // check result
                $resultAction = json_decode($currentAction->action_result);
                if ($resultAction->result == 'decline') {
                    $proceedNext = false;
                }
            } elseif ($currentAction->actions->action_type == 'group_valid') {  // alluser have answer
                // check result of all if all have validate
                $actions = $this
                    ->detailsActions()
                    ->where('action_validate', '=', 0)
                    ->where('action_order', '=', $actionOrder)
                    ->get();

                if ($actions->count() == 0) {
                    $actions = $this
                        ->detailsActions()
                        ->where('action_validate', '=', 1)
                        ->where('action_order', '=', $actionOrder)
                        ->get();

                    $allValidate = true;

                    foreach ($actions as $subAction) {
                        $subActionResult = json_decode($subAction->action_result);

                        if ($subActionResult->result == 'decline') {
                            $allValidate = false;
                        }
                    }

                    $proceedNext = $allValidate;
                }
            }
        }

        // proceed next action
        if ($proceedNext === true) {
            $action = $this->detailsActions()->where('action_validate', '=', 0)->orderBy('action_order')->first();
            // test if next action exists
            if ($action != null) {
                $this->doAction($action);

                // launch notifications
                $notifActions = $this->detailsActions()->where('action_order', '=', $action->action_order)->get();
                $this->manageNotif($notifActions);
            }
        }
    }

    private function doAction($action)
    {
        if ($action->actions->action_type == 'destination_folder') {
            $wfDatas = json_decode($this->wf_datas);
            $destFolderId = $action->action_parameters;
            $destFolder = MediasFolder::find($destFolderId);
            $folderProfile = $destFolder->profile;

            if ($destFolder != null) {
                $medias = Media::whereIn('id', $wfDatas->mediasIds)->get();
                foreach ($medias as $media) {
                    // detach media of its current profile and current directory,
                    $currentProfile = $media->mainProfile();
                    if ($currentProfile != null) {
                        $currentProfile->medias()->detach($media->id);
                    }

                    // attach it to new profile and new directory
                    $folderProfile->medias()->attach($media->id, [
                        'medias_folders_id' => $destFolder->id,
                    ]);
                }
            }
            $action->action_validate = 1;
            $action->save();
            $this->proccedAction($action->id);
        }

        if ($action->actions->action_type == 'lock_file') {
            $wfDatas = json_decode($this->wf_datas);
            $medias = Media::whereIn('id', $wfDatas->mediasIds)->get();
            foreach ($medias as $media) {
                $media->read_only = 1;
                $media->save();
            }
            $action->action_validate = 1;
            $action->save();
            $this->proccedAction($action->id);
        }

        if ($action->actions->action_type == 'publish_file') {
            $wfDatas = json_decode($this->wf_datas);
            $medias = Media::whereIn('id', $wfDatas->mediasIds)->get();
            foreach ($medias as $media) {
                $media->under_workflow = 0;
                $media->save();
            }

            // get profile
            $profile = $medias[0]->author()->first();

            // publish post
            // prepare medias ids for post
            $post = new News();
            $post->users_id = auth()->guard('web')->user()->id;
            $post->instances_id = session('instanceId');
            $post->author_id = $profile->id;
            $post->author_type = get_class($profile);
            $post->content = '';
            $post->confidentiality = $profile->confidentiality;
            $post->save();

            foreach ($wfDatas->mediasIds as $mediaId) {
                $post->medias()->attach($mediaId);
            }
            // link media state
            Media::whereIn('id', $wfDatas->mediasIds)->update(['linked' => 1]);

            // newsfeed
            $post->author_id = $profile->id;
            $post->author_type = get_class($profile);
            $post->true_author_id = $profile->id;
            $post->true_author_type = get_class($profile);
            event(new NewPost("news", $post, null, $wfDatas->mediasIds));
        }
    }

    private function manageNotif($workflowActions)
    {
        foreach ($workflowActions as $wfAction) {
            switch ($this->type) {
                case 'validate_file':
                    $wfDatas = json_decode($this->wf_datas);

                    foreach ($wfDatas->mediasIds as $mediaId) {
                        $notifParameter = [
                            'workflow_type' => $wfAction->actions->notif_slug,
                            'file_id' => $mediaId,
                            'workflow_action_id' => $wfAction->id,
                        ];

                        if ($wfAction->actions->notif_slug != null) {
                            $target_user_id = ($wfAction->users_id != null) ? $wfAction->users_id : $this->users_id;

                            $notification = [
                                'instances_id' => $wfAction->instances_id,
                                'author_id' => $target_user_id,
                                'author_type' => 'App\\User',
                                'type' => 'workflow',
                                'user_from' => $this->users_id,
                                'parameter' => json_encode($notifParameter),
                            ];
                            event(new PostNotif($notification));
                        }
                    }

                    if ($wfAction->actions->is_final_action) {
                        $wfAction->action_validate = 1;
                        $wfAction->save();

                        $this->finished = 1;
                        $this->save();
                    }

                    break;
            }
        }
    }

    public function mergePostedFields()
    {
        $fieldsRefs = request()->get('actionSlug');

        $fieldsActions = [];
        foreach ($fieldsRefs as $field) {
            $fieldsActions[] = [
                'action' => request()->get('actionType_' . $field),
                'action_datas' => request()->get('actionDatas_' . $field),
                'action_date' => (request()->has('actionDate_'.$field)) ? request()->get('actionDate_'.$field) : null,
            ];
        }

        return $fieldsActions;
    }

    public function makeNew($type, $fields, $datas)
    {
        $this->type = $type;
        $this->users_id = auth()->guard('web')->user()->id;
        $this->instances_id = session('instanceId');
        $this->wf_datas = json_encode($datas);
        $this->save();

        $this->{$type}($this, $fields, $datas);
        $this->proccedAction();
    }

    // phpcs:ignore PSR1.Methods.CamelCapsMethodName
    private function validate_file($workflow, $fields, $datas)
    {
        $order = 1;
        foreach ($fields as $action) {
            $wfAction = WorkflowAction::where('action_type', '=', $action['action'])->first();
            if ($wfAction != null) {
                switch ($wfAction->action_type) {
                    case 'group_valid':
                        foreach ($action['action_datas'] as $user_id) {
                            $wfDetails = new WorkflowDetailsAction();
                            $wfDetails->users_id = $user_id;
                            $wfDetails->instances_id = session('instanceId');
                            $wfDetails->action_order = $order;
                            $wfDetails->action_date = $action['action_date'];
                            $wfDetails->workflows_id = $workflow->id;
                            $wfDetails->workflow_actions_id = $wfAction->id;
                            $wfDetails->save();
                        }
                        break;

                    case 'user_valid':
                        $wfDetails = new WorkflowDetailsAction();
                        $wfDetails->users_id = $action['action_datas'];
                        $wfDetails->instances_id = session('instanceId');
                        $wfDetails->action_order = $order;
                        $wfDetails->action_date = $action['action_date'];
                        $wfDetails->workflows_id = $workflow->id;
                        $wfDetails->workflow_actions_id = $wfAction->id;
                        $wfDetails->save();
                        break;

                    case 'publish_file':
                        $wfDetails = new WorkflowDetailsAction();
                        $wfDetails->instances_id = session('instanceId');
                        $wfDetails->action_order = $order;
                        $wfDetails->workflows_id = $workflow->id;
                        $wfDetails->workflow_actions_id = $wfAction->id;
                        $wfDetails->action_parameters = $action['action_datas'];
                        $wfDetails->save();
                        break;

                    case 'lock_file':
                        $wfDetails = new WorkflowDetailsAction();
                        $wfDetails->instances_id = session('instanceId');
                        $wfDetails->action_order = $order;
                        $wfDetails->workflows_id = $workflow->id;
                        $wfDetails->workflow_actions_id = $wfAction->id;
                        $wfDetails->action_parameters = $action['action_datas'];
                        $wfDetails->save();
                        break;

                    case 'destination_folder':
                        $wfDetails = new WorkflowDetailsAction();
                        $wfDetails->instances_id = session('instanceId');
                        $wfDetails->action_order = $order;
                        $wfDetails->workflows_id = $workflow->id;
                        $wfDetails->workflow_actions_id = $wfAction->id;
                        $wfDetails->action_parameters = $action['action_datas'];
                        $wfDetails->save();
                        break;
                }
            }
            $order++;
        }
    }
}
