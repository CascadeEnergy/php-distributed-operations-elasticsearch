<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Elasticsearch\Interfaces\ReadWriteInterface;
use CascadeEnergy\DistributedOperations\Elasticsearch\Traits\ReadWriteTrait;
use CascadeEnergy\DistributedOperations\OperationInterface;
use CascadeEnergy\DistributedOperations\Utility\StorageInterface;

class Storage implements StorageInterface, ReadWriteInterface
{
    use ReadWriteTrait;

    public function reload(OperationInterface $operation)
    {
        $indexName = $operation->getStorageAttribute('indexName');

        if (empty($indexName)) {
            $indexName = $this->readFromIndex;
        }

        $hit = $this->client->get([
            'index' => $indexName,
            'type' => $operation->getType(),
            'id' => $operation->getId()
        ]);

        $source = $hit['_source'];

        $operation->setOptions($source['options']);
        $operation->setState($source['state']);
        $operation->setDisposition($source['disposition']);

        return $operation;
    }

    public function store(OperationInterface $operation)
    {
        $indexName = $operation->getStorageAttribute('indexName');

        if (empty($indexName)) {
            $dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
            $dateTimeFormatted = $dateTime->format('Y-m');
            $indexName = "{$this->writeToIndex}-$dateTimeFormatted";
            $operation->setStorageAttribute('indexName', $indexName);
        }

        $parameters = [
            'index' => $indexName,
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
