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
			
			if(!$connection->getId()){
				$connection->setStoreId($store->getId());
				$connection->setStoreName($store->getName());
				$connection->setStoreCode($store->getCode());
				//@TODO - Add all extra nessecairy data
				try {
					$connection->save();
	                $this->messageManager->addSuccess(__('<strong>%1 (%2)</strong> was succesfully created.', [$connection->getStoreName(), $connection->getStoreCode()]));
	            } catch (LocalizedException $e) {
	                $this->messageManager->addError($e->getMessage());
	            } catch (\Exception $e) {
	                $this->messageManager->addException($e, __('Something went wrong while trying to create <strong>%1 (%2)</strong>.', [$store->getName(), $store->getCode()]));
	            }
			} else{
				$this->messageManager->addSuccess(__('<strong>%1 (%2)</strong> already exists, creation skipped.', [$connection->getStoreName(), $connection->getStoreCode()]));
			}
			
	        if($connection->getStatus() && $connection->getAdcurveShopId()){
	        	//@TODO - Add connection check here for existing stores
	        	continue;
			} else{
				$this->messageManager->addNotice(__('Registration for <strong>%1 (%2)</strong> is not complete.', [$connection->getStoreName(), $connection->getStoreCode()]));
			}
		}
		
		return $resultRedirect->setPath('*/*/');
	}
}