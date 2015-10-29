<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use Elasticsearch\Client;

interface ElasticsearchUtilityInterface
{
    public function setClient(Client $client);
    public function setIndexName($indexName);
}