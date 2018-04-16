<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

use Magento\Framework\Exception\LocalizedException;

class Register extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{
	protected $_storeManager;
	protected $resultPageFactory;
	protected $connectionFactory;
	
	/**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Adcurve\Adcurve\Model\ConnectionFactory $connectionFactory
    ) {
    	$this->_storeManager = $storeManager;
		$this->resultPageFactory = $resultPageFactory;
		$this->connectionFactory = $connectionFactory;
        parent::__construct($context, $coreRegistry);
    }
	
	/**
     * Register a shop to Adcurve and establish a connection
	 * 
     * @return void
     */
    public function execute()
    {
    	// 1. Get ID and create model
        $id = $this->getRequest()->getParam('connection_id');
        $connection = $this->connectionFactory->create();
        
        // 2. Initial checking
        if ($id) {
            $connection->load($id);
            if (!$connection->getId()) {
                $this->messageManager->addError(__('This Connection no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
		
		$this->_coreRegistry->register('adcurve_adcurve_connection', $connection);
		
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(__('Register shop to Adcurve'), __('Register shop to Adcurve'));
        $resultPage->getConfig()->getTitle()->prepend(__('Register %1 (%2) at Adcurve', [$connection->getStoreName(), $connection->getStoreCode()]));
		
        return $resultPage;
    }
}