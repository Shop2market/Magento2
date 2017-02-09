<?php
namespace Adcurve\Adcurve\Cron;

class QueueAllProducts
{
	/** Pagesize to minimize load on webshop during product collection queueing */
	const QUEUE_PRODUCT_PAGING_SIZE = 100;
	
	protected $logger;
	protected $storeManager;
	protected $productFactory;
	protected $productHelper;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
    	\Psr\Log\LoggerInterface $logger,
    	\Magento\Store\Model\StoreManagerInterface $storeManager,
    	\Magento\Catalog\Model\ProductFactory $productFactory,
    	\Adcurve\Adcurve\Helper\Product $productHelper
	){
        $this->logger = $logger;
		$this->storeManager = $storeManager;
		$this->productFactory = $productFactory;
		$this->productHelper = $productHelper;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob Adcurve QueueAllProducts is executed.");
		
		$productCollection = $this->productFactory->create()->getCollection();
		
		$productCollection->setPageSize(self::QUEUE_PRODUCT_PAGING_SIZE);
        $lastPageNumber = $productCollection->getLastPageNumber();
		
		for ($i = 1; $i <= $lastPageNumber; $i++) {
            /* Possible addition, escape on processing time
            if ($startTime + self::MAX_PROCESSING_TIME <= microtime(true)) {
                break(2);
            }
			*/
            $productCollection->setCurPage($i);
            $productCollection->load();
			foreach($productCollection as $product){
				$_product = $this->productFactory->create()->load($product->getId());
				// TO DO: add website scope support
				if($product->getStoreId() == 0){ // Update all storeviews when global scope is edited
					foreach($product->getStoreIds() as $storeId){
						$preparedData = $this->productHelper->getProductData($product, $storeId);
						$this->productHelper->saveUpdateForAdcurve($preparedData, \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_NEW);
					}
				} else{
					$preparedData = $this->productHelper->getProductData($product, $product->getStoreId());
					$this->productHelper->saveUpdateForAdcurve($preparedData, \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_NEW);
				}
				$_product->clearInstance();
			}
            $productCollection->clear();
        }
    }
}