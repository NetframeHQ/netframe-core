<?php

namespace App\Search;

class Query
{
    private $term = '';
    private $from = 0;
    private $size = 100;
    private $types = [];
    private $indexes = [];
    private $fullQuery = [];

    /**
     *
     */
    public function __construct(
        $term = null,
        $from = 0,
        $size = 100,
        $types = ['houses', 'projects', 'community', 'users', 'channels', 'medias', 'news', 'events']
    ) {
        $currentInstanceId = session('instanceId');
        $currentUser = auth()->guard('web')->user();
        $currentUserId = $currentUser->id;
        $currentProfiles = array_reduce(
            $currentUser->allProfiles(),
            function ($profiles, $profile) {
                if (!empty($profile->slug)) {
                    $profiles[] = $profile->slug;
                }
                return $profiles;
            },
            []
        );

        $this->term = $term;
        $this->from = $from;
        $this->size = $size;
        $this->types = $types;

        $this->indexes = array_map(function ($el) {
            return env('SEARCH_INDEX_PREFIX', '') . $el;
        },
        $this->types);

        $this->fullQuery = [
            'index' => $this->indexes,
            'type' => $this->types,
            'body' => [
                'from' => $this->from,
                'size' => $this->size,
                'sort' => [
                    ['_score' => ['order' => 'desc']],
                    ['created_at' => ['order' => 'desc']]
                ],
                'query' => [
                    'bool' => [
                        'must' => [

                            /* l'objet doit être lié à l'instance */
                            [
                                'bool' => [
                                    'should' => [
                                        ['match' => ['instance' => $currentInstanceId]],
                                        ['match' => ['instances.id' => $currentInstanceId]],
                                    ],
                                    'minimum_should_match' => 1,
                                ]
                            ],
                            /* on doit avoir le droit de trouver l'objet : */
                            [
                                'bool' => [
                                    'should' => [

                                        /* publié par un utilisateur */
                                        [
                                            'bool' => [
                                                'must' => [
                                                    ['term' => ['confidentiality' => 1]],
                                                    ['match'  => ['profile_id' => 'user-']],
                                                ],
                                            ],
                                        ],

                                        /* publié dans un profil ou je suis inscrit */
                                        [
                                            'bool' => [
                                                'should' => [
                                                    ['terms' => ['profile.slug' => $currentProfiles]],
                                                    ['term' => ['profile.confidentiality' => 1]],
                                                ],
                                                'minimum_should_match' => 1,
                                            ],
                                        ],

                                        /* les profils publics ou auquels je suis inscrit */
                                        [
                                            'bool' => [
                                                'should' => [
                                                    ['term' => ['confidentiality' => 1]],
                                                    ['term' => ['users.id' => $currentUserId]],
                                                    ['terms' => ['_index' => [
                                                        'users', 'houses', 'projects', 'community'
                                                    ]]],
                                                ],
                                                'minimum_should_match' => 2,
                                            ],
                                        ],

                                    ],
                                    'minimum_should_match' => 1,
                                ]
                            ],

                            /* on exclue les news qui n'ont aucun contenu et juste des documents */
                            [
                                'bool' => [
                                    'should' => [
                                        ['match' => ['only_images' => false]],
                                        ['terms' => ['_index' => [
                                            'houses',
                                            'projects',
                                            'community',
                                            'users',
                                            'channels',
                                            'medias',
                                            'events',
                                        ]]],
                                    ],
                                    'minimum_should_match' => 1,
                                ],
                            ],

                        ],

                    ]
                ]
            ]
        ];

        if ($term) {
            $this->fullQuery['body']['query']['bool']['must'][]['bool'] = [
                'should' => [
                    ['match_phrase_prefix' => ['fullname' => ['query' => $term, 'boost' => 5]]],
                    ['match_phrase_prefix' => ['name' => ['query' => $term, 'boost' => 5]]],
                    ['match_phrase_prefix' => ['email' => ['query' => $term]]],
                    ['match_phrase_prefix' => ['title' => ['query' => $term, 'boost' => 3]]],
                    ['match_phrase_prefix' => ['description' => ['query' => $term, 'boost' => 2]]],
                    ['match_phrase_prefix' => ['author.fullname' => ['query' => $term, 'boost' => 1]]],
                    ['match_phrase_prefix' => ['data' => $term]],
                    ['match_phrase_prefix' => ['content' => $term]],
                    ['match_phrase_prefix' => ['training' => $term]],
                    ['term' =>  ['tags.name' => $term]]
                ],
                'minimum_should_match' => 1
            ];
        }
    }

    /**
     * Get Elastic fullquery as an array.
     *
     * @return Array
     */
    public function get()
    {
        return $this->fullQuery;
    }

    /**
     * Get query term.
     *
     * @return string
     */
    public function term()
    {
        return $this->term;
    }

    public function from()
    {
        return $this->from;
    }

    public function size()
    {
        return $this->size;
    }

    /**
     * Get types (and index) where the query has to search.
     *
     * @return array
     */
    public function types()
    {
        return $this->types;
    }
}
