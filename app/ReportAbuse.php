<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReportAbuse extends Model
{

    protected $table = "report_abuses";

    protected $fillable = array();

    protected $guarded = array();


    public static function getTableName()
    {
        return with(new static())->getTable();
    }


    public function user()
    {
        return $this->belongsToMany('App\User', 'users_has_report_abuses', 'report_abuses_id', 'users_id')
                    ->withTimestamps();
    }


    /**
     * Check if report abuse exist and is not the same user who run report
     *
     * @param integer $authId
     * @param integer $userProperty id to property from post
     * @param integer $postId
     * @param string $postType
     * @param integer $typeAbuse key of abuse type traitment
     * @return false|object|null false if use already sign report
     */
    public function reportExist($authId, $userProperty, $postId, $postType, $typeAbuse = null)
    {

        $query = $this->select('*')
                  ->leftJoin('users_has_report_abuses AS tb2', $this->table.'.id', '=', 'tb2.report_abuses_id')
                  ->where($this->table.'.users_id_property', '=', $userProperty)
                  ->where($this->table.'.post_id', '=', $postId)
                  ->where($this->table.'.post_type', '=', $postType)
                  ->where($this->table.'.type_abuse', '=', $typeAbuse)
                  ->where('tb2.users_id', '<>', $authId);

        $queryAlready = $this->select('*')
                  ->leftJoin('users_has_report_abuses AS tb2', $this->table.'.id', '=', 'tb2.report_abuses_id')
                  ->where($this->table.'.users_id_property', '=', $userProperty)
                  ->where($this->table.'.post_id', '=', $postId)
                  ->where($this->table.'.post_type', '=', $postType)
                  ->where('tb2.users_id', '=', $authId);

        if ($queryAlready->count() > 0) {
            return false;
        } else {
            return $query->first();
        }
    }
}
