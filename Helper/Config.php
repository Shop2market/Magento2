<?php
namespace Adcurve\Adcurve\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XPATH_ENABLED             = 'adcurve/settings/enabled';
    const XPATH_TEST_MODE           = 'adcurve/settings/test_mode';
    const XPATH_SHOP_ATTRIBUTES     = 'adcurve/settings/shop_attributes';
    const XPATH_API_TOKEN           = 'adcurve/settings/token';
    const XPATH_CONTACT     		= 'adcurve/settings/contact';
    const XPATH_REGISTER_URL_LIVE   = 'adcurve/settings/url_register_live';
    const XPATH_REGISTER_URL_TEST   = 'adcurve/settings/url_register_test';
    const XPATH_TAG_URL_LIVE        = 'adcurve/settings/url_tag_live';
    const XPATH_TAG_URL_TEST        = 'adcurve/settings/url_tag_test';

    const XPATH_API_UPDATE_URL_LIVE = 'adcurve/api/update_url_live';
    const XPATH_API_UPDATE_URL_TEST = 'adcurve/api/update_url_test';
    const XPATH_API_STATUS_URL_LIVE = 'adcurve/api/status_url_live';
    const XPATH_API_STATUS_URL_TEST = 'adcurve/api/status_url_test';
	const XPATH_API_ROLE_CREATED_FAILED_URL = 'adcurve/api/role_created_failed';

    const XPATH_PAYMENT_METHOD 		= 'payment/checkmo/active';

	protected $storeManager;
	protected $resourceInterface;
	protected $connectionRepository;
	protected $adcurveConnection;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Module\ResourceInterface $resourceInterface,
		\Adcurve\Adcurve\Model\ConnectionRepository $connectionRepository
	){
		parent::__construct($context);
		
		$this->storeManager = $storeManager;
		$this->resourceInterface = $resourceInterface;
		$this->connectionRepository = $connectionRepository;
	}

	/**
	 * Get Adcurve connection model if one exists for the current store
	 * 
	 * @return \Adcurve\Adcurve\Model\Connection
	 */
	public function getAdcurveConnection()
	{
		if (!$this->adcurveConnection) {
			// @TODO: Look into a more secure method for try catch to avoid crash at all time
			try{
				$storeId = $this->storeManager->getStore()->getId();
				$connection = $this->connectionRepository->getByStoreId($storeId);
				if ($connection->getId()) {
					$this->adcurveConnection = $connection;
				} else {
					$this->adcurveConnection = 'n/a';
				}
			} catch (Exception $e) {
				// No logging here for now.
				$this->adcurveConnection = 'n/a';
			}
		}
		
		if($this->adcurveConnection == 'n/a') {
			return false;
		}
		
		return $this->adcurveConnection;
	}

	/**
	 * Get current Adcurve Shop ID
	 * 
	 * @return string $shopId
	 */
	public function getAdcurveShopId()
	{
		$connection = $this->getAdcurveConnection();
		if ($connection->getAdcurveShopId()) {
			return $connection->getAdcurveShopId();
		}
		return false;
	}

    /**
     * Get the base url for tags
     *
     * @return string $tagUrl
     */
    public function getTagUrl()
    {
        if ($this->getAdcurveConnection() && $this->getAdcurveConnection()->getIsTestmode()) {
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
    public function getRegisterUrl($connection)
    {
        if ($connection->getIsTestmode()) {
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
    public function shouldRenderTags()
    {
    	if(!$this->getAdcurveConnection()) {
    		return false;
    	}
		
		$connection = $this->getAdcurveConnection();
        if (!$connection->getEnabled()) {
            /** If module is disabled, don't render tags */
            return false;
        }
		
        if (!$connection->getAdcurveShopId()) {
            /** If no ShopId is set, don't render tags */
            return false;
        }
		
        return true;
    }

    /**
     * @param \Adcurve\Adcurve\Model\Connection $connection
     *
     * @return bool
     */
    public function isApiConfigured($connection)
    {
        if (!$connection->getEnabled()) {
            /** If module is disabled, can't use API */
            return false;
        }
		
        if (!$connection->getAdcurveShopId()) {
            /** If no Adcurve ShopId is set, can't use API */
            return false;
        }
		
        if (!$connection->getAdcurveToken()) {
            /** If no Adcurve ApiToken is set, can't use API */
            return false;
        }

        return true;
    }

    /**
     * Get Adcurve product data Api Url based on connection
     *
     * @param \Adcurve\Adcurve\Model\Connection $connection
     *
     * @return string $apiUrl
     */
    public function getProductApiUrl($connection)
    {
        if ($connection->getIsTestmode()) {
            $apiUrl = $this->scopeConfig->getValue(self::XPATH_API_UPDATE_URL_TEST);
        } else {
            $apiUrl = $this->scopeConfig->getValue(self::XPATH_API_UPDATE_URL_LIVE);
        }
		
        $apiUrl = str_replace('{{SHOP_ID}}', $connection->getAdcurveShopId(), $apiUrl);
		
        return $apiUrl;
    }

    /**
	 * Get status request API url based on connection
     *
     * @param \Adcurve\Adcurve\Model\Connection $connection
     *
     * @return string $apiUrl
     */
    public function getStatusApiUrl($connection)
    {
        if ($connection->getIsTestmode()) {
            $apiUrl = $this->scopeConfig->getValue(self::XPATH_API_STATUS_URL_TEST);
        } else {
            $apiUrl = $this->scopeConfig->getValue(self::XPATH_API_STATUS_URL_LIVE);
        }
		
        $apiUrl = str_replace('{{SHOP_ID}}', $connection->getAdcurveShopId(), $apiUrl);
		
        return $apiUrl;
    }

    /**
	 * Get support url if api role created failed
     *
     * @return string $url
     */
    public function getApiRoleCreatedFailedUrl()
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
        
		// @TODO: Create some kind of crypt / decrypt for Adcurve Api token
		// @TODO: Move this to the connection object preferrably (the getAdcurveToken() fuction )
        return $this->scopeConfig->getValue(self::XPATH_API_TOKEN); //$helper->decrypt($this->scopeConfig->getValue(self::XPATH_API_TOKEN, $store));
    }

    /**
     * Get the version of the Adcurve_Adcurve module
     *
     * @return string $versionNumber
     */
    public function getModuleVersion()
    {
    	return $this->resourceInterface->getDbVersion('Adcurve_Adcurve');
    }
}
