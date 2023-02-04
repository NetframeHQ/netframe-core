<?php

namespace App\Search;

use Elasticsearch\Client as ElasticsearchClient;
use Elasticsearch\Common\Exceptions\Missing404Exception as Missing404ElasticsearchException;
use Log;

class Engine
{
    /**
     * @var ElasticsearchClient
     */
    private $elasticsearchClient;

    /**
     * @param ElasticsearchClient $elasticsearchClient
     */
    public function __construct(ElasticsearchClient $elasticsearchClient)
    {
        $this->elasticsearchClient = $elasticsearchClient;
    }

    /**
     * @param Query $query
     *
     * @return Array
     */
    public function search(Query $query)
    {
        $results = null;
        $types = $query->types();
        
        do {
            try {
                $results = $this->elasticsearchClient->search($query->get());

            /* au tout début d'une instance, il est possible que les index n'existent
               pas car aucune données ne sont crées. On recommence la requête sans les
               index qui n'éxistent pas au lieu d'avoir une erreur. */
            } catch (Missing404ElasticsearchException $e) {
                $message = json_decode($e->getMessage());
                if ('index_not_found_exception' === $message->error->type) {
                    Log::error("Index not found", (array) $message->error);

                    $index = array_search($message->error->index, $query->types());
                    unset($types[$index]);
                    $query = new Query($query->term(), $query->from(), $query->size(), $types);
                }
            }
        } while (!$results && !empty($types));

        return $results;
    }
}
