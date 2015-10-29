<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Operation;
use CascadeEnergy\DistributedOperations\Utility\CounterInterface;
use CascadeEnergy\DistributedOperations\Utility\UtilityFactoryConsumerInterface;
use CascadeEnergy\DistributedOperations\Utility\UtilityFactoryConsumerTrait;
use CascadeEnergy\DistributedOperations\Utility\WaiterInterface;

class Waiter implements WaiterInterface, UtilityFactoryConsumerInterface
{
    use UtilityFactoryConsumerTrait;

    const DEFAULT_DELAY_TIME = 5;

    /** @var int */
    private $delayTime = self::DEFAULT_DELAY_TIME;

    /** @var CounterInterface[] */
    private $counterCache = [];

    /**
     * @param int $delayTime The delay time, in seconds
     */
    public function setDelayTime($delayTime)
    {
        $this->delayTime = $delayTime;
    }

    public function isBatchDone($batchId)
    {
        $counter = $this->getCounter($batchId);

        if ($counter->getCount() == 0) {
            $this->deleteCounter($batchId);
            return true;
        }

        return false;
    }

    public function waitForBatch($batchId)
    {
        $counter = $this->getCounter($batchId);

        do {
            sleep($this->delayTime);
        } while ($counter->getCount() > 0);

        $this->deleteCounter($batchId);
    }
    
    private function getCounter($batchId)
    {
        if (!array_key_exists($batchId, $this->counterCache)) {
            $counter = $this->utilityFactory->createCounter();
            $counter->setBatchId($batchId);
            $counter->setState(Operation::STATE_NEW);
            
            $this->counterCache[$batchId] = $counter;
        }
        
        return $this->counterCache[$batchId];
    }

    private function deleteCounter($batchId)
    {
        unset($this->counterCache[$batchId]);
    }
}
