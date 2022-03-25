<?php

namespace Adcurve\Adcurve\Block\Adminhtml\Connection\Setup;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Adcurve\Adcurve\Block\Adminhtml\Connection\GenericButton;

class RegisterButton extends GenericButton implements ButtonProviderInterface
{
    public $configHelper;
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context
     * @param \Adcurve\Adcurve\Helper\Config
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Adcurve\Adcurve\Helper\Config $configHelper
    ) {
        parent::__construct($context);
        $this->configHelper = $configHelper;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Register in Adcurve'),
            'on_click' => sprintf("location.href = '%s';", $this->getRegisterUrl()),
            'class' => 'save primary',
            'sort_order' => 0
        ];
    }

    /**
     * Get URL for Adcurve registration form post
     *
     * @return string
     */
    public function getRegisterUrl()
    {
        // @TODO Replace by url getter to get url from configuration
        $connection = $this->getConnection();
        $this->configHelper->isApiConfigured($connection);
    }

    /**
     * Retrieve template object
     *
     * @return \Adcurve\Adcurve\Model\Connection
     */
    public function getConnection()
    {
        return $this->_coreRegistry->registry('adcurve_adcurve_connection');
    }
}
