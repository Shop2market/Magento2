<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

use Magento\Framework\Exception\LocalizedException;

class Refresh extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{
	protected $_storeManager;
	protected $connectionFactory;
	
	/**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Adcurve\Adcurve\Model\ConnectionFactory $connectionFactory
    ) {
    	$this->_storeManager = $storeManager;
		$this->connectionFactory = $connectionFactory;
        parent::__construct($context, $coreRegistry);
    }
	
	/**
     * Refreshes all store connections with Adcurve
	 * 
	 * Creates new entites is stores are missing and checks connection if store exists and is enabled
     *
     * @return void
     */
    public function execute()
    {
    	/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
		
    	$storeList = $this->_storeManager->getStores();
		
		foreach($storeList as $store){
			$connection = $this->connectionFactory->create();
	        $connection->load($store->getId(), 'store_id');
			
			if(!$connection->getId() || true){
				$connection->setStoreId($store->getId());
				$connection->setStoreCode($store->getCode());
				//@TODO - Add all data nessecairy
				try {
					$connection->save();
	                $this->messageManager->addSuccess(__('Store: %1 (%2) was succesfully saved', [$store->getName(), $store->getCode()]));
	            } catch (LocalizedException $e) {
	                $this->messageManager->addError($e->getMessage());
	            } catch (\Exception $e) {
	                $this->messageManager->addException($e, __('Something went wrong while saving the Connection.'));
	            }
				
				
				
				
			}
			
	        if ($connection->getId()) {
	        	//@TODO - Add connection check here for existing stores
	        	continue;
			}
		}
		//@TODO - Complete messaging
		
		
		return $resultRedirect->setPath('*/*/');
	}
}