<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch\Interfaces;

interface ReadOnlyInterface
{
    /**
     * @param string $indexName
     */
    public function setIndexName($indexName);
}
