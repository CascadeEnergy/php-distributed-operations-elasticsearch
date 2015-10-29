<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Operation;
use CascadeEnergy\DistributedOperations\Utility\ProviderInterface;
use Elasticsearch\Helper\Iterators\SearchHitIterator;
use Elasticsearch\Helper\Iterators\SearchResponseIterator;

class Provider implements ProviderInterface, ElasticsearchUtilityInterface, \IteratorAggregate
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

        return new ProviderIterator($this->hitIterator);
    }

    public function setScrollTime($scrollTime)
    {
        $this->scrollTime = $scrollTime;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
}
