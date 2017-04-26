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
		$queueCollection = $queue->getCollection();
		foreach ($queueCollection as $queue) {
			$this->productQueue->queueAllProducts($queue->getStoreId());
			$queueToDelete = $this->queueFactory->create()->load($queue->getId());
			try {
				$queueToDelete->delete();
				unset($queueToDelete);
			} catch (\Exception $e) {
	            $this->messageManager->addException($e, __('Something went wrong while trying to sync all products.'));
	        }
		}
    }
}