<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tables_templates';

    public function getCols()
    {
        return json_decode($this->cols, true);
    }

    public function projects()
    {
        return $this->hasMany('App\TaskTable', 'tables_templates_id', 'id');
    }

    public function getMediaCol()
    {
        $cols = $this->getCols();
        foreach ($cols as $colName => $colParam) {
            if ($colParam['type'] == 'file') {
                return $colName;
            }
        }
        return null;
    }
}
