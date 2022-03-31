<?php

namespace Adcurve\Adcurve\Api\Data;

interface QueueInterface
{
    public const QUEUE_ID = 'queue_id';
    public const STORE_ID = 'store_id';
    public const PAGE_NO  = 'page_no';
    public const STATUS   = 'status';

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
    public function setQueueId($queueId);

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

    /**
     * Get page_no
     * @return string
     */
    public function getPageNo();

    /**
     * Set page_no
     * @param string $page_no
     * @return Adcurve\Adcurve\Api\Data\QueueInterface
     */
    public function setPageNo($page_no);

    /**
    * Get status
    * @return string
    *
    */
    public function getStatus();

    /**
    * set status
    * @param string $status
    * @return Adcurve\Adcurve\Api\Data\QueueInterface
    */
    public function setStatus($status);
}
