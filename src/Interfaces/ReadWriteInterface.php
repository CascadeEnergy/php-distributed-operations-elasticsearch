<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch\Interfaces;

interface ReadWriteInterface
{
    /**
     * @param string $indexName
     */
    public function setReadFromIndex($indexName);

    /**
     * @param string $indexName
     */
    public function setWriteToIndex($indexName);
}
