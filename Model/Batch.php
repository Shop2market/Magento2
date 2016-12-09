<?php


namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Api\Data\BatchInterface;

class Batch extends \Magento\Framework\Model\AbstractModel implements BatchInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Adcurve\Adcurve\Model\ResourceModel\Batch');
    }

    /**
     * Get batch_id
     * @return string
     */
    public function getBatchId()
    {
        return $this->getData(self::BATCH_ID);
    }

    /**
     * Set batch_id
     * @param string $batchId
     * @return Adcurve\Adcurve\Api\Data\BatchInterface
     */
    public function setBatchId($batchId)
    {
        return $this->setData(self::BATCH_ID, $batchId);
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
     * @param string $status
     * @return Adcurve\Adcurve\Api\Data\BatchInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}
