<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Operation;

class ProviderIterator implements \Iterator
{
    /** @var array The raw Elasticsearch response we are iterating through */
    private $response;

    private $hitIterator;

    public function __construct(array $response)
    {
        $this->response = $response;
        $this->hitIterator = new \ArrayIterator($response['hits']['hits']);
    }

    public function current()
    {
        $hit = $this->hitIterator->current();
        $source = $hit['_source'];

        $operation = new Operation($hit['_type'], $source['batchId'], $source['options']);
        $operation->setState($source['state']);
        $operation->setDisposition($source['disposition']);
        $operation->setId($hit['_id']);
        $operation->setStorageAttribute('indexName', $hit['_index']);
        $operation->setChannel($source['channel']);
        $operation->setPreconditions($source['preconditions']);

        return $operation;
    }

    public function next()
    {
        $this->hitIterator->next();
    }

    public function key()
    {
        return $this->hitIterator->key();
    }

    public function valid()
    {
        return $this->hitIterator->valid();
    }

    public function rewind()
    {
        $this->hitIterator->rewind();
    }
}
