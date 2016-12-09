<?php


namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Api\BatchRepositoryInterface;
use Adcurve\Adcurve\Api\Data\BatchSearchResultsInterfaceFactory;
use Adcurve\Adcurve\Api\Data\BatchInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Adcurve\Adcurve\Model\ResourceModel\Batch as ResourceBatch;
use Adcurve\Adcurve\Model\ResourceModel\Batch\CollectionFactory as BatchCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class BatchRepository implements BatchRepositoryInterface
{

    protected $resource;

    protected $BatchFactory;

    protected $BatchCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataBatchFactory;

    private $storeManager;


    /**
     * @param ResourceBatch $resource
     * @param BatchFactory $batchFactory
     * @param BatchInterfaceFactory $dataBatchFactory
     * @param BatchCollectionFactory $batchCollectionFactory
     * @param BatchSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceBatch $resource,
        BatchFactory $batchFactory,
        BatchInterfaceFactory $dataBatchFactory,
        BatchCollectionFactory $batchCollectionFactory,
        BatchSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->batchFactory = $batchFactory;
        $this->batchCollectionFactory = $batchCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataBatchFactory = $dataBatchFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Adcurve\Adcurve\Api\Data\BatchInterface $batch
    ) {
        /* if (empty($batch->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $batch->setStoreId($storeId);
        } */
        try {
            $this->resource->save($batch);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the batch: %1',
                $exception->getMessage()
            ));
        }
        return $batch;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($batchId)
    {
        $batch = $this->batchFactory->create();
        $batch->load($batchId);
        if (!$batch->getId()) {
            throw new NoSuchEntityException(__('Batch with id "%1" does not exist.', $batchId));
        }
        return $batch;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $collection = $this->batchCollectionFactory->create();
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
        
        foreach ($collection as $batchModel) {
            $batchData = $this->dataBatchFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $batchData,
                $batchModel->getData(),
                'Adcurve\Adcurve\Api\Data\BatchInterface'
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $batchData,
                'Adcurve\Adcurve\Api\Data\BatchInterface'
            );
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Adcurve\Adcurve\Api\Data\BatchInterface $batch
    ) {
        try {
            $this->resource->delete($batch);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Batch: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($batchId)
    {
        return $this->delete($this->getById($batchId));
    }
}
