<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

class Setup extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{
    protected $resultPageFactory;
	protected $connectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Adcurve\Adcurve\Model\ConnectionFactory $connectionFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
		$this->connectionFactory = $connectionFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
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
		
        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(__('Setup Adcurve Connection'), __('Setup Adcurve Connection'));
        $resultPage->getConfig()->getTitle()->prepend(__('Connection'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage connection "%1"', [$connection->getStoreCode()]));
        return $resultPage;
    }
}
