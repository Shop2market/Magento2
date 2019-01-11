<?php
namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Adcurve\Adcurve\Api\Data\QueueInterfaceFactory;
use Adcurve\Adcurve\Model\ResourceModel\Queue as ResourceQueue;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Adcurve\Adcurve\Api\Data\QueueSearchResultsInterfaceFactory;
use Adcurve\Adcurve\Api\QueueRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;

class QueueRepository implements QueueRepositoryInterface
{

    protected $dataObjectHelper;
    protected $searchResultsFactory;
    protected $QueueFactory;
    protected $dataQueueFactory;
    private $storeManager;
    protected $resource;
    protected $dataObjectProcessor;
    protected $QueueCollectionFactory;

    /**
     * @param ResourceQueue $resource
     * @param QueueFactory $queueFactory
     * @param QueueInterfaceFactory $dataQueueFactory
     * @param QueueCollectionFactory $queueCollectionFactory
     * @param QueueSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceQueue $resource,
        QueueFactory $queueFactory,
        QueueInterfaceFactory $dataQueueFactory,
        QueueCollectionFactory $queueCollectionFactory,
        QueueSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->queueFactory = $queueFactory;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataQueueFactory = $dataQueueFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Adcurve\Adcurve\Api\Data\QueueInterface $queue
    ) {
        /* if (empty($queue->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $queue->setStoreId($storeId);
        } */
        try {
            $this->resource->save($queue);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the queue: %1',
                $exception->getMessage()
            ));
        }
        return $queue;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($queueId)
    {
        $queue = $this->queueFactory->create();
        $queue->load($queueId);
        if (!$queue->getId()) {
            throw new NoSuchEntityException(__('Queue with id "%1" does not exist.', $queueId));
        }
        return $queue;
    }

	/**
     * {@inheritdoc}
     */
    public function getByStoreId($storeId)
    {
        $queue = $this->queueFactory->create();
        $queue->load($storeId, 'store_id');
        if (!$queue->getId()) {
            throw new NoSuchEntityException(__('Queue with store_id "%1" does not exist.', $storeId));
        }
        return $queue;
    }
  	/**
       * {@inheritdoc}
       */
      public function getByStatus($status)
      {
          $queue = $this->queueFactory->create();
          $queue->load($status, 'status');
          if (!$queue->getId()) {
              throw new NoSuchEntityException(__('Queue with status "%1" does not exist.', $status));
          }
          return $queue;
      }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->queueCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        $items = [];

        foreach ($collection as $queueModel) {
            $queueData = $this->dataQueueFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $queueData,
                $queueModel->getData(),
                'Adcurve\Adcurve\Api\Data\QueueInterface'
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $queueData,
                'Adcurve\Adcurve\Api\Data\QueueInterface'
            );
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Adcurve\Adcurve\Api\Data\QueueInterface $queue
    ) {
        try {
            $this->resource->delete($queue);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Queue: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($queueId)
    {
        return $this->delete($this->getById($queueId));
    }
}
