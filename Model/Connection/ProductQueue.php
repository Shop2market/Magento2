<?php
namespace Adcurve\Adcurve\Model\Connection;

class ProductQueue
{
	/** Pagesize to minimize load on webshop during product collection queueing */
	const QUEUE_PRODUCT_PAGING_SIZE = 100;
	
	protected $productFactory;
	protected $productHelper;

    /**
     * Constructor
     */
    public function __construct(
    	\Magento\Catalog\Model\ProductFactory $productFactory,
    	\Adcurve\Adcurve\Helper\Product $productHelper
	){
		$this->productFactory = $productFactory;
		$this->productHelper = $productHelper;
    }
	
	/**
	 * Queue all products for synchronisation to Adcurve for a given store ID
	 * 
	 * @param string $storeId
	 * 
	 * @return boolean $successful
	 */
	public function queueAllProducts($storeId = null)
	{
		if (!$storeId) {
			return false;
		}
		
		$productCollection = $this->productFactory->create()
			->getCollection();
				
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
				$preparedData = $this->productHelper->getProductData($product, $storeId);
				$this->productHelper->saveUpdateForAdcurve($preparedData, \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_NEW);
				$_product->clearInstance();
			}
            $productCollection->clear();
        }
		return true;
	}
	
}
