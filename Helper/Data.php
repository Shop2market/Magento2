<?php
/**
 * Class Adcurve_Adcurve_Helper_Data
 */
class Adcurve_Adcurve_Helper_Data extends Mage_Core_Helper_Abstract
{
    const API_ROLE_NAME = 'AdCurve API Role';
    const EMAIL_SUCCESS_REG_TEMPLATE_ID = 'adcurve_store_installation';
    const CRON_CHECK_FILE_NAME =  'cron_checked.log';
    
    protected $_apiRoleResources = array(
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
        return Mage_Api_Model_Acl::ROLE_TYPE_GROUP;
    }

    /**
     * @return array
     */
    public function getApiResources()
    {
        return $this->_apiRoleResources;
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
        $config = Mage::getConfig();

        //Store specific settings
        $config->saveConfig(
            Adcurve_Adcurve_Helper_Config::XPATH_SHOP_ID,
            $shopId,
            'stores',
            $storeId
        );

        /** @var Mage_Core_Helper_Data $helper */
        $helper = Mage::helper('core');

        $config->saveConfig(
            Adcurve_Adcurve_Helper_Config::XPATH_API_TOKEN,
            $helper->encrypt($apiKey),
            'stores',
            $storeId
        );        
        
        $config->saveConfig(
                Adcurve_Adcurve_Helper_Config::XPATH_API_IS_REGISTERED,
                '1',
                'stores',
                $storeId
        );
            
        $config->saveConfig(
            Adcurve_Adcurve_Helper_Config::XPATH_ENABLED,
            '1',
            'stores',
            $storeId
        );
        
        if (Adcurve_Adcurve_Helper_Config::isTestMode($storeId) == true) {
            $config->saveConfig(
                Adcurve_Adcurve_Helper_Config::XPATH_TEST_SHOP_ID,
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
        /** @var Mage_Adminhtml_Helper_Data $helper */
        $helper = Mage::helper('adminhtml');

        return $helper->getUrl('adminhtml/adcurveAdminhtml_registration/success', array('store_id' => $storeId));
    }

    /**
     * Return url to come back after an failed installation
     *
     * @return mixed
     */
    public function getFailUrl()
    {
        /** @var Mage_Adminhtml_Helper_Data $helper */
        $helper = Mage::helper('adminhtml');

        return $helper->getUrl('adminhtml/adcurveAdminhtml_registration/failed');
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
        if (empty($store)) {
            $store = Mage::app()->getDefaultStoreView();
        }
        $code = $store->getId();

        /** @var Mage_Core_Model_Design_Package $designPackage */
        $designPackage = Mage::getSingleton('core/design_package');

        $package = Mage::getStoreConfig('design/package/name', $code);
        $package = (empty($package)) ? $designPackage->getPackageName() : $package;
        $theme = Mage::getStoreConfig('design/theme/default', $code);
        $theme = (empty($package)) ? $designPackage->getTheme('frontend') : $theme;

        return Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src'), array('_area' => 'frontend', '_theme' => $theme, '_package' => $package));
    }

    /**
     * Return url of website
     *
     * @param Mage_Core_Model_Store|null $store
     *
     * @return string
     */
    public function getShopUrl(Mage_Core_Model_Store $store = null)
    {
        if (empty($store)) {
            $store = Mage::app()->getDefaultStoreView();
        }

        //$path = $store->getBaseUrl();
        $path = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
        
        return $path;
    }

    /**
     * Returns the String version of the Module.
     */
    public function getModuleVersion()
    {
        /** @noinspection PhpUndefinedFieldInspection */
        return (string) Mage::getConfig()->getModuleConfig('Adcurve_Adcurve')->version;
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
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if ( !isset($array2[$key]) || !is_array($array2[$key]) ) {
                    $difference[$key] = $value;
                } else {
                    $new_diff = self::arrayDiffAssocRecursive($value, $array2[$key]);
                    if ( !empty($new_diff) )
                        $difference[$key] = $new_diff;
                }
            } else if ( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
                $difference[$key] = $value;
            }
        }
        return $difference;
    }
    
