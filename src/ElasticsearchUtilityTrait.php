<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use Elasticsearch\Client;

trait ElasticsearchUtilityTrait
{
    /** @var Client */
    protected $client;

    /** @var string */
    protected $indexName;

    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    public function setIndexName($indexName)
    {
        $this->indexName = $indexName;
    }
}