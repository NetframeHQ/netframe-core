<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\BaseController as Controller;
use App\Search\Engine as SearchEngine;
use App\Search\Query as SearchQuery;

class Search extends Controller
{
    const DEFAULT_RESPONSE_CONTENT_TYPE = 'text/html';

    /**
     * @var SearchEngine
     */
    private $engine;

    /**
     * @param SearchEngine $engine
     */
    public function __construct(SearchEngine $engine)
    {
        $this->engine = $engine;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $acceptableContentTypes = $request->getAcceptableContentTypes();
        $response = null;
        $results = $resultsBySection = [];
        $resultsPerPage = 10;
        $term  = $request->get('term', $request->get('query', ''));
        $types = $request->get('types', []);
        $section = $request->get('section', '');
        $page = (int) $request->get('page', 1);
        $from = ($page-1) * $resultsPerPage;
        $size = $resultsPerPage*$page;
        $groupBySections = count($types)<=0;
        $query = new SearchQuery($term, $from, $resultsPerPage);
        $sections = [
            'documents' => [
                'types' => ['medias'],
                'nbResultsMax' => 5,
                'icon' => 'doc',
                'iconBig' => 'doc',
                'i18n' => 'search.menu.medias',
            ],
            'contents' => [
                'types' => ['news', 'events'],
                'nbResultsMax' => 10,
                'icon' => 'publication',
                'iconBig' => 'publication',
                'i18n' => 'search.menu.posts',
            ],
            'profiles' => [
                'types' => ['users', 'houses', 'projects', 'community', 'channels'],
                'nbResultsMax' => 20,
                'icon' => 'profile',
                'iconBig' => 'profile',
                'i18n' => 'search.menu.profiles',
            ],
        ];

        if ($groupBySections) {
            foreach ($sections as $idSection => $params) {
                $sectionQuery = new SearchQuery(
                    $term,
                    $from,
                    $params['nbResultsMax'],
                    $params['types']
                );
                $sectionResults = $this->engine->search($sectionQuery);
                $resultsBySection[$idSection] = $sectionResults;
            }
        } else {
            if ($section) {
                // redirect with the right types if section specified
                $shouldHaveTypes = $sections[$section]['types'];
                if ($types != $shouldHaveTypes) {
                    return redirect(
                        request()->fullUrlWithQuery(['types' => $shouldHaveTypes])
                    );
                }
            }
            $query = new SearchQuery($term, $from, $resultsPerPage, $types);
        }

        $results = $this->engine->search($query);

        // Build reponse formatted in first acceptable content type found
        do {
            switch (array_shift($acceptableContentTypes) ?: self::DEFAULT_RESPONSE_CONTENT_TYPE) {
                // simple json response
                case 'application/json':
                    $response = response()->json($this->formatter($results));
                    break;

                // html response using blade template
                case 'text/html':
                    if (isset($results['hits']['total']['value']) && is_int($results['hits']['total']['value'])) {
                        $total = $results['hits']['total']['value'];
                    } elseif (isset($results['hits']['total']) && is_int($results['hits']['total'])) {
                        $total = $results['hits']['total'];
                    } else {
                        $total = 0;
                    }

                    $data = [
                        'query' => $query,
                        'sections' => $sections,
                        'results' => $results,
                        'total' => $total,
                        'groupBySections' => $groupBySections,
                        'resultsBySection' => $resultsBySection,
                        'pagination' => [
                            'current' => $page,
                            'total' => ceil($total/$resultsPerPage),
                        ],
                    ];

                    $response = view(
                        'search.results',
                        $data
                    );
                    break;
            }
        } while (is_null($response));

        return $response;
    }

