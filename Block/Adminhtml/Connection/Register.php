<?php

namespace Adcurve\Adcurve\Block\Adminhtml\Connection;

class Register extends \Magento\Backend\Block\Template implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    public const API_WSDL_ENDPOINT         = 'soap/default?wsdl_list=1';
    public const INSTALLATION_TYPE_TEST    = 'test';
    public const INSTALLATION_TYPE_LIVE    = 'live';
    public const URL_PATH_ADCURVE_CONNECTION_SAVE = 'adcurve_adcurve/connection/ajaxSave';

    protected $storeManager;
    protected $resourceInterface;
    public $configHelper;
    public $connectionHelper;
    protected $statusRequest;
    protected $_attributeFactory;
    protected $_attributeTypeFactory;
    protected $productAttributeOptions;
    protected $connectionRepository;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface
     * @param \Adcurve\Adcurve\Helper\Connection
     * @param \Adcurve\Adcurve\Model\Rest\StatusRequest
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Adcurve\Adcurve\Helper\Config $configHelper,
        \Adcurve\Adcurve\Helper\Connection $connectionHelper,
        \Adcurve\Adcurve\Model\Rest\StatusRequest $statusRequest,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory,
        \Magento\Eav\Model\Entity\TypeFactory $attributeTypeFactory,
        \Adcurve\Adcurve\Model\ConnectionRepository $connectionRepository,
        \Adcurve\Adcurve\Ui\Component\Listing\Column\Connection\Attributes $productAttributeOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
        $this->connectionHelper = $connectionHelper;
        $this->statusRequest = $statusRequest;
        $this->_attributeFactory = $attributeFactory;
        $this->_attributeTypeFactory = $attributeTypeFactory;
        $this->productAttributeOptions = $productAttributeOptions;
        $this->connectionRepository = $connectionRepository;
    }

    /**
     * @return string
     */
    public function getWsdlEndpoint()
    {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK) . self::API_WSDL_ENDPOINT;
    }

    public function getStoreInstalltionTypeStatus($storeId)
    {
        $connection = $this->connectionRepository->getByStoreId($storeId);


        /*if ($this->configHelper->isTestMode($storeId) == true
            && $this->configHelper->isApiConfigured($connection)
        )*/
        if ($this->configHelper->isApiConfigured($connection)) {
            return '<span class="installation-type-status">' . __('TEST') . '</span>';
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

    /**
     * Return connection save url for Ajax saving the form data
     *
     * Currently not in use!
     *
     * @return string $formSaveUrl
     */
    public function getAjaxSaveUrl()
    {
        return $this->_urlBuilder->getUrl(self::URL_PATH_ADCURVE_CONNECTION_SAVE);
    }


    public function getAttributesArray()
    {
        $attributesToSkip = $this->productAttributeOptions->toOptionArray();
        return $attributesToSkip;
    }

    public function getAllValues()
    {
        $allValues = $this->productAttributeOptions->getAllValues();
        return $allValues;
    }
}
