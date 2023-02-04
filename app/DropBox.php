<?php
namespace App;

use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\Dropbox as DBox;

class DropBox
{
    private $client;
    private $service = null;
    private $accessToken;

    public function __construct()
    {
        $this->client = new DropboxApp(env('DROPBOX_CLIENT_ID'), env('DROPBOX_CLIENT_SECRET'));
        $this->service = new DBox($this->client);
    }

    public function getAuthUrl()
    {
        $authHelper = $this->service->getAuthHelper();
        return $authHelper->getAuthUrl(route('drive_authorize', ['drive'=>'dropbox']));
    }

    public function auth($refresh_token = null, $access_token = null, $code = null)
    {
        if (isset($code)) {
            $authHelper = $this->service->getAuthHelper();
            $this->access_token = $authHelper->getAccessToken(
                $code,
                null,
                route('drive_authorize', ['drive'=>'dropbox'])
            )->getToken();
            $this->refresh_token = '';
        }
        if (isset($this->access_token) || isset($access_token)) {
            $this->service->setAccessToken(isset($this->access_token) ? $this->access_token : $access_token);
        }
    }

    public function getFiles($path = '/')
    {
        if ($path!='/') {
            $path = '/'.$path;
        }
        $path = str_replace("-", "/", $path);
        $collection = $this->service->listFolder($path)->getItems();
        $data = array();
        foreach ($collection as $media) {
            if ($media instanceof \Kunnu\Dropbox\Models\FileMetadata) {
                $data['files'][] = $this->toFile($media);
            } else {
                $data['folders'][] = $this->toFolder($media);
            }
        }
        return $data;
    }

    public function toFile($file)
    {
        $metadata = $this->service->getMetadata(
            $file->getPathLower(),
            ["include_media_info" => true, "include_deleted" => true]
        );
        $type = 3;
        $mime_type = 'document/'.pathinfo($file->getPathLower(), PATHINFO_EXTENSION);
        $mediaInfos = $metadata->getMediaInfo();
        if ($mediaInfos!=null) {
            $mime = $mediaInfos->getData()['metadata']['.tag'];
            $mime_type = $mime.'/'.pathinfo($file->getPathLower(), PATHINFO_EXTENSION);
            if ($mime=='photo' || $mime=='image') {
                $type = 0;
            } elseif ($mime=='video') {
                $type=1;
            } elseif ($mime=='audio') {
                $type = 2;
            }
        }
        $path = $file->getPathLower();
        $path = substr($path, 1);
        return new \App\DriveFile(array(
            'id' => $path,
            'name' => $file->getName(),
            'file_name' => $file->getName(),
            'mime_type' => $mime_type,
            'created_at' => $file->getClientModified(),
            'updated_at' => $file->getServerModified(),
            'file_path' => $this->service->getTemporaryLink($file->getPathLower())->getLink(),
            'platform' => 'dropbox',
            'type' => $type,
        ));
    }

    public function toFolder($folder)
    {
        $path = $folder->getPathLower();
        $path = substr($path, 1);

        return new \App\DriveFolder(array(
            'id' => $path,
            'folder_name' => $folder->getName(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'default_folder' => 0,
            'platform' => 'dropbox',
        ));
    }

    public function addFolder($name, $parent = '/')
    {
        if ($parent!='/') {
            $parent = '/'.$parent;
        }
        $path = $parent.'/'.$name;
        $folder = $this->service->createFolder($path);
        return  $this->toFolder($folder);
    }

    public function addFile($name, $file_path, $parent = '/')
    {
        if ($parent!='/') {
            $parent = '/'.$parent;
        }
        $path = $parent.'/'.$name;
        $dropboxFile = new \Kunnu\Dropbox\DropboxFile($file_path);
        $file = $this->service->upload($dropboxFile, $path, ['autorename'=>true]);
        return  $this->toFile($file);
    }

    public function deleteMedia($path)
    {
        $folder = $this->service->delete('/'.$path);
    }

    public function deleteFolder($path)
    {
        $folder = $this->service->delete('/'.$path);
    }
}
