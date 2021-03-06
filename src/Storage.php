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
        $operation->setBatchId($source['batchId']);

        if (array_key_exists('familyId', $source)) {
            $operation->setFamilyId($source['familyId']);
        }

        if (array_key_exists('channel', $source)) {
            $operation->setChannel($source['channel']);
        }

        if (array_key_exists('preconditions', $source)) {
            $operation->setPreconditions($source['preconditions']);
        }

        if (array_key_exists('createdTimestamp', $source)) {
            $operation->setCreatedTimestamp($source['createdTimestamp']);
        }

        if (array_key_exists('modifiedTimestamp', $source)) {
            $operation->setModifiedTimestamp($source['modifiedTimestamp']);
        }

        return $operation;
    }

    public function store(OperationInterface $operation)
    {
        $indexName = $operation->getStorageAttribute('indexName');
        $dateTime = new \DateTime('now', new \DateTimeZone('UTC'));

        if (empty($indexName)) {
            $dateTimeFormatted = $dateTime->format('Y-m');
            $indexName = "{$this->writeToIndex}-$dateTimeFormatted";
            $operation->setStorageAttribute('indexName', $indexName);
        }

        $operation->setModifiedTimestamp($dateTime->format('c'));

        $parameters = [
            'index' => $indexName,
            'type' => $operation->getType(),
            'body' => [
                'createdTimestamp' => $operation->getCreatedTimestamp(),
                'modifiedTimestamp' => $operation->getModifiedTimestamp(),
                'state' => $operation->getState(),
                'disposition' => $operation->getDisposition(),
                'batchId' => $operation->getBatchId(),
                'familyId' => $operation->getFamilyId(),
                'options' => $operation->getOptions(),
                'channel' => $operation->getChannel(),
                'preconditions' => $operation->getPreconditions()
            ]
        ];

        if (!empty($operation->getId())) {
            $parameters['id'] = $operation->getId();
        }

        $this->client->index($parameters);
    }
}
