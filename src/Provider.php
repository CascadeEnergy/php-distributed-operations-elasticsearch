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

        $now = time() * 1000;

        $should = [
            ['range' => ['preconditions.notBefore' => ['lt' => $now]]],
            ['missing' => ['field' => 'preconditions.notBefore']]
        ];

        $query = ['bool' => ['filter' => $must, 'should' => $should, 'minimum_should_match' => 1]];

        $sort = [
            "_script" => [
                "script" => "doc['createdTimestamp'].value % slice",
                "type" => "number",
                "params" => [
                    "slice" => floor(mt_rand(1, 2500))
                ],
                "order" => "asc",
                "lang" => "expression"
            ]
        ];

        $body = [
            'query' => $query,
            'sort' => $sort
        ];

        $searchParams = [
            'index' => $this->indexName,
            'size' => 100,
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
