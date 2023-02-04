<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MediasFolder extends Model
{
    protected $table = 'medias_folders';

    public static function boot()
    {
        parent::boot();

        self::deleting(function ($folder) {

            //delete attached medias
            $folder->allMedias()->get()->each(function ($media) {
                $media->delete();
            });

            // delete sub folders
            foreach ($folder->childrenFolder as $subFolder) {
                $subFolder->delete();
            }
        });
    }


    public function instance()
    {
        return $this->belongsTo('App\Instance', 'instances_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id', 'id');
    }

    public function getNameDisplay($profile = null)
    {
        if ($this->personnal_folder) {
            if ($profile != null && class_basename($profile) == 'User') {
                return $this->profile->getNameDisplay();
            } else {
                return $this->user->getNameDisplay();
            }
        } elseif ($this->default_folder == 0) {
            return $this->name;
        } else {
            return trans('xplorer.defaultFolders.'.$this->name);
        }
    }

    /**
    * morph relation with profile creator
    */
    public function profile()
    {
        return $this->morphTo();
    }

    public function parentFolder()
    {
        return $this->belongsTo('App\MediasFolder', 'medias_folders_id');
    }

    public function childrenFolder()
    {
        return $this->hasMany('App\MediasFolder', 'medias_folders_id', 'id')->orderBy('name');
    }

    public function getParentsTree($onlyName = false, $withMe = false)
    {
        $tree = [];
        $current = $this;
        if ($withMe) {
            $tree[] = ($onlyName) ? $current->name : $current;
        }
        while ($current->parentFolder) {
            $tree[] = ($onlyName) ? $current->parentFolder->name : $current->parentFolder;
            $current = $current->parentFolder;
        }
        return array_reverse($tree);
    }

    public function getChildrenTree($onlyId = false, $withMe = false, $current = false, $loop = false)
    {
        $tree = [];
        $canWithMe = (!$current) ? true : false;
        $current = (!$current) ? $this : $current;
        if ($canWithMe && $withMe) {
            $tree[] = ($onlyId) ? $current->id : $current;
        }

        foreach ($current->childrenFolder as $child) {
            $tree[] = ($onlyId) ? $child->id : $child;
            if ($loop) {
                $tree2 = $this->getChildrenTree($onlyId, false, $child, $loop);
                $tree = array_merge($tree, $tree2);
            }
        }
        return $tree;
    }

    public function getUrl()
    {
        $profile = $this->profile;
        return url()->route('medias_explorer', [
            'profileType' => $profile->getType(),
            'profileId' => $profile->id,
            'folder' => $this->id
        ]);
    }

    public function medias()
    {
        switch ($this->profile_type) {
            case 'App\User':
                $profile_type = 'users';
                break;
            case 'App\House':
                $profile_type = 'houses';
                break;
            case 'App\Community':
                $profile_type = 'community';
                break;
            case 'App\Project':
                $profile_type = 'projects';
                break;
        }

        return $this
            ->belongsToMany('App\Media', $profile_type . '_has_medias', 'medias_folders_id', 'medias_id')
            ->where('under_workflow', '=', 0);
    }

    public function allMedias()
    {
        switch ($this->profile_type) {
            case 'App\User':
                $profile_type = 'users';
                break;
            case 'App\House':
                $profile_type = 'houses';
                break;
            case 'App\Community':
                $profile_type = 'community';
                break;
            case 'App\Project':
                $profile_type = 'projects';
                break;
        }

        return $this
            ->belongsToMany('App\Media', $profile_type . '_has_medias', 'medias_folders_id', 'medias_id');
    }

    public function hasWorkflowFiles()
    {
        return $this->allMedias()->where('under_workflow', '=', 1);
    }

    /*
     * generate default folders for new profiles
     */
    public static function generateDefault($profile, $instanceId, $user)
    {
        $defaultFolders = config('media.default_folders');
        foreach ($defaultFolders as $defaultFolder) {
            $folderProfile = new MediasFolder();
            $folderProfile->instances_id = $instanceId;
            $folderProfile->users_id = $user->id;
            $folderProfile->profile_id = $profile->id;
            $folderProfile->profile_type = get_class($profile);
            $folderProfile->name = $defaultFolder['name'];
            $folderProfile->access_rights = $defaultFolder['rights'];
            $folderProfile->default_folder = 1;
            $folderProfile->save();
        }
    }

    public function duplicate($target_profile, $parent_folder_id)
    {
        $folder = new MediasFolder();
        $folder->name = $this->name;
        $folder->instances_id = session('instanceId');
        $folder->users_id = auth()->guard('web')->user()->id;
        $folder->profile_id = $target_profile->id;
        $folder->profile_type = get_class($target_profile);
        $folder->medias_folders_id = $parent_folder_id;
        $folder->access_rights = $this->access_rights;
        $folder->save();

        $childsFolders = $this->getChildrenTree(false, false, false, false);
        foreach ($childsFolders as $subFolder) {
            $subFolder->duplicate($target_profile, $folder->id);
        }

        // duplicate medias
        foreach ($this->medias as $media) {
            $newMedia = $media->duplicateFiles();
            $target_profile->medias()->attach($newMedia->id, [
                'medias_folders_id' => $folder->id,
            ]);
        }

        return $folder;
    }

    public function drive()
    {
        return $this->hasOne('App\Drive', 'medias_folders_id');
    }

    public function isInPublic()
    {
        if ($this->medias_folders_id == null && $this->public_folder == 1) {
            return true;
        } elseif ($this->medias_folders_id != null) {
            $parents = $this->getParentsTree();
            if ($parents[0]->public_folder) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function formatFolderTree($level, $moveFolderId)
    {
        $level++;
        $spaces = '';
        for ($i=0; $i< $level; $i++) {
            $spaces .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        $folderTree = $this->getChildrenTree();
        $parentId = null;
        $folders = [];
        foreach ($folderTree as $fold) {
            if ($moveFolderId == $fold->id) {
                continue;
            }
            $folders[$fold->id] = $spaces.$fold->name;
            $subFolders = $fold->formatFolderTree($level, $moveFolderId);
            $folders = array_replace_recursive($folders, $subFolders);
        }
        return $folders;
    }
}
