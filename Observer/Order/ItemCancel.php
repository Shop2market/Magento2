<?php
namespace Adcurve\Adcurve\Observer\Order;

class ItemCancel implements \Magento\Framework\Event\ObserverInterface
{
	protected $productHelper;
	protected $configHelper;
	protected $_productloader; 
	protected $connectionRepository;
	
	public function __construct(
		\Adcurve\Adcurve\Helper\Product $productHelper,
		\Adcurve\Adcurve\Helper\Config $configHelper,
		\Magento\Catalog\Model\ProductFactory $_productloader,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Adcurve\Adcurve\Model\QueueFactory $queueFactory,
		\Adcurve\Adcurve\Model\ConnectionRepository $connectionRepository
	)
	{
		$this->productHelper = $productHelper;
		$this->_productloader = $_productloader;
		$this->_storeManager = $storeManager;
		$this->queueFactory = $queueFactory;
		$this->_configHelper=$configHelper;
		$this->connectionRepository = $connectionRepository;
	}
	
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
		
		
		$item = $observer->getEvent()->getItem();

        $qty = $item->getQtyOrdered() - max($item->getQtyShipped(), $item->getQtyInvoiced()) - $item->getQtyCanceled();

        if ($item->getId() && ($productId = $item->getProductId()) && empty($children) && $qty) {
            $productId = $item->getProductId();
            $this->_addProductsToBatch($productId);
        }

        return $this;
		
		
    }
	
	/**
     * Load product from product id
     *
     * @param int $productId
     *
     *
     */
	public function getLoadProduct($productId)
    {
        return $this->_productloader->create()->load($productId);
    }
	
	/**
     * Add a products to batch that need to be processed for the given storeview
     *
     * @param int $productId
     *
     * @param int $storeId
     *
     */
    protected function _addProductsToBatch($productId, $storeId = 0)
    {    
        /** @var Adcurve_Adcurve_Helper_Product $helper */
		$helper = $this->productHelper;

        $product = $this->getLoadProduct($productId);
        if (!$helper->isProductValidForExport($product)) {
            /** If product is not valid for export, skip */
            return;
        }
        
        if ($storeId == 0) {        
            $storeIds = $product->getStoreIds();
            
            if (count($storeIds) == 0) {        
				$storeList = $this->_storeManager->getStores();
               
                /** @var Mage_Core_Model_Store $storeObject */
                foreach ($storeList as $storeObject) {
                
                    if ($storeObject->getId() == 0) {
                        continue;
                    }
                    
                    $storeIds[] = $storeObject->getId();                    
                }
            }
            
            if (count($storeIds) > 0) {
            
                foreach ($storeIds as $storeId) {
                
                    try {
                        if ($storeId == 0) {
                            continue;
                        }
                        
                        if (!in_array($storeId, $product->getStoreIds())) {
                            return;
                        }
                        $connection = $this->connectionRepository->getByStoreId($storeId);
		
                        if (!$this->_configHelper->isApiConfigured($connection)) {
                            /** If the api is not configured, don't process this storeview */
                            continue;
                        }
                        
                        /** @var Adcurve_Adcurve_Model_Product_Batches $batch */
                        //$batch = Mage::getModel('adcurve_adcurve/product_batches');
                        /** Create and save the product update entity */
                        //$batch->setStoreId($storeId)
                        //    ->save();
							
						$preparedData = $this->productHelper->getProductData($product, $storeId);
						$this->productHelper->saveUpdateForAdcurve($preparedData);	

                            
                    } catch (Exception $e) {
                        Mage::logException($e);
                    }
                }
            }
        } else {
        
            try {
                if (!in_array($storeId, $product->getStoreIds())) {
                    return;
                }
                $connection = $this->connectionRepository->getByStoreId($storeId);
		
                if (!$this->_configHelper->isApiConfigured($connection)) {
                    return;
                }
                
                /** @var Adcurve_Adcurve_Model_Product_Batches $batch */
                //$batch = Mage::getModel('adcurve_adcurve/product_batches');
                /** Create and save the product update entity */
                //$batch->setProductId($productId)
                //    ->setStoreId($storeId)
                //    ->save();
					
				$preparedData = $this->productHelper->getProductData($product, $storeId);
				$this->productHelper->saveUpdateForAdcurve($preparedData);	
	
                    
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }
}