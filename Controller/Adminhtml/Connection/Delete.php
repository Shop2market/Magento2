<?php

namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

class Delete extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{
    protected $connectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Adcurve\Adcurve\Model\ConnectionFactory $connectionFactory
    ) {
        $this->connectionFactory = $connectionFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Delete Adcurve Connection Action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('connection_id');
        if (!$id) {
            $this->messageManager->addError(__('We can\'t find a Connection to delete.'));
            return $resultRedirect->setPath('*/*/');
        }
        $connection = $this->connectionFactory->create()->load($id);

        if (!$connection->getId() && $id) {
            $this->messageManager->addError(__('This Connection no longer exists.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $connection->delete();
            $this->messageManager->addSuccess(__('Adcurve Connection successfully removed.'));
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('*/*/');
        }
    }
}
