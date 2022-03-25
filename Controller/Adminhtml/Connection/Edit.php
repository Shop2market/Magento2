<?php

namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

class Edit extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{
    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('connection_id');
        $connection = $this->_objectManager->create('Adcurve\Adcurve\Model\Connection');

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
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Connection') : __('New Connection'),
            $id ? __('Edit Connection') : __('New Connection')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Connections'));
        $resultPage->getConfig()->getTitle()->prepend($connection->getId() ? $connection->getTitle() : __('New Connection'));
        return $resultPage;
    }
}
