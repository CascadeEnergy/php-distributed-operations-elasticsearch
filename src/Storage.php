<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\OperationInterface;
use CascadeEnergy\DistributedOperations\Utility\StorageInterface;
use Elasticsearch\Client;

class Storage implements StorageInterface
{
    /** @var Client */
    private $client;

    /** @var string */
    private $readFromIndex;

    /** @var string */
    private $writeToIndex;

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string $readFromIndex
     */
    public function setReadFromIndex($readFromIndex)
    {
        $this->readFromIndex = $readFromIndex;
    }

    /**
     * @param string $writeToIndex
     */
    public function setWriteToIndex($writeToIndex)
    {
        $this->writeToIndex = $writeToIndex;
    }

    public function reload(OperationInterface $operation)
    {
        $indexName = $operation->getStorageAttribute('indexName');

        if (empty($indexName)) {
            $indexName = $this->readFromIndex;
        }

        $hit = $this->client->get([
            'index' => $indexName,
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
