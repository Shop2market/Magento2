<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{
    protected $dataPersistor;
	protected $connectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Adcurve\Adcurve\Model\ConnectionFactory $connectionFactory
    ) {
        $this->dataPersistor = $dataPersistor;
		$this->connectionFactory = $connectionFactory;
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
            $id = $this->getRequest()->getParam('connection_id');
            $connection = $this->connectionFactory->create()->load($id);
			
            if ($id) {
                $connection->load($id);
            }
			
            if (!$connection->getId() && $id) {
                $this->messageManager->addError(__('This Connection no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
			
			if (!$this->_validateData($data)) {
				$this->messageManager->addError(__('Data is missing, try to fill in all the required fields.'));
                return $resultRedirect->setPath('*/*/edit');
			}
			
			$data['suggestion'] = __('All required information is present, please continue by registering on Adcurve (button to the right).');
			$data['status'] = \Adcurve\Adcurve\Model\Connection::STATUS_PRE_REGISTRATION;
            $connection->setData($data);
        	
            try {
                $connection->save();
                $this->messageManager->addSuccess(__('Adcurve connection succesfully enriched with required information.'));
                $this->dataPersistor->clear('adcurve_adcurve_connection');
        		
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['connection_id' => $connection->getId()]);
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

	private function _validateData($data)
	{
		if (!isset($data['connection_id'])
			|| !isset($data['store_id'])
			|| !isset($data['store_id'])
			|| !isset($data['store_name'])
			|| !isset($data['store_code'])
			|| !isset($data['production_mode'])
			|| !isset($data['contact_firstname'])
			|| !isset($data['contact_lastname'])
			|| !isset($data['contact_email'])
			|| !isset($data['contact_telephone'])
			|| !isset($data['company_name'])
			|| !isset($data['company_address'])
			|| !isset($data['company_zipcode'])
			|| !isset($data['company_city'])
			|| !isset($data['company_region'])
			|| !isset($data['company_country'])
		) {
			return false;
		}
		return true;
	}
}
