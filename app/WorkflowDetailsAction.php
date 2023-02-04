<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkflowDetailsAction extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    public function actions()
    {
        return $this->belongsTo('App\WorkflowAction', 'workflow_actions_id', 'id');
    }

    public function workflow()
    {
        return $this->belongsTo('App\Workflow', 'workflows_id', 'id');
    }

    public function getNotifiableUsers()
    {
    }

    public function proceedResult()
    {
        // get result from notification box and record result, in some case, send notifications


        // proceed next step or not
        $workflow = $this->workflow;
        $workflow->proccedAction($this->id);
    }

    public function destinationFolder()
    {
        if ($this->action_parameters != null) {
            $mediaFolder = MediasFolder::find($this->action_parameters);
            return $mediaFolder;
        } else {
            return null;
        }
    }

    public function destinationFolderProfile()
    {
        $folder = $this->destinationFolder();
        if ($folder != null) {
            return $folder->profile;
        } else {
            return null;
        }
    }
}
