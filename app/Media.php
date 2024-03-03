<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use \Netframe\Media\Model\Media as TlMedia;
use App\User;
use App\Notif;
use App\Observers\Searchable;
use Enzim\Lib\TikaWrapper\TikaWrapper;
use \RuntimeException;

class Media extends TlMedia
{
    /*
     * add Elasticsearch as observer
    */
    use Searchable;

    protected $table = "medias";
    protected $appends = ['url','thumb'];

    public function getUrlAttribute()
    {
        return route('media_download', array('id' => $this->id));
    }

    public function getThumbAttribute()
    {
        return route('media_download', ['id' => $this->id, 'thumb' => 1]);
    }

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($media) {

            // test if profile media of a profile
            User::where('profile_media_id', '=', $media->id)->update(['profile_media_id' => null]);
            House::where('profile_media_id', '=', $media->id)->update(['profile_media_id' => null]);
            Community::where('profile_media_id', '=', $media->id)->update(['profile_media_id' => null]);
            Project::where('profile_media_id', '=', $media->id)->update(['profile_media_id' => null]);

            // delete notification attached to this media
            $notification = Notif::where('parameter', 'LIKE', '%Media%post_id":"'.$media->id.'"%')->delete();

            // delete shares, comments, actions, likes
            $media->liked()->get()->each(function ($like) {
                $like->delete();
            });
            $media->shares()->get()->each(function ($share) {
                $share->delete();
            });
            $media->comments()->get()->each(function ($comment) {
                $comment->delete();
            });
            $media->actions()->get()->each(function ($action) {
                $action->delete();
            });

            $media->tags()->detach();

            // check if medias has news and if there is only one media in news
            $news = $media->news;
            foreach ($news as $post) {
                if ($post->medias->count() == 1 && $post->content == null) {
                    $post->delete();
                }
            }

            // delete archives
            $media->archives()->get()->each(function ($archive) {
                $archive->delete();
            });

            // delete file on filesystem
            if ($media->keep_files == 0) {
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
            }

            // delete notifications
            $notification = Notif::where('type', '=', 'workflow')
                ->where('parameter', 'LIKE', '%file_id":"' . $media->id . '"%')
                ->delete();

            // delete associated workflows
            if ($media->under_workflow) {
                $workflows = Workflow::where('type', '=', 'validate_file')
                    ->where('wf_datas', 'like', '%mediasIds":["' . $media->id . '"]%')
                    ->get();
                foreach ($workflows as $workflow) {
                    $workflow->delete();
                }

                $workflows = Workflow::where('type', '=', 'workflow')
                    ->where('wf_datas', 'like', '%file_id":"' . $media->id . '"%')
                    ->get();
                foreach ($workflows as $workflow) {
                    $workflow->delete();
                }
            }
        });
    }

    /**
     * morph relation when profile make actions
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function getType()
    {
        return "Media";
    }

    protected $fillable = ['name', 'description', 'meta_title', 'type', 'file_name', 'file_path', 'date', 'platform'];

    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    public function user()
    {
        return $this->belongsToMany('App\User', 'users_has_medias', 'medias_id', 'users_id');
    }

    /**
     * morph relation with tags
     */
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

    public function lastNetframeMedias($limit = 40)
    {
        $date = new \Carbon\Carbon;
        $date->subDays(7);

        return $this->leftJoin('users_has_medias', function ($joinU) {
            $joinU->on('users_has_medias.medias_id', '=', 'medias.id')
            ->where('users_has_medias.profile_image', '=', '0');
        })
        ->leftJoin('houses_has_medias', function ($joinH) {
            $joinH->on('houses_has_medias.medias_id', '=', 'medias.id')
            ->where('houses_has_medias.profile_image', '=', '0');
        })
        ->leftJoin('projects_has_medias', function ($joinP) {
            $joinP->on('projects_has_medias.medias_id', '=', 'medias.id')
            ->where('projects_has_medias.profile_image', '=', '0');
        })
        ->leftJoin('community_has_medias', function ($joinC) {
            $joinC->on('community_has_medias.medias_id', '=', 'medias.id')
            ->where('community_has_medias.profile_image', '=', '0');
        })
        ->where('medias.instances_id', '=', session('instanceId'))
        ->where(function ($whereNN) {
            $whereNN->orWhereNotNull('users_has_medias.profile_image')
            ->orWhereNotNull('houses_has_medias.profile_image')
            ->orWhereNotNull('projects_has_medias.profile_image')
            ->orWhereNotNull('community_has_medias.profile_image');
        })
        //->where('created_at', '>=', $date->toDateTimeString())
        ->orderBy('created_at', 'desc')
        ->take($limit)
        ->get();
    }

    public function duplicateFiles()
    {
        if (($this->platform == 'local')) {
            $extension = $extension = \File::extension($this->file_path);
            $newName = sha1($this->file_name . microtime()) . '.' . $extension;

            $filePath = str_replace($this->file_name, $newName, $this->file_path);
            $feedPath = ($this->feed_path != null)
                ? str_replace($this->file_name, 'feed-' . $newName, $this->file_path)
                : null;
            $thumbPath = ($this->thumb_path != null)
                ? str_replace($this->file_name, 'thumbs' . $newName, $this->file_path)
                : null;

            copy($this->file_path, $filePath);
            if ($this->feed_path != null) {
                copy($this->feed_path, $feedPath);
            }
            if ($this->thumb_path != null) {
                copy($this->thumb_path, $thumbPath);
            }
        } else {
            $newName = $this->file_name;
            $filePath = $this->file_path;
            $feedPath = $this->feed_path;
            //$thumbPath = $this->thumb_path != null;
            $thumbName = $this->platform.$this->file_name;
            $thumbPath = str_replace($thumbName, str_random(15).'.jpg', $this->thumb_path);

            if ($this->thumb_path != null) {
                copy($this->thumb_path, $thumbPath);
            }
        }

        $newMedia = new Media();
        $newMedia->users_id = auth()->guard('web')->user()->id;
        $newMedia->instances_id = session('instanceId');
        $newMedia->active = $this->active;
        $newMedia->linked = 0;
        $newMedia->language = $this->language;
        $newMedia->name = $this->name;
        $newMedia->access_rights = $this->access_rights;
        $newMedia->description = $this->description;
        $newMedia->latitude = $this->latitude;
        $newMedia->longitude = $this->longitude;
        $newMedia->meta_title = $this->meta_title;
        $newMedia->meta_alt = $this->meta_alt;
        $newMedia->type = $this->type;
        $newMedia->feed_width = $this->feed_width;
        $newMedia->feed_height = $this->feed_height;
        $newMedia->date = $this->date;
        $newMedia->confidentiality = $this->confidentiality;
        $newMedia->platform = $this->platform;
        $newMedia->mime_type = $this->mime_type;
        $newMedia->file_size = $this->file_size;
        $newMedia->encoded = $this->encoded;
        $newMedia->startEncode = $this->startEncode;
        $newMedia->endEncode = $this->endEncode;
        $newMedia->file_path = $filePath;
        $newMedia->feed_path = $feedPath;
        $newMedia->thumb_path = $thumbPath;
        $newMedia->file_name = $newName;
        $newMedia->save();

        return $newMedia;
    }

    public function lastComments()
    {
        $comments = $this->morphMany('App\Comment', 'post')->get();

        if (count($comments) > 5) {
            $skip = count($comments) - 5;
            return $this->morphMany('App\Comment', 'post')->orderBy('created_at')->skip($skip)->take(5);
        } else {
            return $this->morphMany('App\Comment', 'post')->orderBy('created_at')->take(5);
        }
    }

    public function formatSizeUnits()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }


    //Returns true if $str ends with $sub
    private function endsWith($str, $sub)
    {
        return ( substr($str, strlen($str) - strlen($sub)) == $sub );
    }

    public function toReducedArray()
    {
        $content = '';
        try {
            if ($this->type == TlMedia::TYPE_DOCUMENT &&
                file_exists($this->file_path) &&
                is_readable($this->file_path)
            ) {
                if (app()->runningInConsole()) {
                    $indexClient = \Vaites\ApacheTika\Client::make(
                        base_path('_bin/tika-app-2.6.0.jar'),
                        '/usr/bin/java'
                    );
                } else {
                    $indexClient = \Vaites\ApacheTika\Client::make(base_path('_bin/tika-app-2.6.0.jar'));
                }

                try {
                    $content = $indexClient->getText($this->file_path);
                } catch (\Exception $e) {
                }
            } else {
                $content = '';
            }
            /*
            $content = $this->type == TlMedia::TYPE_DOCUMENT &&
                       file_exists($this->file_path) &&
                       is_readable($this->file_path)
                        // indexe le contenu du document sous forme de texte si possible
                        ? TikaWrapper::getText($this->file_path)
                        : '';
            */
        } catch (\RuntimeException $e) {
            \Log::warning($e->getMessage());
            $content = '';
        }

        $array = [
            "id" => $this->id,
            "name" => $this->name,
            "confidentiality" => $this->confidentiality,
            "description" => $this->description,
            "data" => $content,
            "file_name" => $this->file_name,
            "file_path" => $this->file_path,
            "instance" => $this->instances_id,
            "user" => $this->author_id,
            "like" => $this->like,
            "share" => $this->share,
            "mime_type" => $this->mime_type,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "created_at" => $this->created_at->format(\DateTimeInterface::ISO8601),
            "updated_at" => $this->updated_at->format(\DateTimeInterface::ISO8601),
            "type" => $this->type,
            "platform" => $this->platform,
            "isDocument" => $this->isDocument(),
            "isTypeDisplay" => $this->isTypeDisplay(),
            "hasOffice" => $this->instance->hasApplication('office'),
            "thumbnail" => [
                "thumb_path" => $this->thumb_path,
                "feed_path" => $this->feed_path,
                "encoded" => $this->encoded,
            ],
            "url" => parse_url($this->getUrl())['path'],
        ];

        $setProfile = function (&$array, $profile) {
            $array['profile_id'] = sprintf(
                '%s-%d',
                (!$profile instanceof User) ? $profile->getType() : 'user',
                $profile->id
            );
            $array['profile'] = $profile;
        };

        if ($this->author instanceof Collection) {
            foreach ($this->author as $profile) {
                $setProfile($array, $profile);
            }
        } else {
            $setProfile($array, $this->author);
        }

        $array['tags'] = array_map(function ($tag) {
            return ["id" => $tag['id'], "name" => $tag['name']];
        }, $this->tags()->getResults()->toArray());

        return $array;
    }

    //Returns an array of users id
    private function usersId()
    {
        return array_map(function ($user) {
            return ["id" => $user['id']];
        }, $this->users()->getResults()->toArray());
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
                            'created_at' => ['type' => 'text', 'fielddata' => true],
                        ]
                    ]
                ]
            ]
        ];
    }

    public function profileImage()
    {
        return $this->hasOne('App\Media', 'id', 'id');
    }

    public function getNameDisplay()
    {
        return $this->name;
    }

    public function getDescription()
    {
        return $this->data;
    }

    public function isLiked()
    {
        return auth()->guard('web')->check()
            ? Like::isLiked(['liked_id'=>$this->id, 'liked_type'=>'App\\Media'])
            : false;
    }

    /**
     * morph relation to likes when user is liker
     */
    public function liked()
    {
        return $this->morphMany('App\Like', 'liked')
            ->where('instances_id', '=', session('instanceId'));
    }

    public function archives()
    {
        return $this->hasMany('App\MediasArchive', 'medias_id', 'id')
            ->orderBy('updated_at', 'desc');
    }

    public function mainProfile($returnFolder = false)
    {
        return $this->folder();
    }

    public function folderRel()
    {
        $profiles = config('media.owner_relation');

        foreach ($profiles as $model => $relTable) {
            $owner = $this
            ->belongsToMany('App\\'.$model, $relTable.'_has_medias', 'medias_id', $relTable.'_id')
            ->withPivot(['profile_image', 'medias_folders_id']);

            if ($owner->get()->first() != null) {
                return $owner;
            }
        }

        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    /*
     * defaut return profile
     * if $returnForlder true, return folder
     */
    public function folder($returnFolder = false)
    {
        $media = $this->leftJoin('users_has_medias', function ($joinU) {
                $joinU->on('users_has_medias.medias_id', '=', 'medias.id');
        })
            ->leftJoin('houses_has_medias', function ($joinH) {
                $joinH->on('houses_has_medias.medias_id', '=', 'medias.id');
            })
            ->leftJoin('projects_has_medias', function ($joinP) {
                $joinP->on('projects_has_medias.medias_id', '=', 'medias.id');
            })
            ->leftJoin('community_has_medias', function ($joinC) {
                $joinC->on('community_has_medias.medias_id', '=', 'medias.id');
            })
            ->where('medias.id', '=', $this->id)
            ->where('medias.instances_id', '=', session('instanceId'))
            ->first();

        $profile = null;

        if ($media == null) {
            return $profile;
        }

        if ($media->houses_id) {
            $profile = House::find($media->houses_id);
            $media = $this->leftJoin('houses_has_medias', function ($joinH) {
                    $joinH->on('houses_has_medias.medias_id', '=', 'medias.id');
            })
                ->where('medias.id', '=', $this->id)
                ->where('medias.instances_id', '=', session('instanceId'))
                ->first();
        } elseif ($media->projects_id) {
            $profile = Project::find($media->projects_id);
            $media = $this->leftJoin('projects_has_medias', function ($joinP) {
                    $joinP->on('projects_has_medias.medias_id', '=', 'medias.id');
            })
                ->where('medias.id', '=', $this->id)
                ->where('medias.instances_id', '=', session('instanceId'))
                ->first();
        } elseif ($media->community_id) {
            $profile = Community::find($media->community_id);
            $media = $this->leftJoin('community_has_medias', function ($joinC) {
                    $joinC->on('community_has_medias.medias_id', '=', 'medias.id');
            })
                ->where('medias.id', '=', $this->id)
                ->where('medias.instances_id', '=', session('instanceId'))
                ->first();
        } else {
            $profile = User::find($media->users_id);
            $media = $this->leftJoin('users_has_medias', function ($joinU) {
                $joinU->on('users_has_medias.medias_id', '=', 'medias.id');
            })
            ->where('medias.id', '=', $this->id)
            ->where('medias.instances_id', '=', session('instanceId'))
            ->first();
        }
        $folderId = $media->medias_folders_id;
        if ($returnFolder) {
            return MediasFolder::find($folderId);
        }
        if ($profile) {
            $profile->medias_folders_id = $folderId;
        }

        return $profile;
    }

    // get media folder url
    public function getFolderUrl()
    {
        $folder = $this->folderRel->first();
        $profile = $this->author()->first();

        if ($folder != null && $profile != null && $folder->pivot != null) {
            $folderId = $folder->pivot->medias_folders_id;


            return url()->route('medias_explorer', [
                'profileType' => $profile->getType(),
                'profileId' => $profile->id,
                'folder' => $folderId,
            ]);
        } elseif ($folder == null && $profile != null) {
            return url()->route('medias_explorer', [
                'profileType' => $profile->getType(),
                'profileId' => $profile->id
            ]);
        } else {
            return null;
        }
    }

    //onlyoffice
    public function getDocumentType()
    {
        $ext = strtolower('.' . pathinfo($this->file_path, PATHINFO_EXTENSION));

        if (in_array($ext, config('office.ExtsDocument'))) {
            return "text";
        }
        if (in_array($ext, config('office.ExtsSpreadsheet'))) {
            return "spreadsheet";
        }
        if (in_array($ext, config('office.ExtsPresentation'))) {
            return "presentation";
        }
        return "";
    }

    public function views($type = null)
    {
        if ($type == null) {
            return $this->morphMany('App\View', 'post');
        } elseif ($type = 'trueViews') {
            return $this->morphMany('App\View', 'post')
            ->whereIn('views.type', [View::TYPE_OPEN, View::TYPE_DOWNLOAD]);
        }
    }

    public function view($type = View::TYPE_OPEN)
    {
        $userId = auth()->guard('web')->user()->id;
        if (!($this->author_type=="App\\User" && $this->author_id==$userId)) {
            $view = View::where([
                'post_id' => $this->id,
                'post_type' => get_class($this),
                'type' => $type,
                'users_id' => $userId
            ])->first();

            if (!$view) {
                $v = new View();
                $v->post_id = $this->id;
                $v->post_type = get_class($this);
                $v->users_id = $userId;
                $v->type = $type;
                $v->save();
            }
        }
    }
}
