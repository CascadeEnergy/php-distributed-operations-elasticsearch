<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Elasticsearch\Interfaces\ReadOnlyInterface;
use CascadeEnergy\DistributedOperations\Elasticsearch\Traits\ReadOnlyTrait;
use CascadeEnergy\DistributedOperations\Utility\ProviderInterface;
use Elasticsearch\Helper\Iterators\SearchResponseIterator;

class Provider implements ProviderInterface, ReadOnlyInterface
{
    use ReadOnlyTrait;

    /** @var string */
    private $scrollTime = '1m';

    /** @var string */
    private $type;

    public function setScrollTime($scrollTime)
    {
        $this->scrollTime = $scrollTime;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function begin()
    {
        $searchParams = [
            'index' => $this->indexName,
            'scroll' => $this->scrollTime,
            'body' => ['query' => ['bool' => ['must' => ['term' => ['state' => 'new']]]]]
        ];

        if (!empty($this->type)) {
            $searchParams['type'] = $this->type;
        }

        $responseIterator = new SearchResponseIterator($this->client, $searchParams);

        return new ProviderIterator($responseIterator);
    }

    public function end(\Traversable $providerIterator)
    {
        if ($providerIterator instanceof ProviderIterator) {
            $providerIterator->end();
        }
    }
}
