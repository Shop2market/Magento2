<?php

namespace Adcurve\Adcurve\Block\Adminhtml\Integration;

class Activation extends \Magento\Framework\View\Element\Template
{
    protected $backendUrlBuilder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Model\UrlInterface $backendUrlBuilder,
        array $data = []
    ) {
        $this->backendUrlBuilder = $backendUrlBuilder;
        parent::__construct($context, $data);
    }

    public function getActivationUrl()
    {
        return $this->backendUrlBuilder->getUrl('adcurve_adcurve/integration/activate');
    }
}
