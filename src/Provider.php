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
        $body = [
            'query' => [
                'function_score' => [
                    'query' => ['term' => ['state' => 'new']],
                    'random_score' => new \stdClass()
                ]
            ]
        ];

        $searchParams = [
            'index' => $this->indexName,
            'scroll' => $this->scrollTime,
            'body' => $body
        ];

        if (!empty($this->type)) {
            $searchParams['type'] = $this->type;
        }

        $results = $this->client->search($searchParams);

        return new ProviderIterator($results);
    }
}
