<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\OperationInterface;
use CascadeEnergy\DistributedOperations\Utility\StorageInterface;

class Storage implements StorageInterface, ElasticsearchUtilityInterface
{
    use ElasticsearchUtilityTrait;

    public function store(OperationInterface $operation)
    {
        $parameters = [
            'index' => $this->indexName,
            'type' => $operation->getType(),
            'body' => [
                'state' => $operation->getState(),
                'disposition' => $operation->getDisposition(),
                'batchId' => $operation->getBatchId(),
                'options' => $operation->getOptions()
            ]
        ];

        if (!empty($operation->getId())) {
            $parameters['id'] = $operation->getId();
        }

        $this->client->index($parameters);
    }
}