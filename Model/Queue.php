<?php

namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Api\Data\QueueInterface;

class Queue extends \Magento\Framework\Model\AbstractModel implements QueueInterface
{
    public const QUEUE_STATUS_NEW         = 'new';
    public const QUEUE_STATUS_UPDATE      = 'update';
    public const QUEUE_STATUS_COMPLETE    = 'complete';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Adcurve\Adcurve\Model\ResourceModel\Queue');
    }

    /**
     * Get queue_id
     * @return string
     */
    public function getQueueId()
    {
        return $this->getData(self::QUEUE_ID);
    }

    /**
     * Set queue_id
     * @param string $queueId
     * @return Adcurve\Adcurve\Api\Data\QueueInterface
     */
    public function setQueueId($queue_id)
    {
        return $this->setData(self::QUEUE_ID, $queue_id);
    }

    /**
     * Get store_id
     * @return string
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Set store_id
     * @param string $store_id
     * @return Adcurve\Adcurve\Api\Data\QueueInterface
     */
    public function setStoreId($store_id)
    {
        return $this->setData(self::STORE_ID, $store_id);
    }


    /**
     * Get page_no
     * @return string
     */
    public function getPageNo()
    {
        return $this->getData(self::PAGE_NO);
    }

    /**
     * Set page_no
     * @param string $page_no
     * @return Adcurve\Adcurve\Api\Data\QueueInterface
     */
    public function setPageNo($page_no)
    {
        return $this->setData(self::PAGE_NO, $page_no);
    }

    /**
     * Get status
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set status
     * @param string status
     * @return Adcurve\Adcurve\Api\Data\QueueInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}
