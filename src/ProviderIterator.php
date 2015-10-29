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
        $source = $hit['source'];

        $operation = new Operation($source['batchId'], $hit['_type'], $source['options']);
        $operation->setState($source['state']);
        $operation->setDisposition($source['disposition']);

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
