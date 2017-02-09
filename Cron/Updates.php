<?php
namespace Adcurve\Adcurve\Cron;

class Updates
{
	/** The maximum amount of time the cron can spend on processing product updates */
    const MAX_PROCESSING_TIME       = 30;

    /** The maximum number of product updates allowed in one request as determined by Adcurve */
    const MAX_UPDATES_PER_REQUEST   = 200;

    protected $logger;
	protected $storeManager;
	protected $updateCollection;
	protected $updateRequest;
	protected $configHelper;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Adcurve\Adcurve\Model\UpdateFactory $updateFactory
	 * @param \Adcurve\Adcurve\Model\Rest\UpdateRequest $updateRequest
	 * @param \Adcurve\Adcurve\Helper\Config $configHelper
     */
    public function __construct(
    	\Psr\Log\LoggerInterface $logger,
    	\Magento\Store\Model\StoreManagerInterface $storeManager,
    	\Adcurve\Adcurve\Model\UpdateFactory $updateFactory,
    	\Adcurve\Adcurve\Model\Rest\UpdateRequest $updateRequest,
    	\Adcurve\Adcurve\Helper\Config $configHelper
	){
        $this->logger = $logger;
		$this->storeManager = $storeManager;
		$this->updateFactory = $updateFactory;
		$this->updateRequest = $updateRequest;
		$this->configHelper = $configHelper;
    }

    /**
     * Execute the cronjob, processing all updates pending for Adcurve
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob Adcurve Updates is executed.");
		
		$stores = $this->storeManager->getStores();
        $startTime = microtime(true);
        foreach ($stores as $store) {
            if(!$this->configHelper->isApiConfigured($store->getId())) {
                continue;
            }
			
            $updateCollection = $this->_getUpdateCollection($store->getId());
			
            if ($updateCollection->getSize() < 1) {
                continue;
            }
			
            /** Set the page size to the max number of updates allowed in one request */
            $updateCollection->setPageSize(self::MAX_UPDATES_PER_REQUEST);
            $lastPageNumber = $updateCollection->getLastPageNumber();
			
            /** Loop through all page numbers */
            for ($i = 1; $i <= $lastPageNumber; $i++) {
                if ($startTime + self::MAX_PROCESSING_TIME <= microtime(true)) {
                    /** If we've exceeded the maximum processing time, break from both loops */
                    break(2);
                }
				
                $updateCollection->setCurPage($i);
                $updateCollection->load();
                $this->processUpdatesBatch($updateCollection, $store->getId());
                $updateCollection->clear();
            }
        }
    }

    /**
     * Process a batch of product updates
     *
     * @param \Adcurve\Adcurve\Model\ResourceModel\Update\Collection $updateCollection
     * @param $storeId = null
     *
     * @return $this
     */
    public function processUpdatesBatch(
    	\Adcurve\Adcurve\Model\ResourceModel\Update\Collection $updateCollection,
    	$storeId = null
	){
        /** @var array $productData A list of all product data to be synced */
        $productData = array();
        foreach ($updateCollection as $update) {
            $data = $update->getProductData();
            if (!isset($data['entity_id'])) {
                $data['entity_id'] = $update->getProductId();
            }
			$productData[] = $data;
        }
		
        /** Only get the array values because the API can't handle associative arrays */
        $productData = array_values($productData);
		
        $error = false;
        try {
            /** Send the batch to Adcurve, an empty response is given on succes */
            $response = $this->updateRequest->sendData($productData, $storeId);
        } catch (Exception $e) {
            $this->logger->addError($e->getMessage());
            $error = true;
            foreach ($updateCollection as $update) {
                $update->error($e->getMessage());
            }
        }
		
        if ($error === false) {
            foreach ($updateCollection as $update) {
                $update->complete();
            }
        }
        return $this;
    }

    /**
     * Get a collection of updates that need to be processed for a given storeview
     *
     * @param int $storeId
     *
     * @return \Adcurve\Adcurve\Model\ResourceModel\Update\Collection
     */
    protected function _getUpdateCollection($storeId = 0)
    {
    	$update = $this->updateFactory->create();
        $updateCollection = $update->getCollection()
            ->addFieldToFilter(
                array('status', 'status', 'status'),
                array(
                    array('eq' => \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_NEW),
                    array('eq' => \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_ERROR),
                    array('eq' => \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_UPDATE),
                ))
            ->addFieldToFilter('retry_count', array('lt' => 5))
            //->addFieldToFilter('exported_at', array('null' => true)) //Redundant filter, may be activated..
            ->addFieldToFilter('store_id', array('eq' => $storeId))
			->setOrder('update_id', 'ASC');
        return $updateCollection;
    }
}
