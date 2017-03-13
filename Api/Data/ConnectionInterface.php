<?php
namespace Adcurve\Adcurve\Api\Data;

interface ConnectionInterface
{
    const CONNECTION_ID 	= 'connection_id';
    const STORE_ID 			= 'store_id';
    const STORE_CODE 		= 'store_code';
    const ADCURVE_SHOP_ID 	= 'adcurve_shop_id';
    const ADCURVE_TOKEN 	= 'adcurve_token';
    const ENABLED 			= 'enabled';
    const STATUS 			= 'status';
    const SUGGESTION 		= 'suggestion';
    const API_USER_INFO 	= 'api_user_info';
    const UPDATED_AT 		= 'updated_at';

    /**
     * Get connection_id
     * @return string|null
     */
    
    public function getConnectionId();

    /**
     * Set connection_id
     * @param string $connectionId
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    
    public function setConnectionId($connectionId);

    /**
     * Get store_id
     * @return string|null
     */
    
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    
    public function setStoreId($storeId);

    /**
     * Get store_code
     * @return string|null
     */
    
    public function getStoreCode();

    /**
     * Set store_code
     * @param string $storeCode
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    
    public function setStoreCode($storeCode);

    /**
     * Get adcurve_shop_id
     * @return string|null
     */
    
    public function getAdcurveShopId();

    /**
     * Set adcurve_shop_id
     * @param string $adcurveShopId
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    
    public function setAdcurveShopId($adcurveShopId);

    /**
     * Get adcurve_token
     * @return string|null
     */
    
    public function getAdcurveToken();

    /**
     * Set adcurve_token
     * @param string $adcurveToken
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    
    public function setAdcurveToken($adcurveToken);

    /**
     * Get enabled
     * @return string|null
     */
    
    public function getEnabled();

    /**
     * Set enabled
     * @param string $enabled
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    
    public function setEnabled($enabled);

    /**
     * Get status
     * @return string|null
     */
    
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    
    public function setStatus($status);

    /**
     * Get suggestion
     * @return string|null
     */
    
    public function getSuggestion();

    /**
     * Set suggestion
     * @param string $suggestion
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    
    public function setSuggestion($suggestion);

    /**
     * Get api_user_info
     * @return string|null
     */
    
    public function getApiUserInfo();

    /**
     * Set api_user_info
     * @param string $apiUserInfo
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    
    public function setApiUserInfo($apiUserInfo);

    /**
     * Get updated_at
     * @return string|null
     */
    
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return Adcurve\Adcurve\Api\Data\ConnectionInterface
     */
    
    public function setUpdatedAt($updatedAt);
}
