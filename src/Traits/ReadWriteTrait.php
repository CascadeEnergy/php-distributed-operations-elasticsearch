<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch\Traits;

trait ReadWriteTrait
{
    use ClientConsumerTrait;

    protected $readFromIndex;
    protected $writeToIndex;

    public function setReadFromIndex($indexName)
    {
        $this->readFromIndex = $indexName;
    }

    public function setWriteToIndex($indexName)
    {
        $this->writeToIndex = $indexName;
    }
}
