<?php

namespace App;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use CacheQueryBuilder;

    protected $table = "comments";

    protected $fillable = ['author_id', 'author_type', 'post_id', 'post_type'];


    public static function boot()
    {
        parent::boot();

        self::deleting(function ($comment) {
            $comment->liked()->delete();
            $comment->replies()->delete();

            // delete notification attached to this media
            $notification = Notif::where('parameter', 'LIKE', '%id_comment":"'.$comment->id.'"%')->delete();
        });
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }


    public function users()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }


    public function author()
    {
        return $this->morphTo();
    }

    public function liked()
    {
        return $this->morphMany('App\Like', 'liked');
    }

    public function post()
    {
        return $this->morphTo();
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'comments_id');
    }


    /**
     * Get Comments for Actuality
     *
     * @param string $typePost
     * @param int $idPost
     * @return object request eloquent
     */
    public static function getCommentActuality($typePost, $idPost, $all = false)
    {
        $config = config('netframe');
        $query = \DB::table(static::getTableName());
        $query->where('post_type', '=', $typePost)
              ->where('post_id', '=', $idPost)
              ->orderBy('updated_at', 'asc');

        $totalComments = $query->count();

        if ($all) {
            return $query->get();
        } else {
            return array($query->take($config['number_comment'])->get(), $totalComments);
        }
    }
}
