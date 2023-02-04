<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class MediasArchive extends Model
{
    
    public static function boot()
    {
        parent::boot();

        self::deleting(function ($media) {
            $file_path = $media->file_path;
            $feed_path = $media->feed_path;
            $thumb_path = $media->thumb_path;
            if ($file_path != null && file_exists($file_path)) {
                unlink($file_path);
            }
            if ($feed_path != null && file_exists($feed_path)) {
                unlink($feed_path);
            }
            if ($thumb_path != null && file_exists($thumb_path)) {
                unlink($thumb_path);
            }
        });
    }
    
    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    
    public function media()
    {
        return $this->belongsTo('App\Media', 'medias_id', 'id');
    }
}
