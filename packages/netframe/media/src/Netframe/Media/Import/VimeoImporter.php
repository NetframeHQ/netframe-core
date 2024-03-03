<?php

namespace Netframe\Media\Import;

use Netframe\Media\Model\Media;

/**
 * Imports Vimeo videos.
 */
class VimeoImporter implements ImporterInterface
{
    const REGEX = '#(https?://)?(www.)?(player.)?vimeo.com/([a-z]*/)*([0-9]{6,11})[?]?.*#';

    const CLIENTID = 'fcf45dca8ef4b9cfe0df4a8a888fd2e1e4a8101e';
    const CLIENTSECRET = 'mzSsK7KkN+M0Z+'
        . 'jXWT1TmByvTpoYPJORYMYEdEFfewmB6hOtu6z0gkEKrAZi8EEvvntps8NE0FDhkPGHatYz0o6EevMHEQckudMyqSI4l9xCNVBFrwJNeg3'
        . '+ngRxoA+7';
    const OAUTHURL = 'https://api.vimeo.com/oauth/authorize';
    const ACCESTOKENURL = 'https://api.vimeo.com/oauth/access_token';

    /**
     * {@inheritdoc}
     */
    public function parseId($url)
    {
        preg_match(self::REGEX, $url, $matches);

        if (isset($matches[5])) {
            $urlSc = 'https://vimeo.com/api/v2/video/'.$matches[5].'.json';
            $json = json_decode(file_get_contents($urlSc), true);

            //get thumbnail from youtube and record it to local tmp storage

            $fileName = 'vimeo' . $matches[5] . '.jpg';
            $tmpDir = config('media.tmp_storage');
            if (!file_exists($tmpDir)) {
                $result = \File::makeDirectory($tmpDir, 0775, true);
            }

            $imgThumb = $tmpDir.'/'.$fileName;
            file_put_contents($imgThumb, file_get_contents($json[0]['thumbnail_large']));

            $response = new \stdClass();
            $response->file_name = $matches[5];
            $response->name = $json[0]['title'];
            $response->description = $json[0]['title'];
            $response->url = $url;
            $response->thumb = $fileName;
            $response->file_type = Media::TYPE_VIDEO;

            return $response;
            //return $matches[5];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlatform()
    {
        return 'vimeo';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return array(
            'name' => 'Vimeo',
            'icon' => 'socicon socicon-vimeo',
        );
    }
}
