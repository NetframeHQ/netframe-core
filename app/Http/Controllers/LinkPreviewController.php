<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Link;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Goutte;
use Illuminate\Support\Facades\Storage;

class LinkPreviewController extends BaseController
{
    public function getMetas()
    {
        $url = request()->get('url');

        if (false === strpos($url, '://')) {
            $url = 'http://' . $url;
        }

        if (filter_var($url, FILTER_VALIDATE_URL) !== false) {
            $final_url = '';
            $title = '';
            $desc = '';
            $result = 'error';
            $linkId = null;
            $screenPath = null;

            // check if link already exists
            $testLink = Link::where('url', '=', $url)->orWhere('final_url', '=', $url)->first();
            if ($testLink != null && 1==0) {
                $result = 'success';
                $linkId = $testLink->id;
                $title = $testLink->title;
                $desc = $testLink->description;
                $screenPath = $testLink->screenshot_path;
            } else {
                // instanciate crawler
                $guzzleClient = new GuzzleClient();
                $onRedirect = function (
                    RequestInterface $request,
                    ResponseInterface $response,
                    UriInterface $uri
                ) {
                };

                $crawlerOption = [
                    'allow_redirects' => [
                        'max'             => 10,        // allow at most 10 redirects.
                        'strict'          => true,      // use "strict" RFC compliant redirects.
                        'referer'         => true,      // add a Referer header
                        'protocols'       => ['https'], // only allow https URLs
                        'on_redirect'     => $onRedirect,
                        'track_redirects' => true
                    ]
                ];

                try {
                    $crawler = $guzzleClient->request('GET', $url, $crawlerOption);

                    // test if response 200 OK and if document is html
                    $lastRedirect = $crawler->getHeaderLine('X-Guzzle-Redirect-History');
                    $final_url = (!empty($lastRedirect)) ? $lastRedirect : $url;

                    if ($crawler->getStatusCode() == 200
                        && mb_eregi('html', $crawler->getHeaderLine('Content-Type'))
                    ) {
                        $crawler2 = Goutte::request('GET', $final_url);
                        try {
                            $title = $crawler2->filter('title')->text();
                            $result = 'success';
                        } catch (\Exception $e) {
                        }

                        try {
                            if ($crawler2->filterXpath('//meta[@name="description"]')->count() == 1) {
                                $desc = $crawler2->filterXpath('//meta[@name="description"]')->attr('content');
                            } else {
                                $desc = '';
                            }
                            $result = 'success';
                        } catch (\Exception $e) {
                        }

                        try {
                            if ($crawler2->filterXpath('//meta[@property="og:image"]')->count() == 1) {
                                $mainImage = $crawler2->filterXpath('//meta[@property="og:image"]')->attr('content');
                            } else {
                                $mainImage = '';
                                $allImages = $crawler2
                                    ->filterXpath('//img')
                                    ->extract(array('src'));

                                if (count($allImages) > 0) {
                                    $mainImage = $allImages[0];
                                }
                            }

                            if (strpos($mainImage, $final_url) === false) {
                                $mainImage = $url . '/' . $mainImage;
                            }
                            if (isset($mainImage)) {
                                $filename = storage_path().'/uploads/screenshots/'.rand().'.jpg';
                                $contents = file_get_contents($mainImage);
                                file_put_contents($filename, $contents);
                            }

                            $result = 'success';
                        } catch (\Exception $e) {
                        }
                    }

                    if ($result == 'success') {
                        $link = new Link();
                        $link->url = $url;
                        $link->final_url = $final_url;
                        $link->title = $title;
                        $link->description = $desc;
                        $link->screenshot_path = (isset($filename)) ? $filename : '';
                        $link->save();
                        $linkId = $link->id;
                    }
                } catch (\Exception $e) {
                    return response()->json([
                        'result' => 'error',
                    ]);
                }
            }

            return response()->json([
                'result' => $result,
                'url' => $url,
                'final_url' => $final_url,
                'title' => $title,
                'desc' => $desc,
                'linkId' => $linkId,
                'screenPath' => $link->screenshot_path,
            ]);
        }
        return response()->json(['result' => 'error']);
    }

    public function download($linkId)
    {
        $link = Link::find($linkId);
        $screenshot = $link->screenshot_path;
        if ($screenshot != null) {
            $response = new Response();
            $response->headers->set(config('media.proxy_file_sending_header'), $screenshot);
            $response->headers->set('Content-Type', 'image/jpg');
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Cache-Control', 'post-check=0, pre-check=0', false);
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $link->id.'.jpg'));
            return $response;
        }
    }
}
