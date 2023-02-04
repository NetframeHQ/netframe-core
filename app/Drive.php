<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\GoogleDrive;
use App\DropBox;
use App\OneDrive;
use App\Box;

class Drive extends Model
{
    protected $table = "drives";
    
    const GOOGLE = 0;
    const ONEDRIVE = 1;
    const BOX = 2;
    const DROPBOX = 3;

    public function getDrive()
    {
        $drive = null;
        switch ($this->type) {
            case self::GOOGLE:
                $drive = new GoogleDrive();
                break;
            case self::ONEDRIVE:
                $drive = new OneDrive();
                break;
            case self::DROPBOX:
                $drive = new DropBox();
                break;
            case self::BOX:
                $drive = new Box();
                break;
        }
        return $drive;
    }

    public function auth($save = true)
    {
        $drive = $this->getDrive();
        $drive->auth($this->refresh_token, $this->access_token, $this->code);
        $this->refresh_token = $drive->refresh_token;
        $this->access_token = $drive->access_token;
        if (isset($this->code)) {
            unset($this->code);
        }
    }

    public function getFiles($folder = null)
    {
        $drive = $this->getDrive();
        $drive->auth($this->refresh_token, $this->access_token);
        if (isset($folder)) {
            $data = $drive->getFiles($folder);
        } elseif (isset($this->path)) {
            $data = $drive->getFiles($this->path);
        } else {
            $data = $drive->getFiles();
        }
        if (isset($drive->refresh_token)) {
            $this->refresh_token = $drive->refresh_token;
        }
        if (isset($drive->access_token)) {
            $this->access_token = $drive->access_token;
        }
        if (isset($drive->refresh_token) && isset($drive->access_token)) {
            $this->save();
        }
        return $data;
    }

    public function mediasFolders()
    {
        return $this->belongsTo('App\MediasFolder', 'medias_folders_id', 'id');
    }

    public function addFolder($name, $parent = null)
    {
        $drive = $this->getDrive();
        $drive->auth($this->refresh_token, $this->access_token);
        if (isset($parent)) {
            return $drive->addFolder($name, $parent);
        } elseif (isset($this->path)) {
            return $drive->addFolder($name, $this->path);
        } else {
            return $drive->addFolder($name);
        }
    }

    public function addFile($name, $path, $parent = null)
    {
        $drive = $this->getDrive();
        $drive->auth($this->refresh_token, $this->access_token);
        if (isset($parent)) {
            return $drive->addFile($name, $path, $parent);
        } elseif (isset($this->path)) {
            return $drive->addFile($name, $path, $this->path);
        } else {
            return $drive->addFile($name, $path);
        }
    }

    public function deleteFolder($folder)
    {
        $drive = $this->getDrive();
        $drive->auth($this->refresh_token, $this->access_token);
        $drive->deleteFolder($folder);
    }

    public function deleteMedia($media)
    {
        $drive = $this->getDrive();
        $drive->auth($this->refresh_token, $this->access_token);
        $drive->deleteMedia($media);
    }

    public function getParentsTree($folder = null)
    {
        $drive = $this->getDrive();
        $drive->auth($this->refresh_token, $this->access_token);
        return $drive->getParentsTree($this->path, $folder);
    }

    public function getFolder($id)
    {
        $drive = $this->getDrive();
        $drive->auth($this->refresh_token, $this->access_token);
        return $drive->getFolderInfo($id);
    }
}
