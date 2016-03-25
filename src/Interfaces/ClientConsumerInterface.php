<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch\Interfaces;

use Elasticsearch\Client;

interface ClientConsumerInterface
{
    public function setClient(Client $client);
}
