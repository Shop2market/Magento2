<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection\Products;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Queueall extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{
	protected $queueRepository;
	protected $queueFactory;
	
	/**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Adcurve\Adcurve\Model\QueueRepository $queueRepository,
        \Adcurve\Adcurve\Model\QueueFactory $queueFactory
    ) {
    	$this->queueRepository = $queueRepository;
		$this->queueFactory = $queueFactory;
        parent::__construct($context, $coreRegistry);
    }
	
    /**
     * Queue all products for sync to Adcurve
     * 
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $storeId = $this->getRequest()->getParam('store_id');
		
		if (!$storeId) {
			$this->messageManager->addError(__('Store id could not be retrieved.'));
            return $resultRedirect->setPath('*/*/');
		}
		
		try{
			$queue = $this->queueRepository->getByStoreId($storeId);
			if ($queue && $queue->getId()) {
				$this->messageManager->addNotice(__('All products for store id %1 are already queued for synchronisation to Adcurve.', $storeId));
	        	return $resultRedirect->setPath('*/*/');
			}
		} catch (NoSuchEntityException $e) {
			// Nothing here, continue if no queue entry was found yet.
		} catch (\Exception $e) {
			// Nothing here
		}
		
		$newQueue = $this->queueFactory->create();
		$newQueue->setStoreId($storeId);
		try {
			$newQueue->save();
			$this->messageManager->addSuccess(__('All products for store id %1 will be synchronised to Adcurve in the background.', $storeId));
        	return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while trying to sync all products.'));
        }
		
    	$this->messageManager->addError(__('An error occurred during product sync, please try again.'));
        return $resultRedirect->setPath('*/*/');
    }
}
