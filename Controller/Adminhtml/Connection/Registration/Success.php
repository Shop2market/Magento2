<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection\Registration;

use Magento\Framework\Exception\LocalizedException;

class Success extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
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
		$shopId = $this->getRequest()->getParam('shop_id');
		$apiToken = $this->getRequest()->getParam('api_token');
		
		if (!$apiToken || !$shopId) {
			$this->messageManager->addError(__('Something went wrong during the registration process, please contact Adcurve support to assist.'));
            return $resultRedirect->setPath('*/*/');
		}
		
        $id = $this->getRequest()->getParam('connection_id');
        $connection = $this->connectionFactory->create()->load($id);
		
        if (!$connection->getId() && $id) {
            $this->messageManager->addError(__('This Connection entity no longer exists in Magento.'));
            return $resultRedirect->setPath('*/*/');
        }
		
        $connection->setSuggestion(__('Registration completed. Please validate the connection by testing the connectivity to the right.'));
		$connection->setStatus(\Adcurve\Adcurve\Model\Connection::STATUS_POST_REGISTRATION);
		$connection->setAdcurveShopId($shopId);
        $connection->setAdcurveToken($apiToken);
        try {
            $connection->save();
            $this->messageManager->addSuccess(__(
            	'Adcurve registration successfully completed.<br />
            	A email has been sent to %1, where you can complete the registration process.',
            	$connection->getContactEmail()
			));
    		
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['connection_id' => $connection->getId()]);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the Connection.'));
        }
    	
        return $resultRedirect->setPath('*/*/');
    }
	
	private function _validateData($data)
	{
		if (!isset($data['connection_id'])
			|| !isset($data['shop_id'])
			|| !isset($data['api_token'])
		) {
			return false;
		}
		return true;
	}
}

