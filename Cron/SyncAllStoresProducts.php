<?php

namespace Adcurve\Adcurve\Cron;

class SyncAllStoresProducts
{
    protected $logger;
    protected $storeManager;
    protected $updateRequest;
    protected $configHelper;
    protected $queueFactory;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Adcurve\Adcurve\Model\QueueFactory $queueFactory
     * @param \Adcurve\Adcurve\Helper\Config $configHelper
     * @param \Adcurve\Adcurve\Model\ConnectionRepository $connectionRepository
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Adcurve\Adcurve\Model\QueueFactory $queueFactory,
        \Adcurve\Adcurve\Helper\Config $configHelper,
        \Adcurve\Adcurve\Model\ConnectionRepository $connectionRepository
    ) {
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        $this->queueFactory = $queueFactory;
        $this->configHelper = $configHelper;
        $this->connectionRepository = $connectionRepository;
    }

    /**
     * Execute the cronjob, processing all stores
     *
     * @return void
     */
    public function execute()
    {
        $this->logger->Info("Cronjob Adcurve SyncAllStoresProducts is executed.");
        $stores = $this->storeManager->getStores();
        $startTime = microtime(true);
        foreach ($stores as $store) {
            $connection = $this->connectionRepository->getByStoreId($store->getId());
            $this->logger->Info("Connection id: " . $connection->getId() . " status: " . $connection->getStatus() . "Store id: " . $store->getId() . " success status: " . \Adcurve\Adcurve\Model\Connection::STATUS_SUCCESS);
            if (!$this->configHelper->isApiConfigured($connection)) {
                continue;
            }
            if (!$connection->getStatus() == \Adcurve\Adcurve\Model\Connection::STATUS_SUCCESS) {
                continue;
            }
            $queue = $this->queueFactory->create();
            $queueCollection = $queue->getCollection()
                                     ->addFieldToFilter(
                                         'store_id',
                                         ['eq' => $store->getId()]
                                     )
                                     ->addFieldToFilter(
                                         'status',
                                         ['eq' => \Adcurve\Adcurve\Model\Queue::QUEUE_STATUS_NEW]
                                     );
            $this->logger->Info("SyncAllStoresProducts Collection SQL Dumps:");
            $this->logger->info($queueCollection->getSelect()->__toString());
            if ($queueCollection->getSize() > 0) {
                continue;
            }

            $queue = $this->queueFactory->create();
            $queue->setStoreId($store->getId());
            $queue->setPageNo(0);
            $queue->setStatus(\Adcurve\Adcurve\Model\Queue::QUEUE_STATUS_NEW);
            $queue->save();
        }
    }
}
