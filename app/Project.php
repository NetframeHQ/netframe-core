<?php

namespace App;

use App\Support\Database\CacheQueryBuilder;
use App\BaseProfile;
use App\Observers\Searchable;

class Project extends BaseProfile
{
    use CacheQueryBuilder;

    /*
     * add Elasticsearch as observer
    */
    use Searchable;

    protected $table = 'projects';
    protected $fillable = array(
        'title',
        'description',
        'confidentiality'
    );
    protected $type = Profile::TYPE_PROJECT;
    protected $instanceRelation = 'projects';

    // morph relation to profiles
    /*
    public function communities()
    {
        return $this->morphedByMany('App\Community', 'profils_has_project');
    }

    public function houses()
    {
        return $this->morphedByMany('App\House', 'profils_has_project');
    }
    */

    public function medias()
    {
        return $this->belongsToMany('App\Media', 'projects_has_medias', 'projects_id', 'medias_id')
            ->withPivot([
                'profile_image',
                'favorite'
            ])
            ->where('under_workflow', '=', 0);
    }

    public function allMedias()
    {
        return $this->belongsToMany('App\Media', 'projects_has_medias', 'projects_id', 'medias_id')
        ->withPivot([
            'profile_image',
            'favorite'
        ]);
    }

    public function lastMedias()
    {
        return $this->belongsToMany('App\Media', 'projects_has_medias', 'projects_id', 'medias_id')
        ->withPivot([
            'profile_image', 'favorite'
        ])
        ->where('under_workflow', '=', 0)
        ->orderBy('projects_has_medias.favorite', 'desc')
        ->orderBy('updated_at', 'desc');
    }

    public function bookmarks()
    {
        return $this->hasMany('App\Bookmark', 'projects_id', 'id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User', 'projects_has_users', 'projects_id', 'users_id')
            ->where('active', '=', 1)
            ->withPivot('status', 'roles_id')
            ->withTimestamps();
    }

    public function allUsers()
    {
        return $this->belongsToMany('App\User', 'projects_has_users', 'projects_id', 'users_id')
            ->withPivot('status', 'roles_id')
            ->withTimestamps();
    }

    public function validatedUsers()
    {
        return $this->belongsToMany('App\User', 'projects_has_users', 'projects_id', 'users_id')
            ->wherePivot('status', '=', 1)
            ->where('active', '=', 1)
            ->withPivot('status', 'roles_id')
            ->withTimestamps();
    }

    public function nbUsers()
    {
        return $this->validatedUsers()->count();
    }

    public static function findProject($id)
    {
        $query = Project::where('id', '=', $id);
        return $query;
    }

    public function findByUsersId($users_id)
    {
        $query = Project::where('users_id', '=', $users_id);

        return $query;
    }

    //Returns an array of users id
    private function usersId()
    {
        return array_map(function ($user) {
            return ["id" => $user['id']];
        }, $this->users()->getResults()->toArray());
    }

    public function toReducedArray()
    {
        $array = [
            "id" => $this->id,
            "active" => $this->active,
            "title" => $this->title,
            "slug" => $this->slug,
            "description" => $this->description,
            "confidentiality" => $this->confidentiality,
            "users" => $this->usersId(),
            "instance" => $this->instances_id,
            "profile_media_id" => $this->profile_media_id,
            "created_at" => $this->created_at->format(\DateTimeInterface::ISO8601),
            "pin" => [
                "location" => [
                    "lon" => floatval($this->longitude),
                    "lat" => floatval($this->latitude),
                ]
            ],
            "url" => parse_url($this->getUrl())['path'],
        ];
        $array['tags'] = array_map(function ($tag) {
            return ["id" => $tag['id'], "name" => $tag['name']];
        }, $this->tags()->getResults()->toArray());
        return $array;
    }

    public static function mapping()
    {
        $index = self::first()->getSearchIndex();
        $type = self::first()->getSearchType();
        return [
            'index' => $index,
            'body' => [
                'mappings' => [
                    $type => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'confidentiality' => ['type' => 'long'],
                            'title' => ['type' => 'text'],
                            'description' => ['type' => 'text'],
                            'slug' => ['type' => 'text'],
                            'profile_media_id' => ['type' => 'long'],
                            'instance' => ['type' => 'long'],
                            'created_at' => ['type' => 'text', 'fielddata' => true],
                            'pin' => [
                                'properties' => [
                                    'location' => ['type' => 'geo_point']
                                ]
                            ],
                            'users' => [
                                'properties' => [
                                    'id' => ['type' => 'long']
                                ]
                            ],
                            'tags' => [
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'name' => ['type' => 'text'],
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function profileImage()
    {
        return $this->hasOne('App\Media', 'id', 'profile_media_id');
    }
}
