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
        return 'https://app.adcurve.com/integrations/magento/install';
    }
}
