<?php
namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Api\Data\UpdateInterface;

class Update extends \Magento\Framework\Model\AbstractModel implements UpdateInterface
{
	const PRODUCT_UPDATE_STATUS_NEW         = 'new';
	const PRODUCT_UPDATE_STATUS_UPDATE 	    = 'update';
    const PRODUCT_UPDATE_STATUS_COMPLETE    = 'complete';
    const PRODUCT_UPDATE_STATUS_ERROR       = 'error';
    const PRODUCT_UPDATE_STATUS_HOLD        = 'hold';
	
	protected $dateTime;
	
	public function __construct(
    	\Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
		\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
	)
    {
    	parent::__construct($context, $registry, $resource, $resourceCollection, $data);
		$this->dateTime = $dateTime;
	}
	
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
	
	/**
     * Get the unserialized info
     * @param string|null $key
     * @return mixed
     */
    public function getProductData($key = null)
    {
    	$serializedData = $this->getData('product_data');
		if (!$serializedData) {
			return false;
		}
        $productData = unserialize($serializedData);
        if ($key && isset($productData[$key])) {
            return $productData[$key];
        }
		
        return $productData;
    }

    /**
     * Serialize the info and add it to the update's data
     * @param array $productData
     * @return $this
     */
    public function setProductData($productData)
    {
        $this->setData('product_data', serialize($productData));
        return $this;
    }
	
	/**
     * Complete the update
     *
     * @throws Exception
     */
    public function complete()
    {
        $this->setStatus(self::PRODUCT_UPDATE_STATUS_COMPLETE);
		$currentDateTime = $this->dateTime->gmtDate();
		$this->setExportedAt($currentDateTime);
        $this->save();
    }

    /**
     * Set the update to error status
     *
     * @param string $message
     *
     * @throws Exception
     */
    public function error($message = '')
    {
        $this->setStatus(self::PRODUCT_UPDATE_STATUS_ERROR);
        $this->setExceptionMessage($message);
        $this->incrementRetryCount();
        $this->save();
    }

    /**
     * Increment the update retry count
     *
     * @param bool $save
     *
     * @return $this
     * @throws Exception
     */
    public function incrementRetryCount($save = false)
    {
        $this->setRetryCount($this->getRetryCount() + 1);
		
        if ($save) {
            $this->save();
        }
		
        return $this;
    }
}
