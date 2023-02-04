<?php

namespace App;

class GoogleDrive
{
    private $client;
    private $service = null;

    public function __construct()
    {
        $this->client = new \Google_Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID'));
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $this->client->addScope(\Google_Service_Drive::DRIVE);
        $this->client->setRedirectUri(route('drive_authorize', ['drive'=>'google']));
        $this->client->setApprovalPrompt("force");
        $this->client->setAccessType(env('GOOGLE_ACCESS_TYPE'));
    }

    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    public function auth($refresh_token = null, $access_token = null, $code = null)
    {
        if (isset($access_token)) {
            $this->access_token = $access_token;
        }
        if (isset($refresh_token)) {
            $this->refresh_token = $refresh_token;
        }

        if (isset($code)) {
            $this->client->authenticate($code);
            $this->access_token = $this->client->getAccessToken()['access_token'];
            $this->refresh_token = $this->client->getRefreshToken();
        }

        if ($this->client->isAccessTokenExpired()) {
            $this->client->fetchAccessTokenWithRefreshToken($this->refresh_token);
            $this->access_token = $this->client->getAccessToken()['access_token'];
            $this->refresh_token = $this->client->getRefreshToken();
        }

        if (isset($this->access_token) || isset($access_token)) {
            $this->client->setAccessToken(isset($this->access_token) ? $this->access_token : $access_token);
            $this->service = new \Google_Service_Drive($this->client);
        }
    }

    public function getFiles($id = null)
    {
        $parameters = [
            'fields' => 'files(size, name, id, createdTime, parents, mimeType, webViewLink, webContentLink)'
        ];
        if ($id!=null) {
            $parameters['q'] = "parents in '".$id."'";
        } else {
            $parameters['q'] = "parents in 'root'";
        }
        $files = $this->service->files->listFiles($parameters)->files;
        $data = array();
        foreach ($files as $file) {
            if (preg_match('/folder$/', $file->mimeType)) {
                $data['folders'][] = $this->toFolder($file);
            } else {
                $data['files'][] = $this->toFile($file);
            }
        }

        return $data;
    }

    public function toFile(\Google_Service_Drive_DriveFile $file)
    {
        $type = 3;
        $mimeType = $file->mimeType;
        if (strpos($mimeType, 'image') !== false) {
            $type = 0;
        } elseif (strpos($mimeType, 'video') !== false) {
            $type = 1;
        } elseif (strpos($mimeType, 'audio') !== false) {
            $type = 2;
        }
        return new \App\DriveFile(array(
            'id' => $file->id,
            'name' => $file->name,
            'file_name' => $file->name,
            'mime_type' => $mimeType,
            'created_at' => $file->createdTime,
            'updated_at' => $file->createdTime,
            'file_path' => str_replace("&export=download", "", $file->webContentLink),
            'platform' => 'google_drive',
            'type' => $type,
        ));
    }

    public function toFolder(\Google_Service_Drive_DriveFile $folder)
    {
        return new \App\DriveFolder(array(
            'id' => $folder->id,
            'folder_name' => $folder->name,
            'created_at' => $folder->createdTime,
            'updated_at' => $folder->createdTime,
            'default_folder' => 0,
            'platform' => 'google_drive',
        ));
    }

    public function addFolder($name, $parent = null)
    {
        $fileMetadata = new \Google_Service_Drive_DriveFile(array(
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [isset($parent) ? $parent : 'root']));
        $folder = $this->service->files->create($fileMetadata, [
            'fields' => 'name, id, createdTime, parents, mimeType'
        ]);
        return  $this->toFolder($folder);
    }

    public function addFile($name, $file_path, $parent = null)
    {
        $fileMetadata = new \Google_Service_Drive_DriveFile(array(
            'name' => $name,
            'parents' => [isset($parent) ? $parent : 'root']));
        $content = (file_exists($file_path) && is_readable($file_path)) ? file_get_contents($file_path) : '';
        $file = $this->service->files->create($fileMetadata, array(
            'data' => $content,
            'mimeType' => mime_content_type($file_path),
            'uploadType' => 'multipart',
            'fields' => 'size, name, id, createdTime, parents, mimeType, webViewLink, webContentLink'
        ));
        return  $this->toFile($file);
    }
}
