<?php
namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Api\Data\ConnectionInterface;

class Connection extends \Magento\Framework\Model\AbstractModel implements ConnectionInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Adcurve\Adcurve\Model\ResourceModel\Connection');
    }

    /**
     * Get connection_id
     * @return string
     */
    public function getConnectionId()
    {
        return $this->getData(self::CONNECTION_ID);
    }

    /**
     * Set connection_id
     * @param string $connectionId
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setConnectionId($connectionId)
    {
        return $this->setData(self::CONNECTION_ID, $connectionId);
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
     * @param string $storeId
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get store_code
     * @return string
     */
    public function getStoreCode()
    {
        return $this->getData(self::STORE_CODE);
    }

    /**
     * Set store_code
     * @param string $store_code
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setStoreCode($store_code)
    {
        return $this->setData(self::STORE_CODE, $store_code);
    }

    /**
     * Get adcurve_shop_id
     * @return string
     */
    public function getAdcurveShopId()
    {
        return $this->getData(self::ADCURVE_SHOP_ID);
    }

    /**
     * Set adcurve_shop_id
     * @param string $adcurveShopId
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setAdcurveShopId($adcurveShopId)
    {
        return $this->setData(self::ADCURVE_SHOP_ID, $adcurveShopId);
    }

    /**
     * Get adcurve_token
     * @return string
     */
    public function getAdcurveToken()
    {
        return $this->getData(self::ADCURVE_TOKEN);
    }

    /**
     * Set adcurve_token
     * @param string $adcurve_token
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setAdcurveToken($adcurve_token)
    {
        return $this->setData(self::ADCURVE_TOKEN, $adcurve_token);
    }

    /**
     * Get enabled
     * @return string
     */
    public function getEnabled()
    {
        return $this->getData(self::ENABLED);
    }

    /**
     * Set enabled
     * @param string $enabled
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setEnabled($enabled)
    {
        return $this->setData(self::ENABLED, $enabled);
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
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get suggestion
     * @return string
     */
    public function getSuggestion()
    {
        return $this->getData(self::SUGGESTION);
    }

    /**
     * Set suggestion
     * @param string $suggestion
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setSuggestion($suggestion)
    {
        return $this->setData(self::SUGGESTION, $suggestion);
    }

    /**
     * Get api_user_info
     * @return string
     */
    public function getApiUserInfo()
    {
        return $this->getData(self::API_USER_INFO);
    }

    /**
     * Set api_user_info
     * @param string $apiUserInfo
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setApiUserInfo($apiUserInfo)
    {
        return $this->setData(self::API_USER_INFO, $apiUserInfo);
    }

    /**
     * Get updated_at
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
