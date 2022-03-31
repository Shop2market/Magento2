<?php

namespace Adcurve\Adcurve\Model;

use Adcurve\Adcurve\Api\Data\ConnectionInterfaceFactory;
use Magento\Framework\Reflection\DataObjectProcessor;
use Adcurve\Adcurve\Model\ResourceModel\Connection as ResourceConnection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotSaveException;
use Adcurve\Adcurve\Api\ConnectionRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Adcurve\Adcurve\Model\ResourceModel\Connection\CollectionFactory as ConnectionCollectionFactory;
use Adcurve\Adcurve\Api\Data\ConnectionSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Store\Model\StoreManagerInterface;

class ConnectionRepository implements ConnectionRepositoryInterface
{
    protected $dataObjectHelper;
    protected $ConnectionCollectionFactory;
    protected $searchResultsFactory;
    protected $dataConnectionFactory;
    private $storeManager;
    protected $resource;
    protected $dataObjectProcessor;
    protected $ConnectionFactory;

    /**
     * @param ResourceConnection $resource
     * @param ConnectionFactory $connectionFactory
     * @param ConnectionInterfaceFactory $dataConnectionFactory
     * @param ConnectionCollectionFactory $connectionCollectionFactory
     * @param ConnectionSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceConnection $resource,
        ConnectionFactory $connectionFactory,
        ConnectionInterfaceFactory $dataConnectionFactory,
        ConnectionCollectionFactory $connectionCollectionFactory,
        ConnectionSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->connectionFactory = $connectionFactory;
        $this->connectionCollectionFactory = $connectionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataConnectionFactory = $dataConnectionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Adcurve\Adcurve\Api\Data\ConnectionInterface $connection
    ) {
        /* if (empty($connection->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $connection->setStoreId($storeId);
        } */
        try {
            $this->resource->save($connection);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the connection: %1',
                $exception->getMessage()
            ));
        }
        return $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($connectionId)
    {
        $connection = $this->connectionFactory->create();
        $connection->load($connectionId);
        if (!$connection->getId()) {
            throw new NoSuchEntityException(__('Connection with id "%1" does not exist.', $connectionId));
        }
        return $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getByStoreId($storeId)
    {
        $connection = $this->connectionFactory->create();
        $connection->load($storeId, 'store_id');
        if (!$connection->getId()) {
            throw new NoSuchEntityException(__('Connection with store_id "%1" does not exist.', $storeId));
        }
        return $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $collection = $this->connectionCollectionFactory->create();
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

        foreach ($collection as $connectionModel) {
            $connectionData = $this->dataConnectionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $connectionData,
                $connectionModel->getData(),
                'Adcurve\Adcurve\Api\Data\ConnectionInterface'
            );
            $items[] = $this->dataObjectProcessor->buildOutputDataArray(
                $connectionData,
                'Adcurve\Adcurve\Api\Data\ConnectionInterface'
            );
        }
        $searchResults->setItems($items);
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Adcurve\Adcurve\Api\Data\ConnectionInterface $connection
    ) {
        try {
            $this->resource->delete($connection);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Connection: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($connectionId)
    {
        return $this->delete($this->getById($connectionId));
    }
}
