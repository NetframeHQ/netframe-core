<?php

namespace App;

use App\Support\Database\CacheQueryBuilder;
use App\BaseProfile;
use App\Observers\Searchable;

class Community extends BaseProfile
{

    use CacheQueryBuilder;

    use Searchable;

    protected $table = "community";
    protected $fillable = [
        'name',
        'description'
    ];
    protected $type = Profile::TYPE_COMMUNITY;
    protected $instanceRelation = 'communities';

    public function users()
    {
        return $this->belongsToMany('App\User', 'community_has_users', 'community_id', 'users_id')
            ->where('active', '=', 1)
            ->withPivot('status', 'roles_id')
            ->withTimestamps();
    }

    public function allUsers()
    {
        return $this->belongsToMany('App\User', 'community_has_users', 'community_id', 'users_id')
            ->withPivot('status', 'roles_id')
            ->withTimestamps();
    }

    public function validatedUsers()
    {
        return $this->belongsToMany('App\User', 'community_has_users', 'community_id', 'users_id')
            ->wherePivot('status', '=', 1)
            ->where('active', '=', 1)
            ->withPivot('status', 'roles_id')
            ->withTimestamps();
    }

    public function nbUsers()
    {
        return $this->validatedUsers()->count();
    }

    /*
    public function project()
    {
        return $this->belongsToMany('Project', 'community_has_projects', 'community_id', 'projects_id');
    }
    */


    public function medias()
    {
        return $this->belongsToMany('App\Media', 'community_has_medias', 'community_id', 'medias_id')->withPivot([
            'profile_image', 'favorite'
        ])->where('under_workflow', '=', 0);
    }

    public function allMedias()
    {
        return $this->belongsToMany('App\Media', 'community_has_medias', 'community_id', 'medias_id')->withPivot([
            'profile_image', 'favorite'
        ]);
    }

    public function lastMedias()
    {
        return $this->belongsToMany('App\Media', 'community_has_medias', 'community_id', 'medias_id')
        ->withPivot([
            'profile_image', 'favorite'
        ])
        ->where('under_workflow', '=', 0)
        ->orderBy('community_has_medias.favorite', 'desc')
        ->orderBy('updated_at', 'desc');
    }

    public function getNameAttribute($value)
    {
        return $value;
    }

    public function findById($id)
    {
        $query = Community::where('id', '=', $id);
        return $query;
    }

    //Returns an array of users id
    private function usersId()
    {
        return array_map(function ($user) {
            return ["id" => $user['id']];
        }, $this->users()->getResults()->toArray());
    }

    //Data to persist into elasticsearch
    public function toReducedArray()
    {
        $array = [
            "id" => $this->id,
            "active" => $this->active,
            "confidentiality" => $this->confidentiality,
            "name" => $this->name,
            "description" => $this->description,
            "slug" => $this->slug,
            "pin" => [
                "location" => [
                    "lon" => floatval($this->longitude),
                    "lat" => floatval($this->latitude),
                ]
            ],
            "users" => $this->usersId(),
            "instance" => $this->instances_id,
            "profile_media_id" => $this->profile_media_id,
            "created_at" => $this->created_at->format(\DateTimeInterface::ISO8601),
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
                            'name' => ['type' => 'text'],
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
