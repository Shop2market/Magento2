<?php
namespace Adcurve\Adcurve\Observer\Catalog;

class ProductDeleteAfterDone implements \Magento\Framework\Event\ObserverInterface
{
	protected $productHelper;
	protected $storeManager;
	
	public function __construct(
		\Adcurve\Adcurve\Helper\Product $productHelper,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	)
	{
		$this->productHelper = $productHelper;
		$this->storeManager = $storeManager;
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
    	$origData = $observer->getEvent()->getProduct()->getOrigData();
		
		foreach($this->storeManager->getStores() as $store){
			$configurableId = $this->productHelper->getConfigurableproductId($origData['entity_id'], $origData['type_id']);
			$preparedData = [
				'entity_id' 		=> $origData['entity_id'],
				'sku' 				=> $origData['sku'],
				'enabled' 			=> 'false',
				'store_id' 			=> $store->getId(),
				'simple_id' 		=> $origData['entity_id'],
				'configurable_id' 	=> $configurableId
			];
			$this->productHelper->saveUpdateForAdcurve($preparedData);
		}
    }
}