<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TaskTable extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tables_tasks';

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($project) {

            $project->tasks()->get()->each(function ($task) {
                $task->delete();
            });


            $project->shares()->get()->each(function ($share) {
                $share->delete();
            });


            $project->posts()->get()->each(function ($post) {
                $post->delete();
            });

            $project->liked()->delete();

            $project->comments()->get()->each(function ($comment) {
                $comment->delete();
            });


            // delete notifications
            $notification = Notif::where('type', '=', 'likeContent')
                ->where('parameter', 'LIKE', '%TaskTable%element_id":"'.$project->id.'"%')
                ->delete();
            $notification = Notif::where('type', '=', 'comment')
                ->where('parameter', 'LIKE', '%TaskTable%post_id":"'.$project->id.'"%')
                ->delete();
            $notification = Notif::where('type', '=', 'share')
                ->where('parameter', 'LIKE', '%TaskTable%post_id":"'.$project->id.'"%')
                ->delete();
        });
    }

    public function getType()
    {
        return 'tasks';
    }

    public function getNameDisplay()
    {
        return $this->name;
    }

    public function users()
    {
        return $this->hasOne('App\User', 'id', 'users_id');
    }

    /**
     * morph relation with news publications in newsfeeds
     */
    public function posts()
    {
        return $this->morphMany('App\NewsFeed', 'post');
    }

    /**
     * morph relation with news publications in newsfeeds
     */
    public function post()
    {
        return $this->morphOne('App\NewsFeed', 'post');
    }

    public function tasks()
    {
        return $this->hasMany('App\TaskRow', 'tables_tasks_id', 'id');
    }

    public function directTasks()
    {
        return $this->tasks()->whereNull('parent');
    }

    /**
     * morph relation with news is liked
     */
    public function liked()
    {
        return $this->morphMany('App\Like', 'liked');
    }

    /**
     * morph relation with shares
     */
    public function shares()
    {
        return $this->morphMany('App\Share', 'post');
    }

    /**
     * morph relation with news comments
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'post');
    }

    /**
     * morph relation with news comments for 2 lasts comments
     */
    public function lastComments()
    {
        $comments = $this->morphMany('App\Comment', 'post')->whereNull('comments_id');
        if ($comments->count() > config('netframe')['number_comment']) {
            $skip = $comments->count() - config('netframe')['number_comment'];
            return $this
            ->morphMany('App\Comment', 'post')
            ->whereNull('comments_id')
            ->skip($skip)
            ->take(config('netframe')['number_comment']);
        } else {
            return $this
            ->morphMany('App\Comment', 'post')
            ->whereNull('comments_id')
            ->take(config('netframe')['number_comment']);
        }
    }

    public function medias()
    {
        return $this->belongsToMany('App\Media', 'news_has_medias', 'news_id', 'medias_id');
    }

    public function template()
    {
        return $this->hasOne('App\Template', 'id', 'tables_templates_id');
    }

    public function tags()
    {
        return $this->morphToMany('App\Tag', 'taggable');
    }

    public function tagsList($onlyIds = false)
    {
        $tagsTab = [];
        foreach ($this->tags as $tag) {
            if ($onlyIds) {
                $tagsTab[] =$tag->id;
            } else {
                $tagsTab[$tag->id] = $tag->name;
            }
        }

        return $tagsTab;
    }

    /**
     * morph relation with news author (profile)
     */
    public function author()
    {
        return $this->morphTo();
    }


    public function getUrl()
    {
        return url()->route('task.project', ['project' => $this->id]);
    }

    public function onlyImages()
    {
        return false;
    }

    public function todoTasks()
    {
        return $this->tasks()->whereHas('workflow', function ($query) {
            $query->where('finished', '=', 2);
        });
    }

    public function inProgressTasks()
    {
        return $this->tasks()->whereHas('workflow', function ($query) {
            $query->where('finished', '=', 0);
        });
    }

    public function finishedTasks()
    {
        return $this->tasks()->whereHas('workflow', function ($query) {
            $query->where('finished', '=', 1);
        });
    }

    public function lateTasks()
    {
        return $this->tasks()->where('deadline', '<', date('Y-m-d'));
    }
}
