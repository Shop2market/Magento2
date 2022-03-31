<?php

namespace Adcurve\Adcurve\Cron;

class ProcessQueue
{
    protected $queueFactory;
    protected $productQueue;
    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Adcurve\Adcurve\Model\QueueFactory $queueFactory,
        \Adcurve\Adcurve\Model\Connection\ProductQueue $productQueue,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->queueFactory = $queueFactory;
        $this->productQueue = $productQueue;
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $queue = $this->queueFactory->create();
        $queueCollection = $queue->getCollection()
                            ->addFieldToFilter(
                                'status',
                                ['neq' => \Adcurve\Adcurve\Model\Queue::QUEUE_STATUS_COMPLETE]
                            );
        $this->logger->Info("ProcessQueue 1 Collection SQL Dumps:");
        $this->logger->info($queueCollection->getSelect()->__toString());
        foreach ($queueCollection as $queue) {
            $this->productQueue->queueAllProducts($queue->getId(), $queue->getStoreId());
        }
    }
}
