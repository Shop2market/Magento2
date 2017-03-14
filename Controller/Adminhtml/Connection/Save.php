<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $model = $this->_objectManager->create('Adcurve\Adcurve\Model\Connection');
			
            $id = $this->getRequest()->getParam('connection_id');
            if ($id) {
                $model->load($id);
            }
			
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This Connection no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            $model->setData($data);
        	
            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the Connection.'));
                $this->dataPersistor->clear('adcurve_adcurve_connection');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['connection_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Connection.'));
            }
        
            $this->dataPersistor->set('adcurve_adcurve_connection', $data);
            return $resultRedirect->setPath('*/*/edit', ['connection_id' => $this->getRequest()->getParam('connection_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
