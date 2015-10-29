<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Utility\CounterInterface;
use CascadeEnergy\DistributedOperations\Utility\ProviderInterface;
use CascadeEnergy\DistributedOperations\Utility\StorageInterface;
use CascadeEnergy\DistributedOperations\Utility\UtilityFactoryConsumerInterface;
use CascadeEnergy\DistributedOperations\Utility\UtilityFactoryInterface;
use CascadeEnergy\DistributedOperations\Utility\WaiterInterface;

class UtilityFactory implements UtilityFactoryInterface
{
    use ElasticsearchUtilityTrait;

    /**
     * @return CounterInterface
     */
    public function createCounter()
    {
        return $this->configureObject(new Counter());
    }

    /**
     * @return ProviderInterface
     */
    public function createProvider()
    {
        return $this->configureObject(new Provider());
    }

    /**
     * @return StorageInterface
     */
    public function createStorage()
    {
        return $this->configureObject(new Storage());
    }

    /**
     * @return WaiterInterface
     */
    public function createWaiter()
    {
        return $this->configureObject(new Waiter());
    }

    /**
     * @param ElasticsearchUtilityInterface $object
     * @return ElasticsearchUtilityInterface
     */
    private function configureObject(ElasticsearchUtilityInterface $object)
    {
        $object->setClient($this->client);
        $object->setIndexName($this->indexName);

        if ($object instanceof UtilityFactoryConsumerInterface) {
            $object->setUtilityFactory($this);
        }

        return $object;
    }
}