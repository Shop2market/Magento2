<?php
namespace Adcurve\Adcurve\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
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
			try {
				$storeId = $this->storeManager->getStore()->getId();
				$connection = $this->connectionRepository->getByStoreId($storeId);
				if ($connection->getId()) {
					$this->adcurveConnection = $connection;
				} else {
					$this->adcurveConnection = 'n/a';
				}
			} catch (\Exception $e) {
				// No logging here for now.
				$this->adcurveConnection = 'n/a';
			}
		}
		
		if ($this->adcurveConnection == 'n/a') {
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
        if ($this->getAdcurveConnection() && !$this->getAdcurveConnection()->getProductionMode()) {
            $tagUrl = $this->scopeConfig->getValue(self::XPATH_TAG_URL_TEST);
        } else {
            $tagUrl = $this->scopeConfig->getValue(self::XPATH_TAG_URL_LIVE);
        }
		
        return $tagUrl;
    }

    /**
     * Get Adcurve register url
     * 
     * @param \Adcurve\Adcurve\Model\Connection $connection
     * 
     * @return string $url
     */
    public function getRegisterUrl($connection)
    {
        if (!$connection->getProductionMode()) {
            $registerUrl = $this->scopeConfig->getValue(self::XPATH_REGISTER_URL_TEST);
        } else {
            $registerUrl = $this->scopeConfig->getValue(self::XPATH_REGISTER_URL_LIVE);
        }
		
        return $registerUrl;
    }

    /**
     * Get wether or not Adcurve tracking tags should render based on the current storeview connection
	 * 
     * @return bool $shouldRender
     */
    public function shouldRenderTags()
    {
    	if (!$this->getAdcurveConnection()) {
    		return false;
    	}
		
		$connection = $this->getAdcurveConnection();
        if (!$connection->getEnabled()) {
            return false;
        }
		
        if (!$connection->getAdcurveShopId()) {
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
		return true;
        if (!$connection->getEnabled()) {
            return false;
        }
		
        if (!$connection->getAdcurveShopId()) {
            return false;
        }
		
        if (!$connection->getAdcurveToken()) {
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
        if (!$connection->getProductionMode()) {
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
        if (!$connection->getProductionMode()) {
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
     * Get the version of the Adcurve_Adcurve module
     * 
     * @return string $versionNumber
     */
    public function getModuleVersion()
    {
    	return $this->resourceInterface->getDbVersion('Adcurve_Adcurve');
    }
}
