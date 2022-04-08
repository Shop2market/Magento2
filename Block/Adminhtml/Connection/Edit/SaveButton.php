<?php

namespace Adcurve\Adcurve\Block\Adminhtml\Connection\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Adcurve\Adcurve\Block\Adminhtml\Connection\GenericButton;

class SaveButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context
     * @param \Magento\Framework\Registry
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($this->getConnection()->getStatus() == \Adcurve\Adcurve\Model\Connection::STATUS_SUCCESS) {
            $label = __('Save');
        } else {
            $label = __('Save and create connection');
        }

        return [
            'label' => $label,
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
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
