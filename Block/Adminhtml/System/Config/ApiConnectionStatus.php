<?php
 
class Adcurve_Adcurve_Block_Adminhtml_System_Config_ApiConnectionStatus extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Template file used
     *
     * @var string
     */
    protected $_template = 'Adcurve/adcurve/system/config/api_connection_status.phtml';

    
    /**
     * @param $storeId
     *
     * @return int
     */
    public function getApiConnectionStatus($storeId)
    {
        $_helper = Mage::helper('adcurve_adcurve');


        /** $var  Adcurve_Adcurve_Helper_Config $confHelper */
        $confHelper = Mage::helper('adcurve_adcurve/config');

        $statusResult = array(
            'stepsCompleted' => 0,
            'suggestion' => $_helper->__('Register the Storeview with the form below.')
        );

        if (!$confHelper->isApiConfigured($storeId) && !( $confHelper->getShopIsRegistered($storeId) && $confHelper->isEnabled($storeId) == false)) {
            return $statusResult;
        }

        /** @var Adcurve_Adcurve_Model_Rest_Status $api */
        $api = Mage::getModel('adcurve_adcurve/rest_status');
        $apiStatus = $api->getConnectionStatus($storeId);

        switch ($apiStatus['status']) {
            case Adcurve_Adcurve_Model_Rest_Status::STATUS_ERROR_CONNECTION_TO_ADCURVE:
                $statusResult['stepsCompleted'] = 1;
                $statusResult['suggestion'] = $_helper->__('Something went wrong with the installation, please contact support.');
                break;
            case Adcurve_Adcurve_Model_Rest_Status::STATUS_ERROR_RESULT_FROM_ADCURVE:
                $statusResult['stepsCompleted'] = 2;
                $urlStep2 = "http://support.adcurve.com/support/solutions/articles/4000080241-maak-de-api-connectie-magento-users-";
                $msgStep2 = "Connection with AdCurve API not established yet. Please follow this %s for more information";
                $msgStep2V = "<a target='_blank' class='manual-url' href='". $urlStep2 ."'>" . $_helper->__('manual') . "</a>";

                $statusResult['suggestion'] = $_helper->__($msgStep2, $msgStep2V);
                break;
            case Adcurve_Adcurve_Model_Rest_Status::STATUS_SUCCESS:
                $statusResult['stepsCompleted'] = 3;
                if (Adcurve_Adcurve_Helper_Config::isTestMode($storeId) == true) {
                    $msgStep3 = 'Ready for testing. Please make sure you install the live version using the registration form bellow to enjoy all the functionality!';
                    $statusResult['suggestion'] = $_helper->__($msgStep3);
                } else {
                    $statusResult['suggestion'] = $_helper->__('Connection successfully established. No further action is needed.');
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
