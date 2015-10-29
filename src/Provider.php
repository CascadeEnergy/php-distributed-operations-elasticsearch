<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Operation;
use CascadeEnergy\DistributedOperations\Utility\ProviderInterface;
use Elasticsearch\Helper\Iterators\SearchHitIterator;
use Elasticsearch\Helper\Iterators\SearchResponseIterator;

class Provider implements ProviderInterface, ElasticsearchUtilityInterface, \Iterator
{
    use ElasticsearchUtilityTrait;

    /** @var SearchResponseIterator */
    private $responseIterator;

    /** @var SearchHitIterator */
    private $hitIterator;

    /** @var string */
    private $scrollTime = '1m';

    /** @var string */
    private $type;

    public function getIterator()
    {
        $searchParams = [
            'index' => $this->indexName,
            'scroll' => $this->scrollTime,
            'body' => ['query' => ['bool' => ['must' => ['term' => ['state' => 'new']]]]]
        ];

        if (!empty($this->type)) {
            $searchParams['type'] = $this->type;
        }

        $this->responseIterator = new SearchResponseIterator($this->client, $searchParams);
        $this->hitIterator = new SearchHitIterator($this->responseIterator);

        return $this;
    }

    public function setScrollTime($scrollTime)
    {
        $this->scrollTime = $scrollTime;
    }

    public function setType($type)
    {
        $this->type = $type;
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