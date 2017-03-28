<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Api;

class CreateUser extends \Magento\Backend\App\Action
{
	/**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
    	$result = array();
        $result['status'] = 'success';
        $result['user_info'] = array(
        	"username" => $data['username'], 
        	"api_key" => $data['api_key']
        );
        $result['message'] = $this->__('The user has been saved.');
        /** @var Mage_Core_Helper_Data $coreHelper */
        $coreHelper = Mage::helper('core');
        $this->getResponse()->setBody($coreHelper->jsonEncode($result));
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__("Connection"));
        return $resultPage;
    }
}
