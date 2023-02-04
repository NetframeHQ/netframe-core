<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskRow extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tables_rows';

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($row) {
            /*
            $wf = $row->workflow;
            $row->workflows_id = null;
            $row->update();

            if($wf)
                $wf->delete();
            */
            // delete sub tasks
            $tasks = TaskRow::where('parent', $row->id)->get();
            foreach ($tasks as $task) {
                //$task->parent = null;
                $task->delete();
            }
            $row->comments()->get()->each(function ($comment) {
                $comment->delete();
            });

            /*
            $project = $row->project;
            $project->comments()->get()->each(function($comment){
                $comment->delete();
            });
            */
        });
    }


    // public function user()
    // {
    //     return $this->hasOne('App\User', 'id', 'users_id');
    // }

    public function workflow()
    {
        return $this->hasOne('App\Workflow', 'id', 'workflows_id');
    }

    public function project()
    {
        return $this->hasOne('App\TaskTable', 'id', 'tables_tasks_id');
    }

    public function childs()
    {
        return $this->hasMany('App\TaskRow', 'parent', 'id');
    }

    public function getCol($key)
    {
        $cols = json_decode($this->cols, true);
        return $cols[$key] ?? "&nbsp;";
    }

    public function setCol($key, $value)
    {
        $cols = json_decode($this->cols, true);
        $cols[$key] = $value;
        $this->cols = json_encode($cols);
        $this->save();
    }

    /**
     * morph relation with news comments
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'post');
    }
}
