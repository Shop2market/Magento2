<?php
namespace Adcurve\Adcurve\Block\Adminhtml\Connection\Setup;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Adcurve\Adcurve\Block\Adminhtml\Connection\GenericButton;

class RegisterButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Register shop in Adcurve'),
            'on_click' => sprintf("location.href = '%s';", $this->getRegisterUrl()),
            'class' => 'save primary',
            'sort_order' => 10
        ];
    }
	
    /**
     * Get URL to register using form submit
     *
     * @return string
     */
    public function getRegisterUrl()
    {
    	// @TODO Change this to register url
        return $this->getUrl('*/*/');
    }
}
