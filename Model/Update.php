<?php


namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Api\Data\UpdateInterface;

class Update extends \Magento\Framework\Model\AbstractModel implements UpdateInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Adcurve\Adcurve\Model\ResourceModel\Update');
    }

    /**
     * Get update_id
     * @return string
     */
    public function getUpdateId()
    {
        return $this->getData(self::UPDATE_ID);
    }

    /**
     * Set update_id
     * @param string $updateId
     * @return Adcurve\Adcurve\Api\Data\UpdateInterface
     */
    public function setUpdateId($updateId)
    {
        return $this->setData(self::UPDATE_ID, $updateId);
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
     * @return Adcurve\Adcurve\Api\Data\UpdateInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }
}