    /**
     * Return list of countries used on shop registration
     * @return array
     */
    function getCountriesList(){
    
         return $countries = array(
                        array("value" => "AT", "label" => "Austria"),
                        array("value" => "BE", "label" => "Belgium"),
                        array("value" => "DK", "label" => "Denmark"),
                        array("value" => "FI", "label" => "Finland"),
                        array("value" => "FR", "label" => "France"),
                        array("value" => "DE", "label" => "Germany"),
                        array("value" => "IT", "label" => "Italy"),
                        array("value" => "LU", "label" => "Luxembourg"),
                        array("value" => "MT", "label" => "Malta"),
                        array("value" => "NL", "label" => "Netherlands"),
                        array("value" => "NO", "label" => "Norway"),
                        array("value" => "PL", "label" => "Poland"),
                        array("value" => "PT", "label" => "Portugal"),
                        array("value" => "ES", "label" => "Spain"),
                        array("value" => "SE", "label" => "Sweden"),
                        array("value" => "CH", "label" => "Switzerland"),
                        array("value" => "GB", "label" => "United Kingdom")
                        );
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
        $syncTime= $data['sync_time'];
        $enableFeatures = $data['enable_features'];

       
       if ($enableFeatures != 'DO_NOT_UPDATE') {
            $config->saveConfig(
                Adcurve_Adcurve_Helper_Config::XPATH_PAYMENT_METHOD, 
                $enableFeatures,
                "default"
            );
            
            $config->saveConfig(
                Adcurve_Adcurve_Helper_Config::XPATH_SETTINGS_FEATURES, 
                $enableFeatures,
                "default"
            );
        }

       if ($syncTime != 'DO_NOT_UPDATE') {
           //Store specific settings
            $config->saveConfig(
                Adcurve_Adcurve_Helper_Config::XPATH_CRON_SYNC_TIME,
                $syncTime,
                "default"
            );  

            $config->saveConfig(
                Adcurve_Adcurve_Helper_Config::XPATH_CRON_SYNC_TIME_EXPR,
                $syncTime,
                "default"
            );
            $config->saveConfig(
                Adcurve_Adcurve_Helper_Config::XPATH_CRON_SYNC_MODEL,
                "adcurve_adcurve/cron_product_sync::process",
                "default"
            );

        }

        if ($data['is_enabled_use_website'] == 1) {
            // remove it            
            $config->deleteConfig(
                Adcurve_Adcurve_Helper_Config::XPATH_ENABLED,
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
                Adcurve_Adcurve_Helper_Config::XPATH_ENABLED,
                $enabled,
                $scope,
                $storeId
            );
        }

        if (Adcurve_Adcurve_Helper_Config::isApiConfigured($storeId) && $shopId != "DO_NOT_UPDATE" && $token != "DO_NOT_UPDATE") {
        
            //Store specific settings
            $config->saveConfig(
                Adcurve_Adcurve_Helper_Config::XPATH_SHOP_ID,
                $shopId,
                'stores',
                $storeId
            );

            /** @var Mage_Core_Helper_Data $helper */
            $helper = Mage::helper('core');

            $config->saveConfig(
                Adcurve_Adcurve_Helper_Config::XPATH_API_TOKEN,
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
            
            if (!$this->isApiRoleCreated()) {
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
        $totalAttributes = count(unserialize(Adcurve_Adcurve_Helper_Config::getShopAttributes($storeId)));    
        return $totalAttributes;
    }
    
    /**
     * Return email address of shop contact 
     *
     * @return string
     */
    public function getShopContactEmailTo($storeId){
        $data = unserialize(Adcurve_Adcurve_Helper_Config::getContact($storeId));
        return $data->email;
    }
    
    /**
     * Return email address of shop contact 
     *
     * @return string
     */
    public function getShopContactName($storeId){
        $data = unserialize(Adcurve_Adcurve_Helper_Config::getContact($storeId));
        return $data->first_name.' '.$data->last_name;
    }
    
    /**
     * Return list of all installed stores mode, current store mode status and current store test_shop_id
     *
     * @return array
     */    
    public function getStoresInstallationModeList($currentStoreCode = "")
    {
        $storeList = Mage::app()->getStores(true);
        $data = array(
                        Adcurve_Adcurve_Block_Adminhtml_Registration::INSTALLATION_TYPE_LIVE => array(), 
                        Adcurve_Adcurve_Block_Adminhtml_Registration::INSTALLATION_TYPE_TEST => array(),
                        'current_store_mode_status' => '',
                        'current_store_test_shop_id' => '',
                        'not_installed' => array(),
                    );
        $helper = Mage::helper('adcurve_adcurve/config');
        
        /** @var Mage_Core_Model_Store $storeObject */
        foreach ($storeList as $storeObject) {
        
            $storeId = $storeObject->getId();
            if ($storeId == 0) {
                continue;
            }
            if (!$helper->getShopId($storeId) || !$helper->getApiToken($storeId) ) {
                continue;
            }
            
            $storeCode = $storeObject->getCode();
            $mode = $helper->getMode($storeId);
            $testShopId = $helper->getTestShopId($storeId);
            
            if ($storeCode == $currentStoreCode) {
                $data['current_store_mode_status'] = $mode;
                $data['current_store_test_shop_id'] = $testShopId;
            }
            
            if ($mode == Adcurve_Adcurve_Block_Adminhtml_Registration::INSTALLATION_TYPE_LIVE) {

                $data[Adcurve_Adcurve_Block_Adminhtml_Registration::INSTALLATION_TYPE_LIVE][] = $storeId.":".$storeObject->getName();

            } else if ($mode == Adcurve_Adcurve_Block_Adminhtml_Registration::INSTALLATION_TYPE_TEST) {

                $data[Adcurve_Adcurve_Block_Adminhtml_Registration::INSTALLATION_TYPE_TEST][] = $storeId.":".$storeObject->getName();

            }            
            else {
                $data['not_installed'][] = $storeId.":".$storeObject->getName();
            }
            
        }
        
        return $data;
    }

}
