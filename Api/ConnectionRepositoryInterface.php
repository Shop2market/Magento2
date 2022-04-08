<?php

namespace Adcurve\Adcurve\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ConnectionRepositoryInterface
{
    /**
     * Save Connection
     * @param \Adcurve\Adcurve\Api\Data\ConnectionInterface $connection
     * @return \Adcurve\Adcurve\Api\Data\ConnectionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function save(
        \Adcurve\Adcurve\Api\Data\ConnectionInterface $connection
    );

    /**
     * Retrieve Connection
     * @param string $connectionId
     * @return \Adcurve\Adcurve\Api\Data\ConnectionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getById($connectionId);

    /**
     * Retrieve Connection matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Adcurve\Adcurve\Api\Data\ConnectionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Connection
     * @param \Adcurve\Adcurve\Api\Data\ConnectionInterface $connection
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function delete(
        \Adcurve\Adcurve\Api\Data\ConnectionInterface $connection
    );

    /**
     * Delete Connection by ID
     * @param string $connectionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */

    public function deleteById($connectionId);
}