    private function formatter($results)
    {
        $data = [];
        foreach ($results['hits']['hits'] as $result) {
            switch ($result['_type']) {
                case 'users':
                    if ($result['_source']['active'] == 1) {
                        $data[] = [
                            'type' => $result['_type'],
                            'id' => sprintf('%s-%d', $result['_type'], $result['_id']),
                            'label' => $result['_source']['fullname'],
                            'thumb' => \HTML::thumbImage(
                                $result['_source']['profile_media_id'],
                                40,
                                40,
                                [],
                                $result['_type'].'_big',
                                'avatar'
                            ),
                            'value' => $result['_source']['url'],
                            'user' => User::find($result['_id']),
                        ];
                    }
                    break;
                case 'medias':
                    $data[] = [
                        'type' => $result['_type'],
                        'id' => sprintf('%s-%d', $result['_type'], $result['_id']),
                        'label' => $result['_source']['name'],
                        'thumb' => \HTML::thumbImage(
                            $result['_id'],
                            100,
                            100,
                            [],
                            $result['_type']
                        ),
                        'value' => $result['_source']['url'],
                    ];
                    break;
                case 'houses':
                    if ($result['_source']['active'] == 1) {
                        $data[] = [
                            'type' => $result['_type'],
                            'id' => sprintf('%s-%d', $result['_type'], $result['_id']),
                            'label' => $result['_source']['name'],
                            'profilemedia' => $result['_source']['profile_media_id'],
                            'thumb' => \HTML::thumbImage(
                                $result['_source']['profile_media_id'],
                                40,
                                40,
                                [],
                                $result['_type']
                            ),
                            'value' => $result['_source']['url'],
                        ];
                    }
                    break;
                case 'community':
                    if ($result['_source']['active'] == 1) {
                        $data[] = [
                            'type' => $result['_type'],
                            'id' => sprintf('%s-%d', $result['_type'], $result['_id']),
                            'label' => $result['_source']['name'],
                            'thumb' => \HTML::thumbImage(
                                $result['_source']['profile_media_id'],
                                40,
                                40,
                                [],
                                $result['_type']
                            ),
                            'value' => $result['_source']['url'],
                        ];
                    }
                    break;
                case 'projects':
                    if ($result['_source']['active'] == 1) {
                        $data[] = [
                            'type' => $result['_type'],
                            'id' => sprintf('%s-%d', $result['_type'], $result['_id']),
                            'label' => $result['_source']['title'],
                            'thumb' => \HTML::thumbImage(
                                $result['_source']['profile_media_id'],
                                40,
                                40,
                                [],
                                $result['_type']
                            ),
                            'value' => $result['_source']['url'],
                        ];
                    }
                    break;

                case 'news':
                    $defaultThumbs = [
                        'App\Community' => 'community',
                        'App\Project' => 'project',
                        'App\House' => 'house',
                        'App\User' => 'user',
                    ];
                    $data[] = [
                        'type' => $result['_type'],
                        'id' => sprintf('%s-%d', $result['_type'], $result['_id']),
                        'label' => $result['_source']['content'],
                        'thumb' => \HTML::thumbImage(
                            $result['_source']['media_id'] ?: $result['_source']['author']['profile_media_id'],
                            40,
                            40,
                            [],
                            $defaultThumbs[$result['_source']['author_type']]
                        ),
                        'value' => $result['_source']['url'],
                    ];
                    break;

                case 'channels':
                    if ($result['_source']['active'] == 1) {
                        $data[] = [
                            'type' => $result['_type'],
                            'id' => sprintf('%s-%d', $result['_type'], $result['_id']),
                            'label' => 'personnal'!=$result['_source']['name']
                                ? $result['_source']['name']
                                : array_reduce(
                                    $result['_source']['users'],
                                    function ($label, $user) {
                                        if ($user['id'] != auth()->guard('web')->user()->id) {
                                            $label = $user['fullname'];
                                        }
                                        return $label;
                                    },
                                    $result['_source']['name']
                                ),
                            'thumb' => \HTML::thumbImage(
                                $result['_source']['profile_media_id'],
                                40,
                                40,
                                [],
                                $result['_type']
                            ),
                            'value' => $result['_source']['url'],
                        ];
                    }
                    break;

                default:
                    $data[] = [
                        'type' => $result['_type'],
                        'id' => sprintf('%s-%d', $result['_type'], $result['_id']),
                        'label' => sprintf('%s-%d', $result['_type'], $result['_id']),
                        'thumb' => null,
                        'value' => $result['_source']['url'],
                    ];
                    break;
            }
        }
        return $data;
    }
}
