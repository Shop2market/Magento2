<?php
namespace Adcurve\Adcurve\Cron;

class Updates
{
	/**
     * The maximum amount of time the cron can spend on processing product updates
     */
    const MAX_PROCESSING_TIME       = 30;

    /**
     * The maximum number of product updates allowed in one request as determined by AdCurve
     */
    const MAX_UPDATES_PER_REQUEST   = 200;
	
    protected $logger;
	protected $updateCollection;
	protected $updateRequest;
	protected $configHelper;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
    	\Psr\Log\LoggerInterface $logger,
    	\Adcurve\Adcurve\Model\UpdateFactory $updateFactory,
    	\Adcurve\Adcurve\Model\Rest\UpdateRequest $updateRequest,
    	\Adcurve\Adcurve\Helper\Config $config
	){
        $this->logger = $logger;
		$this->updateFactory = $updateFactory;
		$this->updateRequest = $updateRequest;
		$this->configHelper = $config;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->addInfo("Cronjob Updates is executed.");
		$updateCollection = $this->_getCollection(1);
		$this->processApiBatch($updateCollection, 1);
    }

    /**
     * Get a collection of updates that need to be processed for the given storeview
     *
     * @param int $storeId
     *
     * @return Adcurve_Adcurve_Model_Resource_Product_Update_Collection
     */
    protected function _getCollection($storeId = 0)
    {
    	$update = $this->updateFactory->create();
        $collection = $update->getCollection()
            ->addFieldToFilter(
                array('status', 'status', 'status'),
                array(
                    array('eq' => \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_NEW),
                    array('eq' => \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_ERROR),
                    array('eq' => \Adcurve\Adcurve\Model\Update::PRODUCT_UPDATE_STATUS_UPDATE),
                ))
            ->addFieldToFilter('retry_count', array('lt' => 5))
            ->addFieldToFilter('exported_at', array('null' => true))
            ->addFieldToFilter('store_id', array('eq' => $storeId));
		
        return $collection;
    }

    /**
     * Process a batch of product updates
     *
     * @param Adcurve_Adcurve_Model_Resource_Product_Update_Collection $updateCollection
     * @param null                                                 $storeId
     *
     * @return $this
     */
    public function processApiBatch(
    	\Adcurve\Adcurve\Model\ResourceModel\Update\Collection $updateCollection,
    	$storeId = null
	){
        /** @var array $productData A list of all product data to be synced */
        $productData = array();
        /** @var Adcurve_Adcurve_Model_Product_Update $update */
        foreach ($updateCollection as $update) {
            $data = $update->getProductData();
			
            if (!isset($data['entity_id'])) {
                /** Make sure the entity_id is set */
                $data['entity_id'] = $update->getProductId();
            }

			$productData[] = $data;
        }
		
        /** Only get the array values because the API can't handle associative arrays */
        $productData = array_values($productData);
		
        $error = false;
        try {
            /** Send the batch to Adcurve */
            $this->updateRequest->sendData($productData, $storeId);
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
     * Process the Product Updates through the API
     *
     * @return $this
     */
    public function process()
    {
        /** Get all stores */
        $stores = Mage::app()->getStores(true);

        /** Save when we started processing api requests */
        $startTime = microtime(true);

        /** @var Mage_Core_Model_Store $store */
        foreach ($stores as $store) {
            if(!$this->configHelper->isApiConfigured($store->getId())) {
                /** If the api is not configured, don't process this storeview */
                continue;
            }

            /** Get all queue'd items for this store */
            $collection = $this->_getCollection($store->getId());

            $size = $collection->getSize();
            if (0 === $size) {
                /** If there are no queue'd items, continue */
                continue;
            }

            /** Set the collection order to process items oldest to newest */
            $collection->setOrder('entity_id', $collection::SORT_ORDER_ASC);

            /** Set the page size to the max number of updates allowed in one request */
            $collection->setPageSize(self::MAX_UPDATES_PER_REQUEST);

            /** Get the last page number from the collection */
            $lastPageNumber = $collection->getLastPageNumber();

            for ($i = 1; $i <= $lastPageNumber; $i++) {
                /** Loop through all page numbers */

                if ($startTime + self::MAX_PROCESSING_TIME <= microtime(true)) {
                    /** If we've exceeded the maximum processing time, break from both loops */
                    break(2);
                }

                /** Set the current page to the collection */
                $collection->setCurPage($i);

                /** Load the collection items */
                $collection->load();

                /** Process this batch */
                $this->processApiBatch($collection, $store->getId());

                /** Clear the collection items, to be reloaded on the next loop */
                $collection->clear();
            }
        }

        return $this;
    }
}
