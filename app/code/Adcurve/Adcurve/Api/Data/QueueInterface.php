<?php
namespace Adcurve\Adcurve\Api\Data;

interface QueueInterface
{
    const QUEUE_ID = 'queue_id';
    const STORE_ID = 'store_id';

    /**
     * Get queue_id
     * @return string|null
     */
    public function getQueueId();

    /**
     * Set queue_id
     * @param string $queue_id
     * @return Adcurve\Adcurve\Api\Data\QueueInterface
     */
    public function setQueueId($queueId, $queue_id);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $store_id
     * @return Adcurve\Adcurve\Api\Data\QueueInterface
     */
    public function setStoreId($store_id);
}
