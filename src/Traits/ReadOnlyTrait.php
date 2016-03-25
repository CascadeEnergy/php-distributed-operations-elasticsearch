<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch\Traits;

trait ReadOnlyTrait
{
    use ClientConsumerTrait;

    protected $indexName;

    public function setIndexName($indexName)
    {
        $this->indexName = $indexName;
    }
}
