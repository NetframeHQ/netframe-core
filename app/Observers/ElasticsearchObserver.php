<?php

namespace App\Observers;

use Elasticsearch\Client;

class ElasticsearchObserver
{

    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function saved($model)
    {

        if ($model instanceof \App\Taggable) {
            $post = $model->taggable_type::find($model->taggable_id);
            if (!$post instanceof \App\TaskTable) {
                self::index($post);
            }
        } elseif ($model instanceof \App\NewsFeed
            && in_array(class_basename($model->post), ['News', 'Offer', 'TEvent'])) {
            if (class_basename($model->author) != 'Channel') {
                self::index($model->post);
            } elseif ($model->author->personnal == 0) {
                self::index($model->post);
            }
        } elseif (in_array(class_basename($model), ['Community', 'House', 'Project', 'Channel'])) {
            if ($model->active==1
                && ((class_basename($model) == 'Channel'
                    && $model->personnal == 0)
                    || class_basename($model) != 'Channel'
                )
            ) {
                self::index($model);
            }
        } elseif ($model instanceof \App\Media) {
            self::index($model);
        }
    }

    public function deleted($model)
    {
        if ($model instanceof \App\Taggable) {
            $post = $model->taggable_type::find($model->taggable_id);
            self::delete($post);
        } elseif (class_basename($model) != 'NewsFeed') {
            self::delete($model);
        }
    }

    public function index($model)
    {
        $this->client->index([
                    'index' => $model->getSearchIndex(),
                    'type' => $model->getSearchType(),
                    'id' => $model->id,
                    'body' => $model->toSearchArray(),
                ]);
    }

    private function delete($model)
    {
        if ((class_basename($model) == 'Media' && $model->keep_files == 0 && $model->under_workflow == 0) ||
            class_basename($model) != 'Media') {
            $this->client->delete([
                'index' => $model->getSearchIndex(),
                'type' => $model->getSearchType(),
                'id' => $model->id,
            ]);
        }
    }
}
