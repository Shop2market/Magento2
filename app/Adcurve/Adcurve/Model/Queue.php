<?php


namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Api\Data\QueueInterface;

class Queue extends \Magento\Framework\Model\AbstractModel implements QueueInterface
{

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
    public function setQueueId($queueId, $queue_id)
    {
        return $this->setData(self::QUEUE_ID, $queueId);

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
}
