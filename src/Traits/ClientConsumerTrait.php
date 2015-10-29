<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch\Traits;

use Elasticsearch\Client;

trait ClientConsumerTrait
{
    /** @var Client */
    protected $client;

    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}
