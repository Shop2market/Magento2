<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

class Validate extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{
	protected $connectionFactory;
	protected $statusRequest;
	
	/**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Adcurve\Adcurve\Model\ConnectionFactory $connectionFactory,
        \Adcurve\Adcurve\Model\Rest\StatusRequest $statusRequest
    ) {
		$this->connectionFactory = $connectionFactory;
		$this->statusRequest = $statusRequest;
        parent::__construct($context, $coreRegistry);
    }
	
    /**
     * Validate Connectivity to Adcurve
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('connection_id');
        if (!$id) {
        	 $this->messageManager->addError(__('We can\'t find the connection.'));
        	return $resultRedirect->setPath('*/*/');
		}
		$connection = $this->connectionFactory->create()->load($id);
		
		if (!$connection->getId() && $id) {
            $this->messageManager->addError(__('This Connection no longer exists.'));
            return $resultRedirect->setPath('*/*/');
        }
		
		$result = $this->statusRequest->getConnectionStatus($connection);
		if (!$result) {
			$this->messageManager->addError(__('This Connection is disabled or missing shop_id and api_token.'));
            return $resultRedirect->setPath('*/*/');
		}
		
		if (isset($result['error'])) {
			$this->messageManager->addError($result['error']);
		} else {
			$this->messageManager->addSuccess(__('Connection to Adcurve is successfully established. No further action is needed.'));
		}
		
		return $resultRedirect->setPath('*/*/');
    }
}
