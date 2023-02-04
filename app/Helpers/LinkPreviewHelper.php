<?php

namespace App\Helpers;

use App\Link;
use JonnyW\PhantomJs\Client as Phantom;

class LinkPreviewHelper
{
    /*
     * call sample
     * \App\Helpers\LinkPreviewHelper::getInfos('http://www.illisite.fr');
     * \App\Helpers\LinkPreviewHelper::getInfos('http://www.livreblancdefenseetsecurite.gouv.fr/pdf/le_livre_blanc_de_la_defense_2013.pdf');
     * \App\Helpers\LinkPreviewHelper::getInfos('https://www.ovh.com/manager/');
     */

    public static function getInfos($url)
    {
        $title = '';
        $desc = '';
        $result = 'error';
        $linkId = null;
        $screenPath = null;

        // check if link already exists
        $testLink = Link::where('url', '=', $url)->orWhere('final_url', '=', $url)->first();
        if ($testLink != null && 1 == 0) {
            $result = 'success';
            $linkId = $testLink->id;
            $title = $testLink->title;
            $desc = $testLink->description;
            $screenPath = $testLink->screenshot_path;
        } else {
            // instanciate crawler
            $crawlerInstance = new \Goutte();
            $crawlerInstance::setMaxRedirects(5);
            $crawler = $crawlerInstance::request('GET', $url);

            // instanciate response
            $crawlResponse = $crawlerInstance::getResponse();

            // test if response 200 OK and if document is html
            $final_url = $crawler->getUri();
            if ($crawlResponse->getStatus() == 200 && mb_eregi('html', $crawlResponse->getHeader('Content-Type'))) {
                try {
                    $title = $crawler->filter('title')->text();
                    $result = 'success';
                } catch (Exception $e) {
                }

                try {
                    $desc = $crawler->filterXpath('//meta[@name="description"]')->attr('content');
                    $result = 'success';
                } catch (Exception $e) {
                }
            }

            // screenshot website
            $filename = storage_path().'/uploads/tmp/test-phantom.jpg';

            // try with phantomjs   http://jonnnnyw.github.io/php-phantomjs/
            $phantom = Phantom::getInstance();
            $phantom->getEngine()->setPath(base_path().'/bin/phantomjs');
            $phantom->isLazy();
            $width  = 1280;
            $height = 1024;
            $top    = 0;
            $left   = 0;
            $prequest = $phantom->getMessageFactory()->createCaptureRequest($final_url, 'GET');
            $prequest->setOutputFile($filename);
            $prequest->setViewportSize($width, $height);
            $prequest->setCaptureDimensions($width, $height, $top, $left);
            $presponse = $phantom->getMessageFactory()->createResponse();
            $phantom->send($prequest, $presponse);

            if ($result == 'success') {
                $link = new Link();
                $link->url = $url;
                $link->final_url = $final_url;
                $link->title = $title;
                $link->description = $desc;
                $link->screenshot_path = '';
                $link->save();
                $linkId = $link->id;
            }
        }

        return [
            'result' => $result,
            'url' => $url,
            'title' => $title,
            'desc' => $desc,
            'linkId' => $linkId,
            'screenPath' => $screenPath,
        ];
    }
}
