<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Operation;
use Elasticsearch\Helper\Iterators\SearchHitIterator;

class ProviderIterator implements \Iterator
{
    /** @var SearchHitIterator */
    private $hitIterator;

    public function __construct(SearchHitIterator $hitIterator)
    {
        $this->hitIterator = $hitIterator;
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
