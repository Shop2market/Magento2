<?php
class Adcurve_Adcurve_Block_Adminhtml_System_Config_Registration extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Template file used
     *
     * @var string
     */
    protected $_template = 'Adcurve/adcurve/system/config/registration_form.phtml';

    
    const API_WSDL_ENDPOINT = 'api/v2_soap?wsdl=1';
    const INSTALLATION_TYPE_TEST = 'test';
    const INSTALLATION_TYPE_LIVE = 'live';

    /**
     * @return string
     */
    public function getStoreSwitcherHtml($currentStore = 0, $currentMode = "")
    {
        $html = '';
        /** @var Adcurve_Adcurve_Helper_Config $confHelper */
        $confHelper = Mage::helper('adcurve_adcurve/config');
        /** @var Mage_Core_Model_Website[] $websites */
        $websites = Mage::app()->getWebsites();
        if ($websites) {
            $html .= '<select name="shop[store_id]" id="store_switcher_registration">';

            /** @var Mage_Core_Model_Website $website */
            foreach ($websites as $website) {
                $showWebsite = false;

                /** @var Mage_Core_Model_Store_Group $group */
                foreach ($website->getGroups() as $group) {
                    $showGroup = false;

                    /** @var Mage_Core_Model_Store $store */
                    foreach ($group->getStores() as $store) {
                        if ($confHelper->isApiConfigured($store->getId()) == true || ( $confHelper->getShopIsRegistered($store->getId()) && $confHelper->isEnabled($store->getId()) == false)) {
                            continue;
                        }
                        
                        if ($showWebsite == false) {
                            $showWebsite = true;
                            $html .= '<optgroup label="'
                                . $this->escapeHtml($website->getName())
                                . '"></optgroup>';
                        }

                        if ($showGroup == false) {
                            $showGroup = true;
                            $html .= '<optgroup label="&nbsp;&nbsp;&nbsp;'
                                . $this->escapeHtml($group->getName())
                                . '">';
                        }

                        $html .= '<option value="'
                            . $this->escapeHtml($store->getId())
                            . '"'
                            . (($currentStore == $store->getId() && $currentStore > 0) ? ' selected="selected" ' : '')
                            . (($currentStore != $store->getId() && $currentStore > 0 && $store->getId() > 0) ? ' disabled="disabled" ' : '')
                            . '>&nbsp;&nbsp;&nbsp;&nbsp;'
                            . $this->escapeHtml($store->getName())
                            . '</option>';
                    }

                    if ($showGroup) {
                        $html .= '</optgroup>';
                    }
                }
            }

            $html .= '</select>';
        }

        return $html;
    }

    /**
     * @return Mage_Core_Model_Store
     */
    public function getDefaultStore()
    {
        return Mage::app()->getDefaultStoreView();
    }

    /**
     * @return string
     */
    public function getWsdlEndpoint()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_DIRECT_LINK) . self::API_WSDL_ENDPOINT;
    }

    /**
     * @return string
     */
    public function getAttributeSelectHtml()
    {
        $html = '';
        $html .= '<select name="shop[attributes][]" id="product_attributes"';
        $html .= ' class="select multiselect" size="10" multiple="multiple"  readonly="readonly">';
        /** @var Adcurve_Adcurve_Helper_Product $helper */
        $helper = Mage::helper('adcurve_adcurve/product');
        $attributes = $helper->getAllProductAttributes();

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        foreach ($attributes as $attribute) {
            $html .= '<option value="'
                . $this->escapeHtml($attribute->getAttributecode())
                . '" selected="selected">'
                . $this->escapeHtml($attribute->getFrontendLabel())
                . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    /**
     * @param $storeId
     *
     * @return int
     */
    public function getApiConnectionStatus($storeId)
    {
        $_helper = Mage::helper('adcurve_adcurve');

        $statusResult = array(
            'stepsCompleted' => 0,
            'suggestion' => $_helper->__('Register the Storeview with the form below.')
        );

        if (!Adcurve_Adcurve_Helper_Config::isApiConfigured($storeId)) {
            return $statusResult;
        }

        /** @var Adcurve_Adcurve_Model_Rest_Status $api */
        $api = Mage::getModel('adcurve_adcurve/rest_status');
        $apiStatus = $api->getConnectionStatus($storeId);

        switch ($apiStatus['status']) {
            case Adcurve_Adcurve_Model_Rest_Status::STATUS_ERROR_CONNECTION_TO_ADCURVE:
                $statusResult['stepsCompleted'] = 1;
                $msgStep = 'Could not establish a connection with Adcurve. Read the %s for more information.';
                $urlRF = Adcurve_Adcurve_Helper_Config::getApiRoleCreatedFailedUrl($storeId);
                $msgStepV = "<a target='_blank' class='manual-url' href='" . $urlRF . "'>" . $_helper->__('manual') . "</a>";
                $statusResult['suggestion'] = $_helper->__($msgStep, $msgStepV);
                break;
            case Adcurve_Adcurve_Model_Rest_Status::STATUS_ERROR_RESULT_FROM_ADCURVE:
                $statusResult['stepsCompleted'] = 2;
                $statusResult['suggestion'] = $_helper->__('Something went wrong with the installation, please contact support.');
                break;
            case Adcurve_Adcurve_Model_Rest_Status::STATUS_SUCCESS:
                $statusResult['stepsCompleted'] = 3;
                if (Adcurve_Adcurve_Helper_Config::isTestMode($storeId) == true) {
                    $msgStep3 = 'Ready for testing. Please make sure you install the live version';
                    $msgStep3 .= ' using the registration form bellow to enjoy all the functionality!';
                    $statusResult['suggestion'] = $_helper->__($msgStep3);
                } else {
                    $msgStep3 = 'Connection successfully established. No further action is needed.';
                    $statusResult['suggestion'] = $_helper->__($msgStep3);
                }
                break;
        }

        return $statusResult;
    }
    
    /**
     * @param $storeId
     *
     * @return string
     */
    public function getStoreInstalltionTypeStatus($storeId)
    {
        $_helper = Mage::helper('adcurve_adcurve');
        if (Adcurve_Adcurve_Helper_Config::isTestMode($storeId) == true && 
            Adcurve_Adcurve_Helper_Config::isApiConfigured($storeId)) {
            return '<span class="installation-type-status">'.$_helper->__('TEST').'</span>';
        }

        return '';
    }    
    
    /**
     * @return string
     */
    public function getInstallationsTypeTestValue()
    {
        return self::INSTALLATION_TYPE_TEST;
    }
    
    /**
     * @return string
     */
    public function getInstallationsTypeLiveValue()
    {
        return self::INSTALLATION_TYPE_LIVE;
    }
    
    /**
     * Render html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->toHtml();
    }
}
