<?php
namespace Adcurve\Adcurve\Controller\Adminhtml\Connection;

use Magento\Framework\Exception\LocalizedException;

class Refresh extends \Adcurve\Adcurve\Controller\Adminhtml\Connection
{
	protected $_storeManager;
	protected $connectionFactory;
	protected $adminUser;
	protected $soapPassword;
	
	/**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Adcurve\Adcurve\Model\ConnectionFactory $connectionFactory,
        \Adcurve\Adcurve\Model\AdminUser $adminUser
    ) {
    	$this->_storeManager = $storeManager;
		$this->connectionFactory = $connectionFactory;
		$this->adminUser = $adminUser;
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
		
		$roleCreated = $this->adminUser->createAdcurveRole();
		if($roleCreated){
			$this->messageManager->addSuccess(__('<strong>Adcurve</strong> API role was successfully created.'));
		}
		
		$this->soapPassword = false;
		$userCreated = $this->adminUser->createAdcurveUser();
		if($userCreated && $userCreated['created'] == true){
			$this->soapPassword = $userCreated['password'];
			$this->messageManager->addSuccess(__('<strong>Adcurve</strong> API user was successfully created.'));
		}
		
		foreach($storeList as $store){
			$connection = $this->connectionFactory->create();
	        $connection->load($store->getId(), 'store_id');
			
			if(!$connection->getId()){
				$connection->setStoreId($store->getId());
				$connection->setStoreName($store->getName());
				$connection->setStoreCode($store->getCode());
				$connection->setSoapUsername('Adcurve');
				if($this->soapPassword){
					$connection->setSoapApiKey($this->soapPassword);
				}
				//@TODO - Add all extra nessecairy data
				try {
					$connection->save();
	                $this->messageManager->addSuccess(__('<strong>%1 (%2)</strong> was succesfully created.', [$connection->getStoreName(), $connection->getStoreCode()]));
	            } catch (LocalizedException $e) {
	                $this->messageManager->addError($e->getMessage());
	            } catch (\Exception $e) {
	                $this->messageManager->addException($e, __('Something went wrong while trying to create <strong>%1 (%2)</strong>.', [$store->getName(), $store->getCode()]));
	            }
			} elseif(!$connection->getSoapApiKey() && $this->soapPassword){
				$connection->setSoapApiKey($this->soapPassword);
				try {
					$connection->save();
					$this->messageManager->addSuccess(__('<strong>%1 (%2)</strong> was missing an Api key and was succesfully resaved with the correct key.', [$connection->getStoreName(), $connection->getStoreCode()]));
				} catch (LocalizedException $e) {
	                $this->messageManager->addError($e->getMessage());
	            } catch (\Exception $e) {
	                $this->messageManager->addException($e, __('Something went wrong while trying to resave <strong>%1 (%2)</strong> with the correct api_key.', [$store->getName(), $store->getCode()]));
	            }
			} else{
				if(!$this->soapPassword && $connection->getSoapApiKey()){
					$this->soapPassword = $connection->getSoapApiKey();
				}
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