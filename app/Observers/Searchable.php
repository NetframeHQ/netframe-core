<?php

namespace App\Observers;

trait Searchable
{

    public static function bootSearchable()
    {
        if (config('netframe.enabled_elasticsearch')) {
            static::observe(ElasticsearchObserver::class);
        }
    }

    public function getSearchIndex()
    {
        return env('SEARCH_INDEX_PREFIX', '') . $this->getTable();
    }

    public function getSearchType()
    {
        return $this->getTable();
    }

    public function toSearchArray()
    {
        return $this->toReducedArray();
    }
}
