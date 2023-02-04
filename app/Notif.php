<?php

namespace App;

use App\Support\Database\CacheQueryBuilder;
use Illuminate\Database\Eloquent\Model;

class Notif extends Model
{
    use CacheQueryBuilder;

    const TYPE_ASK_ANGEL = 'askAngel';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    //protected $fillable = array('id_foreign', 'type_foreign', 'type', 'parameter');
    protected $fillable = array('*');

    public $timestamps = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    //public $timestamps = false;

    public function findByAthorId($author_id, $user_from, $type = '')
    {
        $query = Notif::where(array(
            'instances_id'  => session('instanceId'),
            'author_id'     => $author_id,
            'user_from'     => $user_from,
            'type'          => $type,
        ));

        return $query;
    }

    public function findByAuthorId($author_id, $user_from, $type = '')
    {
        $query = Notif::where(array(
            'instances_id'  => session('instanceId'),
            'author_id'     => $author_id,
            'user_from'     => $user_from,
            'type'          => $type,
        ));

        return $query;
    }

    public function insertAuthor($author_id, $user_from, $parameter, $type, $author_type = 'user')
    {
        $query = \DB::table('notifications')->insert(array(
            'instances_id'  => session('instanceId'),
            'author_id'     => $author_id,
            'author_type'   => "App\\".ucfirst($author_type),
            'type'          => $type,
            'user_from'     => $user_from,
            'parameter'     => json_encode($parameter),
            'read'          => 0,
            'created_at'    => new \DateTime(),
            'updated_at'    => new \DateTime()
        ));

        return $query;
    }

    public function findWaitingNotifByUserId($limit, $type = null)
    {
        $query = Notif::where('instances_id', '=', session('instanceId'))
            ->where('author_type', '=', 'App\\User')
            ->where('author_id', '=', auth()->guard('web')->user()->id)
            ->where(function ($whereType) use ($type) {
                if ($type == null) {
                    //$whereType->where('type', '!=', 'askFriend');
                } else {
                    $whereType->where('type', '=', $type);
                }
            })
            ->orderBy('notifications.created_at', 'desc')
            ->orderBy('id', 'desc')
            ->take($limit[1])
            ->offset($limit[0])
            ->get([
                'notifications.id',
                'notifications.created_at',
                'author_id',
                'user_from',
                'type',
                'author_type',
                'parameter',
                'read'
            ]);

        return $query;
    }

    public static function markReadForUser()
    {
        $query = Notif::where('instances_id', '=', session('instanceId'))
            ->where('author_type', '=', 'App\\User')
            ->where('author_id', '=', auth()->guard('web')->user()->id)
            ->where('read', '=', 0)
            ->update(['read' => 1]);
    }
}
