<?php

namespace Netframe\Media\Import;

use Netframe\Media\Model\Media;

/**
 * Imports soundcloud sounds.
 */
class SoundcloudImporter implements ImporterInterface
{
    const REGEX = '#(https?://)?(www.)?soundcloud.com/([a-zA-Z0-9\-\.]*)*/([a-zA-Z0-9\-\.])*#';

    /**
     * {@inheritdoc}
     */
    public function parseId($url)
    {
        preg_match(self::REGEX, $url, $matches);

        $keyApi = config('external-api.soundCloud.key');

        if (isset($matches[3]) && isset($matches[4])) {
            $urlSc = "https://api.soundcloud.com/resolve.json?url=".urlencode($url)."&client_id=".$keyApi;
            $json = json_decode(file_get_contents($urlSc), true);

            if (!isset($json['errors'])) {
                //get thumbnail from youtube and record it to local tmp storage
                $fileName = 'soundcloud'.$json['id'].'.jpg';

                $tmpDir = config('media.tmp_storage');
                $imgThumb = $tmpDir.'/'.$fileName;
                file_put_contents($imgThumb, file_get_contents($json['artwork_url']));

                $response = new \stdClass();
                $response->file_name = $json['id'];
                $response->name = $json['title'];
                $response->description = $json['title'];
                $response->url = $json['permalink_url'];
                $response->thumb = $fileName;
                $response->file_type = Media::TYPE_AUDIO;
            }

            return $response;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlatform()
    {
        return 'soundcloud';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return array(
            'name' => 'Soundcloud',
            'icon' => 'socicon socicon-soundcloud',
        );
    }
}
