<?php

namespace Netframe\Media\Import;

use Netframe\Media\Model\Media;

/**
 * Imports Dailymotion videos.
 */
class DailymotionImporter implements ImporterInterface
{
    /**
     * {@inheritdoc}
     */
    public function parseId($url)
    {
        preg_match('#(https?://)?www.dailymotion.com/swf/video/([A-Za-z0-9\-_]+)#s', $url, $matches);

        $response = new \stdClass();
        $response->name = $url;
        $response->url = $url;
        $response->thumb = '';
        $response->file_type = Media::TYPE_VIDEO;

        if (!isset($matches[2])) {
            preg_match('#(https?://)?www.dailymotion.com/video/([A-Za-z0-9\-_]+)#s', $url, $matches);

            if (!isset($matches[2])) {
                preg_match('#(https?://)?www.dailymotion.com/embed/video/([A-Za-z0-9\-_]+)#s', $url, $matches);

                if (isset($matches[2]) && strlen($matches[2])) {
                    $response->file_name = $matches[2];
                    $idDailymotion = $matches[2];
                }
            } elseif (strlen($matches[1])) {
                $response->file_name = $matches[2];
                $idDailymotion = $matches[2];
            }
        } else {
            $response->file_name = $matches[2];
            $idDailymotion = $matches[2];
        }

        if (isset($idDailymotion)) {
            $urlSc = 'https://api.dailymotion.com/video/'
                . $matches[2]
                . '?fields=title,thumbnail_180_url,thumbnail_large_url';
            $json = json_decode(file_get_contents($urlSc), true);

            //get thumbnail from youtube and record it to local tmp storage
            $fileName = 'dailymotion'.$idDailymotion.'.png';
            $tmpDir = config('media.tmp_storage');
            if (!file_exists($tmpDir)) {
                $result = \File::makeDirectory($tmpDir, 0775, true);
            }
            $imgThumb = $tmpDir.'/'.$fileName;
            file_put_contents($imgThumb, file_get_contents($json['thumbnail_large_url']));

            $response = new \stdClass();
            $response->file_name = $idDailymotion;
            $response->name = $json['title'];
            $response->description = $json['title'];
            $response->url = $url;
            $response->thumb = $fileName;
            $response->file_type = Media::TYPE_VIDEO;

            return $response;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlatform()
    {
        return 'dailymotion';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return array(
            'name' => 'Dailymotion',
            'icon' => 'socicon socicon-dailymotion',
        );
    }
}
