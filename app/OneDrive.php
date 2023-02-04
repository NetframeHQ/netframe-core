<?php
namespace App;

use GuzzleHttp\Client as Guzzle;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

class OneDrive
{
    private $client;
    private $service = null;
    private $accessToken;

    public function getAuthUrl()
    {

        return 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?client_id=' . env('ONEDRIVE_CLIENT_ID')
        . '&response_type=code&redirect_uri=' . route('drive_authorize', ['drive'=> 'onedrive'])
        . '&response_mode=query&scope=files.readwrite.all%20offline_access';
    }

    public function auth($refresh_token = null, $access_token = null, $code = null)
    {
        if (isset($access_token)) {
            $this->access_token = $access_token;
        }
        if (isset($refresh_token)) {
            $this->refresh_token = $refresh_token;
        }
        $guzzle = new Guzzle();
        $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
        if (isset($code)) {
            $token = json_decode($guzzle->post($url, [
                'form_params' => [
                    'client_id' => env('ONEDRIVE_CLIENT_ID'),
                    'client_secret' => env('ONEDRIVE_CLIENT_SECRET'),
                    'redirect_uri' => route('drive_authorize', ['drive'=> 'onedrive']),
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                ],
            ])->getBody()->getContents());
            $this->access_token = $token->access_token;
            $this->refresh_token = $token->refresh_token;
        }
        if (isset($refresh_token) && !isset($access_token)) {
            $token = json_decode($guzzle->post($url, [
                'form_params' => [
                    'client_id' => env('ONEDRIVE_CLIENT_ID'),
                    'client_secret' => env('ONEDRIVE_CLIENT_SECRET'),
                    'redirect_uri' => route('drive_authorize', ['drive'=> 'onedrive']),
                    'refresh_token' => $refresh_token,
                    'grant_type' => 'refresh_token',
                ],
            ])->getBody()->getContents());
            $this->refresh_token = $token->refresh_token;
            $this->access_token = $token->access_token;
        }
        if (isset($this->access_token) || isset($access_token)) {
            $this->service = new Graph();
            $this->service->setAccessToken(isset($this->access_token) ? $this->access_token : $access_token);
        }
    }

    public function getFiles($id = 'root')
    {
        $items = null;
        $req = ($id == 'root') ? '/me/drive/root/children' : "/me/drive/items/".$id."/children";
        try {
            $items = $this->service->createRequest("GET", $req)
                                ->setReturnType(Model\DriveItem::class)
                                ->execute();
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $this->auth($this->refresh_token);
            $items = $this->service->createRequest("GET", $req)
                                ->setReturnType(Model\DriveItem::class)
                                ->execute();
        }
        $data = array();
        foreach ($items as $item) {
            if ($item->getFile()) {
                // dump($item);
                $data['files'][] = $this->toFile($item);
            } elseif ($item->getFolder()) {
                $data['folders'][] = $this->toFolder($item);
            }
        }
        return $data;
    }

    public function toFile(Model\DriveItem $file)
    {
        $type = 3;
        $mimeType = $file->getFile()->getMimeType();
        if (strpos($mimeType, 'image') !== false) {
            $type = 0;
        } elseif (strpos($mimeType, 'video') !== false) {
            $type = 1;
        } elseif (strpos($mimeType, 'audio') !== false) {
            $type = 2;
        }
        return new \App\DriveFile(array(
            'id' => $file->getId(),
            'name' => $file->getName(),
            'file_name' => $file->getName(),
            'mime_type' => $mimeType,
            'created_at' => $file->getCreatedDateTime()->format('Y-m-d H:i:s'),
            'updated_at' => $file->getLastModifiedDateTime()->format('Y-m-d H:i:s'),
            'file_path' => $file->getWebUrl(),
            'platform' => 'onedrive',
            'type' => $type,
        ));
    }

    public function toFolder(Model\DriveItem $folder)
    {
        return new \App\DriveFolder([
            'id' => $folder->getId(),
            'folder_name' => $folder->getName(),
            'created_at' => $folder->getCreatedDateTime()->format('Y-m-d H:i:s'),
            'updated_at' => $folder->getLastModifiedDateTime()->format('Y-m-d H:i:s'),
            'default_folder' => 1,
            'platform' => 'onedrive'
        ]);
    }

    public function addFolder($name, $parent = 'root')
    {
        $parent = ($parent == 'root') ? $parent : "items/".$parent;
        $url = '/me/drive/'.$parent.'/children';

        $folder = $this->service->createRequest("POST", $url)
            ->attachBody(json_encode([
                'name' => $name,
                'folder' => ["childCount" => '0'],
                '@microsoft.graph.conflictBehavior' => 'rename'
            ]))
            ->setReturnType(Model\DriveItem::class)
            ->execute();
        return $this->toFolder($folder);
    }

    public function addFile($name, $file_path, $parent = 'root')
    {
        $parent = ($parent == 'root') ? $parent : "items/".$parent;
        $url = '/me/drive/'.$parent.'/children/'.$name.'/content';

        $file = $this->service->createRequest("PUT", $url)
            ->setReturnType(Model\DriveItem::class)
            ->upload($file_path);
        return $this->toFile($file);
    }
}
