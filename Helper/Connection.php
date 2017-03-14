<?php
namespace Adcurve\Adcurve\Helper;

class Connection extends \Magento\Framework\App\Helper\AbstractHelper
{
	const INSTALLATION_TYPE_TEST = 'test';
    const INSTALLATION_TYPE_LIVE = 'live';
	
    const API_ROLE_NAME 				= 'AdCurve API Role';
    const EMAIL_SUCCESS_REG_TEMPLATE_ID = 'adcurve_store_installation';
    const CRON_CHECK_FILE_NAME 			= 'cron_checked.log';
	
	const XPATH_DESIGN_PACKAGE_NAME 	= 'design/package/name';
	const XPATH_DESIGN_THEME_DEFAULT 	= 'design/theme/default';
	const XPATH_DESIGN_HEADER_LOGO_SRC 	= 'design/header/logo_src';
	
	protected $storeManager;
	protected $backendUrlBuilder;
	protected $configHelper;
	protected $countries;
    protected $apiRoleResources;
	
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Backend\Model\UrlInterface $backendUrlBuilder,
		\Adcurve\Adcurve\Helper\Config $configHelper
	){
		parent::__construct($context);
		
		$this->storeManager = $storeManager;
		$this->backendUrlBuilder = $backendUrlBuilder;
		$this->configHelper = $configHelper;
	}
	
	/**
	 * @return string
	 */
	public function getInstallationTypeTest()
	{
		return self::INSTALLATION_TYPE_TEST;
	}
	
	/**
	 * @return string
	 */
	public function getInstallationTypeLive()
	{
		return self::INSTALLATION_TYPE_LIVE;
	}
	
    /**
     * @return string
     */
    public function getApiRoleName()
    {
        return self::API_ROLE_NAME;
    }
	
    /**
     * @return string
     */
    public function getApiRoleType()
    {
    	//@TODO: At api role creation, specify the correct role for Magento 2 here
        return 'G';//Mage_Api_Model_Acl::ROLE_TYPE_GROUP;
    }
	
    /**
     * @return array
     */
    public function getApiResources()
    {
    	if(!$this->apiRoleResources){
    		$this->apiRoleResources = array(
		        '__root__',
		        'cart',
		        'cart/customer',
		        'cart/customer/addresses',
		        'cart/customer/set',
		        'cart/shipping',
		        'cart/shipping/list',
		        'cart/shipping/method',
		        'cart/payment',
		        'cart/payment/list',
		        'cart/payment/method',
		        'cart/product',
		        'cart/product/list',
		        'cart/product/remove',
		        'cart/product/update',
		        'cart/product/add',
		        'cart/license',
		        'cart/order',
		        'cart/info',
		        'cart/totals',
		        'cart/create',
		        'core',
		        'core/store',
		        'core/store/list',
		        'customer',
		        'customer/info',
		        'customer/create',
		        'customer/update',
		        'customer/address',
		        'customer/address/info',
		        'customer/address/update',
		        'customer/address/create',
		    );
    	}
        return $this->apiRoleResources;
    }
    	
    /**
     * Saves the shop id and activates both the feed and tagging
     *
     * @param $shopId
     * @param $apiKey
     * @param $storeId
     *
     * @return $this
     */
    public function activateAdcurve($shopId, $apiKey, $storeId)
    {
    	return false;
        $config = Mage::getConfig();

        //Store specific settings
        $config->saveConfig(
            \Adcurve\Adcurve\Helper\Config::XPATH_SHOP_ID,
            $shopId,
            'stores',
            $storeId
        );

        /** @var Mage_Core_Helper_Data $helper */
        $helper = Mage::helper('core');

        $config->saveConfig(
            \Adcurve\Adcurve\Helper\Config::XPATH_API_TOKEN,
            $helper->encrypt($apiKey),
            'stores',
            $storeId
        );        
        
        $config->saveConfig(
                \Adcurve\Adcurve\Helper\Config::XPATH_API_IS_REGISTERED,
                '1',
                'stores',
                $storeId
        );
            
        $config->saveConfig(
            \Adcurve\Adcurve\Helper\Config::XPATH_ENABLED,
            '1',
            'stores',
            $storeId
        );
        
        if($this->configHelper->isTestMode($storeId) == true){
            $config->saveConfig(
                \Adcurve\Adcurve\Helper\Config::XPATH_TEST_SHOP_ID,
                $shopId,
                'stores',
                $storeId
            );
        }
        
        Mage::app()->getCacheInstance()->cleanType('config');

        return $this;
    }

    /**
     * Return url to come back after an successfully installation
     *
     * @param $storeId
     *
     * @return mixed
     */
    public function getSuccessUrl($storeId = null)
    {
        return $this->backendUrlBuilder->getUrl('adminhtml/adcurveAdminhtml_registration/success', array('store_id' => $storeId));
    }

    /**
     * Return url to come back after an failed installation
     *
     * @return mixed
     */
    public function getFailUrl()
    {
        return $this->backendUrlBuilder->getUrl('adminhtml/adcurveAdminhtml_registration/failed');
    }

    /**
     ** Return logo url to send to adcurve
     *
     * @param null $store
     *
     * @return string
     *
     */
    public function getLogoUrl($store = null)
    {
    	return 'test.jpg';
		//@TODO: Get logo url based on store.
        if (empty($store)) {
        	$store = $this->storeManager->getDefaultStoreView();
        }
		
        /** @var Mage_Core_Model_Design_Package $designPackage */
        $designPackage = Mage::getSingleton('core/design_package');
        
        $package = $this->scopeConfig->getValue(self::XPATH_DESIGN_PACKAGE_NAME, $store);
        $package = (empty($package)) ? $designPackage->getPackageName() : $package;
        $theme = $this->scopeConfig->getValue(self::XPATH_DESIGN_THEME_DEFAULT, $storeId);
        $theme = (empty($package)) ? $designPackage->getTheme('frontend') : $theme;

        return Mage::getDesign()->getSkinUrl(Mage::getStoreConfig(self::XPATH_DESIGN_HEADER_LOGO_SRC), array('_area' => 'frontend', '_theme' => $theme, '_package' => $package));
    }

    /**
     * Return url of website
     *
     * @return string
     */
    public function getShopUrl($store = null)
    {
    	//@TODO: make web url website dependant
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }

    /**
     * array_diff_assoc for multidimensional arrays
     *
     * @param $array1
     * @param $array2
     *
     * @return array
     *
     * @see http://php.net/manual/en/function.array-diff-assoc.php#111675
     */
    public static function arrayDiffAssocRecursive($array1, $array2) {
        $difference=array();
        foreach($array1 as $key => $value) {
            if( is_array($value) ) {
                if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = self::arrayDiffAssocRecursive($value, $array2[$key]);
                    if( !empty($new_diff) )
                        $difference[$key] = $new_diff;
                }
            } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }
    
    /**
     * Return list of countries used on shop registration
	 * 
     * @return array
     */
    public function getCountriesList(){
    	if(!$this->countries){
    		$this->countries = [
		        ['value' => 'AT', 'label' => 'Austria'],
		        ['value' => 'BE', 'label' => 'Belgium'],
		        ['value' => 'DK', 'label' => 'Denmark'],
		        ['value' => 'FI', 'label' => 'Finland'],
		        ['value' => 'FR', 'label' => 'France'],
		        ['value' => 'DE', 'label' => 'Germany'],
		        ['value' => 'IT', 'label' => 'Italy'],
		        ['value' => 'LU', 'label' => 'Luxembourg'],
		        ['value' => 'MT', 'label' => 'Malta'],
		        ['value' => 'NL', 'label' => 'Netherlands'],
		        ['value' => 'NO', 'label' => 'Norway'],
		        ['value' => 'PL', 'label' => 'Poland'],
		        ['value' => 'PT', 'label' => 'Portugal'],
		        ['value' => 'ES', 'label' => 'Spain'],
		        ['value' => 'SE', 'label' => 'Sweden'],
		        ['value' => 'CH', 'label' => 'Switzerland'],
		        ['value' => 'GB', 'label' => 'United Kingdom']
	        ];
    	}
		return $this->countries;
    }
    
    /**
     * Saves the shop settings
     *
     * @data array
     *
     * @return $this
     */
    public function saveShopSettings($data)
    {
        $config = Mage::getConfig();
        $storeId = $data['store_id'];
        $shopId  = $data['shop_id'];
        $token   = $data['token'];
        $exclude = $data['exclude'];

       
       if ($exclude != 'DO_NOT_UPDATE') {
           //Store specific settings
            $config->saveConfig(
                \Adcurve\Adcurve\Helper\Config::XPATH_EXCLUDE_ATTRIBUTES,
                $exclude,
                "stores",
                $storeId
            );
        }

        if ($data['is_enabled_use_website'] == 1) {
            // remove it            
            $config->deleteConfig(
                \Adcurve\Adcurve\Helper\Config::XPATH_ENABLED,
                'stores',
                $storeId
            );
            
        } else {
            $enabled = $data['enabled'];
            
            if ($storeId == "0") {
                $scope = "default";
            } else {
                $scope = "stores";
            }
            
            $config->saveConfig(
                \Adcurve\Adcurve\Helper\Config::XPATH_ENABLED,
                $enabled,
                $scope,
                $storeId
            );
        }

        if ($this->configHelper->isApiConfigured($storeId) && $shopId != "DO_NOT_UPDATE" && $token != "DO_NOT_UPDATE") {
        
            //Store specific settings
            $config->saveConfig(
                \Adcurve\Adcurve\Helper\Config::XPATH_SHOP_ID,
                $shopId,
                'stores',
                $storeId
            );

            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');

            $config->saveConfig(
                \Adcurve\Adcurve\Helper\Config::XPATH_API_TOKEN,
                $helper->encrypt($token),
                'stores',
                $storeId
            );
        }
        
        Mage::app()->getCacheInstance()->cleanType('config');

        return $this;
    }

    /**
     * Return status of API User is created or not
     *
     * @param int |null $storeId
     *
     * @return string
     */    
    public function isApiUserCreated($storeId)
    {
        $store = Mage::getModel('core/store')->load($storeId);
        $storeCode = $store->getCode();
        
        $username = 'adcurve_'.$storeCode;
        $uc = Mage::getResourceModel('api/user_collection');
        $uc->addFieldToFilter('username', $username);
        if ($uc->getSize() > 0) {
            return Mage::helper("adcurve_adcurve")->__("OK");
        } else {
            return Mage::helper("adcurve_adcurve")->__("Failed");
        }
    }
    /**
     * Return status of API Role is created or not
     * @return string
     */    
    
    public function isApiRoleCreated()
    {
        $roleId = 0;
        $role = Mage::getResourceModel('api/role_collection');
        $role->addFieldToFilter('role_name', Adcurve_Adcurve_Helper_Data::API_ROLE_NAME);                    
        if ($role->getSize() > 0) {
            foreach ($role as $r) {
                $roleId = $r->getRoleId();
                break;
            }
        }
        
        if ($roleId > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Return data for email after successfully store installation 
     *
     * @param int |null $shopId
     *
     * @param string |null $apiToken
     *
     * @param int |null $storeId
     *
     * @return array
     */    
    public function getDataAfterSuccessInstall($shopId, $apiToken, $storeId)
    {
        $configHelper = Mage::helper("adcurve_adcurve/config");
        $helper = Mage::helper("adcurve_adcurve");
        $data = array();
        
        if ($shopId > 0 && $apiToken !="" && $storeId > 0) {
            
            $mode            = $configHelper->getMode($storeId);
            $emailTo         = $this->getShopContactEmailTo($storeId);
            $totalAttributes = $this->getSentAttributes($storeId);
            
            $accountCreated  = $helper->__("Your %s account was successfully created!", $mode);
            $emailSent       = $helper->__("We sent an email to %s", $emailTo);
            $attributSent       = $helper->__("Attributes sent: %s of %s OK", $totalAttributes, $totalAttributes);
            $cronChecked       = $helper->__("Cron check: ");
            $cronChecked      .= (is_file(Mage::getBaseDir('log').'/'.self::CRON_CHECK_FILE_NAME)) ? "OK" : $helper->__("Failed"."<br />The cron job seems to be disabled. You need to enable it to be able to send products to AdCurve. Please follow these steps to make it work");
            
            $tagImplemented  = $helper->__("Tags implemented: OK");
            $apiUserCreated = $helper->__("API user created: %s", $this->isApiUserCreated($storeId));
            $apiRoleCreated = $helper->__("API role created: ");
            
            if(!$this->isApiRoleCreated()) {
                $apiRoleCreated.= $helper->__("Something went wrong when trying to create or associate the API role to the API user. Please follow <a href='%s' target='_blank'>these</a> steps to make it work", $configHelper->getApiRoleCreatedFailedUrl());
            } else {
                $apiRoleCreated.= $helper->__("OK");
            }
            
            return $data = array(
                        "account_created" => $accountCreated,
                        "email_sent" => $emailSent,
                        "attribut_sent" => $attributSent,
                        "cron_checked" => $cronChecked,
                        "tag_implemented" => $tagImplemented,
                        "api_user_created" => $apiUserCreated,
                        "api_role_created" => $apiRoleCreated,
                        );
        }
    }
    
    /**
     * Return size of attributes sent to Adcurve
     * @return int
     */
    public function getSentAttributes($storeId){
        $totalAttributes = count(unserialize($this->configHelper->getShopAttributes($storeId)));    
        return $totalAttributes;
    }
    
    /**
     * Return email address of shop contact 
     *
     * @return string
     */
    public function getShopContactEmailTo($storeId){
        $data = unserialize($this->configHelper->getContact($storeId));
        return $data->email;
    }
    
    /**
     * Return email address of shop contact 
     *
     * @return string
     */
    public function getShopContactName($storeId){
        $data = unserialize($this->configHelper->getContact($storeId));
        return $data->first_name.' '.$data->last_name;
    }
    
    /**
     * Return list of all installed stores mode, current store mode status and current store test_shop_id
     *
     * @return array
     */    
    public function getStoresInstallationModeList($currentStoreCode = "")
    {
        $storeList = $this->storeManager->getStores();
        $data = array(
            $this->getInstallationTypeLive() => array(), 
            $this->getInstallationTypeTest() => array(),
            'current_store_mode_status' => '',
            'current_store_test_shop_id' => '',
            'not_installed' => array(),
        );
        
        /** @var Mage_Core_Model_Store $storeObject */
        foreach ($storeList as $storeObject){
        
            $storeId = $storeObject->getId();
            if($storeId == 0){
                continue;
            }
            if(!$this->configHelper->getShopId($storeId) || !$this->configHelper->getApiToken($storeId)){
                continue;
            }
            
            $storeCode = $storeObject->getCode();
            $mode = $this->configHelper->getMode($storeId);
            $testShopId = $this->configHelper->getTestShopId($storeId);
            
            if ($storeCode == $currentStoreCode){
                $data['current_store_mode_status'] = $mode;
                $data['current_store_test_shop_id'] = $testShopId;
            }
            
            if ($mode == $this->getInstallationTypeLive()){

                $data[$this->getInstallationTypeLive()][] = $storeId.":".$storeObject->getName();

            } elseif($mode == $this->getInstallationTypeTest()){

                $data[$this->getInstallationTypeTest()][] = $storeId.":".$storeObject->getName();

            }            
            else {
                $data['not_installed'][] = $storeId.":".$storeObject->getName();
            }
            
        }
        
        return $data;
    }
}