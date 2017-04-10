<?php
namespace Adcurve\Adcurve\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XPATH_ENABLED             = 'adcurve/settings/enabled';
    const XPATH_TEST_MODE           = 'adcurve/settings/test_mode';
    const XPATH_MODE           		= 'adcurve/settings/mode';
    const XPATH_SHOP_ID             = 'adcurve/settings/shop_id';
    const XPATH_SHOP_ATTRIBUTES     = 'adcurve/settings/shop_attributes';
    const XPATH_API_TOKEN           = 'adcurve/settings/token';
    const XPATH_CONTACT     		= 'adcurve/settings/contact';
    const XPATH_REGISTER_URL_LIVE   = 'adcurve/settings/url_register_live';
    const XPATH_REGISTER_URL_TEST   = 'adcurve/settings/url_register_test';
    const XPATH_TAG_URL_LIVE        = 'adcurve/settings/url_tag_live';
    const XPATH_TAG_URL_TEST        = 'adcurve/settings/url_tag_test';
	
    const XPATH_API_IS_REGISTERED   = 'adcurve/api/is_registered';
    const XPATH_API_UPDATE_URL_LIVE = 'adcurve/api/update_url_live';
    const XPATH_API_UPDATE_URL_TEST = 'adcurve/api/update_url_test';
    const XPATH_API_STATUS_URL_LIVE = 'adcurve/api/status_url_live';
    const XPATH_API_STATUS_URL_TEST = 'adcurve/api/status_url_test';
	const XPATH_TEST_SHOP_ID		= 'adcurve/api/test_shop_id';
	const XPATH_API_ROLE_CREATED_FAILED_URL = 'adcurve/api/role_created_failed';
	
    const XPATH_PAYMENT_METHOD 		= 'payment/checkmo/active';
	
	protected $resourceInterface;
	
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\Module\ResourceInterface $resourceInterface
	){
		parent::__construct($context);
		
		$this->resourceInterface = $resourceInterface;
	}
	
	/**
     * Return the shop id in shop2market configured
     *
     * @param null|int $store
     *
     * @return int
     */
    public function getShopId($store = null)
    {
        return $this->scopeConfig->getValue(self::XPATH_SHOP_ID);
    }
	
    /**
     * Return the true if shop is registered
     *
     * @param null|int $store
     *
     * @return bool
     */
    public function getShopIsRegistered($store = null)
    {
        $isRegistered = $this->scopeConfig->getValue(self::XPATH_API_IS_REGISTERED);
		return ($isRegistered == '1' ? '1' : '0');
    }
	
    /**
     * Is the extension enabled
     *
     * @param null|int $store
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->isSetFlag(self::XPATH_ENABLED);
    }
	
    /**
     * get the extension mode
     *
     * @param null|int $store
     *
     * @return string
     */
    public function getMode($store = null)
    {
        return $this->scopeConfig->getValue(self::XPATH_MODE);
    }
	
    /**
     * Is the extension currently in test mode
     *
     * @param null|int $store
     *
     * @return bool
     */
    public function isTestMode($store = null)
    {
    	return false;
		//return ($this->getMode($store) == Adcurve_Adcurve_Block_Adminhtml_Registration::INSTALLATION_TYPE_TEST)? true : false;
        //return $this->scopeConfig->isSetFlag(self::XPATH_TEST_MODE);
    }

    /**
     * Get the base url for tags
     *
     * @param null|int $store
     *
     * @return string
     */
    public function getTagUrl($store = null)
    {
        if ($this->isTestMode($store)) {
            $tagUrl = $this->scopeConfig->getValue(self::XPATH_TAG_URL_TEST);
        } else {
            $tagUrl = $this->scopeConfig->getValue(self::XPATH_TAG_URL_LIVE);
        }

        return $tagUrl;
    }

    /**
     * Return url to register in adcurve
     *
     * @param null|int $store
     *
     * @return string
     */
    public function getRegisterUrl($store = null)
    {
        if ($this->isTestMode()) {
            $registerUrl = $this->scopeConfig->getValue(self::XPATH_REGISTER_URL_TEST);
        } else {
            $registerUrl = $this->scopeConfig->getValue(self::XPATH_REGISTER_URL_LIVE);
        }

        return $registerUrl;
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function shouldRenderTags($store = null)
    {
        if (!$this->isEnabled($store)) {
            /** If module is disabled, don't render tags */
            return false;
        }

        if (!$this->getShopId()) {
            /** If no ShopId is set, don't render tags */
            return false;
        }

        return true;
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isApiConfigured($store = null)
    {
        if (!$this->isEnabled($store)) {
            /** If module is disabled, can't use API */
            return false;
        }

        if (!$this->getShopId($store)) {
            /** If no ShopId is set, can't use API */
            return false;
        }

        if (!$this->getApiToken($store)) {
            /** If no ApiToken is set, can't use API */
            return false;
        }

        return true;
    }

    /**
     * Return url to register in adcurve
     *
     * @param null|int $store
     *
     * @return string
     */
    public function getProductApiUrl($store = null)
    {
        if ($this->isTestMode()) {
            $apiUrl = $this->scopeConfig->getValue(self::XPATH_API_UPDATE_URL_TEST);
        } else {
            $apiUrl = $this->scopeConfig->getValue(self::XPATH_API_UPDATE_URL_LIVE);
        }

        $shopId = $this->getShopId($store);
        $apiUrl = str_replace('{{SHOP_ID}}', $shopId, $apiUrl);

        return $apiUrl;
    }
	
    /**
      * Return url  register in adcurve on the base of mode
     *
     * @param null|int $store
     *
     * @return mixed
     */
    public function getStatusApiUrl($store = null)
    {
        if ($this->isTestMode()) {
            $apiUrl = $this->scopeConfig->getValue(self::XPATH_API_STATUS_URL_TEST);
        } else {
            $apiUrl = $this->scopeConfig->getValue(self::XPATH_API_STATUS_URL_LIVE);
        }
		
        $shopId = $this->getShopId($store);
        $apiUrl = str_replace('{{SHOP_ID}}', $shopId, $apiUrl);
		
        return $apiUrl;
    }
	
    /**
	 * Return support url if api role created failed
     *
     * @param null|int $store
     *
     * @return string
     */
    public function getApiRoleCreatedFailedUrl($store = null)
    {
        return $this->scopeConfig->getValue(self::XPATH_API_ROLE_CREATED_FAILED_URL);
    }
	
    /**
	* Return decrypted api token
     *
     * @param null|int $store
     *
     * @return string
     */
    public function getApiToken($store = null)
    {
        /** @var Mage_Core_Helper_Data $helper */
        //$helper = Mage::helper('core');

        return $this->scopeConfig->getValue(self::XPATH_API_TOKEN); //$helper->decrypt($this->scopeConfig->getValue(self::XPATH_API_TOKEN, $store));
    }
	
	/**
	* Return shop attributes to send to adcurve
     *
     * @param null|int $store
     *
     * @return string
     */
    public function getShopAttributes($store = null)
    {
        return $this->scopeConfig->getValue(self::XPATH_SHOP_ATTRIBUTES);
    }
	
	/**
	* Return shop attributes to send to adcurve
     *
     * @param null|int $store
     *
     * @return string
     */
    public function getTestShopId($store = null)
    {
        return $this->scopeConfig->getValue(self::XPATH_TEST_SHOP_ID);
    }
	
	
    /**
     * Returns the current version of the Module.
     *
     * @return string
     */
    public function getModuleVersion()
    {
    	return $this->resourceInterface->getDbVersion('Adcurve_Adcurve');
    }
}
