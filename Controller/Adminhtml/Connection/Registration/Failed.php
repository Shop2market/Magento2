<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection\Registration;

class Failed extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{

    /**
     * Failed registration action
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
    	$this->messageManager->addError(__('An error occurred during registration, please verify your data and retry the registration process.'));
        return $resultRedirect->setPath('*/*/');
    }
}
