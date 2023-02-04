<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkflowAction extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workflow_actions';

    public function workflows()
    {
        return $this
            ->belongsToMany('App\WorkflowAction', 'workflow_details_ations', 'workflow_actions_id', 'workflows_id')
            ->withPivot('action_order')
            ->orderBy('workflow_details_ations.action_order')
            ->withTimestamps();
    }

    public function detailsActions()
    {
        return $this->hasMany('App\WorkflowDetailsAction', 'workflow_actions_id', 'id');
    }

    public function getActions($type)
    {
        $actions = $this->where('object_type', '=', $type)->get();

        return $actions;
    }
}
