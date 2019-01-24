<?php
namespace Adcurve\Adcurve\Cron;

class ProcessQueue
{
	protected $queueFactory;
	protected $productQueue;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
    	\Adcurve\Adcurve\Model\QueueFactory $queueFactory,
    	\Adcurve\Adcurve\Model\Connection\ProductQueue $productQueue
	){
        $this->queueFactory = $queueFactory;
				$this->productQueue = $productQueue;
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
					->addAttributeToFilter(
							[
									['attribute'=>'status','neq'=> \Adcurve\Adcurve\Model\Update::QUEUE_STATUS_NEW]
							]
					);

			foreach ($queueCollection as $queue) {
				$this->productQueue->queueAllProducts($queue->getId(), $queue->getStoreId());
				/*
				$queueToDelete = $this->queueFactory->create()->load($queue->getId());
				try {
					$queueToDelete->delete();
					unset($queueToDelete);
				} catch (\Exception $e) {
		            $this->messageManager->addException($e, __('Something went wrong while trying to sync all products.'));
		        }
				*/
			}
    }
}
