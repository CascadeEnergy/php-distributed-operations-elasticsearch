<?php

namespace CascadeEnergy\DistributedOperations\Elasticsearch;

use CascadeEnergy\DistributedOperations\Operation;
use CascadeEnergy\DistributedOperations\Utility\UtilityFactoryConsumerTrait;
use CascadeEnergy\DistributedOperations\Utility\WaiterInterface;

class Waiter implements WaiterInterface, ElasticsearchUtilityInterface
{
    use ElasticsearchUtilityTrait;
    use UtilityFactoryConsumerTrait;

    const DEFAULT_DELAY_TIME = 5;

    /** @var int */
    private $delayTime = self::DEFAULT_DELAY_TIME;

    /**
     * @param int $delayTime The delay time, in seconds
     */
    public function setDelayTime($delayTime)
    {
        $this->delayTime = $delayTime;
    }

    public function waitForBatch($batchId)
    {
        $counter = $this->utilityFactory->createCounter();
        $counter->setBatchId($batchId);
        $counter->setState(Operation::STATE_NEW);

        do {
            sleep($this->delayTime);
        } while ($counter->getCount() > 0);
    }
}