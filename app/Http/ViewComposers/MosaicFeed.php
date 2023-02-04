<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Repository\SearchRepository2;

class MosaicFeed
{

    /**
     * get profile for mosaic empty feed
     */

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $searchRepository = new SearchRepository2();
        $searchRepository->route = 'search_mosaic';
        $searchRepository->targetsProfiles = [
            'user' => 1
        ];
        $searchRepository->toggleFilter = true;
        $searchRepository->byInterests = 0;
        $searchRepository->newProfile = 0;
        $searchRepository->search_limit = config('netframe.search_limit') + 1;
        $searchRepository->validTalents = 1;

        $searchParameters = $searchRepository->initializeConfig();
        $results = $searchRepository->search($searchParameters, $searchRepository->targetsProfiles);

        $profiles = $results[0];
        foreach ($profiles as $key => $profile) {
            if (isset($profiles[$key + 1])) {
                $profiles[$key]->nextId = $profiles[$key + 1]->id;
                $profiles[$key]->nextProfile = $profiles[$key + 1]->getType();
            } else {
                $profiles[$key]->nextId = 0;
                $profiles[$key]->nextProfile = '0';
            }

            if (isset($profiles[$key - 1])) {
                $profiles[$key]->prevId = $profiles[$key - 1]->id;
                $profiles[$key]->prevProfile = $profiles[$key - 1]->getType();
            } else {
                $profiles[$key]->prevId = 0;
                $profiles[$key]->prevProfile = '0';
            }
        }
        return $view->with('profiles', $profiles);
    }
}
