<?php
namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Api\UpdateRepositoryInterface;
use Adcurve\Adcurve\Api\Data\UpdateSearchResultsInterfaceFactory;
use Adcurve\Adcurve\Api\Data\UpdateInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Adcurve\Adcurve\Model\ResourceModel\Update as ResourceUpdate;
use Adcurve\Adcurve\Model\ResourceModel\Update\CollectionFactory as UpdateCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class UpdateRepository implements UpdateRepositoryInterface
{
    protected $resource;
    protected $UpdateFactory;
    protected $UpdateCollectionFactory;
    protected $searchResultsFactory;
    protected $dataObjectHelper;
    protected $dataObjectProcessor;
    protected $dataUpdateFactory;

    private $storeManager;

    /**
     * @param ResourceUpdate $resource
     * @param UpdateFactory $updateFactory
     * @param UpdateInterfaceFactory $dataUpdateFactory
     * @param UpdateCollectionFactory $updateCollectionFactory
     * @param UpdateSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceUpdate $resource,
        UpdateFactory $updateFactory,
        UpdateInterfaceFactory $dataUpdateFactory,
        UpdateCollectionFactory $updateCollectionFactory,
        UpdateSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->updateFactory = $updateFactory;
        $this->updateCollectionFactory = $updateCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataUpdateFactory = $dataUpdateFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Adcurve\Adcurve\Api\Data\UpdateInterface $update
    ) {
        /* if (empty($update->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $update->setStoreId($storeId);
        } */
        try {
            $this->resource->save($update);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the update: %1',
                $exception->getMessage()
            ));
        }
        return $update;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($updateId)
    {
        $update = $this->updateFactory->create();
        $update->load($updateId);
        if (!$update->getId()) {
            throw new NoSuchEntityException(__('Update with id "%1" does not exist.', $updateId));
        }
        return $update;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $collection = $this->updateCollectionFactory->create();
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
        
        foreach ($collection as $updateModel) {
            $updateData = $this->dataUpdateFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $updateData,
                $updateModel->getData(),
                'Adcurve\Adcurve\Api\Data\UpdateInterface'
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $updateData,
                'Adcurve\Adcurve\Api\Data\UpdateInterface'
            );
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Adcurve\Adcurve\Api\Data\UpdateInterface $update
    ) {
        try {
            $this->resource->delete($update);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Update: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($updateId)
    {
        return $this->delete($this->getById($updateId));
    }
}