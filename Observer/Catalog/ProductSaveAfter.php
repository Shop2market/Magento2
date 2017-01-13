<?php
namespace Adcurve\Adcurve\Observer\Catalog;

class ProductSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
	protected $productHelper;
	
	public function __construct(
		\Adcurve\Adcurve\Helper\Product $productHelper
	)
	{
		$this->productHelper = $productHelper;
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
        $product = $observer->getEvent()->getProduct();
		
		// TO DO: add website scope support
		if($product->getStoreId() == 0){ // Update all storeviews when global scope is edited
			foreach($product->getStoreIds() as $storeId){
				$preparedData = $this->productHelper->getProductData($product, $storeId);
				$this->saveUpdateForAdcurve($preparedData);
			}
		} else{
			$preparedData = $this->productHelper->getProductData($product, $product->getStoreId());
			$this->productHelper->saveUpdateForAdcurve($preparedData);
		}
    }
}