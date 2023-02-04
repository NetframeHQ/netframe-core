<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\HandlerStack;

class Glowbl
{

    public function connect()
    {
        $url = 'https://live.glowbl.com/web/user/login/lti';

        $stack = HandlerStack::create();

        $oauth = new Oauth1([
            'consumer_key'    => env('GLOWBL_PUBLIC_KEY'),
            'consumer_secret' => env('GLOWBL_SHARED_KEY'),
            'signature' => hash_hmac('SHA1', env('SECRET'), env('GLOWBL_SHARED_KEY'), true),
            'signature_method' => 'HMAC-SHA1',//Oauth1::SIGNATURE_METHOD_HMAC,
            'user_id' => auth()->user()->id,
            'resource_link_id' => auth()->user()->slug,
            'token_secret' => '',
        ]);
        $stack->push($oauth);

        $client = new Client([
            'handler' => $stack
        ]);
        $response = $client->post($url, ['auth' => 'oauth']);
        dump($response->getBody()->getContents());

        // return json_decode($guzzle->get($url, [
        //     'headers' => [
        //         'Authorization' => 'Bearer ' . $this->access_token,
        //         'Accept'        => 'application/json',
        //     ],
        //     'query' => [
        //         'fields' => 'id,modified_at,created_at,name,path_collection,shared_link,extension,download_url',
        //     ],
        // ])->getBody()->getContents());
    }
}
