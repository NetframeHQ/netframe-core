<?php

namespace App;

use GuzzleHttp\Client as Guzzle;

class Box
{
    private $client;
    private $service;

    public function getAuthUrl()
    {
        return 'https://account.box.com/api/oauth2/authorize?response_type=code&client_id=' . env('BOX_CLIENT_ID')
            . '&redirect_uri=' . route('drive_authorize', ['drive'=> 'box'])
            . '&state=' . uniqId();
    }

    public function auth($refresh_token = null, $access_token = null, $code = null)
    {
        if (isset($access_token)) {
            $this->access_token = $access_token;
        }
        if (isset($refresh_token)) {
            $this->refresh_token = $refresh_token;
        }
        $guzzle = new Guzzle(['verify'=>false]);
        $url = 'https://api.box.com/oauth2/token';
        if (isset($code)) {
            $token = json_decode($guzzle->post($url, [
                'form_params' => [
                    'client_id' => env('BOX_CLIENT_ID'),
                    'client_secret' => env('BOX_CLIENT_SECRET'),
                    'redirect_uri' => route('drive_authorize', ['drive'=> 'box']),
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                ],
            ])->getBody()->getContents());
            $this->refresh_token = $token->refresh_token;
            $this->access_token = $token->access_token;
        }
        if (isset($refresh_token) && !isset($access_token)) {
            try {
                $token = json_decode($guzzle->post($url, [
                    'form_params' => [
                        'client_id' => env('BOX_CLIENT_ID'),
                        'client_secret' => env('BOX_CLIENT_SECRET'),
                        'redirect_uri' => route('drive_authorize', ['drive'=> 'box']),
                        'refresh_token' => $refresh_token,
                        'grant_type' => 'refresh_token',
                    ],
                ])->getBody()->getContents());
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                return false;
            }
            dump($token);
            $this->refresh_token = $token->refresh_token;
            $this->access_token = $token->access_token;
        }
    }

    public function getFiles($id = 0)
    {
        $content = [];
        try {
            $content = $this->get($id);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $return = $this->auth($this->refresh_token);
            if (!isset($return)) {
                $content = $this->get($id);
            }
        }
        $data = array();
        if (isset($content->entries)) {
            foreach ($content->entries as $item) {
                if ($item->type=='file') {
                    $data['files'][] = $this->toFile($item);
                } elseif ($item->type=='folder') {
                    $data['folders'][] = $this->toFolder($item);
                }
            }
        }

        return $data;
    }

