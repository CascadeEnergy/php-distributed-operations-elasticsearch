<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Elasticsearch\Interfaces\ReadOnlyInterface;
use CascadeEnergy\DistributedOperations\Elasticsearch\Traits\ReadOnlyTrait;
use CascadeEnergy\DistributedOperations\Utility\ProviderInterface;

class Provider implements ProviderInterface, ReadOnlyInterface
{
    use ReadOnlyTrait;

    /** @var string */
    private $scrollTime = '1m';

    /** @var string */
    private $type;

    /** @var string */
    private $channel;

    public function setScrollTime($scrollTime)
    {
        $this->scrollTime = $scrollTime;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
    }

    public function begin()
    {
        $must = [
            ['term' => ['state' => 'new']]
        ];

        if (!empty($this->channel)) {
            $must[] = ['term' => ['channel' => $this->channel]];
        }

        $query = ['bool' => ['must' => $must]];

        $body = [
            'query' => [
                'function_score' => [
                    'query' => $query,
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

    public function end(\Traversable $providerIterator)
    {
    }
}
