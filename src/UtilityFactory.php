<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Elasticsearch\Interfaces\ClientConsumerInterface;
use CascadeEnergy\DistributedOperations\Elasticsearch\Interfaces\ReadOnlyInterface;
use CascadeEnergy\DistributedOperations\Elasticsearch\Interfaces\ReadWriteInterface;
use CascadeEnergy\DistributedOperations\Elasticsearch\Traits\ReadWriteTrait;
use CascadeEnergy\DistributedOperations\Utility\CounterInterface;
use CascadeEnergy\DistributedOperations\Utility\ProviderInterface;
use CascadeEnergy\DistributedOperations\Utility\StorageInterface;
use CascadeEnergy\DistributedOperations\Utility\UtilityFactoryConsumerInterface;
use CascadeEnergy\DistributedOperations\Utility\UtilityFactoryInterface;
use CascadeEnergy\DistributedOperations\Utility\WaiterInterface;

class UtilityFactory implements UtilityFactoryInterface, ReadWriteInterface
{
    use ReadWriteTrait;

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

    private function configureObject($object)
    {
        if ($object instanceof ClientConsumerInterface) {
            $object->setClient($this->client);
        }

        if ($object instanceof ReadOnlyInterface) {
            $object->setIndexName($this->readFromIndex);
        }

        if ($object instanceof ReadWriteInterface) {
            $object->setReadFromIndex($this->readFromIndex);
            $object->setWriteToIndex($this->writeToIndex);
        }

        if ($object instanceof UtilityFactoryConsumerInterface) {
            $object->setUtilityFactory($this);
        }

        return $object;
    }
}