    private function get($id)
    {
        $guzzle = new Guzzle(['verify'=>false]);
        $url = 'https://api.box.com/2.0/folders/'.$id.'/items';
        // $url = 'https://api.box.com/2.0/files/302478513586';
        return json_decode($guzzle->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Accept'        => 'application/json',
            ],
            'query' => [
                'fields' => 'id,modified_at,created_at,name,path_collection,shared_link,extension,download_url',
            ],
        ])->getBody()->getContents());
    }

    /**
    * Get file info
    * https://developer.box.com/reference#files
    **/
    private function getFileInfo($id)
    {
        $guzzle = new Guzzle(['verify'=>false]);
        $url = 'https://api.box.com/2.0/files/'.$id;
        return json_decode($guzzle->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Accept'        => 'application/json',
            ],
            'query' => [
                'fields' => 'id,modified_at,created_at,name,path_collection,shared_link,extension,download_url',
            ],
        ])->getBody()->getContents());
    }

    /**
    * Get folder info
    * https://developer.box.com/reference#get-folder-info
    **/
    public function getFolderInfo($id)
    {
        $guzzle = new Guzzle(['verify'=>false]);
        $url = 'https://api.box.com/2.0/folders/'.$id;
        return $this->toFolder(json_decode($guzzle->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Accept'        => 'application/json',
            ],
            'query' => [
                'fields' => 'id,modified_at,created_at,name,path_collection,shared_link,extension,download_url',
            ],
        ])->getBody()->getContents()));
    }

    public function toFile($file)
    {
        $type = 3;
        $mimeType = 'document/';

        if (isset($file->extension)) {
            $images_extensions = [
                'ai',
                'bmp',
                'gif',
                'eps',
                'jpeg',
                'jpg',
                'png',
                'ps',
                'psd',
                'svg',
                'tif',
                'tiff',
                'dcm',
                'dicm',
                'dicom',
                'svs',
                'tga'
            ];

            $videos_extensions = [
                '3g2',
                '3gp',
                'avi',
                'm2v',
                'm2ts',
                'm4v',
                'mkv',
                'mov',
                'mp4',
                'mpeg',
                'mpg',
                'ogg',
                'mts',
                'qt',
                'wmv'
            ];

            $audio_extensions = [
                'aac',
                'aifc',
                'aiff',
                'amr',
                'au',
                'flac',
                'm4a',
                'mp3',
                'ogg',
                'ra',
                'wav',
                'wma'
            ];

            if (in_array($file->extension, $images_extensions)) {
                $type = 0;
                $mimeType = 'image/';
            } elseif (in_array($file->extension, $videos_extensions)) {
                $type = 1;
                $mimeType = 'video/';
            } elseif (in_array($file->extension, $audio_extensions)) {
                $type = 2;
                $mimeType = 'audio/';
            }

            $mimeType = $mimeType . $file->extension;
        }

        return new \App\DriveFile(array(
            'id' => $file->id,
            'name' => $file->name,
            'file_name' => $file->name,
            'mime_type' => $mimeType,
            'created_at' => $file->created_at,
            'updated_at' => $file->modified_at,
            'file_path' => $file->download_url,
            'platform' => 'box',
            'type' => $type,
        ));
    }

    public function toFolder($folder)
    {
        return new \App\DriveFolder(array(
            'id' => $folder->id,
            'folder_name' => $folder->name,
            'created_at' => $folder->created_at,
            'updated_at' => $folder->modified_at,
            'default_folder' => 0,
            'platform' => 'box',
        ));
    }

    public function addFolder($name, $parent = 0)
    {
        $guzzle = new Guzzle(['verify'=>false]);
        $url = 'https://api.box.com/2.0/folders';
        $folder = json_decode($guzzle->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Accept'        => 'application/json',
            ],
            'json' => [
                'name' => $name,
                'parent' => ['id' => (string)$parent],
            ]
        ])->getBody()->getContents());
        return $this->toFolder($folder);
    }

    public function addFile($name, $path, $parent = 0)
    {
        $guzzle = new Guzzle(['verify'=>false]);
        try {
            $content = file_get_contents($path);
        } catch (RuntimeException $e) {
            $content='';
        }
        $url = 'https://upload.box.com/api/2.0/files/content';
        $file = json_decode($guzzle->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                // 'Accept'        => 'application/json',
                'Content-MD5'   => sha1($content),
            ],
            'multipart' => [
                [
                    'name' => 'contents',
                    'contents' => $content,
                    'filename' =>$name
                ],
                [
                    'name' => 'attributes',
                    'contents' => json_encode([
                        'name' => $name,
                        'parent' => [
                            'id' => (string)$parent
                        ]
                    ])
                ],
            ]
        ])->getBody()->getContents())->entries[0];
        $file = $this->getFileInfo($file->id);
        return $this->toFile($file);
    }

    public function deleteFolder($media)
    {
        $guzzle = new Guzzle(['verify'=> false]);
        $url = 'https://api.box.com/2.0/folders/'.$media;
        $media = $guzzle->delete($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Accept'        => 'application/json',
            ],
            'query' => [
                'recursive' => true,
            ]
        ]);
    }

    public function deleteMedia($media)
    {
        $guzzle = new Guzzle(['verify'=> false]);
        $url = 'https://api.box.com/2.0/files/'.$media;
        $media = $guzzle->delete($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Accept'        => 'application/json',
            ]
        ]);
    }

    public function getParentsTree($path, $id)
    {
        if (!isset($id)) {
            return [];
        }
        $guzzle = new Guzzle(['verify'=>false]);
        $url = 'https://api.box.com/2.0/folders/'.$id;
        $content = json_decode($guzzle->get($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->access_token,
                'Accept'        => 'application/json',
            ],
            'query' => [
                'fields' => 'id,name,path_collection',
            ],
        ])->getBody()->getContents());
        $folders = $content->path_collection->entries;
        $tree = [$content->id => $content->name];
        $yes = !isset($path);
        foreach (array_reverse($folders, true) as $folder) {
            if ($folder->id == $path) {
                break;
            }
            $tree[$folder->id] = $folder->name;
        }
        return array_reverse($tree, true);
    }
}
