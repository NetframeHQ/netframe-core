<?php

namespace Netframe\Media\Import;

use Netframe\Media\Model\Media;

/**
 * Imports Youtube videos.
 */
class YoutubeImporter implements ImporterInterface
{
    const REGEX = '#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)'
        . '|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+'
        . '|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+#';

    /**
     * {@inheritdoc}
     */
    public function parseId($url)
    {
        preg_match(self::REGEX, $url, $matches);

        if (isset($matches[0])) {
            $keyApi = config('external-api.youtubeApi.server-key');
            $urlSc = 'https://www.googleapis.com/youtube/v3/videos?id='.$matches[0].'&part=snippet&key='.$keyApi;
            $json = json_decode(file_get_contents($urlSc), true);

            //get thumbnail from youtube and record it to local tmp storage
            $fileName = 'youtube'.$matches[0].'.jpg';
            $tmpDir = config('media.tmp_storage');
            if (!file_exists($tmpDir)) {
                $result = \File::makeDirectory($tmpDir, 0775, true);
            }

            $imgThumb = $tmpDir.'/'.$fileName;
            file_put_contents(
                $imgThumb,
                file_get_contents($json['items'][0]['snippet']['thumbnails']['medium']['url'])
            );

            $response = new \stdClass();
            $response->file_name = $matches[0];
            $response->name = $json['items'][0]['snippet']['title'];
            $response->description = $json['items'][0]['snippet']['title'];
            $response->url = $url;
            $response->thumb = $fileName;
            $response->file_type = Media::TYPE_VIDEO;
            //$response->description =

            return $response;
            //return $matches[0];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlatform()
    {
        return 'youtube';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return array(
            'name' => 'Youtube',
            'icon' => 'socicon socicon-youtube',
        );
    }
}
