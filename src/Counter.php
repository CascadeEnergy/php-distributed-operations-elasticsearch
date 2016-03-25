<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Elasticsearch\Interfaces\ClientConsumerInterface;
use CascadeEnergy\DistributedOperations\Elasticsearch\Interfaces\ReadOnlyInterface;
use CascadeEnergy\DistributedOperations\Elasticsearch\Traits\ReadOnlyTrait;
use CascadeEnergy\DistributedOperations\Utility\AbstractCounter;

class Counter extends AbstractCounter implements ReadOnlyInterface, ClientConsumerInterface
{
    use ReadOnlyTrait;

    public function getCount()
    {
        $termList = [];

        $this->appendTerm($termList, 'batchId', $this->batchId);
        $this->appendTerm($termList, 'disposition', $this->disposition);
        $this->appendTerm($termList, 'state', $this->state);
        $this->appendTerm($termList, 'type', $this->type);

        $parameters = [
            'index' => $this->indexName,
            'body' => ['query' => ['bool' => ['must' => $termList]]]
        ];

        $result = $this->client->count($parameters);

        return intval($result['count']);
    }

    private function appendTerm(&$termList, $name, $value)
    {
        if (!empty($value)) {
            $termList[] = ['term' => [$name => $value]];
        }
    }
}
