<?php

namespace Netframe\Media\DocumentThumbnail;

use Netframe\MediaNetframe\Media\Model\Media;

class OnlyOfficeGenerator implements Generator
{
    public function __construct()
    {
        $this->urlToConverter = sprintf(
            'https://%s:%s%s',
            env('ONLYOFFICE_IP_ADDRESS'),
            env('ONLYOFFICE_PORT'),
            config("office.DOC_SERV_CONVERTER_URL")
        );
    }

    public function execute(Media $media): Thumbnail
    {
        $arrContextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $context  = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'timeout' => config('office.DOC_SERV_TIMEOUT'),
                'header' => "Content-type: application/json\r\n" .
                            "Accept: application/json\r\n",
                'content' => json_encode([
                    "async" => false,
                    "url" => sprintf(
                        'https://%s%s',
                        env('ONLYOFFICE_IP_ADDRESS'),
                        url()->route('office.download', ['mediaId' => $media->id])
                    ),
                    "outputtype" => 'jpg',
                    "filetype" => trim($media->getExtension(), '.'),
                    "title" => $media->name,
                    "key" => sha1($media->id),
                    "thumbnail" => [
                        "first" => true
                    ]
                ]),
            ],
            "ssl" => $arrContextOptions['ssl']
        ]);

        $response = json_decode(
            file_get_contents(
                $this->urlToConverter,
                false,
                $context
            ),
            true
        );

        if (array_key_exists('error', $response)) {
            throw new Exception($response);
        }

        $new_data = file_get_contents($response['fileUrl'], false, stream_context_create($arrContextOptions));
        if (false === $new_data) {
            throw new Exception("Unable to fetch fileUrl data.");
        }

        $thumb_path = storage_path("uploads/images/thumbs-".uniqid().".jpg");
        file_put_contents($thumb_path, $new_data, LOCK_EX);

        return new Thumbnail($thumb_path);
    }
}
