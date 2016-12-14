<?php


namespace Adcurve\Adcurve\Observer\Catalog;

class ProductSaveAfter implements \Magento\Framework\Event\ObserverInterface
{
	protected $updateFactory;
	protected $updateRepository;
	protected $productHelper;
	protected $dateTime;
	
	public function __construct(
		\Adcurve\Adcurve\Model\UpdateFactory $updateFactory,
		\Adcurve\Adcurve\Model\UpdateRepository $updateRepository,
		\Adcurve\Adcurve\Helper\Product $productHelper,
		\Magento\Framework\Stdlib\DateTime\DateTime $date
	)
	{
		$this->updateFactory = $updateFactory;
		$this->updateRepository = $updateRepository;
		$this->productHelper = $productHelper;
		$this->dateTime = $date;
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
		
		// To do: add website scope support
		if($product->getStoreId() == 0){ // Update all storeviews when global scope is edited
			foreach($product->getStoreIds() as $storeId){
				$preparedData = $this->productHelper->getProductData($product, $storeId);
				$this->saveUpdateForAdcurve($product, $preparedData, $storeId);
			}
		} else{
			$preparedData = $this->productHelper->getProductData($product, $product->getStoreId());
			$this->saveUpdateForAdcurve($product, $preparedData, $storeId);
		}
		
    }
	
	public function saveUpdateForAdcurve($product, $preparedData, $storeId)
	{
		$update = $this->updateFactory->create();
		$update->setProductId($product->getId());
		$update->setStoreId($storeId);
		$update->setProductData(serialize($preparedData));
		$currentDateTime = $this->dateTime->gmtDate();
		$update->setCreatedAt($currentDateTime);
		$update->setUpdatedAt($currentDateTime);
		$update->setStatus('update');
		$this->updateRepository->save($update);
	}
}
