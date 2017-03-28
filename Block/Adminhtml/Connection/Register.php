<?php
namespace Adcurve\Adcurve\Block\Adminhtml\Connection;

class Register
	extends \Magento\Backend\Block\Template
	implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    const API_WSDL_ENDPOINT 		= 'soap/default?wsdl_list=1';
    const INSTALLATION_TYPE_TEST 	= 'test';
    const INSTALLATION_TYPE_LIVE 	= 'live';
	
	protected $storeManager;
	protected $resourceInterface;
	public $configHelper;
	public $connectionHelper;
	protected $statusRequest;
	
	/**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context
	 * @param \Magento\Framework\App\Config\ScopeConfigInterface
     */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Adcurve\Adcurve\Helper\Config $configHelper,
		\Adcurve\Adcurve\Helper\Connection $connectionHelper,
		\Adcurve\Adcurve\Model\Rest\StatusRequest $statusRequest,
		array $data = []
	){
		parent::__construct($context, $data);
		$this->configHelper = $configHelper;
		$this->connectionHelper = $connectionHelper;
		$this->statusRequest = $statusRequest;
	}
	
    /**
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        $html = '<select name="shop[store_id]" id="store_switcher">';
		
        /** @var \Magento\Core\Model\Store $store */
        foreach($this->getAllStores() as $store){
            if ($this->configHelper->isApiConfigured($store->getId()) == true) {
                continue;
            }
            
            $html .= '<optgroup label="'
                . $this->escapeHtml($website->getName())
                . '"></optgroup>';
			
            $html .= '<option value="'
                . $this->escapeHtml($store->getId())
                . '"'
                . (($this->getDefaultStore()->getId() == $store->getId()) ? 'selected="selected"' : '')
                . '>&nbsp;&nbsp;&nbsp;&nbsp;'
                . $this->escapeHtml($store->getName())
                . '</option>';
        }
        $html .= '</select>';
		
        return $html;
    }
	
    /**
     * @return Mage_Core_Model_Store
     */
    public function getDefaultStore()
    {
        return $this->_storeManager->getDefaultStoreView();
    }
	
	/**
	 * @return \Magento\Code\Model\Store\Collection $stores
	 */
	public function getAllStores()
	{
		return $this->_storeManager->getStores();
	}
	
    /**
     * @return string
     */
    public function getWsdlEndpoint()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK) . self::API_WSDL_ENDPOINT;
    }

    /**
     * @return string
     */
    public function getAttributeSelectHtml()
    {
        $html = '<select name="shop[attributes][]" id="product_attributes"';
        $html .= ' class="select multiselect" size="10" multiple="multiple"  readonly="readonly">';
		/* TO DO, rcomplete function with Magento 2 logic
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFieldToFilter('frontend_label', array('neq' => ''))
            ->addFieldToFilter('is_visible', array('eq' => '1'))
            ->getItems();

        /** @var Mage_Catalog_Model_Resource_Eav_Attribute $attribute */
        /*
        foreach ($attributes as $attribute) {
            $html .= '<option value="'
                . $this->escapeHtml($attribute->getAttributecode())
                . '" selected="selected">'
                . $this->escapeHtml($attribute->getFrontendLabel())
                . '</option>';
        }
		*/
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
        $statusResult = array(
            'stepsCompleted' => 0,
            'suggestion' => __('Register the Storeview with the form below.')
        );

        if (!$this->configHelper->isApiConfigured($storeId)){
            return $statusResult;
        }
		
        $apiStatus = $this->statusRequest->getConnectionStatus($storeId);
		
        switch ($apiStatus['status']) {
            case \Adcurve\Adcurve\Model\Rest\StatusRequest::STATUS_ERROR_CONNECTION_TO_ADCURVE:
                $statusResult['stepsCompleted'] = 1;
                $message = 'Something went wrong with the installation, please contact support';
                $statusResult['suggestion'] = __($message);
                break;
            case \Adcurve\Adcurve\Model\Rest\StatusRequest::STATUS_ERROR_RESULT_FROM_ADCURVE:
                $statusResult['stepsCompleted'] = 2;
                $msgStep2 = 'Connection with AdCurve API not established yet. Please follow this %s for more information';
                $urlRF = $this->configHelper->getApiRoleCreatedFailedUrl($storeId);
                $msgStep2D = "<a target='_blank' class='manual-url' href='" . $urlRF . "'>" . __('manual') . "</a>";
                $statusResult['suggestion'] = __($msgStep2, $msgStep2D);
                break;
            case \Adcurve\Adcurve\Model\Rest\StatusRequest::STATUS_SUCCESS:
                $statusResult['stepsCompleted'] = 3;
                if ($this->configHelper->isTestMode($storeId) == true) {
                    $msgStep3 = 'Ready for testing. Please make sure you install the live version';
                    $msgStep3 .= ' using the registration form bellow to enjoy all the functionality!';
                    $statusResult['suggestion'] = __($msgStep3);
                } else {
                    $msgStep3 = 'Connection successfully established. No further action is needed.';
                    $statusResult['suggestion'] = __($msgStep3);
                }
                break;
        }
		
        return $statusResult;
    }
    
    public function getStoreInstalltionTypeStatus($storeId)
    {
        if($this->configHelper->isTestMode($storeId) == true
        	&& $this->configHelper->isApiConfigured($storeId)
		){
            return '<span class="installation-type-status">'.__('TEST').'</span>';
        }
		
        return '';
    }
	
    /**
     * Render html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->toHtml();
    }
	
	/**
     * Return form block HTML
     *
     * @return string
     */
    public function getForm()
    {
        return $this->getLayout()->createBlock('Adcurve\Adcurve\Block\Adminhtml\Connection\Register\Form')->toHtml();
    }
}
